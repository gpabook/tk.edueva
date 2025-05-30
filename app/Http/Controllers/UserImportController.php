<?php

namespace App\Http\Controllers;

use App\Imports\UsersImport; // Your import class
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;
use Inertia\Inertia;
use Maatwebsite\Excel\Facades\Excel;
use Maatwebsite\Excel\Validators\ValidationException; // For specific Excel validation errors

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
            // You can pass existing flash messages or other data if needed
            'successMessage' => session('successMessage'),
            'importErrors' => session('importErrors', []), // Default to empty array
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
        $request->validate([
            'csv_file' => 'required|file|mimes:csv,txt|max:2048', // Adjust max size as needed
        ]);

        $import = new UsersImport(); // Create an instance of your import class

        try {
            // Import the file using your UsersImport class
            Excel::import($import, $request->file('csv_file'));

            // Get statistics from your import class
            $importedCount = $import->getImportedCount();
            $skippedCount = $import->getSkippedCount(); // This would include validation failures and other errors handled by SkipsOnFailure/SkipsOnError

            $successMessage = "Successfully imported {$importedCount} users.";
            if ($skippedCount > 0) {
                $successMessage .= " Skipped {$skippedCount} rows due to errors or validation failures.";
            }

            // Combine validation failures (from SkipsOnFailure) and other errors (from SkipsOnError)
            // The UsersImport class should provide methods to retrieve these
            $validationFailures = $import->getFailures(); // Assuming getFailures() returns structured validation errors
            $otherImportErrors = $import->getErrors();   // Assuming getErrors() returns other processing errors

            $allImportProcessErrors = [];

            // Format validation failures
            foreach ($validationFailures as $failure) {
                $allImportProcessErrors[] = [
                    'row' => $failure['row'] ?? 'N/A', // The UsersImport::onFailure method provides 'row'
                    'message' => is_array($failure['errors']) ? implode(', ', $failure['errors']) : ($failure['errors'] ?? 'Unknown validation error'),
                    'values' => isset($failure['values']) ? (is_array($failure['values']) ? implode(', ', $failure['values']) : $failure['values']) : 'N/A',
                ];
            }

            // Format other errors
            foreach ($otherImportErrors as $error) {
                 // Assuming $error is a string or an array with a 'message' key
                if (is_string($error)) {
                    $allImportProcessErrors[] = ['row' => 'N/A', 'message' => $error, 'values' => 'N/A'];
                } elseif (is_array($error) && isset($error['message'])) {
                     $allImportProcessErrors[] = [
                        'row' => $error['row'] ?? 'N/A',
                        'message' => $error['message'],
                        'values' => isset($error['values']) ? (is_array($error['values']) ? implode(', ', $error['values']) : $error['values']) : 'N/A'
                    ];
                }
            }


            return redirect()->route('users.import.create')
                ->with('successMessage', $successMessage)
                ->with('importErrors', $allImportProcessErrors);

        } catch (ValidationException $e) {
            // This catch block handles validation exceptions specifically from Maatwebsite/Excel
            // if you are NOT using SkipsOnFailure in your import class, or if an earlier validation fails.
            // If SkipsOnFailure is used, most row-level validation errors are handled there.
            $failures = $e->failures();
            $importErrors = [];
            foreach ($failures as $failure) {
                $importErrors[] = [
                    'row' => $failure->row(),
                    'message' => implode(', ', $failure->errors()), // Get all errors for the row
                    'values' => implode(', ', $failure->values()),  // Show the problematic row's data
                ];
            }
            Log::warning("UserImport ValidationException: ", $failures);
            return redirect()->route('users.import.create')
                ->with('successMessage', 'Import process completed with validation errors.')
                ->with('importErrors', $importErrors);

        } catch (\Exception $e) {
            // Catch any other unexpected errors during the import process
            Log::error("UserImport Controller - General Exception: " . $e->getMessage(), [
                'trace' => $e->getTraceAsString(),
            ]);
            // It's better to flash a generic error message and log the details
            // rather than exposing potentially sensitive error details to the user.
            return redirect()->route('users.import.create')
                ->withErrors(['csv_file' => 'An unexpected error occurred during the import process. Please check the logs or contact support.']);
        }
    }
}