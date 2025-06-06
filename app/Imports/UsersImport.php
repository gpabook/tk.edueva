<?php

namespace App\Imports;

use App\Models\User;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Log;
use Spatie\Permission\Models\Role as SpatieRole; // Aliased
use Throwable;
use Maatwebsite\Excel\Concerns\ToModel;
use Maatwebsite\Excel\Concerns\WithHeadingRow;
use Maatwebsite\Excel\Concerns\WithValidation;
use Maatwebsite\Excel\Concerns\SkipsOnError;
use Maatwebsite\Excel\Concerns\SkipsOnFailure;
use Maatwebsite\Excel\Validators\Failure;
use Illuminate\Database\QueryException; // Import QueryException

class UsersImport implements
    ToModel,
    WithHeadingRow,
    WithValidation,
    SkipsOnError,
    SkipsOnFailure
{
    protected int $importedCount = 0;
    protected int $skippedCount = 0;
    protected array $failures = []; // For Maatwebsite/Excel validation failures
    protected array $processingErrors = []; // For errors caught within model() or by SkipsOnError

    public function model(array $row)
    {
        // Ensure all keys are lowercase for consistency, as Maatwebsite/Excel might snake_case them
        $row = array_change_key_case($row, CASE_LOWER);

        $studentId = (string)($row['student_id'] ?? '');
        $email     = trim($row['email'] ?? '');
        $nameTh    = trim($row['name_th'] ?? '');
        $surnameTh = trim($row['surname_th'] ?? '');

        // Construct the 'name' field. This is a common requirement for Laravel's default User setup.
        // We'll use the Thai full name here. Adjust if your 'name' field should be something else (e.g., English name).
        $fullName = trim($nameTh . ' ' . $surnameTh);

        // Pre-emptive checks for essential data
        if (empty($fullName) && (empty($nameTh) || empty($surnameTh))) { // If name_th or surname_th is empty, fullName will be too.
            $this->processingErrors[] = [
                'row_identifier' => $studentId ?: $email ?: 'Unknown Row',
                'attribute' => 'name_th/surname_th', // Or 'name' if you prefer
                'message' => "Cannot construct user's full name because Thai first name or Thai surname is missing from the CSV.",
                'values' => $this->simplifyRowData($row),
            ];
            $this->skippedCount++;
            return null;
        }

        if (empty($studentId)) { // Email uniqueness is usually handled by validation rules
             $this->processingErrors[] = [
                'row_identifier' => $email ?: 'Unknown Row',
                'attribute' => 'student_id',
                'message' => 'Student ID is missing.',
                'values' => $this->simplifyRowData($row),
            ];
            $this->skippedCount++;
            return null;
        }
         if (empty($email)) {
             $this->processingErrors[] = [
                'row_identifier' => $studentId ?: 'Unknown Row',
                'attribute' => 'email',
                'message' => 'Email is missing.',
                'values' => $this->simplifyRowData($row),
            ];
            $this->skippedCount++;
            return null;
        }


        try {
            $userData = [
                'name'              => $fullName, // ** Populate the 'name' field **
                'student_id'        => $studentId,
                'name_th'           => $nameTh,
                'surname_th'        => $surnameTh,
                'name_en'           => trim($row['name_en'] ?? ''),       // Assuming 'name_en' might be in CSV. If not, ensure DB column is nullable.
                'surname_en'        => trim($row['surname_en'] ?? ''),   // Assuming 'surname_en' might be in CSV. If not, ensure DB column is nullable.
                'gender'            => $row['gender'] ?? null,        // From CSV. Ensure 'gender' column exists in DB & $fillable or is ignored.
                'email'             => $email,
                'password'          => Hash::make('secret'), // Consider a more robust default password strategy
                'email_verified_at' => now(),
                'status'            => $row['status'] ?? 1,       // Default to 1 (active)
                'role'              => $row['role'] ?? 4,       // For your custom integer 'role' column
            ];

            $user = User::create($userData);

            // Assign Spatie role using the 'role' value from CSV as the Spatie role ID
            $spatieRoleId = $row['role'] ?? null; // Use the same role value from CSV
            if ($user && $spatieRoleId) {
                $spatieModelRole = SpatieRole::findById((int)$spatieRoleId); // Find Spatie Role by its ID
                if ($spatieModelRole) {
                    $user->assignRole($spatieModelRole->name);
                } else {
                    $warningMsg = "Spatie Role ID '{$spatieRoleId}' not found for user {$email}. User created, but this specific Spatie role was not assigned.";
                    Log::warning($warningMsg);
                    // Optionally add to a list of non-fatal warnings to show the user
                    $this->processingErrors[] = [
                        'row_identifier' => $email,
                        'attribute' => 'spatie_role_assignment',
                        'message' => $warningMsg,
                        'values' => ['role_id_attempted' => $spatieRoleId],
                    ];
                }
            }

            $this->importedCount++;
            return $user;

        } catch (QueryException $qe) {
            $errorMsg = $qe->getMessage();
            $message = "Database error: " . substr($errorMsg, 0, 250); // Default message

            if (preg_match('/1364 Field \'([a-zA-Z0-9_]+)\' doesn\'t have a default value/', $errorMsg, $matches)) {
                $message = "Database error: Field '{$matches[1]}' is required but was not provided or has no default value.";
            } elseif (preg_match('/Column \'([a-zA-Z0-9_]+)\' cannot be null/', $errorMsg, $matches)) {
                 $message = "Database error: Field '{$matches[1]}' cannot be null and no value was provided.";
            } elseif (str_contains($errorMsg, 'Duplicate entry') || str_contains($errorMsg, 'UNIQUE constraint failed')) {
                 if (str_contains($errorMsg, 'users_email_unique') || str_contains($errorMsg, $email)) { // Adjust for SQLite vs MySQL messages
                    $message = "Database error: Email '{$email}' already exists.";
                } elseif (str_contains($errorMsg, 'users_student_id_unique') || str_contains($errorMsg, $studentId)) {
                    $message = "Database error: Student ID '{$studentId}' already exists.";
                } else {
                    $message = "Database error: A unique constraint was violated (e.g., duplicate email or student ID).";
                }
            }

            $this->processingErrors[] = [
                'row_identifier' => $studentId ?: $email,
                'attribute' => 'database_operation',
                'message' => $message,
                'values' => $this->simplifyRowData($row),
            ];
            Log::error("User Import QueryException for {$email} (Row data: " . json_encode($row) . "): " . $qe->getMessage());
            $this->skippedCount++;
            return null;
        } catch (Throwable $e) {
            $this->processingErrors[] = [
                'row_identifier' => $studentId ?: $email,
                'attribute' => 'general_error',
                'message' => "An unexpected error occurred during processing: " . substr($e->getMessage(), 0, 250),
                'values' => $this->simplifyRowData($row),
            ];
            Log::error("User Import Throwable for {$email} (Row data: " . json_encode($row) . "): " . $e->getMessage() . ' Trace: ' . $e->getTraceAsString());
            $this->skippedCount++;
            return null;
        }
    }

    public function rules(): array
    {
        // Add '*. ' prefix for row-level validation when using WithValidation with ToModel
        return [
            // The 'name' field itself is constructed, so we validate its components
            '*.student_id' => 'required|string|max:20|unique:users,student_id',
            '*.name_th'    => 'required|string|max:255', // Required to construct 'name'
            '*.surname_th' => 'required|string|max:255', // Required to construct 'name'
            // If name_en and surname_en are not in your CSV, make them nullable here
            // AND ensure the database columns are also nullable or defaults are handled.
            '*.name_en'    => 'nullable|string|max:255',
            '*.surname_en' => 'nullable|string|max:255',
            '*.gender'     => 'nullable|string|in:male,female,other', // CSV header: gender
            '*.email'      => 'required|email|max:255|unique:users,email',
            '*.role'       => 'nullable|integer|exists:roles,id', // Validates Spatie Role ID against 'id' in 'roles' table
            '*.status'     => 'nullable|integer|in:0,1',
        ];
    }

    public function onFailure(Failure ...$failures)
    {
        // This method is called when validation rules in rules() fail for a row.
        $this->skippedCount += count($failures);
        foreach ($failures as $failure) {
            $this->failures[] = [ // $this->failures is used by getFailures() in the controller
                'row' => $failure->row(), // Row number from CSV
                'attribute' => $failure->attribute(), // e.g., '*.email'
                'errors' => $failure->errors(), // Array of error messages from validation
                'values' => $this->simplifyRowData($failure->values()), // Original row data that failed
            ];
        }
    }

    public function onError(Throwable $e)
    {
        // This method is called for other types of errors during the import process by Maatwebsite/Excel,
        // or if an exception is re-thrown from model().
        $this->skippedCount++;
        $this->processingErrors[] = [ // Add to the same list as model() processing errors
            'row_identifier' => 'General Import System',
            'attribute' => 'system_error',
            'message' => "An unhandled import system error occurred: " . substr($e->getMessage(),0, 250),
            'values' => [],
        ];
        Log::critical("UserImport Global Error (SkipsOnError): " . $e->getMessage(), ['trace' => $e->getTraceAsString()]);
    }

    public function getImportedCount(): int { return $this->importedCount; }
    public function getSkippedCount(): int { return $this->skippedCount; }
    public function getFailures(): array { return $this->failures; } // Returns validation rule failures
    public function getErrors(): array { return $this->processingErrors; } // Returns model processing errors and SkipsOnError errors

    protected function simplifyRowData(array $row): array
    {
        $simplified = [];
        foreach ($row as $key => $value) {
            $simplified[$key] = is_scalar($value) ? mb_strimwidth((string)$value, 0, 60, "...") : (is_array($value) ? '[ARRAY]' : '[OBJECT]');
        }
        return $simplified;
    }
}
