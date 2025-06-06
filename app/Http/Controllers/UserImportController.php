<?php

namespace App\Http\Controllers;

use App\Imports\UsersImport; // Your import class
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;
use Inertia\Inertia;
use Maatwebsite\Excel\Facades\Excel;
use Maatwebsite\Excel\Validators\ValidationException as ExcelValidationException; // For specific Excel validation errors

class UserImportController extends Controller
{
    /**
     * Show the form for importing users.
     *
     * @return \Inertia\Response
     */
    public function create()
    {
        return Inertia::render('Users/Import', [
            'successMessage' => session('successMessage'),
            'importErrors' => session('importErrors', []), // Default to empty array
            // General form validation errors (e.g., file not provided)
            // will be available via props.errors automatically in Vue
        ]);
    }

    /**
     * Handle the uploaded CSV file and import users.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\RedirectResponse
     */
    public function store(Request $request)
    {
        // It's good practice to set this, but consider if it's always needed or can be higher for very large files.
        // For very large files, queueing the import is a better approach.
        ini_set('max_execution_time', 300); // 5 minutes

        $request->validate([
            'csv_file' => 'required|file|mimes:csv,txt|max:8192', // 8MB max, adjust as needed
        ]);

        $import = new UsersImport(); // Create an instance of your import class

        try {
            // Import the file using your UsersImport class
            Excel::import($import, $request->file('csv_file'));

            $importedCount = $import->getImportedCount();
            $skippedCount = $import->getSkippedCount(); // Total rows skipped (validation or processing errors)

            $successMessage = "Successfully imported {$importedCount} users.";
            if ($skippedCount > 0) {
                $successMessage .= " Skipped {$skippedCount} rows due to errors or validation failures.";
            }

            // Get structured errors from the import class
            $validationRuleFailures = $import->getFailures();   // From SkipsOnFailure (validation rules defined in UsersImport::rules())
            $otherProcessingErrors = $import->getErrors();      // From UsersImport::model() try-catch blocks or UsersImport::onError()

            $allImportProcessErrors = [];

            // Format validation rule failures (attribute-level from rules())
            // These are typically errors where a specific rule (e.g., 'required', 'email', 'unique') failed for a field.
            foreach ($validationRuleFailures as $failure) {
                $allImportProcessErrors[] = [
                    'row' => $failure['row'] ?? 'N/A', // Maatwebsite/Excel provides row number for these
                    'message' => "Row {$failure['row']}: Validation for '{$failure['attribute']}' failed. Errors: " . (is_array($failure['errors']) ? implode(', ', $failure['errors']) : ($failure['errors'] ?? 'Unknown validation error')),
                    'values' => isset($failure['values']) ? (is_array($failure['values']) ? http_build_query($failure['values'], '', ', ') : $failure['values']) : 'N/A',
                ];
            }

            // Format other processing errors (e.g., database errors from model() catches, or general errors from SkipsOnError)
            // These could be due to database constraints not caught by rules, or other logic issues.
            foreach ($otherProcessingErrors as $error) {
                 $allImportProcessErrors[] = [
                    'row' => $error['row_identifier'] ?? ($error['row'] ?? 'N/A'), // 'row_identifier' is what we defined in the modified UsersImport
                    'message' => ($error['attribute'] ? "Error with '{$error['attribute']}': " : '') . ($error['message'] ?? 'Unknown processing error.'),
                    'values' => isset($error['values']) ? (is_array($error['values']) ? http_build_query($error['values'], '', ', ') : $error['values']) : 'N/A',
                ];
            }

            return redirect()->route('users.import.create')
                ->with('successMessage', $successMessage)
                ->with('importErrors', $allImportProcessErrors);

        } catch (ExcelValidationException $e) {
            // This catch block handles validation exceptions specifically from Maatwebsite/Excel
            // if you are NOT using SkipsOnFailure in your import class, or if an earlier validation (e.g., global CSV structure) fails.
            // With SkipsOnFailure implemented, most row-level validation errors are collected by $import->getFailures().
            $failures = $e->failures();
            $importErrors = [];
            foreach ($failures as $failure) {
                $importErrors[] = [
                    'row' => $failure->row(),
                    'message' => "Row {$failure->row()}: Validation for '{$failure->attribute()}' failed. Errors: " . implode(', ', $failure->errors()),
                    'values' => implode(', ', $failure->values()),
                ];
            }
            Log::warning("UserImport ExcelValidationException: ", $failures);
            return redirect()->route('users.import.create')
                ->with('successMessage', 'Import process completed with validation errors.') // Or a more direct error message
                ->with('importErrors', $importErrors);

        } catch (\Exception $e) {
            // Catch any other unexpected errors during the import process
            Log::error("UserImport Controller - General Exception: " . $e->getMessage(), [
                'trace' => $e->getTraceAsString(),
            ]);
            // It's better to flash a generic error message and log the details
            // rather than exposing potentially sensitive error details to the user.
            // The Inertia::render in create() will show these via `props.errors` if they are validation errors on 'csv_file'
            // For a general server error, a flashed 'error' message might be better.
            return redirect()->route('users.import.create')
                ->withErrors(['csv_file' => 'An unexpected error occurred during the import process: ' . $e->getMessage() . '. Please check the logs or contact support.']);
        }
    }
}
