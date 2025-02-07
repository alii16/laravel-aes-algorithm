<!-- resources/views/file-encryption.blade.php -->
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Enkripsi dan Dekripsi File</title>
    <script src="https://cdn.tailwindcss.com"></script>
    <link href="https://cdnjs.cloudflare.com/ajax/libs/flowbite/1.6.3/flowbite.min.css" rel="stylesheet" />
</head>
<body class="flex items-center justify-center min-h-screen bg-gray-100">
    <div class="max-w-md w-full bg-white shadow-lg rounded-lg p-6">
        <h2 class="text-center text-2xl font-bold mb-5 bg-gradient-to-r from-indigo-600 via-purple-500 to-pink-500 bg-clip-text text-transparent">
            Encrypt or Decrypt your file
        </h2>        

        @if ($errors->any())
            <div class="p-4 mb-4 text-sm text-red-800 rounded-lg bg-red-100" role="alert">
                <ul>
                    @foreach ($errors->all() as $error)
                        <li>{{ $error }}</li>
                    @endforeach
                </ul>
            </div>
        @endif

        <form action="{{ route('process') }}" method="POST" enctype="multipart/form-data" class="mt-4 space-y-4">
            @csrf
            <label class="block text-gray-700 font-medium">Select file (PDF or Word):</label>
            <input type="file" name="file" class="block w-full text-sm text-gray-900 border border-gray-300 rounded-lg cursor-pointer bg-gray-50" required>

            <div class="grid grid-cols-2 gap-4">
                <div>
                    <label class="block text-gray-700 font-medium">Select action:</label>
                    <select name="action" class="w-full p-2.5 text-gray-900 border border-gray-300 rounded-lg bg-white" required>
                        <option value="encrypt">Encrypt</option>
                        <option value="decrypt">Decrypt</option>
                    </select>
                </div>
                <div>
                    <label class="block text-gray-700 font-medium">Insert key:</label>
                    <input type="text" name="key" placeholder="16 characters" maxlength="16" class="w-full p-2.5 text-gray-900 border border-gray-300 rounded-lg bg-white" required>
                </div>
            </div>

            <button type="submit" class="w-full px-4 py-2 text-white bg-indigo-600 hover:bg-indigo-700 rounded-lg font-medium">Submit</button>
        </form>

        <div class="flex justify-between items-center mt-4 text-sm text-gray-600">
            <p>Made with 💡 by Ali Polanunu</p>
            <button data-modal-target="aboutModal" data-modal-toggle="aboutModal" class="text-indigo-600 hover:underline">About this tool</button>
        </div>
    </div>

    <div id="aboutModal" tabindex="-1" aria-hidden="true" class="hidden fixed top-0 left-0 right-0 z-50 w-full p-4 overflow-x-hidden overflow-y-auto md:inset-0 h-[calc(100%-1rem)] max-h-full">
        <div class="relative w-full max-w-2xl max-h-full">
            <div class="relative bg-white rounded-lg shadow dark:bg-gray-700">
                <div class="flex items-start justify-between p-4 border-b rounded-t dark:border-gray-600">
                    <h3 class="text-xl font-semibold text-gray-900 dark:text-white">About This Tool</h3>
                    <button type="button" class="text-gray-400 bg-transparent hover:bg-gray-200 hover:text-gray-900 rounded-lg text-sm w-8 h-8 ml-auto inline-flex justify-center items-center" data-modal-hide="aboutModal">
                        <svg class="w-3 h-3" fill="none" stroke="currentColor" viewBox="0 0 24 24" xmlns="http://www.w3.org/2000/svg"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"></path></svg>
                    </button>
                </div>
                <div class="p-6 space-y-4">
                    <p class="text-gray-600 dark:text-gray-300">This tool allows users to encrypt and decrypt PDF or Word files using AES encryption. AES (Advanced Encryption Standard) is a symmetric encryption algorithm that ensures data security by converting plaintext into ciphertext using a specific key.</p>
                    <p class="text-gray-600 dark:text-gray-300">To use this tool, simply upload a file, choose whether to encrypt or decrypt, enter a 16-character key, and click "Submit". Make sure to remember your key, as decryption requires the exact same key used for encryption.</p>
                </div>
                <div class="flex items-center p-6 space-x-2 border-t border-gray-200 rounded-b dark:border-gray-600">
                    <button data-modal-hide="aboutModal" type="button" class="px-4 py-2 text-white bg-indigo-600 hover:bg-indigo-700 rounded-lg">Close</button>
                </div>
            </div>
        </div>
    </div>

    <script src="https://cdnjs.cloudflare.com/ajax/libs/flowbite/1.6.3/flowbite.min.js"></script>
</body>
</html>
