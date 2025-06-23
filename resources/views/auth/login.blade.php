<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Masuk - Sistem Clustering Kejahatan</title>
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
        .floating-animation {
            animation: float 6s ease-in-out infinite;
        }
        @keyframes float {
            0%, 100% { transform: translateY(0px); }
            50% { transform: translateY(-20px); }
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
            <a href="{{ route('register') }}" class="bg-white/20 backdrop-blur-sm text-white px-6 py-2 rounded-full hover:bg-white/30 transition-all duration-300 border border-white/30">
                Daftar
            </a>
        </div>
    </header>

    <!-- Login Section -->
    <section class="relative z-10 px-6 py-16">
        <div class="max-w-md mx-auto">
            <div class="bg-white/10 backdrop-blur-sm rounded-3xl p-8 border border-white/20 fade-in">
                <div class="text-center mb-8">
                    <h2 class="text-3xl font-bold text-white mb-2">Masuk ke Sistem</h2>
                    <p class="text-blue-light">Akses platform analisis kejahatan</p>
                </div>

                <!-- Session Status -->
                @if (session('status'))
                    <div class="mb-4 p-4 bg-green-100/20 border border-green-300/30 rounded-lg">
                        <p class="text-green-200 text-sm">{{ session('status') }}</p>
                    </div>
                @endif

                <form method="POST" action="{{ route('login') }}">
                @csrf
                <div class="space-y-6">
                    <!-- Email Address -->
                    <div>
                        <label class="block text-white font-medium mb-2">Email</label>
                        <input id="email" type="email" class="w-full px-4 py-3 rounded-xl bg-white/20 border border-white/30 text-white placeholder-blue-light focus:outline-none focus:border-white/60 focus:bg-white/30 transition-all duration-300" name="email" value="{{ old('email') }}" placeholder="Masukkan email Anda" required autofocus autocomplete="username">
                        @if($errors->get('email'))
                            <div class="mt-2 text-sm text-red-300">
                                @foreach ($errors->get('email') as $error)
                                    <p>{{ $error }}</p>
                                @endforeach
                            </div>
                        @endif
                    </div>

                    <!-- Password -->
                    <div>
                        <label class="block text-white font-medium mb-2">Kata Sandi</label>
                        <input id="password" type="password" class="w-full px-4 py-3 rounded-xl bg-white/20 border border-white/30 text-white placeholder-blue-light focus:outline-none focus:border-white/60 focus:bg-white/30 transition-all duration-300" name="password" placeholder="Masukkan kata sandi" required autocomplete="current-password">
                        @if($errors->get('password'))
                            <div class="mt-2 text-sm text-red-300">
                                @foreach ($errors->get('password') as $error)
                                    <p>{{ $error }}</p>
                                @endforeach
                            </div>
                        @endif
                    </div>

                    <div class="flex items-center justify-between">
                        <label class="flex items-center">
                            <input id="remember_me" type="checkbox" class="rounded border-white/30 text-blue-primary focus:ring-blue-primary bg-white/20" name="remember">
                            <span class="ml-2 text-blue-light">Ingat saya</span>
                        </label>
                        @if (Route::has('password.request'))
                            <a href="{{ route('password.request') }}" class="text-blue-light hover:text-white transition-colors text-sm">Lupa kata sandi?</a>
                        @endif
                    </div>

                    <button type="submit" class="w-full bg-white text-blue-primary py-3 rounded-xl font-semibold hover:bg-blue-light transition-all duration-300 shadow-lg hover:shadow-xl transform hover:-translate-y-1">
                        Masuk ke Dashboard
                    </button>
                </div>

                <div class="text-center mt-6">
                    <p class="text-blue-light">
                        Belum memiliki akun? 
                        <a href="{{ route('register') }}" class="text-white font-semibold hover:underline">Daftar sekarang</a>
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

    <script>
        // Add smooth scroll behavior for all anchor links
        document.querySelectorAll('a[href^="#"]').forEach(anchor => {
            anchor.addEventListener('click', function (e) {
                e.preventDefault();
                const target = document.querySelector(this.getAttribute('href'));
                if (target) {
                    target.scrollIntoView({ behavior: 'smooth' });
                }
            });
        });

        // Add fade-in animation on scroll
        const observerOptions = {
            threshold: 0.1,
            rootMargin: '0px 0px -50px 0px'
        };

        const observer = new IntersectionObserver((entries) => {
            entries.forEach(entry => {
                if (entry.isIntersecting) {
                    entry.target.classList.add('fade-in');
                }
            });
        }, observerOptions);

        document.querySelectorAll('section').forEach(section => {
            observer.observe(section);
        });
    </script>
</body>
</html>
