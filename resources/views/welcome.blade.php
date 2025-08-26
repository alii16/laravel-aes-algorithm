<!-- resources/views/welcome.blade.php -->
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>SecureFile - File Encryption & Decryption</title>
    <script src="https://cdn.tailwindcss.com"></script>
    <link href="https://cdnjs.cloudflare.com/ajax/libs/flowbite/1.6.3/flowbite.min.css" rel="stylesheet" />
    <link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css" rel="stylesheet">
    <script>
        tailwind.config = {
            darkMode: 'class',
            theme: {
                extend: {
                    animation: {
                        'gradient': 'gradient 8s linear infinite',
                        'float': 'float 6s ease-in-out infinite',
                        'glow': 'glow 2s ease-in-out infinite alternate',
                    },
                    keyframes: {
                        gradient: {
                            '0%, 100%': {
                                'background-size': '200% 200%',
                                'background-position': 'left center'
                            },
                            '50%': {
                                'background-size': '200% 200%',
                                'background-position': 'right center'
                            },
                        },
                        float: {
                            '0%, 100%': { transform: 'translateY(0px)' },
                            '50%': { transform: 'translateY(-10px)' },
                        },
                        glow: {
                            '0%': { 'box-shadow': '0 0 5px #3b82f6, 0 0 10px #3b82f6, 0 0 15px #3b82f6' },
                            '100%': { 'box-shadow': '0 0 10px #8b5cf6, 0 0 20px #8b5cf6, 0 0 30px #8b5cf6' }
                        }
                    }
                }
            }
        }
    </script>
</head>
<body class="min-h-screen bg-gradient-to-br from-slate-900 via-purple-900 to-slate-900 relative overflow-hidden">
    <!-- Background Animation -->
    <div class="absolute inset-0 overflow-hidden">
        <div class="absolute -inset-10 opacity-50">
            <div class="absolute top-1/4 left-1/4 w-96 h-96 bg-purple-500 rounded-full mix-blend-multiply filter blur-xl opacity-70 animate-float"></div>
            <div class="absolute top-1/3 right-1/4 w-96 h-96 bg-blue-500 rounded-full mix-blend-multiply filter blur-xl opacity-70 animate-float" style="animation-delay: 2s;"></div>
            <div class="absolute bottom-1/4 left-1/3 w-96 h-96 bg-pink-500 rounded-full mix-blend-multiply filter blur-xl opacity-70 animate-float" style="animation-delay: 4s;"></div>
        </div>
    </div>

    <!-- Main Container -->
    <div class="relative z-10 flex items-center justify-center min-h-screen p-4">
        <div class="w-full max-w-md">
            <!-- Success Alert -->
            @if(session('success'))
            <div id="alert-success" class="flex items-center p-4 mb-6 text-green-800 border-l-4 border-green-300 bg-green-50 dark:text-green-400 dark:bg-gray-800 dark:border-green-800 rounded-lg shadow-lg backdrop-blur-sm animate-pulse" role="alert">
                <svg class="flex-shrink-0 w-4 h-4" aria-hidden="true" xmlns="http://www.w3.org/2000/svg" fill="currentColor" viewBox="0 0 20 20">
                    <path d="M10 .5a9.5 9.5 0 1 0 9.5 9.5A9.51 9.51 0 0 0 10 .5ZM9.5 4a1.5 1.5 0 1 1 0 3 1.5 1.5 0 0 1 0-3ZM12 15H8a1 1 0 0 1 0-2h1v-3H8a1 1 0 0 1 0-2h2a1 1 0 0 1 1 1v4h1a1 1 0 0 1 0 2Z"/>
                </svg>
                <div class="ml-3 text-sm font-medium flex-grow">
                    <div class="font-semibold">{{ session('success') }}</div>
                    @if(session('download_token'))
                        <div class="mt-3 flex flex-col sm:flex-row gap-2">
                            <a href="{{ route('download', session('download_token')) }}" 
                               id="main-download-btn"
                               class="inline-flex items-center px-4 py-2 text-sm font-medium text-white bg-green-600 border border-green-600 rounded-lg hover:bg-green-700 focus:ring-4 focus:ring-green-200 transition-all duration-200 animate-bounce">
                                <i class="fas fa-download mr-2"></i>
                                Download {{ session('download_filename') }}
                            </a>
                            <span class="inline-flex items-center px-3 py-1 text-xs font-medium text-green-700 bg-green-100 rounded-full">
                                <i class="fas fa-clock mr-1"></i>
                                Valid for 24 hours
                            </span>
                        </div>
                        <div class="mt-2 text-xs text-green-600">
                            <i class="fas fa-info-circle mr-1"></i>
                            File will be automatically deleted after download or expiration for security.
                        </div>
                    @endif
                </div>
                @if(!session('download_token'))
                <button type="button" class="ml-auto -mx-1.5 -my-1.5 bg-green-50 text-green-500 rounded-lg focus:ring-2 focus:ring-green-400 p-1.5 hover:bg-green-200 inline-flex items-center justify-center h-8 w-8 dark:bg-gray-800 dark:text-green-400 dark:hover:bg-gray-700" data-dismiss-target="#alert-success" aria-label="Close">
                    <span class="sr-only">Close</span>
                    <svg class="w-3 h-3" aria-hidden="true" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 14 14">
                        <path stroke="currentColor" stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="m1 1 6 6m0 0 6 6M7 7l6-6M7 7l-6 6"/>
                    </svg>
                </button>
                @endif
            </div>
            @endif

            <!-- Error Alert -->
            @if ($errors->any())
            <div id="alert-error" class="flex items-center p-4 mb-6 text-red-800 border-l-4 border-red-300 bg-red-50 dark:text-red-400 dark:bg-gray-800 dark:border-red-800 rounded-lg shadow-lg backdrop-blur-sm" role="alert">
                <svg class="flex-shrink-0 w-4 h-4" aria-hidden="true" xmlns="http://www.w3.org/2000/svg" fill="currentColor" viewBox="0 0 20 20">
                    <path d="M10 .5a9.5 9.5 0 1 0 9.5 9.5A9.51 9.51 0 0 0 10 .5ZM9.5 4a1.5 1.5 0 1 1 0 3 1.5 1.5 0 0 1 0-3ZM12 15H8a1 1 0 0 1 0-2h1v-3H8a1 1 0 0 1 0-2h2a1 1 0 0 1 1 1v4h1a1 1 0 0 1 0 2Z"/>
                </svg>
                <div class="ml-3 text-sm font-medium">
                    <ul class="list-disc list-inside">
                        @foreach ($errors->all() as $error)
                            <li>{{ $error }}</li>
                        @endforeach
                    </ul>
                </div>
                <button type="button" class="ml-auto -mx-1.5 -my-1.5 bg-red-50 text-red-500 rounded-lg focus:ring-2 focus:ring-red-400 p-1.5 hover:bg-red-200 inline-flex items-center justify-center h-8 w-8 dark:bg-gray-800 dark:text-red-400 dark:hover:bg-gray-700" data-dismiss-target="#alert-error" aria-label="Close">
                    <span class="sr-only">Close</span>
                    <svg class="w-3 h-3" aria-hidden="true" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 14 14">
                        <path stroke="currentColor" stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="m1 1 6 6m0 0 6 6M7 7l6-6M7 7l-6 6"/>
                    </svg>
                </button>
            </div>
            @endif

            <!-- Warning Alert -->
            @if(session('warning'))
            <div id="alert-warning" class="flex items-center p-4 mb-6 text-yellow-800 border-l-4 border-yellow-300 bg-yellow-50 dark:text-yellow-300 dark:bg-gray-800 dark:border-yellow-800 rounded-lg shadow-lg backdrop-blur-sm" role="alert">
                <svg class="flex-shrink-0 w-4 h-4" aria-hidden="true" xmlns="http://www.w3.org/2000/svg" fill="currentColor" viewBox="0 0 20 20">
                    <path d="M10 .5a9.5 9.5 0 1 0 9.5 9.5A9.51 9.51 0 0 0 10 .5ZM9.5 4a1.5 1.5 0 1 1 0 3 1.5 1.5 0 0 1 0-3ZM12 15H8a1 1 0 0 1 0-2h1v-3H8a1 1 0 0 1 0-2h2a1 1 0 0 1 1 1v4h1a1 1 0 0 1 0 2Z"/>
                </svg>
                <div class="ml-3 text-sm font-medium">
                    {{ session('warning') }}
                </div>
                <button type="button" class="ml-auto -mx-1.5 -my-1.5 bg-yellow-50 text-yellow-500 rounded-lg focus:ring-2 focus:ring-yellow-400 p-1.5 hover:bg-yellow-200 inline-flex items-center justify-center h-8 w-8 dark:bg-gray-800 dark:text-yellow-300 dark:hover:bg-gray-700" data-dismiss-target="#alert-warning" aria-label="Close">
                    <span class="sr-only">Close</span>
                    <svg class="w-3 h-3" aria-hidden="true" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 14 14">
                        <path stroke="currentColor" stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="m1 1 6 6m0 0 6 6M7 7l6-6M7 7l-6 6"/>
                    </svg>
                </button>
            </div>
            @endif

            <!-- Main Card -->
            <div class="bg-white/10 backdrop-blur-lg rounded-3xl shadow-2xl border border-white/20 p-8 hover:bg-white/15 transition-all duration-300">
                <!-- Header -->
                <div class="text-center mb-8">
                    <div class="inline-flex items-center justify-center w-16 h-16 bg-gradient-to-r from-blue-500 to-purple-600 rounded-2xl mb-4 animate-glow">
                        <i class="fas fa-shield-alt text-white text-2xl"></i>
                    </div>
                    <h1 class="text-3xl font-bold bg-gradient-to-r from-blue-400 via-purple-400 to-pink-400 bg-clip-text text-transparent mb-2">
                        SecureFile
                    </h1>
                    <p class="text-gray-300 text-sm">Advanced File Encryption & Decryption Tool</p>
                </div>

                <!-- Form -->
                <form action="{{ route('process') }}" method="POST" enctype="multipart/form-data" class="space-y-6" id="encryptionForm">
                    @csrf
                    
                    <!-- File Upload -->
                    <div class="space-y-2">
                        <label class="block text-white font-medium text-sm flex items-center">
                            <i class="fas fa-file-upload mr-2 text-blue-400"></i>
                            Select File (PDF or Word)
                        </label>
                        <div class="relative">
                            <input type="file" name="file" id="fileInput" class="block w-full text-sm text-gray-300 border border-white/20 rounded-xl cursor-pointer bg-white/5 hover:bg-white/10 transition-all duration-200 file:mr-4 file:py-3 file:px-4 file:rounded-l-xl file:border-0 file:text-sm file:font-medium file:bg-gradient-to-r file:from-blue-500 file:to-purple-600 file:text-white hover:file:from-blue-600 hover:file:to-purple-700" required>
                            <div id="fileName" class="mt-2 text-xs text-gray-400 hidden"></div>
                        </div>
                    </div>

                    <!-- Action and Key Grid -->
                    <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                        <!-- Action Selection -->
                        <div class="space-y-2">
                            <label class="block text-white font-medium text-sm flex items-center">
                                <i class="fas fa-cogs mr-2 text-purple-400"></i>
                                Action
                            </label>
                            <select name="action" id="actionSelect" class="w-full p-3 text-white border border-white/20 rounded-xl bg-white/5 hover:bg-white/10 focus:ring-2 focus:ring-blue-500 focus:border-transparent transition-all duration-200" required>
                                <option value="encrypt" class="bg-gray-800">🔒 Encrypt File</option>
                                <option value="decrypt" class="bg-gray-800">🔓 Decrypt File</option>
                            </select>
                        </div>

                        <!-- Encryption Key -->
                        <div class="space-y-2">
                            <label class="block text-white font-medium text-sm flex items-center">
                                <i class="fas fa-key mr-2 text-yellow-400"></i>
                                Encryption Key
                            </label>
                            <div class="relative">
                                <input type="password" name="key" id="keyInput" placeholder="16 characters minimum" maxlength="32" class="w-full p-3 text-white border border-white/20 rounded-xl bg-white/5 hover:bg-white/10 focus:ring-2 focus:ring-blue-500 focus:border-transparent transition-all duration-200 pr-12" required>
                                <button type="button" id="toggleKey" class="absolute inset-y-0 right-0 pr-3 flex items-center text-gray-400 hover:text-white transition-colors">
                                    <i class="fas fa-eye" id="eyeIcon"></i>
                                </button>
                            </div>
                            <div class="flex items-center justify-between text-xs">
                                <span id="keyStrength" class="text-gray-400">Key strength: Weak</span>
                                <span id="keyLength" class="text-gray-400">0/32</span>
                            </div>
                        </div>
                    </div>

                    <!-- Security Notice -->
                    <div class="flex items-start p-4 bg-amber-500/10 border border-amber-500/20 rounded-xl">
                        <i class="fas fa-exclamation-triangle text-amber-400 mt-0.5 mr-3"></i>
                        <div class="text-sm">
                            <p class="text-amber-200 font-medium mb-1">Security Notice</p>
                            <p class="text-amber-300/80">Remember your encryption key. Without it, your encrypted files cannot be recovered.</p>
                        </div>
                    </div>

                    <!-- Submit Button -->
                    <button type="submit" id="submitBtn" class="w-full group relative overflow-hidden bg-gradient-to-r from-blue-600 to-purple-600 hover:from-blue-700 hover:to-purple-700 text-white font-semibold py-4 px-6 rounded-xl transition-all duration-300 transform hover:scale-105 focus:ring-4 focus:ring-blue-500/50 disabled:opacity-50 disabled:cursor-not-allowed">
                        <div class="absolute inset-0 bg-gradient-to-r from-blue-400 to-purple-400 opacity-0 group-hover:opacity-20 transition-opacity duration-300"></div>
                        <div class="relative flex items-center justify-center">
                            <i class="fas fa-lock mr-2"></i>
                            <span id="submitText">Process File</span>
                            <div id="loadingSpinner" class="hidden ml-2">
                                <svg class="animate-spin h-4 w-4 text-white" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24">
                                    <circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"></circle>
                                    <path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4zm2 5.291A7.962 7.962 0 014 12H0c0 3.042 1.135 5.824 3 7.938l3-2.647z"></path>
                                </svg>
                            </div>
                        </div>
                    </button>
                </form>

                <!-- Footer -->
                <div class="flex flex-col sm:flex-row justify-between items-center mt-8 pt-6 border-t border-white/10 text-sm text-gray-400 space-y-3 sm:space-y-0">
                    <p class="flex items-center">
                        Made with <i class="fas fa-heart text-red-400 mx-1 animate-pulse"></i> by 
                        <a class="text-blue-400 hover:text-blue-300 transition-colors ml-1" href="https://github.com/alii16" target="_blank">Ali Polanunu</a>
                    </p>
                    <button data-modal-target="aboutModal" data-modal-toggle="aboutModal" class="flex items-center text-purple-400 hover:text-purple-300 transition-colors">
                        <i class="fas fa-info-circle mr-1"></i>
                        About Tool
                    </button>
                </div>
            </div>
        </div>
    </div>

    <!-- About Modal -->
    <div id="aboutModal" tabindex="-1" aria-hidden="true" class="hidden fixed top-0 left-0 right-0 z-50 w-full p-4 overflow-x-hidden overflow-y-auto md:inset-0 h-[calc(100%-1rem)] max-h-full">
        <div class="relative w-full max-w-2xl max-h-full">
            <div class="relative bg-gray-800 rounded-2xl shadow-2xl border border-gray-700">
                <div class="flex items-start justify-between p-6 border-b border-gray-700 rounded-t">
                    <h3 class="text-xl font-semibold text-white flex items-center">
                        <i class="fas fa-info-circle text-blue-400 mr-2"></i>
                        About SecureFile
                    </h3>
                    <button type="button" class="text-gray-400 bg-transparent hover:bg-gray-600 hover:text-gray-200 rounded-lg text-sm w-8 h-8 ml-auto inline-flex justify-center items-center transition-colors" data-modal-hide="aboutModal">
                        <svg class="w-3 h-3" fill="none" stroke="currentColor" viewBox="0 0 24 24" xmlns="http://www.w3.org/2000/svg"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"></path></svg>
                    </button>
                </div>
                <div class="p-6 space-y-4">
                    <div class="bg-blue-500/10 border border-blue-500/20 rounded-xl p-4">
                        <h4 class="text-blue-400 font-medium mb-2 flex items-center">
                            <i class="fas fa-shield-alt mr-2"></i>
                            AES Encryption Technology
                        </h4>
                        <p class="text-gray-300 text-sm leading-relaxed">SecureFile uses Advanced Encryption Standard (AES) with 128-bit encryption to protect your files. AES is a symmetric encryption algorithm trusted by governments and organizations worldwide for securing sensitive data.</p>
                    </div>
                    
                    <div class="bg-purple-500/10 border border-purple-500/20 rounded-xl p-4">
                        <h4 class="text-purple-400 font-medium mb-2 flex items-center">
                            <i class="fas fa-cog mr-2"></i>
                            How to Use
                        </h4>
                        <ol class="text-gray-300 text-sm space-y-2 list-decimal list-inside">
                            <li>Upload your PDF or Word document</li>
                            <li>Choose to encrypt or decrypt the file</li>
                            <li>Enter a strong encryption key (16+ characters)</li>
                            <li>Click "Process File" to complete the operation</li>
                        </ol>
                    </div>

                    <div class="bg-amber-500/10 border border-amber-500/20 rounded-xl p-4">
                        <h4 class="text-amber-400 font-medium mb-2 flex items-center">
                            <i class="fas fa-exclamation-triangle mr-2"></i>
                            Important Security Notes
                        </h4>
                        <ul class="text-gray-300 text-sm space-y-1 list-disc list-inside">
                            <li>Keep your encryption key safe and secure</li>
                            <li>Use a strong, unique key for each file</li>
                            <li>Without the key, encrypted files cannot be recovered</li>
                            <li>Files are processed locally for maximum security</li>
                        </ul>
                    </div>
                </div>
                <div class="flex items-center justify-end p-6 space-x-2 border-t border-gray-700 rounded-b">
                    <button data-modal-hide="aboutModal" type="button" class="px-6 py-2 text-white bg-gradient-to-r from-blue-600 to-purple-600 hover:from-blue-700 hover:to-purple-700 rounded-lg font-medium transition-all duration-200">
                        Got it!
                    </button>
                </div>
            </div>
        </div>
    </div>

    <script src="https://cdnjs.cloudflare.com/ajax/libs/flowbite/1.6.3/flowbite.min.js"></script>
    <script>
        // File input handler
        document.getElementById('fileInput').addEventListener('change', function(e) {
            const fileName = document.getElementById('fileName');
            if (e.target.files.length > 0) {
                fileName.textContent = `Selected: ${e.target.files[0].name}`;
                fileName.classList.remove('hidden');
            } else {
                fileName.classList.add('hidden');
            }
        });

        // Key visibility toggle
        document.getElementById('toggleKey').addEventListener('click', function() {
            const keyInput = document.getElementById('keyInput');
            const eyeIcon = document.getElementById('eyeIcon');
            
            if (keyInput.type === 'password') {
                keyInput.type = 'text';
                eyeIcon.classList.remove('fa-eye');
                eyeIcon.classList.add('fa-eye-slash');
            } else {
                keyInput.type = 'password';
                eyeIcon.classList.remove('fa-eye-slash');
                eyeIcon.classList.add('fa-eye');
            }
        });

        // Key strength indicator
        document.getElementById('keyInput').addEventListener('input', function(e) {
            const key = e.target.value;
            const keyLength = document.getElementById('keyLength');
            const keyStrength = document.getElementById('keyStrength');
            
            keyLength.textContent = `${key.length}/32`;
            
            let strength = 'Weak';
            let color = 'text-red-400';
            
            if (key.length >= 16) {
                const hasUpper = /[A-Z]/.test(key);
                const hasLower = /[a-z]/.test(key);
                const hasNumbers = /\d/.test(key);
                const hasSpecial = /[!@#$%^&*(),.?":{}|<>]/.test(key);
                
                const score = [hasUpper, hasLower, hasNumbers, hasSpecial].filter(Boolean).length;
                
                if (key.length >= 24 && score >= 3) {
                    strength = 'Very Strong';
                    color = 'text-green-400';
                } else if (key.length >= 20 && score >= 2) {
                    strength = 'Strong';
                    color = 'text-blue-400';
                } else if (key.length >= 16) {
                    strength = 'Medium';
                    color = 'text-yellow-400';
                }
            }
            
            keyStrength.textContent = `Key strength: ${strength}`;
            keyStrength.className = `text-xs ${color}`;
        });

        // Form submission handler
        document.getElementById('encryptionForm').addEventListener('submit', function() {
            const submitBtn = document.getElementById('submitBtn');
            const submitText = document.getElementById('submitText');
            const loadingSpinner = document.getElementById('loadingSpinner');
            
            submitBtn.disabled = true;
            submitText.textContent = 'Processing...';
            loadingSpinner.classList.remove('hidden');
        });

        // Action change handler
        document.getElementById('actionSelect').addEventListener('change', function(e) {
            const submitText = document.getElementById('submitText');
            const icon = submitText.previousElementSibling;
            
            if (e.target.value === 'encrypt') {
                submitText.textContent = 'Encrypt File';
                icon.className = 'fas fa-lock mr-2';
            } else {
                submitText.textContent = 'Decrypt File';
                icon.className = 'fas fa-unlock mr-2';
            }
        });

        // Auto-hide alerts with smart logic
        setTimeout(() => {
            const alerts = document.querySelectorAll('[id^="alert-"]');
            alerts.forEach(alert => {
                // Skip success alerts that have download links - let them stay
                if (alert.id === 'alert-success' && alert.querySelector('a[href*="download"]')) {
                    return;
                }
                
                // Auto-hide other alerts
                if (alert && alert.id !== 'alert-success') {
                    alert.style.transition = 'opacity 0.5s ease-out';
                    alert.style.opacity = '0';
                    setTimeout(() => alert.remove(), 500);
                }
            });
        }, 8000);

        // Add download click handlers
        @if(session('download_token'))
        document.addEventListener('DOMContentLoaded', function() {
            // Handle main download button
            const mainDownloadBtn = document.getElementById('main-download-btn');
            if (mainDownloadBtn) {
                mainDownloadBtn.addEventListener('click', function() {
                    // Change button state after click
                    setTimeout(() => {
                        this.innerHTML = '<i class="fas fa-check mr-2"></i>Downloaded';
                        this.className = this.className.replace('bg-green-600', 'bg-gray-500').replace('hover:bg-green-700', 'cursor-default');
                        this.onclick = null;
                        
                        // Show download complete message
                        const successAlert = document.getElementById('alert-success');
                        if (successAlert) {
                            const messageDiv = successAlert.querySelector('.font-semibold');
                            if (messageDiv) {
                                messageDiv.textContent = 'File downloaded successfully! The file has been removed from our servers for security.';
                            }
                        }
                        
                        // Remove persistent notification if exists
                        const persistentDownload = document.getElementById('persistent-download');
                        if (persistentDownload) {
                            persistentDownload.style.transform = 'translateX(100%)';
                            setTimeout(() => persistentDownload.remove(), 300);
                        }
                    }, 1000);
                });
            }
        });
        @endif
        // Create persistent download notification that stays until clicked
        function createPersistentDownloadNotification() {
            // Remove any existing persistent notifications
            const existing = document.getElementById('persistent-download');
            if (existing) existing.remove();
            
            const notification = document.createElement('div');
            notification.id = 'persistent-download';
            notification.className = 'fixed bottom-4 right-4 z-50 max-w-sm bg-white border border-green-200 rounded-lg shadow-lg p-4 transform transition-all duration-300 hover:scale-105';
            notification.innerHTML = `
                <div class="flex items-center justify-between">
                    <div class="flex items-center">
                        <div class="flex-shrink-0">
                            <div class="w-8 h-8 bg-green-100 rounded-full flex items-center justify-center">
                                <i class="fas fa-download text-green-600 text-sm"></i>
                            </div>
                        </div>
                        <div class="ml-3">
                            <p class="text-sm font-medium text-gray-900">File Ready!</p>
                            <p class="text-xs text-gray-500">{{ session('download_filename') }}</p>
                        </div>
                    </div>
                    <button onclick="this.parentElement.parentElement.remove()" class="ml-2 text-gray-400 hover:text-gray-600">
                        <i class="fas fa-times"></i>
                    </button>
                </div>
                <div class="mt-3">
                    @if(session('download_token'))
                        <a href="{{ route('download', session('download_token')) }}" 
                        id="main-download-btn"
                        class="inline-flex items-center px-4 py-2 text-sm font-medium text-white bg-green-600 border border-green-600 rounded-lg hover:bg-green-700 focus:ring-4 focus:ring-green-200 transition-all duration-200 animate-bounce">
                            <i class="fas fa-download mr-2"></i>
                            Download {{ session('download_filename') }}
                        </a>
                    @endif
                </div>
                <div class="mt-2 text-xs text-gray-400 text-center">
                    <i class="fas fa-clock mr-1"></i>
                    Available for 24 hours
                </div>
            `;
            
            document.body.appendChild(notification);
            
            // Animate in
            setTimeout(() => {
                notification.style.transform = 'translateX(0)';
            }, 100);
        }
        
        // Show persistent notification after 3 seconds
        setTimeout(createPersistentDownloadNotification, 3000);

    </script>
</body>
</html>