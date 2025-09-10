<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Facades\Validator;
use Illuminate\Support\Str;

class UserController extends Controller
{
    /**
     * Display a listing of users.
     */
    public function index()
    {
        $users = User::paginate(10);

        return view('users.index', compact('users'));
    }

    /**
     * Show the form for editing the specified user.
     */
    public function edit(User $user)
    {
        return view('users.edit', compact('user'));
    }

    /**
     * Update the specified user in storage.
     */
    public function update(Request $request, User $user)
    {
        $rules = [
            'name' => ['required', 'string', 'max:255'],
            'email' => ['required', 'string', 'email', 'max:255', 'unique:users,email,'.$user->id],
        ];

        // Only add password validation if password is provided
        if ($request->filled('password')) {
            $rules['password'] = ['required', 'string', 'min:8', 'confirmed'];
        }

        $validator = Validator::make($request->all(), $rules);

        if ($validator->fails()) {
            return redirect()->back()
                ->withErrors($validator)
                ->withInput();
        }

        $updateData = [
            'name' => $request->name,
            'email' => $request->email,
        ];

        // Only update password if provided
        if ($request->filled('password')) {
            $updateData['password'] = Hash::make($request->password);
        }

        $user->update($updateData);

        return redirect()->route('users.edit', $user)
            ->with('success', 'User updated successfully!');
    }

    /**
     * Show the form for deleting the specified user.
     */
    public function delete(User $user)
    {
        return view('users.delete', compact('user'));
    }

    /**
     * Remove the specified user from storage.
     */
    public function destroy(User $user)
    {
        try {
            $userName = $user->name;
            $user->delete();

            return redirect()->route('users.index')
                ->with('success', "User '{$userName}' deleted successfully!");
        } catch (\Exception $e) {
            return redirect()->route('users.index')
                ->with('error', 'Failed to delete user. Please try again.');
        }
    }

    /**
     * Show the form for importing users from CSV.
     */
    public function showImportForm()
    {
        return view('users.import');
    }

    /**
     * Import users from CSV file.
     */
    public function import(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'csv_file' => ['required', 'file', 'mimes:csv,txt', 'max:2048'],
        ]);

        if ($validator->fails()) {
            return redirect()->back()
                ->withErrors($validator)
                ->withInput();
        }

        try {
            $file = $request->file('csv_file');
            $filename = time().'_'.Str::random(10).'.csv';

            // Ensure imports directory exists
            Storage::makeDirectory('imports');

            $path = $file->storeAs('imports', $filename);
            $fullPath = Storage::path($path);

            $csvData = $this->parseCsvFile($fullPath);

            $importedCount = 0;
            $errors = [];

            foreach ($csvData as $index => $row) {
                $rowNumber = $index + 2; // +2 because array is 0-indexed and CSV has header

                // Validate required fields
                if (empty($row['name']) || empty($row['email'])) {
                    $errors[] = "Row {$rowNumber}: Name and email are required.";

                    continue;
                }

                // Validate email format
                if (! filter_var($row['email'], FILTER_VALIDATE_EMAIL)) {
                    $errors[] = "Row {$rowNumber}: Invalid email format for '{$row['email']}'.";

                    continue;
                }

                // Check for duplicate email
                if (User::where('email', $row['email'])->exists()) {
                    $errors[] = "Row {$rowNumber}: Email '{$row['email']}' already exists.";

                    continue;
                }

                // Create user
                User::create([
                    'name' => $row['name'],
                    'email' => $row['email'],
                    'password' => Hash::make($row['password'] ?? 'password123'),
                ]);

                $importedCount++;
            }

            // Clean up uploaded file
            Storage::delete($path);

            $message = "Successfully imported {$importedCount} users.";
            if (! empty($errors)) {
                $message .= ' '.count($errors).' rows had errors: '.implode(' ', array_slice($errors, 0, 5));
                if (count($errors) > 5) {
                    $message .= ' (and '.(count($errors) - 5).' more errors)';
                }
            }

            return redirect()->route('users.index')
                ->with('success', $message);

        } catch (\Exception $e) {
            return redirect()->back()
                ->with('error', 'Failed to import users: '.$e->getMessage());
        }
    }

    /**
     * Export users to CSV file.
     */
    public function export()
    {
        try {
            $users = User::all();

            $filename = 'users_export_'.date('Y-m-d_H-i-s').'.csv';

            // Create exports directory if it doesn't exist
            $exportsDir = storage_path('app/private/exports');
            if (! is_dir($exportsDir)) {
                mkdir($exportsDir, 0755, true);
            }

            // Create the full file path
            $fullPath = $exportsDir.'/'.$filename;

            // Debug: Log the file path
            \Log::info('Export file path: '.$fullPath);

            // Create and write to the file
            $file = fopen($fullPath, 'w');

            if (! $file) {
                throw new \Exception('Could not create file: '.$fullPath);
            }

            // Add CSV header
            fputcsv($file, ['ID', 'Name', 'Email', 'Created At', 'Updated At']);

            // Add user data
            foreach ($users as $user) {
                fputcsv($file, [
                    $user->id,
                    $user->name,
                    $user->email,
                    $user->created_at->format('Y-m-d H:i:s'),
                    $user->updated_at->format('Y-m-d H:i:s'),
                ]);
            }

            fclose($file);

            // Verify file was created
            if (! file_exists($fullPath)) {
                throw new \Exception('File was not created: '.$fullPath);
            }

            \Log::info('Export file created successfully: '.$fullPath);

            // Set headers for download
            $headers = [
                'Content-Type' => 'text/csv',
                'Content-Disposition' => 'attachment; filename="'.$filename.'"',
            ];

            // Return the file for download (don't delete after download)
            return response()->download($fullPath, $filename, $headers)->deleteFileAfterSend(false);

        } catch (\Exception $e) {
            \Log::error('Export failed: '.$e->getMessage());

            return redirect()->back()->with('error', 'Export failed: '.$e->getMessage());
        }
    }

    /**
     * List exported files.
     */
    public function listExports()
    {
        $exports = [];

        if (Storage::disk('private')->exists('exports')) {
            $files = Storage::disk('private')->files('exports');

            foreach ($files as $file) {
                $exports[] = [
                    'filename' => basename($file),
                    'path' => $file,
                    'size' => Storage::disk('private')->size($file),
                    'created_at' => date('Y-m-d H:i:s', Storage::disk('private')->lastModified($file)),
                ];
            }

            // Sort by creation time (newest first)
            usort($exports, function ($a, $b) {
                return strtotime($b['created_at']) - strtotime($a['created_at']);
            });
        }

        return view('users.exports', compact('exports'));
    }

    /**
     * Parse CSV file and return array of data.
     */
    private function parseCsvFile($filePath)
    {
        $data = [];
        $handle = fopen($filePath, 'r');

        if ($handle !== false) {
            $header = fgetcsv($handle);

            while (($row = fgetcsv($handle)) !== false) {
                $data[] = array_combine($header, $row);
            }

            fclose($handle);
        }

        return $data;
    }
}
