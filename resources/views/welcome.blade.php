<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Sistem Clustering Kejahatan - Selamat Datang</title>
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
            <button onclick="scrollToLogin()" class="bg-white/20 backdrop-blur-sm text-white px-6 py-2 rounded-full hover:bg-white/30 transition-all duration-300 border border-white/30">
                Masuk
            </button>
        </div>
    </header>

    <!-- Hero Section -->
    <main class="relative z-10 px-6 py-12">
        <div class="max-w-7xl mx-auto">
            <div class="grid lg:grid-cols-2 gap-12 items-center min-h-[70vh]">
                <!-- Content -->
                <div class="fade-in">
                    <div class="mb-6">
                        <span class="inline-block bg-white/20 backdrop-blur-sm text-white px-4 py-2 rounded-full text-sm font-medium border border-white/30">
                            Sistem Analisis Kejahatan
                        </span>
                    </div>
                    <h1 class="text-5xl lg:text-6xl font-bold text-white mb-6 leading-tight">
                        Analisis Data Kejahatan dengan 
                        <span class="text-blue-light">Clustering</span>
                    </h1>
                    <p class="text-xl text-blue-light mb-8 leading-relaxed">
                        Platform canggih untuk menganalisis pola kejahatan menggunakan algoritma clustering. 
                        Dapatkan wawasan mendalam untuk meningkatkan keamanan dan strategi pencegahan.
                    </p>
                    <div class="flex flex-col sm:flex-row gap-4">
                        <button onclick="scrollToLogin()" class="bg-white text-blue-primary px-8 py-4 rounded-xl font-semibold hover:bg-blue-light transition-all duration-300 shadow-lg hover:shadow-xl transform hover:-translate-y-1">
                            Mulai Analisis
                        </button>
                        <button onclick="scrollToFeatures()" class="bg-white/20 backdrop-blur-sm text-white px-8 py-4 rounded-xl font-semibold hover:bg-white/30 transition-all duration-300 border border-white/30">
                            Pelajari Lebih Lanjut
                        </button>
                    </div>
                </div>

                <!-- Illustration -->
                <div class="relative">
                    <div class="floating-animation">
                        <div class="bg-white/10 backdrop-blur-sm rounded-3xl p-8 border border-white/20">
                            <div class="grid grid-cols-2 gap-4 mb-6">
                                <div class="bg-white/20 rounded-xl p-4 text-center">
                                    <div class="text-2xl font-bold text-white">247</div>
                                    <div class="text-blue-light text-sm">Kasus Dianalisis</div>
                                </div>
                                <div class="bg-white/20 rounded-xl p-4 text-center">
                                    <div class="text-2xl font-bold text-white">89%</div>
                                    <div class="text-blue-light text-sm">Akurasi Prediksi</div>
                                </div>
                            </div>
                            <div class="bg-white/20 rounded-xl p-4 text-center">
                                <div class="text-2xl font-bold text-white">15</div>
                                <div class="text-blue-light text-sm">Cluster Teridentifikasi</div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </main>

    <!-- Features Section -->
    <section id="features" class="relative z-10 px-6 py-16">
        <div class="max-w-7xl mx-auto">
            <div class="text-center mb-16">
                <h2 class="text-4xl font-bold text-white mb-4">Fitur Unggulan</h2>
                <p class="text-xl text-blue-light max-w-2xl mx-auto">
                    Teknologi terdepan untuk analisis data kejahatan yang komprehensif
                </p>
            </div>

            <div class="grid md:grid-cols-3 gap-8">
                <div class="bg-white/10 backdrop-blur-sm rounded-2xl p-8 border border-white/20 hover:bg-white/20 transition-all duration-300">
                    <div class="w-12 h-12 bg-white/20 rounded-xl flex items-center justify-center mb-6">
                        <svg class="w-6 h-6 text-white" fill="currentColor" viewBox="0 0 20 20">
                            <path d="M3 4a1 1 0 011-1h12a1 1 0 011 1v2a1 1 0 01-1 1H4a1 1 0 01-1-1V4zM3 10a1 1 0 011-1h6a1 1 0 011 1v6a1 1 0 01-1 1H4a1 1 0 01-1-1v-6zM14 9a1 1 0 00-1 1v6a1 1 0 001 1h2a1 1 0 001-1v-6a1 1 0 00-1-1h-2z"/>
                        </svg>
                    </div>
                    <h3 class="text-xl font-bold text-white mb-4">Analisis Real-time</h3>
                    <p class="text-blue-light">
                        Memproses data kejahatan secara real-time dengan algoritma clustering canggih untuk hasil yang akurat.
                    </p>
                </div>

                <div class="bg-white/10 backdrop-blur-sm rounded-2xl p-8 border border-white/20 hover:bg-white/20 transition-all duration-300">
                    <div class="w-12 h-12 bg-white/20 rounded-xl flex items-center justify-center mb-6">
                        <svg class="w-6 h-6 text-white" fill="currentColor" viewBox="0 0 20 20">
                            <path d="M2 10a8 8 0 018-8v8h8a8 8 0 11-16 0z"/>
                            <path d="M12 2.252A8.014 8.014 0 0117.748 8H12V2.252z"/>
                        </svg>
                    </div>
                    <h3 class="text-xl font-bold text-white mb-4">Visualisasi Interaktif</h3>
                    <p class="text-blue-light">
                        Dashboard interaktif dengan grafik dan peta untuk memvisualisasikan pola kejahatan dengan jelas.
                    </p>
                </div>

                <div class="bg-white/10 backdrop-blur-sm rounded-2xl p-8 border border-white/20 hover:bg-white/20 transition-all duration-300">
                    <div class="w-12 h-12 bg-white/20 rounded-xl flex items-center justify-center mb-6">
                        <svg class="w-6 h-6 text-white" fill="currentColor" viewBox="0 0 20 20">
                            <path d="M9 2a1 1 0 000 2h2a1 1 0 100-2H9z"/>
                            <path fill-rule="evenodd" d="M4 5a2 2 0 012-2v1a1 1 0 102 0V3a2 2 0 012-2h1a2 2 0 012 2v1a1 1 0 102 0V3a2 2 0 012-2 2 2 0 012 2v6a2 2 0 01-2 2H4a2 2 0 01-2-2V5zm2.5 7a1.5 1.5 0 100-3 1.5 1.5 0 000 3zm2.45 4a2.5 2.5 0 10-4.9 0h4.9zM12 9a1 1 0 100 2h3a1 1 0 100-2h-3zm-1 4a1 1 0 011-1h2a1 1 0 110 2h-2a1 1 0 01-1-1z" clip-rule="evenodd"/>
                        </svg>
                    </div>
                    <h3 class="text-xl font-bold text-white mb-4">Laporan Otomatis</h3>
                    <p class="text-blue-light">
                        Menghasilkan laporan analisis otomatis dengan insight dan rekomendasi untuk tindakan pencegahan.
                    </p>
                </div>
            </div>
        </div>
    </section>

    <!-- Login Section -->
    {{-- <section id="login" class="relative z-10 px-6 py-16">
        <div class="max-w-md mx-auto">
            <div class="bg-white/10 backdrop-blur-sm rounded-3xl p-8 border border-white/20">
                <div class="text-center mb-8">
                    <h2 class="text-3xl font-bold text-white mb-2">Masuk ke Sistem</h2>
                    <p class="text-blue-light">Akses platform analisis kejahatan</p>
                </div>

                <form method="POST" action="{{ route('login') }}">
                @csrf
                <div class="space-y-6">
                    <div>
                        <label class="block text-white font-medium mb-2">Email</label>
                        <input id="email" type="email" class="w-full px-4 py-3 rounded-xl bg-white/20 border border-white/30 text-white placeholder-blue-light focus:outline-none focus:border-white/60 focus:bg-white/30 transition-all duration-300" name="email" placeholder="Masukkan email Anda" required autofocus autocomplete="username">
                    </div>

                    <div>
                        <label class="block text-white font-medium mb-2">Kata Sandi</label>
                        <input id="password" type="password" class="w-full px-4 py-3 rounded-xl bg-white/20 border border-white/30 text-white placeholder-blue-light focus:outline-none focus:border-white/60 focus:bg-white/30 transition-all duration-300" name="password" placeholder="Masukkan kata sandi" required autocomplete="current-password">
                    </div>

                    <div class="flex items-center justify-between">
                        <label class="flex items-center">
                            <input id="remember_me" type="checkbox" class="rounded border-white/30 text-blue-primary focus:ring-blue-primary" name="remember">
                            <span class="ml-2 text-blue-light">Ingat saya</span>
                        </label>
                        <a href="{{ route('password.request') }}" class="text-blue-light hover:text-white transition-colors">Lupa kata sandi?</a>
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
    </section> --}}

    <!-- Footer -->
    <footer class="relative z-10 px-6 py-8 border-t border-white/20">
        <div class="max-w-7xl mx-auto text-center">
            <p class="text-blue-light">
                © 2025 Sistem Clustering Kejahatan. Dikembangkan untuk keamanan yang lebih baik.
            </p>
        </div>
    </footer>

    <script>
        function scrollToLogin() {
            // document.getElementById('login').scrollIntoView({ behavior: 'smooth' });
            window.location.href = '/login';
        }

        function scrollToFeatures() {
            document.getElementById('features').scrollIntoView({ behavior: 'smooth' });
        }

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