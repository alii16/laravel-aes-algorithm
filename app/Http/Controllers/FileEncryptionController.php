<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Facades\Log;
use Exception;

class FileEncryptionController extends Controller
{
    public function index()
    {
        return view('welcome');
    }

    /**
     * Handle file download with token
     */
    public function download(Request $request, $token)
    {
        // Validate download token
        $downloadData = session()->get("download_{$token}");
        
        if (!$downloadData) {
            return redirect()->route('home')->withErrors(['download' => 'Invalid or expired download link.']);
        }

        // Check if file exists
        $filePath = storage_path("app/" . $downloadData['file_path']);
        if (!file_exists($filePath)) {
            // Clean up session
            session()->forget("download_{$token}");
            return redirect()->route('home')->withErrors(['download' => 'File not found or has expired.']);
        }

        // Check if download data is not too old (24 hour limit instead of 1 hour)
        if (now()->diffInHours($downloadData['created_at']) > 24) {
            // Clean up session and file
            session()->forget("download_{$token}");
            if (file_exists($filePath)) {
                unlink($filePath);
            }
            return redirect()->route('home')->withErrors(['download' => 'Download link has expired after 24 hours.']);
        }

        // Clean up session after successful download
        session()->forget("download_{$token}");
        
        // Return file download
        return response()->download($filePath, $downloadData['filename'])
                ->deleteFileAfterSend(true);
    }

    public function process(Request $request)
    {
        try {
            // Tentukan aturan validasi berdasarkan aksi yang dipilih
            $rules = [
                'file' => 'required|file|max:51200', // Max 50MB
                'key' => 'required|min:16|max:32',
                'action' => 'required|in:encrypt,decrypt'
            ];
        
            if ($request->action === 'encrypt') {
                $rules['file'] = 'required|mimes:pdf,doc,docx|max:51200';
            } elseif ($request->action === 'decrypt') {
                $rules['file'] = 'required|max:51200'; // Remove mime validation for .enc files
            }

            // Custom validation messages
            $messages = [
                'file.required' => 'Please select a file to process.',
                'file.mimes' => 'Only PDF and Word documents (DOC, DOCX) are allowed for encryption.',
                'file.max' => 'File size must not exceed 50MB.',
                'key.required' => 'Encryption key is required.',
                'key.min' => 'Encryption key must be at least 16 characters long.',
                'key.max' => 'Encryption key must not exceed 32 characters.',
                'action.required' => 'Please select an action (encrypt or decrypt).',
                'action.in' => 'Invalid action selected.'
            ];
        
            $request->validate($rules, $messages);

            $file = $request->file('file');
            $originalFileName = $file->getClientOriginalName();
            $fileSize = $file->getSize();
            $action = $request->action;
            
            // Create encryption key hash
            $key = substr(hash('sha256', $request->key, true), 0, 16);
            
            // Read file content
            $data = file_get_contents($file);
            if ($data === false) {
                return back()->withErrors(['file' => 'Failed to read the uploaded file. Please try again.'])
                    ->with('warning', 'File reading failed. Please check your file and try again.');
            }

            // Generate a more secure IV (16 bytes for AES-128-CBC)
            $iv = openssl_random_pseudo_bytes(16);
            
            // Log the process attempt
            Log::info("File {$action} attempt", [
                'filename' => $originalFileName,
                'file_size' => $fileSize,
                'action' => $action,
                'ip' => $request->ip()
            ]);

            if ($action === 'encrypt') {
                // Encryption process
                $encryptedData = openssl_encrypt($data, 'aes-128-cbc', $key, 0, $iv);
                
                if ($encryptedData === false) {
                    Log::error("Encryption failed for file: {$originalFileName}");
                    return back()->withErrors(['encryption' => 'File encryption failed. Please try again.'])
                        ->with('warning', 'Encryption process encountered an error.');
                }

                // Combine IV and encrypted data for storage
                $processedData = base64_encode($iv . base64_decode($encryptedData));
                $filename = $originalFileName . '.enc';
                
                // Success message for encryption
                $successMessage = "File '{$originalFileName}' has been successfully encrypted. Keep your encryption key safe!";
                
            } else {
                // Decryption process
                try {
                    // Decode the file content
                    $decodedData = base64_decode($data);
                    if ($decodedData === false) {
                        throw new Exception('Invalid encrypted file format');
                    }

                    // Extract IV and encrypted content
                    $iv = substr($decodedData, 0, 16);
                    $encryptedContent = base64_encode(substr($decodedData, 16));
                    
                    $decryptedData = openssl_decrypt($encryptedContent, 'aes-128-cbc', $key, 0, $iv);
                    
                    if ($decryptedData === false) {
                        Log::warning("Decryption failed - incorrect key or corrupted file", [
                            'filename' => $originalFileName,
                            'ip' => $request->ip()
                        ]);
                        
                        return back()->withErrors([
                                'decryption' => 'Decryption failed. Please verify your encryption key and ensure the file is not corrupted.'
                            ])
                            ->with('warning', 'Unable to decrypt the file. Check your key and file integrity.');
                    }

                    $processedData = $decryptedData;
                    
                    // Remove .enc extension from filename
                    $filename = preg_replace('/\.enc$/', '', $originalFileName);
                    if ($filename === $originalFileName) {
                        // If no .enc extension was found, add a prefix to avoid overwriting
                        $filename = 'decrypted_' . $originalFileName;
                    }
                    
                    // Success message for decryption
                    $successMessage = "File has been successfully decrypted and is ready for download.";
                    
                } catch (Exception $e) {
                    Log::error("Decryption error for file: {$originalFileName}", [
                        'error' => $e->getMessage(),
                        'ip' => $request->ip()
                    ]);
                    
                    return back()->withErrors([
                            'decryption' => 'Decryption failed: ' . $e->getMessage()
                        ])
                        ->with('warning', 'Unable to decrypt the file. Please verify the file format and encryption key.');
                }
            }

            // Store processed file temporarily
            $tempPath = 'temp/' . uniqid() . '_' . $filename;
            if (!Storage::disk('local')->put($tempPath, $processedData)) {
                Log::error("Failed to store processed file: {$filename}");
                return back()->withErrors(['storage' => 'Failed to save processed file. Please try again.'])
                    ->with('warning', 'File processing completed but save operation failed.');
            }

            // Log successful process
            Log::info("File {$action} completed successfully", [
                'original_filename' => $originalFileName,
                'processed_filename' => $filename,
                'original_size' => $fileSize,
                'processed_size' => strlen($processedData),
                'ip' => $request->ip()
            ]);

            // Create a unique download token
            $downloadToken = uniqid();
            session()->put("download_{$downloadToken}", [
                'file_path' => $tempPath,
                'filename' => $filename,
                'message' => $successMessage,
                'created_at' => now()
            ]);

            // Redirect back with success message and download token
            return back()->with('success', $successMessage)
                        ->with('download_token', $downloadToken)
                        ->with('download_filename', $filename);

        } catch (\Illuminate\Validation\ValidationException $e) {
            // Validation errors are automatically handled by Laravel
            Log::warning('Validation failed for file encryption/decryption', [
                'errors' => $e->errors(),
                'ip' => $request->ip()
            ]);
            throw $e;
            
        } catch (Exception $e) {
            // Handle any other unexpected errors
            Log::error('Unexpected error in file encryption/decryption process', [
                'error' => $e->getMessage(),
                'trace' => $e->getTraceAsString(),
                'ip' => $request->ip(),
                'file' => $request->hasFile('file') ? $request->file('file')->getClientOriginalName() : 'No file'
            ]);
            
            return back()->withErrors([
                    'system' => 'An unexpected error occurred during file processing. Please try again later.'
                ])
                ->with('warning', 'System error encountered. Please contact support if this issue persists.');
        }
    }

    /**
     * Generate a secure random IV for encryption
     * 
     * @return string
     */
    private function generateSecureIV()
    {
        return openssl_random_pseudo_bytes(16);
    }

    /**
     * Validate file size and type for security
     * 
     * @param \Illuminate\Http\UploadedFile $file
     * @param string $action
     * @return array|null
     */
    private function validateFileSecurityConstraints($file, $action)
    {
        $errors = [];
        
        // Check file size (50MB limit)
        if ($file->getSize() > 52428800) { // 50MB in bytes
            $errors[] = 'File size exceeds 50MB limit for security reasons.';
        }
        
        // For encryption, ensure it's a valid document type
        if ($action === 'encrypt') {
            $allowedMimes = ['application/pdf', 'application/msword', 'application/vnd.openxmlformats-officedocument.wordprocessingml.document'];
            if (!in_array($file->getMimeType(), $allowedMimes)) {
                $errors[] = 'Only PDF and Word documents are allowed for encryption.';
            }
        }
        
        return empty($errors) ? null : $errors;
    }
}