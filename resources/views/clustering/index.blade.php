<x-app-layout>
<div class="min-h-screen bg-gradient-to-br from-blue-50 to-indigo-100 py-8">
    <div class="max-w-4xl mx-auto px-4 sm:px-6 lg:px-8">
        <!-- Header -->
        <div class="text-center mb-8">
            <h1 class="text-4xl font-bold text-gray-900 mb-2">
                Clustering K-Means
            </h1>
            <p class="text-xl text-gray-600">
                Analisis Data Kejahatan dengan Metode K-Means
            </p>
        </div>

        <!-- Form Card -->
        <div class="bg-white shadow-xl rounded-2xl overflow-hidden">
            <div class="bg-gradient-to-r from-blue-600 to-indigo-600 px-6 py-4">
                <h2 class="text-2xl font-semibold text-white">
                    <i class="fas fa-chart-line mr-2"></i>
                    Konfigurasi Clustering
                </h2>
            </div>
            
            <div class="p-8">
                <form action="{{ route('clustering.cluster') }}" method="POST" class="space-y-6" id="clusteringForm">
                    @csrf
                    
                    <!-- Tahun Selection -->
                    <div class="space-y-2">
                        <label for="tahun" class="block text-sm font-medium text-gray-700">
                            <i class="fas fa-calendar-alt mr-2 text-blue-500"></i>
                            Pilih Tahun Data
                        </label>
                        <select name="tahun" id="tahun" required 
                                class="w-full px-4 py-3 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-blue-500 transition duration-200 bg-white">
                            <option value="">-- Pilih Tahun --</option>
                            @foreach($years as $year)
                                <option value="{{ $year }}" {{ old('tahun') == $year ? 'selected' : '' }}>
                                    {{ $year }}
                                </option>
                            @endforeach
                        </select>
                        @error('tahun')
                            <p class="text-red-500 text-sm">{{ $message }}</p>
                        @enderror
                    </div>

                    <!-- Jumlah Iterasi -->
                    <div class="space-y-2">
                        <label for="iterasi" class="block text-sm font-medium text-gray-700">
                            <i class="fas fa-sync mr-2 text-green-500"></i>
                            Jumlah Iterasi Maximum
                        </label>
                        <input type="number" name="iterasi" id="iterasi" min="1" max="100" 
                               value="{{ old('iterasi', 10) }}" required
                               class="w-full px-4 py-3 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-blue-500 transition duration-200">
                        <p class="text-sm text-gray-500">Masukkan jumlah iterasi yang diinginkan (1-100)</p>
                        @error('iterasi')
                            <p class="text-red-500 text-sm">{{ $message }}</p>
                        @enderror
                    </div>

                    <!-- Auto Convergence Option -->
                    {{-- <div class="bg-gradient-to-r from-purple-50 to-indigo-50 border border-purple-200 rounded-xl p-6">
                        <div class="flex items-start space-x-4">
                            <div class="flex items-center h-6">
                                <input type="checkbox" name="auto_converge" id="auto_converge" 
                                       class="h-5 w-5 text-purple-600 focus:ring-purple-500 border-gray-300 rounded transition duration-200">
                            </div>
                            <div class="flex-1">
                                <label for="auto_converge" class="text-lg font-semibold text-purple-800 cursor-pointer">
                                    <i class="fas fa-magic mr-2 text-purple-600"></i>
                                    Hentikan Otomatis Saat Konvergen
                                </label>
                                <p class="mt-2 text-sm text-purple-700 leading-relaxed">
                                    Jika diaktifkan, algoritma akan berhenti secara otomatis ketika mencapai konvergensi 
                                    (assignment tidak berubah dan perubahan centroid < 0.5), bahkan jika belum mencapai iterasi maksimal. 
                                    Ini menghemat waktu komputasi dan sesuai dengan konsep K-Means yang benar.
                                </p>
                                <div class="mt-3 flex items-center space-x-4 text-xs text-purple-600">
                                    <span class="flex items-center">
                                        <i class="fas fa-clock mr-1"></i>
                                        Hemat waktu
                                    </span>
                                    <span class="flex items-center">
                                        <i class="fas fa-chart-line mr-1"></i>
                                        Hasil optimal
                                    </span>
                                    <span class="flex items-center">
                                        <i class="fas fa-cpu mr-1"></i>
                                        Efisien
                                    </span>
                                </div>
                            </div>
                        </div>
                    </div> --}}

                    <!-- Debug Mode Option -->
                    {{-- <div class="bg-gradient-to-r from-orange-50 to-red-50 border border-orange-200 rounded-xl p-6">
                        <div class="flex items-start space-x-4">
                            <div class="flex items-center h-6">
                                <input type="checkbox" name="debug_mode" id="debug_mode" 
                                       class="h-5 w-5 text-orange-600 focus:ring-orange-500 border-gray-300 rounded transition duration-200">
                            </div>
                            <div class="flex-1">
                                <label for="debug_mode" class="text-lg font-semibold text-orange-800 cursor-pointer">
                                    <i class="fas fa-bug mr-2 text-orange-600"></i>
                                    Mode Debug - Tampilkan Detail Algoritma
                                </label>
                                <p class="mt-2 text-sm text-orange-700 leading-relaxed">
                                    Jika diaktifkan, akan menampilkan informasi detail tentang proses K-Means:
                                    pemilihan centroid awal, evolusi centroid antar iterasi, dan perubahan assignment.
                                    Berguna untuk memahami bagaimana algoritma bekerja dan memverifikasi perhitungan.
                                </p>
                                <div class="mt-3 flex items-center space-x-4 text-xs text-orange-600">
                                    <span class="flex items-center">
                                        <i class="fas fa-microscope mr-1"></i>
                                        Analisis mendalam
                                    </span>
                                    <span class="flex items-center">
                                        <i class="fas fa-search mr-1"></i>
                                        Verifikasi algoritma
                                    </span>
                                    <span class="flex items-center">
                                        <i class="fas fa-graduation-cap mr-1"></i>
                                        Edukasi
                                    </span>
                                </div>
                            </div>
                        </div>
                    </div> --}}

                    <!-- Info Box -->
                    <div class="bg-blue-50 border-l-4 border-blue-400 p-4 rounded">
                        <div class="flex">
                            <div class="flex-shrink-0">
                                <i class="fas fa-info-circle text-blue-400"></i>
                            </div>
                            <div class="ml-3">
                                <h3 class="text-sm font-medium text-blue-800">Informasi Algoritma K-Means</h3>
                                <div class="mt-2 text-sm text-blue-700">
                                    <ul class="list-disc pl-5 space-y-1">
                                        <li><strong>Cluster 1 (Tinggi):</strong> Data dengan nilai kejahatan tinggi</li>
                                        <li><strong>Cluster 2 (Sedang):</strong> Data dengan nilai kejahatan sedang</li>
                                        <li><strong>Cluster 3 (Rendah):</strong> Data dengan nilai kejahatan rendah</li>
                                        <li><strong>Fitur:</strong> Curas, Curat, Curanmor, Anirat, Judi</li>
                                        <li><strong>Proses Iteratif:</strong> Assignment cluster akan berubah antar iterasi sampai konvergen</li>
                                        <li><strong>Konvergensi:</strong> Tercapai ketika assignment stabil dan centroid minimal berubah</li>
                                    </ul>
                                </div>
                            </div>
                        </div>
                    </div>

                    <!-- Submit Button -->
                    <div class="pt-4">
                        <button type="submit" 
                                class="w-full bg-gradient-to-r from-blue-600 to-indigo-600 text-white py-3 px-6 rounded-lg font-semibold text-lg hover:from-blue-700 hover:to-indigo-700 focus:outline-none focus:ring-2 focus:ring-blue-500 focus:ring-offset-2 transform hover:scale-[1.02] transition duration-200 shadow-lg">
                            <i class="fas fa-play mr-2"></i>
                            Mulai Clustering
                        </button>
                    </div>
                </form>
            </div>
        </div>

        <!-- Feature Description -->
        <div class="mt-8 bg-white shadow-lg rounded-xl p-6">
            <h3 class="text-xl font-semibold text-gray-900 mb-4">
                <i class="fas fa-list mr-2 text-indigo-500"></i>
                Jenis Kejahatan yang Dianalisis
            </h3>
            <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-4">
                <div class="bg-red-50 p-4 rounded-lg border-l-4 border-red-400">
                    <h4 class="font-semibold text-red-800">CURAS</h4>
                    <p class="text-sm text-red-600">Pencurian dengan Kekerasan</p>
                </div>
                <div class="bg-orange-50 p-4 rounded-lg border-l-4 border-orange-400">
                    <h4 class="font-semibold text-orange-800">CURAT</h4>
                    <p class="text-sm text-orange-600">Pencurian dengan Pemberatan</p>
                </div>
                <div class="bg-yellow-50 p-4 rounded-lg border-l-4 border-yellow-400">
                    <h4 class="font-semibold text-yellow-800">CURANMOR</h4>
                    <p class="text-sm text-yellow-600">Pencurian Kendaraan Bermotor</p>
                </div>
                <div class="bg-green-50 p-4 rounded-lg border-l-4 border-green-400">
                    <h4 class="font-semibold text-green-800">ANIRAT</h4>
                    <p class="text-sm text-green-600">Penganiayaan Berat</p>
                </div>
                <div class="bg-purple-50 p-4 rounded-lg border-l-4 border-purple-400">
                    <h4 class="font-semibold text-purple-800">JUDI</h4>
                    <p class="text-sm text-purple-600">Perjudian</p>
                </div>
            </div>
        </div>
    </div>
</div>

<script>
    // Simple loading state only - no validation
    document.addEventListener('DOMContentLoaded', function() {
        const form = document.querySelector('form');
        const submitButton = form.querySelector('button[type="submit"]');
        
        form.addEventListener('submit', function(e) {
            console.log('Form submit triggered');
            
            // Just show loading state, let HTML5 and Laravel handle validation
            submitButton.innerHTML = '<i class="fas fa-spinner fa-spin mr-2"></i>Memproses...';
            submitButton.disabled = true;
            
            // Re-enable button after 5 seconds in case of error
            setTimeout(function() {
                if (submitButton.disabled) {
                    submitButton.innerHTML = '<i class="fas fa-play mr-2"></i>Mulai Clustering';
                    submitButton.disabled = false;
                }
            }, 5000);
        });
    });
</script>
</x-app-layout>