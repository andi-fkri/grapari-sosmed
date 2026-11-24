<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Login Admin - GraPARI Social Media Monitoring</title>
    <script src="https://cdn.tailwindcss.com"></script>
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Plus+Jakarta+Sans:ital,wght@0,400;0,500;0,600;0,700;0,800;1,600;1,700&display=swap" rel="stylesheet">
    <style>
        * {
            font-family: 'Plus Jakarta Sans', -apple-system, BlinkMacSystemFont, sans-serif;
        }
        body {
            background: linear-gradient(145deg, #F6F8FC 0%, #E8EEF7 100%);
            color: #1E293B;
            min-height: 100vh;
        }
        .login-card {
            background: #FFFFFF;
            border: 1px solid rgba(226, 232, 240, 0.9);
            box-shadow: 0 20px 50px -12px rgba(15, 23, 42, 0.1), 0 4px 12px -2px rgba(15, 23, 42, 0.04);
        }
        .btn-telkomsel {
            background: linear-gradient(90deg, #E51C28 0%, #D41420 100%);
            color: #FFFFFF;
            box-shadow: 0 8px 20px -4px rgba(229, 28, 40, 0.45);
        }
        .btn-telkomsel:hover {
            background: linear-gradient(90deg, #D41420 0%, #B80D1E 100%);
        }
    </style>
</head>
<body class="antialiased flex items-center justify-center p-4 relative overflow-hidden">

    <!-- Background Decorative Waves -->
    <div class="fixed bottom-0 left-0 right-0 h-56 pointer-events-none opacity-60">
        <svg class="w-full h-full" viewBox="0 0 1440 320" preserveAspectRatio="none">
            <path fill="#FECACA" fill-opacity="0.45" d="M0,192L80,186.7C160,181,320,171,480,186.7C640,203,800,245,960,245.3C1120,245,1280,203,1360,181.3L1440,160L1440,320L1360,320C1280,320,1120,320,960,320C800,320,640,320,480,320C320,320,160,320,80,320L0,320Z"></path>
            <path fill="#E11D2B" fill-opacity="0.12" d="M0,256L80,240C160,224,320,192,480,197.3C640,203,800,245,960,261.3C1120,277,1280,267,1360,261.3L1440,256L1440,320L1360,320C1280,320,1120,320,960,320C800,320,640,320,480,320C320,320,160,320,80,320L0,320Z"></path>
        </svg>
    </div>

    <div class="login-card rounded-3xl max-w-md w-full p-8 relative z-10">
        <!-- Brand Header -->
        <div class="text-center">
            <div class="w-16 h-16 mx-auto flex items-center justify-center">
                <svg class="w-16 h-16 drop-shadow-sm" viewBox="0 0 64 64" fill="none">
                    <defs>
                        <linearGradient id="hexMain" x1="0" y1="0" x2="64" y2="64" gradientUnits="userSpaceOnUse">
                            <stop offset="0%" stop-color="#DC2626"/>
                            <stop offset="55%" stop-color="#E11D2B"/>
                            <stop offset="100%" stop-color="#F97316"/>
                        </linearGradient>
                        <linearGradient id="hexTop" x1="10" y1="4" x2="40" y2="30" gradientUnits="userSpaceOnUse">
                            <stop offset="0%" stop-color="#991B1B"/>
                            <stop offset="100%" stop-color="#E11D2B"/>
                        </linearGradient>
                        <linearGradient id="hexBottom" x1="20" y1="36" x2="56" y2="60" gradientUnits="userSpaceOnUse">
                            <stop offset="0%" stop-color="#EA580C"/>
                            <stop offset="100%" stop-color="#FACC15"/>
                        </linearGradient>
                    </defs>
                    <path d="M32 3L55 17.5V46.5L32 61L9 46.5V17.5L32 3Z" fill="url(#hexMain)" />
                    <path d="M32 3L55 17.5L32 32L9 17.5L32 3Z" fill="url(#hexTop)" opacity="0.85" />
                    <path d="M32 32L55 46.5L32 61L9 46.5L32 32Z" fill="url(#hexBottom)" opacity="0.75" />
                    <circle cx="32" cy="32" r="19" fill="#E11D2B" />
                    <path d="M22 22H42V27.5H35V43H29V27.5H22V22Z" fill="white" />
                </svg>
            </div>

            <span class="block text-xs font-extrabold uppercase tracking-widest text-[#E11D2B] mt-3">Telkomsel • IndiHome</span>
            <h1 class="text-xl font-extrabold text-slate-900 mt-1">GraPARI Social Media Monitoring</h1>
            <p class="text-xs font-medium text-slate-500 mt-1">Cluster Northern Manado | Facebook Marketplace</p>
        </div>

        @if($errors->has('login'))
            <div class="mt-5 px-4 py-3 rounded-xl bg-red-50 border border-red-200 text-red-700 text-xs font-semibold flex items-center gap-2">
                <span>⚠️</span>
                <span>{{ $errors->first('login') }}</span>
            </div>
        @endif

        @if(session('status'))
            <div class="mt-5 px-4 py-3 rounded-xl bg-emerald-50 border border-emerald-200 text-emerald-700 text-xs font-semibold flex items-center gap-2">
                <span>✅</span>
                <span>{{ session('status') }}</span>
            </div>
        @endif

        <!-- Login Form -->
        <form action="{{ route('login.submit') }}" method="POST" class="mt-6 space-y-4">
            @csrf
            <div>
                <label for="username" class="block text-xs font-bold text-slate-700 mb-1.5">Username</label>
                <div class="relative">
                    <span class="absolute inset-y-0 left-0 pl-3.5 flex items-center text-slate-400 pointer-events-none">
                        <svg class="w-4 h-4" viewBox="0 0 24 24" fill="currentColor">
                            <path fill-rule="evenodd" d="M7.5 6a4.5 4.5 0 1 1 9 0 4.5 4.5 0 0 1-9 0ZM3.751 20.105a8.25 8.25 0 0 1 16.498 0 .75.75 0 0 1-.437.695A18.683 18.683 0 0 1 12 22.5c-2.786 0-5.433-.608-7.812-1.7a.75.75 0 0 1-.437-.695Z" clip-rule="evenodd" />
                        </svg>
                    </span>
                    <input type="text" id="username" name="username" value="{{ old('username') }}" required autofocus
                        placeholder="Masukkan username"
                        class="w-full pl-10 pr-4 py-2.5 rounded-xl border border-slate-200 bg-slate-50/60 focus:bg-white text-sm font-medium text-slate-800 focus:outline-none focus:border-[#E11D2B] transition">
                </div>
            </div>

            <div>
                <label for="password" class="block text-xs font-bold text-slate-700 mb-1.5">Password</label>
                <div class="relative">
                    <span class="absolute inset-y-0 left-0 pl-3.5 flex items-center text-slate-400 pointer-events-none">
                        <svg class="w-4 h-4" viewBox="0 0 24 24" fill="currentColor">
                            <path fill-rule="evenodd" d="M12 1.5a5.25 5.25 0 0 0-5.25 5.25v3a3 3 0 0 0-3 3v6.75a3 3 0 0 0 3 3h10.5a3 3 0 0 0 3-3v-6.75a3 3 0 0 0-3-3v-3c0-2.9-2.35-5.25-5.25-5.25Zm3.75 8.25v-3a3.75 3.75 0 1 0-7.5 0v3h7.5Z" clip-rule="evenodd" />
                        </svg>
                    </span>
                    <input type="password" id="password" name="password" required
                        placeholder="Masukkan password"
                        class="w-full pl-10 pr-10 py-2.5 rounded-xl border border-slate-200 bg-slate-50/60 focus:bg-white text-sm font-medium text-slate-800 focus:outline-none focus:border-[#E11D2B] transition">
                    <button type="button" onclick="togglePasswordVisibility()" class="absolute inset-y-0 right-0 pr-3.5 flex items-center text-slate-400 hover:text-slate-600 cursor-pointer">
                        <svg id="eyeIcon" class="w-4 h-4" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
                            <path stroke-linecap="round" stroke-linejoin="round" d="M2.036 12.322a1.012 1.012 0 0 1 0-.639C3.423 7.51 7.36 4.5 12 4.5c4.638 0 8.573 3.007 9.963 7.178.07.207.07.431 0 .639C20.577 16.49 16.64 19.5 12 19.5c-4.638 0-8.573-3.007-9.963-7.178Z" />
                            <path stroke-linecap="round" stroke-linejoin="round" d="M15 12a3 3 0 1 1-6 0 3 3 0 0 1 6 0Z" />
                        </svg>
                    </button>
                </div>
            </div>

            <div class="pt-2">
                <button type="submit" class="btn-telkomsel w-full py-3 rounded-xl text-sm font-extrabold tracking-wide transition cursor-pointer">
                    Masuk ke Dashboard
                </button>
            </div>
        </form>

        <div class="mt-6 pt-4 border-t border-slate-100 text-center">
            <p class="text-[11.5px] font-medium text-slate-400">
                &copy; {{ now()->year }} Telkomsel Cluster Northern Manado
            </p>
        </div>
    </div>

    <script>
        function togglePasswordVisibility() {
            const input = document.getElementById('password');
            input.type = input.type === 'password' ? 'text' : 'password';
        }
    </script>
</body>
</html>
