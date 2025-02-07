<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;

class FileEncryptionController extends Controller
{
    public function index()
    {
        return view('welcome');
    }

    public function process(Request $request)
    {
        // Tentukan aturan validasi berdasarkan aksi yang dipilih
        $rules = [
            'file' => 'required|file',
            'key' => 'required|min:16',
            'action' => 'required|in:encrypt,decrypt'
        ];
    
        if ($request->action === 'encrypt') {
            $rules['file'] = 'required|mimes:pdf,doc,docx';
        } elseif ($request->action === 'decrypt') {
            $rules['file'] = 'required'; // Menghapus validasi mimes karena file .enc tidak dikenali Laravel sebagai format resmi
        }
    
        $request->validate($rules);
    
        $file = $request->file('file');
        $key = substr(hash('sha256', $request->key, true), 0, 16);
        $data = file_get_contents($file);
        $iv = '1234567890123456'; // IV harus 16 karakter (sebaiknya gunakan IV acak dan simpan bersama file terenkripsi)
    
        if ($request->action === 'encrypt') {
            $processedData = openssl_encrypt($data, 'aes-128-cbc', $key, 0, $iv);
            $filename = $file->getClientOriginalName() . '.enc';
        } else {
            $processedData = openssl_decrypt($data, 'aes-128-cbc', $key, 0, $iv);
            if ($processedData === false) {
                return back()->withErrors(['message' => 'Decryption failed. Check your key and file.']);
            }
            $filename = str_replace('.enc', '', $file->getClientOriginalName());
        }
    
        Storage::disk('local')->put($filename, $processedData);
        return response()->download(storage_path("app/" . $filename))->deleteFileAfterSend(true);
    }
    
}
