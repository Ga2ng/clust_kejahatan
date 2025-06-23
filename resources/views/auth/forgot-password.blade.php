<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Lupa Password - Sistem Clustering Kejahatan</title>
    <script src="https://cdn.tailwindcss.com"></script>
    <script>
        tailwind.config = {
            theme: {
                extend: {
                    colors: {
                        'blue-primary': '#1e3a8a',
                        'blue-secondary': '#3b82f6',
                        'blue-light': '#dbeafe',
                        'blue-dark': '#1e40af'
                    }
                }
            }
        }
    </script>
    <style>
        .gradient-bg {
            background: linear-gradient(135deg, #1e3a8a 0%, #3b82f6 50%, #60a5fa 100%);
        }
        .fade-in {
            animation: fadeIn 0.8s ease-in-out;
        }
        @keyframes fadeIn {
            from { opacity: 0; transform: translateY(30px); }
            to { opacity: 1; transform: translateY(0); }
        }
    </style>
</head>
<body class="min-h-screen gradient-bg">
    <!-- Header -->
    <header class="relative z-10 px-6 py-4">
        <div class="max-w-7xl mx-auto flex justify-between items-center">
            <div class="flex items-center space-x-3">
                <div class="w-10 h-10 bg-white rounded-full flex items-center justify-center shadow-lg">
                    <svg class="w-6 h-6 text-blue-primary" fill="currentColor" viewBox="0 0 20 20">
                        <path d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"/>
                    </svg>
                </div>
                <h1 class="text-white font-bold text-xl">CrimeClustering</h1>
            </div>
            <a href="{{ route('login') }}" class="bg-white/20 backdrop-blur-sm text-white px-6 py-2 rounded-full hover:bg-white/30 transition-all duration-300 border border-white/30">
                Masuk
            </a>
        </div>
    </header>

    <!-- Forgot Password Section -->
    <section class="relative z-10 px-6 py-16">
        <div class="max-w-md mx-auto">
            <div class="bg-white/10 backdrop-blur-sm rounded-3xl p-8 border border-white/20 fade-in">
                <div class="text-center mb-8">
                    <h2 class="text-3xl font-bold text-white mb-2">Lupa Password?</h2>
                    <p class="text-blue-light text-sm leading-relaxed">
                        Tidak masalah. Masukkan alamat email Anda dan kami akan mengirimkan link reset password untuk memilih password baru.
                    </p>
                </div>

                <!-- Session Status -->
                @if (session('status'))
                    <div class="mb-6 p-4 bg-green-100/20 border border-green-300/30 rounded-lg">
                        <p class="text-green-200 text-sm">{{ session('status') }}</p>
                    </div>
                @endif

                <form method="POST" action="{{ route('password.email') }}">
                    @csrf
                    <div class="space-y-6">
                        <!-- Email Address -->
                        <div>
                            <label class="block text-white font-medium mb-2">Email</label>
                            <input id="email" type="email" class="w-full px-4 py-3 rounded-xl bg-white/20 border border-white/30 text-white placeholder-blue-light focus:outline-none focus:border-white/60 focus:bg-white/30 transition-all duration-300" name="email" value="{{ old('email') }}" placeholder="Masukkan alamat email Anda" required autofocus>
                            @if($errors->get('email'))
                                <div class="mt-2 text-sm text-red-300">
                                    @foreach ($errors->get('email') as $error)
                                        <p>{{ $error }}</p>
                                    @endforeach
                                </div>
                            @endif
                        </div>

                        <button type="submit" class="w-full bg-white text-blue-primary py-3 rounded-xl font-semibold hover:bg-blue-light transition-all duration-300 shadow-lg hover:shadow-xl transform hover:-translate-y-1">
                            Kirim Link Reset Password
                        </button>
                    </div>

                    <div class="text-center mt-6">
                        <p class="text-blue-light">
                            Ingat password Anda? 
                            <a href="{{ route('login') }}" class="text-white font-semibold hover:underline">Masuk di sini</a>
                        </p>
                    </div>
                </form>
            </div>
        </div>
    </section>

    <!-- Footer -->
    <footer class="relative z-10 px-6 py-8 border-t border-white/20">
        <div class="max-w-7xl mx-auto text-center">
            <p class="text-blue-light">
                © 2025 Sistem Clustering Kejahatan. Dikembangkan untuk keamanan yang lebih baik.
            </p>
        </div>
    </footer>
</body>
</html>
