    <x-app-layout>
    <div class="min-h-screen bg-gradient-to-br from-gray-50 to-gray-100 py-8">
        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
            <!-- Header -->
            <div class="text-center mb-8">
                <h1 class="text-4xl font-bold text-gray-900 mb-2">
                    Hasil Clustering K-Means
                </h1>
                <p class="text-xl text-gray-600">
                    Analisis Data Kejahatan Tahun {{ $tahun }}
                </p>
                <div class="mt-4">
                    <a href="{{ route('clustering.index') }}" 
                        class="inline-flex items-center px-4 py-2 bg-blue-600 text-white rounded-lg hover:bg-blue-700 transition duration-200">
                        <i class="fas fa-arrow-left mr-2"></i>
                        Kembali
                    </a>
                </div>
            </div>
    
            <div class="bg-white rounded-xl shadow-lg mb-8">
                <div class="p-6 border-b border-gray-200">
                    <h2 class="text-2xl font-bold text-gray-900">
                        <i class="fas fa-chart-pie mr-2 text-green-600"></i>
                        Data Lengkap Kejahatan Tahun {{ $data12bulan[0]['tahun'] }}
                    </h2>
                </div>

            <!-- Debug Panel (hanya untuk tahun 2020) -->
            @if(isset($debug_info) && $debug_info)
            <div class="bg-white rounded-xl shadow-lg mb-8">
                <div class="p-6 border-b border-gray-200">
                    <h2 class="text-2xl font-bold text-gray-900">
                        <i class="fas fa-bug mr-2 text-red-600"></i>
                        Debug Information - K-Means Analysis
                    </h2>
                    <p class="text-sm text-gray-600 mt-2">
                        Informasi debug untuk memverifikasi algoritma K-Means bekerja dengan benar
                    </p>
                </div>
                <div class="p-6">
                    <!-- Data Totals -->
                    <div class="mb-6">
                        <h3 class="text-lg font-semibold text-gray-800 mb-3">
                            <i class="fas fa-sort-numeric-down mr-2 text-blue-600"></i>
                            Data Diurutkan Berdasarkan Total
                        </h3>
                        <div class="overflow-x-auto">
                            <table class="min-w-full bg-white border border-gray-200 rounded-lg shadow-sm">
                                <thead class="bg-gray-50">
                                    <tr>
                                        <th class="px-4 py-3 text-left text-xs font-medium text-gray-500 uppercase">Index</th>
                                        <th class="px-4 py-3 text-left text-xs font-medium text-gray-500 uppercase">Bulan</th>
                                        <th class="px-4 py-3 text-center text-xs font-medium text-gray-500 uppercase">Data</th>
                                        <th class="px-4 py-3 text-center text-xs font-medium text-gray-500 uppercase">Total</th>
                                        <th class="px-4 py-3 text-center text-xs font-medium text-gray-500 uppercase">Keterangan</th>
                                    </tr>
                                </thead>
                                <tbody class="divide-y divide-gray-200">
                                    @php
                                        $sortedData = $debug_info['data_totals'];
                                        usort($sortedData, function($a, $b) { return $b['total'] <=> $a['total']; });
                                    @endphp
                                    @foreach($sortedData as $index => $item)
                                        @php
                                            $bgClass = '';
                                            $note = '';
                                            if ($index == 0) {
                                                $bgClass = 'bg-red-50';
                                                $note = 'Centroid C1 (Tinggi)';
                                            } elseif ($index == count($sortedData) - 1) {
                                                $bgClass = 'bg-green-50';
                                                $note = 'Centroid C3 (Rendah)';
                                            } elseif ($index == floor(count($sortedData) / 2)) {
                                                $bgClass = 'bg-yellow-50';
                                                $note = 'Centroid C2 (Sedang)';
                                            }
                                        @endphp
                                        <tr class="{{ $bgClass }}">
                                            <td class="px-4 py-3 text-sm font-medium text-gray-900">{{ $item['index'] }}</td>
                                            <td class="px-4 py-3 text-sm text-gray-700">{{ $item['month'] }}</td>
                                            <td class="px-4 py-3 text-sm text-center text-gray-700">
                                                [{{ implode(', ', $item['data']) }}]
                                            </td>
                                            <td class="px-4 py-3 text-sm text-center font-bold text-gray-900">{{ $item['total'] }}</td>
                                            <td class="px-4 py-3 text-sm text-center text-gray-600">{{ $note }}</td>
                                        </tr>
                                    @endforeach
                                </tbody>
                            </table>
                        </div>
                    </div>

                    <!-- Centroid Evolution -->
                    <div class="mb-6">
                        <h3 class="text-lg font-semibold text-gray-800 mb-3">
                            <i class="fas fa-chart-line mr-2 text-purple-600"></i>
                            Evolusi Centroid Antar Iterasi
                        </h3>
                        <div class="grid grid-cols-1 lg:grid-cols-3 gap-4">
                            @foreach($debug_info['centroid_evolution'] as $evolution)
                                <div class="bg-gray-50 rounded-lg p-4">
                                    <h4 class="font-semibold text-gray-800 mb-3">Iterasi {{ $evolution['iteration'] }}</h4>
                                    <div class="space-y-2">
                                        @foreach(['C1 (Tinggi)', 'C2 (Sedang)', 'C3 (Rendah)'] as $index => $label)
                                            @php
                                                $centroid = $evolution['centroids'][$index] ?? [];
                                                $count = $evolution['cluster_counts'][$index + 1] ?? 0;
                                                $colors = ['text-red-700', 'text-yellow-700', 'text-green-700'];
                                            @endphp
                                            <div class="text-sm {{ $colors[$index] }}">
                                                <div class="font-medium">{{ $label }}: {{ $count }} data</div>
                                                <div class="text-xs">
                                                    [{{ implode(', ', array_map(function($v) { return number_format($v, 1); }, $centroid)) }}]
                                                </div>
                                            </div>
                                        @endforeach
                                    </div>
                                </div>
                            @endforeach
                        </div>
                    </div>

                    <!-- Assignment Changes -->
                    <div class="mb-6">
                        <h3 class="text-lg font-semibold text-gray-800 mb-3">
                            <i class="fas fa-exchange-alt mr-2 text-orange-600"></i>
                            Perubahan Assignment Antar Iterasi
                        </h3>
                        
                        <div class="grid grid-cols-1 lg:grid-cols-2 gap-4 mb-4">
                            @foreach($debug_info['centroid_evolution'] as $evolution)
                                @if($evolution['iteration'] > 1)
                                <div class="bg-gray-50 rounded-lg p-4">
                                    <h4 class="font-semibold text-gray-800 mb-2">
                                        Iterasi {{ $evolution['iteration'] - 1 }} → {{ $evolution['iteration'] }}
                                    </h4>
                                    <div class="text-sm">
                                        @if(isset($evolution['assignment_changed']))
                                            @if($evolution['assignment_changed'])
                                                <div class="flex items-center text-green-700 mb-2">
                                                    <i class="fas fa-check-circle mr-2"></i>
                                                    <span class="font-medium">Assignment Berubah</span>
                                                </div>
                                                <p class="text-green-600 text-xs">
                                                    Ada data yang berpindah cluster - algoritma berjalan dengan benar
                                                </p>
                                            @else
                                                <div class="flex items-center text-red-700 mb-2">
                                                    <i class="fas fa-times-circle mr-2"></i>
                                                    <span class="font-medium">Assignment Tidak Berubah</span>
                                                </div>
                                                <p class="text-red-600 text-xs">
                                                    Tidak ada data yang berpindah cluster - kemungkinan sudah konvergen
                                                </p>
                                            @endif
                                        @endif
                                        
                                        @if(isset($evolution['centroid_change']))
                                            <div class="mt-2">
                                                <span class="text-gray-600 text-xs">
                                                    Perubahan Centroid: <strong>{{ $evolution['centroid_change'] }}</strong>
                                                </span>
                                            </div>
                                        @endif
                                    </div>
                                </div>
                                @endif
                            @endforeach
                        </div>
                        
                        <div class="bg-yellow-50 border border-yellow-200 rounded-lg p-4">
                            <p class="text-sm text-yellow-800">
                                <i class="fas fa-info-circle mr-1"></i>
                                <strong>Prinsip K-Means yang Benar:</strong> Assignment data ke cluster harus berubah antar iterasi 
                                sampai mencapai konvergensi. Jika assignment tidak pernah berubah sejak iterasi awal, 
                                kemungkinan ada masalah dalam algoritma atau data sudah sangat terstruktur.
                            </p>
                        </div>
                        
                        @if(isset($debug_info['algorithm_notes']))
                        <div class="mt-4 bg-blue-50 border border-blue-200 rounded-lg p-4">
                            <h4 class="font-semibold text-blue-800 mb-2">Ringkasan Algoritma</h4>
                            <div class="grid grid-cols-2 lg:grid-cols-3 gap-4 text-sm text-blue-700">
                                <div>
                                    <span class="font-medium">Total Data:</span> 
                                    {{ $debug_info['algorithm_notes']['total_data_points'] }}
                                </div>
                                <div>
                                    <span class="font-medium">Fitur per Data:</span> 
                                    {{ $debug_info['algorithm_notes']['features_per_point'] }}
                                </div>
                                <div>
                                    <span class="font-medium">Jumlah Cluster:</span> 
                                    {{ $debug_info['algorithm_notes']['clusters_used'] }}
                                </div>
                                <div>
                                    <span class="font-medium">Konvergensi:</span> 
                                    {{ $debug_info['algorithm_notes']['convergence_achieved'] ? 'Ya' : 'Tidak' }}
                                </div>
                                <div>
                                    <span class="font-medium">Iterasi Dibutuhkan:</span> 
                                    {{ $debug_info['algorithm_notes']['iterations_needed'] }}
                                </div>
                            </div>
                        </div>
                        @endif
                    </div>
                </div>
            </div>
            @endif

                <!-- Tabel Data Lengkap -->
                <div class="mb-4">
                <div class="overflow-x-auto">
                    <table class="min-w-full bg-white border border-gray-200 rounded-lg shadow-sm">
                        <thead class="bg-gray-50">
                            <tr>
                                <th class="px-3 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                                    No
                                </th>
                                <th class="px-3 py-3 text-center text-xs font-medium text-gray-500 uppercase tracking-wider">
                                    Bulan
                                </th>
                                <th class="px-3 py-3 text-center text-xs font-medium text-gray-500 uppercase tracking-wider">
                                    Curas
                                </th>
                                <th class="px-3 py-3 text-center text-xs font-medium text-gray-500 uppercase tracking-wider">
                                    Curat
                                </th>
                                <th class="px-3 py-3 text-center text-xs font-medium text-gray-500 uppercase tracking-wider">
                                    Curanmor
                                </th>
                                <th class="px-3 py-3 text-center text-xs font-medium text-gray-500 uppercase tracking-wider">
                                    Anirat
                                </th>
                                <th class="px-3 py-3 text-center text-xs font-medium text-gray-500 uppercase tracking-wider">
                                    Judi
                                </th>
                                <th class="px-3 py-3 text-center text-xs font-medium text-gray-500 uppercase tracking-wider">
                                    Total
                                </th>
                            </tr>
                        </thead>
                        <tbody class="divide-y divide-gray-200">
                            @foreach($data12bulan as $index => $item)
                                @php
                                    $clusterColors = [
                                        1 => 'bg-red-50 text-red-800',
                                        2 => 'bg-yellow-50 text-yellow-800',
                                        3 => 'bg-green-50 text-green-800'
                                    ];
                                    $clusterLabels = [1 => 'Tinggi', 2 => 'Sedang', 3 => 'Rendah'];
                                    $total = $item['curas'] + $item['curat'] + $item['curanmor'] + $item['anirat'] + $item['judi'];
                                @endphp
                                <tr class="hover:bg-gray-50">
                                    <td class="px-3 py-3 text-sm font-medium text-gray-900">
                                        {{ $index + 1 }}
                                    </td>
                                    <td class="px-3 py-3 text-sm text-center text-gray-700">
                                        {{ $item['bulan'] }}
                                    </td>
                                    <td class="px-3 py-3 text-sm text-center text-gray-700">
                                        {{ $item['curas'] }}
                                    </td>
                                    <td class="px-3 py-3 text-sm text-center text-gray-700">
                                        {{ $item['curat'] }}
                                    </td>
                                    <td class="px-3 py-3 text-sm text-center text-gray-700">
                                        {{ $item['curanmor'] }}
                                    </td>
                                    <td class="px-3 py-3 text-sm text-center text-gray-700">
                                        {{ $item['anirat'] }}
                                    </td>
                                    <td class="px-3 py-3 text-sm text-center text-gray-700">
                                        {{ $item['judi'] }}
                                    </td>
                                    <td class="px-3 py-3 text-sm text-center font-bold text-gray-900">
                                        {{ $total }}
                                    </td>
                                </tr>
                            @endforeach
                        </tbody>
                    </table>
                </div>
                </div>
            </div>

            <!-- Convergence Information -->
            <div class="bg-white rounded-xl shadow-lg mb-8">
                <div class="p-6 border-b border-gray-200">
                    <h2 class="text-2xl font-bold text-gray-900">
                        <i class="fas fa-info-circle mr-2 text-blue-600"></i>
                        Informasi Konvergensi
                    </h2>
                </div>
                <div class="p-6">
                    <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-4 gap-6">
                        <!-- Auto Convergence Status -->
                        <div class="bg-gradient-to-r {{ $convergence_info['auto_converge_enabled'] ? 'from-green-50 to-green-100 border-green-200' : 'from-gray-50 to-gray-100 border-gray-200' }} border rounded-lg p-4">
                            <div class="flex items-center">
                                <div class="p-3 bg-white rounded-full shadow-sm">
                                    <i class="fas {{ $convergence_info['auto_converge_enabled'] ? 'fa-magic text-green-600' : 'fa-times text-gray-400' }} text-xl"></i>
                                </div>
                                <div class="ml-4">
                                    <h3 class="text-lg font-semibold {{ $convergence_info['auto_converge_enabled'] ? 'text-green-800' : 'text-gray-600' }}">
                                        Auto Convergence
                                    </h3>
                                    <p class="text-sm {{ $convergence_info['auto_converge_enabled'] ? 'text-green-600' : 'text-gray-500' }}">
                                        {{ $convergence_info['auto_converge_enabled'] ? 'Aktif' : 'Tidak Aktif' }}
                                    </p>
                                </div>
                            </div>
                        </div>

                        <!-- Convergence Status -->
                        <div class="bg-gradient-to-r {{ $convergence_info['is_converged'] ? 'from-blue-50 to-blue-100 border-blue-200' : 'from-orange-50 to-orange-100 border-orange-200' }} border rounded-lg p-4">
                            <div class="flex items-center">
                                <div class="p-3 bg-white rounded-full shadow-sm">
                                    <i class="fas {{ $convergence_info['is_converged'] ? 'fa-check-circle text-blue-600' : 'fa-clock text-orange-600' }} text-xl"></i>
                                </div>
                                <div class="ml-4">
                                    <h3 class="text-lg font-semibold {{ $convergence_info['is_converged'] ? 'text-blue-800' : 'text-orange-800' }}">
                                        Status Konvergensi
                                    </h3>
                                    <p class="text-sm {{ $convergence_info['is_converged'] ? 'text-blue-600' : 'text-orange-600' }}">
                                        {{ $convergence_info['is_converged'] ? 'Konvergen' : 'Belum Konvergen' }}
                                    </p>
                                </div>
                            </div>
                        </div>

                        <!-- Total Iterations -->
                        <div class="bg-gradient-to-r from-purple-50 to-purple-100 border border-purple-200 rounded-lg p-4">
                            <div class="flex items-center">
                                <div class="p-3 bg-white rounded-full shadow-sm">
                                    <i class="fas fa-sync text-purple-600 text-xl"></i>
                                </div>
                                <div class="ml-4">
                                    <h3 class="text-lg font-semibold text-purple-800">
                                        Total Iterasi
                                    </h3>
                                    <p class="text-2xl font-bold text-purple-600">
                                        {{ $convergence_info['total_iterations'] }}
                                    </p>
                                    <p class="text-xs text-purple-500">
                                        dari {{ $convergence_info['max_iterations_requested'] }} diminta
                                    </p>
                                </div>
                            </div>
                        </div>

                        <!-- Convergence Iteration -->
                        @if($convergence_info['is_converged'])
                        <div class="bg-gradient-to-r from-emerald-50 to-emerald-100 border border-emerald-200 rounded-lg p-4">
                            <div class="flex items-center">
                                <div class="p-3 bg-white rounded-full shadow-sm">
                                    <i class="fas fa-flag-checkered text-emerald-600 text-xl"></i>
                                </div>
                                <div class="ml-4">
                                    <h3 class="text-lg font-semibold text-emerald-800">
                                        Konvergen di Iterasi
                                    </h3>
                                    <p class="text-2xl font-bold text-emerald-600">
                                        {{ $convergence_info['convergence_iteration'] }}
                                    </p>
                                    <p class="text-xs text-emerald-500">
                                        Berhenti otomatis
                                    </p>
                                </div>
                            </div>
                        </div>
                        @else
                        <div class="bg-gradient-to-r from-amber-50 to-amber-100 border border-amber-200 rounded-lg p-4">
                            <div class="flex items-center">
                                <div class="p-3 bg-white rounded-full shadow-sm">
                                    <i class="fas fa-exclamation-triangle text-amber-600 text-xl"></i>
                                </div>
                                <div class="ml-4">
                                    <h3 class="text-lg font-semibold text-amber-800">
                                        Belum Konvergen
                                    </h3>
                                    <p class="text-sm text-amber-600">
                                        Iterasi berhenti karena mencapai batas maksimal
                                    </p>
                                </div>
                            </div>
                        </div>
                        @endif
                    </div>

                    <!-- Penjelasan Konvergensi -->
                    <div class="mt-6 p-4 bg-gray-50 rounded-lg border">
                        <h4 class="font-semibold text-gray-800 mb-2">
                            <i class="fas fa-lightbulb mr-2 text-yellow-500"></i>
                            Apa itu Konvergensi?
                        </h4>
                        <p class="text-sm text-gray-600 leading-relaxed">
                            Konvergensi dalam K-Means terjadi ketika perubahan centroid antar iterasi sudah sangat kecil (threshold < 0.001). 
                            Ini menandakan bahwa algoritma telah menemukan solusi yang stabil dan tidak perlu melanjutkan iterasi. 
                            @if($convergence_info['auto_converge_enabled'])
                                Dengan auto-convergence aktif, algoritma akan berhenti secara otomatis untuk menghemat waktu komputasi.
                            @else
                                Untuk mengaktifkan penghentian otomatis, centang opsi "Hentikan otomatis saat konvergen" saat memulai clustering.
                            @endif
                        </p>
                    </div>
                </div>
            </div>

            <!-- Iterations Accordion -->
            <div class="bg-white rounded-xl shadow-lg mb-8">
                <div class="p-6 border-b border-gray-200">
                    <h2 class="text-2xl font-bold text-gray-900">
                        <i class="fas fa-history mr-2 text-blue-600"></i>
                        Proses Iterasi Detail ({{ count($iterations) }} Iterasi)
                    </h2>
                    <p class="text-sm text-gray-600 mt-2">
                        <i class="fas fa-info-circle mr-1"></i>
                        Iterasi 1 menggunakan data aktual tertinggi sebagai centroid awal. Iterasi 2+ menggunakan rata-rata cluster.
                    </p>
                </div>
                
                <div class="accordion" id="iterationsAccordion">
                    @foreach($iterations as $index => $iteration)
                    <div class="border-b border-gray-200 last:border-b-0">
                        <div class="p-4">
                            <button class="w-full text-left focus:outline-none" 
                                    type="button" 
                                    onclick="toggleIteration({{ $index }})">
                                <div class="flex items-center justify-between">
                                    <h3 class="text-lg font-semibold text-gray-900">
                                        <span class="bg-blue-100 text-blue-800 px-3 py-1 rounded-full text-sm mr-3">
                                            Iterasi {{ $iteration['iteration'] }}
                                        </span>
                                        @if($iteration['is_first_iteration'])
                                            <span class="bg-orange-100 text-orange-800 px-2 py-1 rounded text-xs">
                                                Centroid Data Aktual
                                            </span>
                                        @else
                                            <span class="bg-green-100 text-green-800 px-2 py-1 rounded text-xs">
                                                Centroid Rata-rata
                                            </span>
                                        @endif
                                        
                                        <!-- Badge Convergence -->
                                        @if(isset($iteration['is_final_iteration']) && $iteration['is_final_iteration'])
                                            @if(isset($iteration['convergence_info']) && $iteration['convergence_info']['converged'])
                                                <span class="bg-emerald-100 text-emerald-800 px-2 py-1 rounded text-xs ml-2">
                                                    <i class="fas fa-flag-checkered mr-1"></i>
                                                    Konvergen
                                                </span>
                                            @else
                                                <span class="bg-amber-100 text-amber-800 px-2 py-1 rounded text-xs ml-2">
                                                    <i class="fas fa-stop mr-1"></i>
                                                    Iterasi Terakhir
                                                </span>
                                            @endif
                                        @endif
                                    </h3>
                                    <i class="fas fa-chevron-down transform transition-transform duration-200" 
                                        id="chevron-{{ $index }}"></i>
                                </div>
                            </button>
                            
                            <div class="mt-4 hidden" id="iteration-{{ $index }}">
                                <!-- Debug Information -->
                                @if(isset($iteration['debug_info']))
                                <div class="mb-4 p-4 bg-blue-50 border-l-4 border-blue-500 rounded">
                                    <h4 class="font-semibold text-blue-800 mb-2">
                                        <i class="fas fa-bug mr-2"></i>
                                        Debug Information - Iterasi {{ $iteration['iteration'] }}
                                    </h4>
                                    <div class="grid grid-cols-1 md:grid-cols-2 gap-4 text-sm">
                                        <div>
                                            @if(isset($iteration['is_initialization_only']) && $iteration['is_initialization_only'])
                                                <h5 class="font-medium text-blue-700 mb-1">Status Iterasi:</h5>
                                                <p class="text-blue-600 mb-3">
                                                    <i class="fas fa-star mr-1"></i>
                                                    <strong>Inisialisasi Centroid Saja</strong> - Belum ada pengelompokan data
                                                </p>
                                                
                                                <h5 class="font-medium text-blue-700 mb-1">Centroid yang Dipilih:</h5>
                                                <ul class="text-blue-600">
                                                    <li>• C1 (Tinggi): Data dengan total tertinggi</li>
                                                    <li>• C2 (Sedang): Data dengan total menengah</li>
                                                    <li>• C3 (Rendah): Data dengan total terendah</li>
                                                </ul>
                                            @else
                                                <h5 class="font-medium text-blue-700 mb-1">Jumlah Data per Cluster:</h5>
                                                <ul class="text-blue-600">
                                                    <li>• C1: {{ $iteration['debug_info']['cluster_counts'][1] }} data ({{ $iteration['cluster_labels'][1] ?? 'Label' }})</li>
                                                    <li>• C2: {{ $iteration['debug_info']['cluster_counts'][2] }} data ({{ $iteration['cluster_labels'][2] ?? 'Label' }})</li>
                                                    <li>• C3: {{ $iteration['debug_info']['cluster_counts'][3] }} data ({{ $iteration['cluster_labels'][3] ?? 'Label' }})</li>
                                                </ul>
                                            @endif
                                            
                                            <h5 class="font-medium text-blue-700 mb-1 mt-3">Total Centroid:</h5>
                                            <ul class="text-blue-600">
                                                <li>• C1: {{ $iteration['debug_info']['centroid_totals'][1] }}</li>
                                                <li>• C2: {{ $iteration['debug_info']['centroid_totals'][2] }}</li>
                                                <li>• C3: {{ $iteration['debug_info']['centroid_totals'][3] }}</li>
                                            </ul>
                                        </div>
                                        <div>
                                            @if(isset($iteration['debug_info']['centroid_change']))
                                            <h5 class="font-medium text-blue-700 mb-1">Perubahan:</h5>
                                            <p class="text-blue-600">
                                                Centroid: <strong>{{ $iteration['debug_info']['centroid_change'] }}</strong>
                                                @if($iteration['debug_info']['centroid_change'] > 0)
                                                    <span class="text-green-600">✓</span>
                                                @else
                                                    <span class="text-red-600">✗</span>
                                                @endif
                                            </p>
                                            @endif
                                            @if(isset($iteration['debug_info']['assignment_changed']) && $iteration['debug_info']['assignment_changed'] !== null)
                                            <p class="text-blue-600">
                                                Assignment: 
                                                @if($iteration['debug_info']['assignment_changed'])
                                                    <span class="text-green-600 font-semibold">Berubah ✓</span>
                                                @else
                                                    <span class="text-red-600 font-semibold">Tidak Berubah ✗</span>
                                                @endif
                                            </p>
                                            @endif
                                            
                                            @if(isset($iteration['debug_info']['centroid_values']))
                                            <h5 class="font-medium text-blue-700 mb-1 mt-3">Nilai Centroid Detail:</h5>
                                            <div class="text-xs text-blue-600">
                                                <div>C1: [{{ implode(', ', array_map(function($v) { return number_format($v, 1); }, $iteration['debug_info']['centroid_values'][1])) }}]</div>
                                                <div>C2: [{{ implode(', ', array_map(function($v) { return number_format($v, 1); }, $iteration['debug_info']['centroid_values'][2])) }}]</div>
                                                <div>C3: [{{ implode(', ', array_map(function($v) { return number_format($v, 1); }, $iteration['debug_info']['centroid_values'][3])) }}]</div>
                                            </div>
                                            @endif
                                        </div>
                                    </div>
                                    
                                    @if(isset($iteration['debug_info']['explanation']))
                                    <div class="mt-3 p-3 bg-blue-100 border border-blue-300 rounded">
                                        <p class="text-blue-800 text-sm">
                                            <i class="fas fa-info-circle mr-1"></i>
                                            <strong>Penjelasan:</strong> {{ $iteration['debug_info']['explanation'] }}
                                        </p>
                                    </div>
                                    @endif
                                    
                                    @if(isset($iteration['debug_info']['assignment_changed']) && !$iteration['debug_info']['assignment_changed'] && $iteration['iteration'] > 2)
                                    <div class="mt-3 p-3 bg-yellow-100 border border-yellow-300 rounded">
                                        <p class="text-yellow-800 text-sm">
                                            <i class="fas fa-exclamation-triangle mr-1"></i>
                                            <strong>Peringatan:</strong> Assignment tidak berubah dari iterasi sebelumnya. 
                                            Algoritma mungkin sudah konvergen atau perlu diperiksa.
                                        </p>
                                    </div>
                                    @endif
                                </div>
                                @endif

                                @if($iteration['is_first_iteration'])
                                    <!-- Penjelasan Iterasi Pertama -->
                                    <div class="mb-4 p-4 bg-orange-50 border-l-4 border-orange-500 rounded">
                                        <h4 class="font-semibold text-orange-800 mb-2">
                                            <i class="fas fa-star mr-2"></i>
                                            Iterasi Pertama - Inisialisasi Centroid dari Data Aktual
                                        </h4>
                                        <p class="text-sm text-orange-700 mb-2">
                                            Pada iterasi pertama, algoritma hanya melakukan <strong>inisialisasi centroid</strong> dari data aktual, belum ada pengelompokan data:
                                        </p>
                                        <ul class="text-sm text-orange-700 space-y-1">
                                            <li class="flex items-start">
                                                <i class="fas fa-arrow-right mr-2 mt-1 text-xs"></i>
                                                <strong>Centroid 1 (Tinggi):</strong> Dipilih dari data dengan total kejahatan tertinggi
                                            </li>
                                            <li class="flex items-start">
                                                <i class="fas fa-arrow-right mr-2 mt-1 text-xs"></i>
                                                <strong>Centroid 2 (Sedang):</strong> Dipilih dari data dengan total kejahatan menengah
                                            </li>
                                            <li class="flex items-start">
                                                <i class="fas fa-arrow-right mr-2 mt-1 text-xs"></i>
                                                <strong>Centroid 3 (Rendah):</strong> Dipilih dari data dengan total kejahatan terendah
                                            </li>
                                        </ul>
                                        <div class="mt-3 p-3 bg-orange-100 border border-orange-300 rounded">
                                            <p class="text-sm text-orange-800">
                                                <i class="fas fa-lightbulb mr-1"></i>
                                                <strong>Catatan:</strong> Pengelompokan data baru dimulai dari iterasi ke-2 menggunakan centroid yang telah diinisialisasi.
                                            </p>
                                        </div>
                                    </div>
                                @else
                                    <!-- Penjelasan Iterasi Kedua dan Seterusnya -->
                                    <div class="mb-4 p-4 bg-green-50 border-l-4 border-green-500 rounded">
                                        <h4 class="font-semibold text-green-800 mb-2">
                                            <i class="fas fa-calculator mr-2"></i>
                                            Iterasi {{ $iteration['iteration'] }} - Assignment dan Update Centroid
                                        </h4>
                                        <p class="text-sm text-green-700">
                                            Pada iterasi ini, algoritma melakukan:
                                        </p>
                                        <ol class="text-sm text-green-700 mt-2 space-y-1 ml-4">
                                            <li>1. Menghitung jarak setiap data ke centroid iterasi {{ $iteration['iteration'] - 1 }}</li>
                                            <li>2. Mengelompokkan data ke cluster berdasarkan jarak minimum</li>
                                            <li>3. Menghitung centroid baru sebagai rata-rata cluster untuk iterasi berikutnya</li>
                                        </ol>
                                    </div>
                                @endif

                                <!-- Convergence Explanation untuk iterasi terakhir -->
                                @if(isset($iteration['is_final_iteration']) && $iteration['is_final_iteration'] && isset($iteration['convergence_info']))
                                    <div class="mb-4 p-4 {{ $iteration['convergence_info']['converged'] ? 'bg-emerald-50 border-l-4 border-emerald-500' : 'bg-amber-50 border-l-4 border-amber-500' }} rounded">
                                        <h4 class="font-semibold {{ $iteration['convergence_info']['converged'] ? 'text-emerald-800' : 'text-amber-800' }} mb-2">
                                            <i class="fas {{ $iteration['convergence_info']['converged'] ? 'fa-flag-checkered' : 'fa-exclamation-triangle' }} mr-2"></i>
                                            {{ $iteration['convergence_info']['reason'] }}
                                        </h4>
                                        <p class="text-sm {{ $iteration['convergence_info']['converged'] ? 'text-emerald-700' : 'text-amber-700' }}">
                                            @if($iteration['convergence_info']['converged'])
                                                Algoritma berhasil mencapai konvergensi pada iterasi {{ $iteration['convergence_info']['iteration'] }}. 
                                                Perubahan centroid sudah sangat kecil (< 0.001), menandakan bahwa clustering telah mencapai solusi yang stabil. 
                                                Iterasi dihentikan secara otomatis untuk menghemat waktu komputasi.
                                            @else
                                                Algoritma belum mencapai konvergensi setelah {{ $iteration['convergence_info']['iteration'] }} iterasi. 
                                                Iterasi dihentikan karena mencapai batas maksimal yang diminta. 
                                                Anda dapat mencoba menambah jumlah iterasi maksimal atau menyesuaikan threshold konvergensi jika diperlukan.
                                            @endif
                                        </p>
                                    </div>
                                @endif

                                <!-- Summary Cards untuk setiap iterasi - HANYA JIKA BUKAN INITIALIZATION ONLY -->                                            
                                @if(!isset($iteration['is_initialization_only']) || !$iteration['is_initialization_only'])
                                <div class="p-6">
                                    <h4 class="font-semibold text-gray-800 mb-4">
                                        <i class="fas fa-object-group mr-2 text-indigo-600"></i>
                                        Hasil Pengelompokan Data
                                    </h4>
                                    <div class="grid grid-cols-1 lg:grid-cols-3 gap-6">
                                        @foreach([1 => 'Tinggi', 2 => 'Sedang', 3 => 'Rendah'] as $clusterNum => $clusterLabel)
                                            @php
                                                // Gunakan data dari iterasi saat ini, bukan final_clusters
                                                $clusterData = $iteration['summary_clusters'][$clusterNum] ?? [];
                                                $borderColor = $clusterNum == 1 ? 'border-red-500' : ($clusterNum == 2 ? 'border-yellow-500' : 'border-green-500');
                                                $bgColor = $clusterNum == 1 ? 'bg-red-50' : ($clusterNum == 2 ? 'bg-yellow-50' : 'bg-green-50');
                                                $textColor = $clusterNum == 1 ? 'text-red-800' : ($clusterNum == 2 ? 'text-yellow-800' : 'text-green-800');
                                                $iconColor = $clusterNum == 1 ? 'text-red-600' : ($clusterNum == 2 ? 'text-yellow-600' : 'text-green-600');
                                                $icon = $clusterNum == 1 ? 'fas fa-exclamation-triangle' : ($clusterNum == 2 ? 'fas fa-minus-circle' : 'fas fa-check-circle');
                                                $countData = count(array_filter($clusterData, function($key) { return $key !== 'average'; }, ARRAY_FILTER_USE_KEY));
                                            @endphp
                                            
                                            <div class="border-l-4 {{ $borderColor }} {{ $bgColor }} rounded-lg p-6">
                                                <div class="flex items-center mb-4">
                                                    <div class="p-3 bg-white rounded-full shadow-sm">
                                                        <i class="{{ $icon }} {{ $iconColor }} text-xl"></i>
                                                    </div>
                                                    <div class="ml-4">
                                                        <h3 class="text-xl font-bold {{ $textColor }}">
                                                            Cluster {{ $clusterNum }} - {{ $clusterLabel }}
                                                        </h3>
                                                        <p class="text-2xl font-bold {{ $textColor }}">{{ $countData }}</p>
                                                        <p class="text-sm text-gray-600">Bulan dengan kejahatan {{ strtolower($clusterLabel) }}</p>
                                                    </div>
                                                </div>
                                                
                                                @if(!empty($clusterData) && $countData > 0)
                                                    <!-- Detail Data Bulan -->
                                                    <div class="space-y-2">
                                                        <h4 class="font-semibold text-gray-800 text-sm">Detail Data:</h4>
                                                        <div class="max-h-64 overflow-y-auto">
                                                            @foreach($clusterData as $key => $item)
                                                                @if($key !== 'average')
                                                                <div class="bg-white p-3 rounded-lg shadow-sm border-l-2 {{ $borderColor }} mb-2">
                                                                    <div class="flex justify-between items-center mb-2">
                                                                        <span class="font-medium text-gray-800 text-sm">{{ $item['month'] }}</span>
                                                                        <span class="text-xs text-gray-500">Data-{{ $item['data_index'] }}</span>
                                                                    </div>
                                                                    <div class="grid grid-cols-5 gap-1 text-xs">
                                                                        <div class="text-center p-1">
                                                                            <div class="text-gray-500 text-xs">Curas</div>
                                                                            <div class="font-bold {{ $textColor }}">{{ $item['data'][0] }}</div>
                                                                        </div>
                                                                        <div class="text-center p-1">
                                                                            <div class="text-gray-500 text-xs">Curat</div>
                                                                            <div class="font-bold {{ $textColor }}">{{ $item['data'][1] }}</div>
                                                                        </div>
                                                                        <div class="text-center p-1">
                                                                            <div class="text-gray-500 text-xs">Curanmor</div>
                                                                            <div class="font-bold {{ $textColor }}">{{ $item['data'][2] }}</div>
                                                                        </div>
                                                                        <div class="text-center p-1">
                                                                            <div class="text-gray-500 text-xs">Anirat</div>
                                                                            <div class="font-bold {{ $textColor }}">{{ $item['data'][3] }}</div>
                                                                        </div>
                                                                        <div class="text-center p-1">
                                                                            <div class="text-gray-500 text-xs">Judi</div>
                                                                            <div class="font-bold {{ $textColor }}">{{ $item['data'][4] }}</div>
                                                                        </div>
                                                                    </div>
                                                                </div>
                                                                @endif
                                                            @endforeach
                                                        </div>

                                                        @if($iteration['is_first_iteration'] == false)
                                                            @if(isset($clusterData['average']))
                                                            <!-- Rata-rata Cluster -->
                                                            <div class="mt-3 p-3 bg-white rounded-lg shadow-sm border-2 {{ $borderColor }}">
                                                                <h5 class="font-semibold text-gray-800 text-xs mb-2">Rata-rata Cluster:</h5>
                                                                <div class="grid grid-cols-5 gap-1 text-xs">
                                                                    <div class="text-center">
                                                                        <div class="text-gray-500">Curas</div>
                                                                        <div class="font-bold {{ $textColor }}">{{ $clusterData['average'][0] }}</div>
                                                                    </div>
                                                                    <div class="text-center">
                                                                        <div class="text-gray-500">Curat</div>
                                                                        <div class="font-bold {{ $textColor }}">{{ $clusterData['average'][1] }}</div>
                                                                    </div>
                                                                    <div class="text-center">
                                                                        <div class="text-gray-500">Curanmor</div>
                                                                        <div class="font-bold {{ $textColor }}">{{ $clusterData['average'][2] }}</div>
                                                                    </div>
                                                                    <div class="text-center">
                                                                        <div class="text-gray-500">Anirat</div>
                                                                        <div class="font-bold {{ $textColor }}">{{ $clusterData['average'][3] }}</div>
                                                                    </div>
                                                                    <div class="text-center">
                                                                        <div class="text-gray-500">Judi</div>
                                                                        <div class="font-bold {{ $textColor }}">{{ $clusterData['average'][4] }}</div>
                                                                    </div>
                                                                </div>
                                                            </div>
                                                            @endif
                                                        @endif
                                                    </div>
                                                @else
                                                    <div class="text-gray-500 italic text-center py-4">
                                                        Tidak ada data dalam cluster ini
                                                    </div>
                                                @endif
                                            </div>
                                        @endforeach
                                    </div>
                                </div>
                                @else
                                <!-- Tampilan khusus untuk iterasi 1 (inisialisasi saja) -->
                                <div class="p-6">
                                    <div class="bg-gradient-to-r from-blue-50 to-indigo-50 border border-blue-200 rounded-lg p-6">
                                        <h4 class="font-semibold text-blue-800 mb-4">
                                            <i class="fas fa-info-circle mr-2"></i>
                                            Centroid yang Diinisialisasi (Belum Ada Pengelompokan)
                                        </h4>
                                        <p class="text-sm text-blue-700 mb-4">
                                            Pada iterasi pertama, hanya centroid yang diinisialisasi dari data aktual. Pengelompokan data dimulai dari iterasi ke-2.
                                        </p>
                                        
                                        <!-- Tampilkan centroid yang dipilih -->
                                        <div class="grid grid-cols-1 md:grid-cols-3 gap-4">
                                            @foreach([1 => ['label' => 'Tinggi', 'color' => 'red', 'icon' => 'fas fa-exclamation-triangle'], 
                                                        2 => ['label' => 'Sedang', 'color' => 'yellow', 'icon' => 'fas fa-minus-circle'], 
                                                        3 => ['label' => 'Rendah', 'color' => 'green', 'icon' => 'fas fa-check-circle']] as $clusterNum => $config)
                                                @php
                                                    $centroid = $iteration['centroids'][$clusterNum - 1] ?? [];
                                                    $total = !empty($centroid) ? array_sum($centroid) : 0;
                                                @endphp
                                                <div class="bg-{{ $config['color'] }}-50 border border-{{ $config['color'] }}-200 rounded-lg p-4">
                                                    <div class="flex items-center mb-3">
                                                        <div class="p-2 bg-white rounded-full shadow-sm">
                                                            <i class="{{ $config['icon'] }} text-{{ $config['color'] }}-600"></i>
                                                        </div>
                                                        <div class="ml-3">
                                                            <h5 class="font-bold text-{{ $config['color'] }}-800">
                                                                Centroid {{ $clusterNum }} - {{ $config['label'] }}
                                                            </h5>
                                                            <p class="text-sm text-{{ $config['color'] }}-600">Total: {{ $total }}</p>
                                                        </div>
                                                    </div>
                                                    @if(!empty($centroid))
                                                    <div class="grid grid-cols-5 gap-1 text-xs">
                                                        @foreach(['Curas', 'Curat', 'Curanmor', 'Anirat', 'Judi'] as $idx => $label)
                                                        <div class="text-center p-2 bg-white rounded">
                                                            <div class="text-gray-500 text-xs">{{ $label }}</div>
                                                            <div class="font-bold text-{{ $config['color'] }}-700">{{ $centroid[$idx] ?? 0 }}</div>
                                                        </div>
                                                        @endforeach
                                                    </div>
                                                    @endif
                                                </div>
                                            @endforeach
                                        </div>
                                    </div>
                                </div>
                                @endif

                                <!-- Tabel Centroid -->
                                <div class="mb-6">
                                    <h4 class="font-semibold text-gray-800 mb-3">
                                        <i class="fas fa-crosshairs mr-2 text-indigo-600"></i>
                                        Centroid Iterasi {{ $iteration['iteration'] }}
                                        @if(isset($iteration['is_initialization_only']) && $iteration['is_initialization_only'])
                                            <span class="text-sm font-normal text-gray-600">(Inisialisasi dari Data Aktual)</span>
                                        @else
                                            <span class="text-sm font-normal text-gray-600">(Hasil Perhitungan = Rata-rata Cluster)</span>
                                        @endif
                                    </h4>
                                    <div class="overflow-x-auto">
                                        <table class="min-w-full bg-white border border-gray-200 rounded-lg shadow-sm">
                                            <thead class="bg-gray-50">
                                                <tr>
                                                    <th class="px-4 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                                                        Centroid
                                                    </th>
                                                    <th class="px-4 py-3 text-center text-xs font-medium text-gray-500 uppercase tracking-wider">
                                                        Curas
                                                    </th>
                                                    <th class="px-4 py-3 text-center text-xs font-medium text-gray-500 uppercase tracking-wider">
                                                        Curat
                                                    </th>
                                                    <th class="px-4 py-3 text-center text-xs font-medium text-gray-500 uppercase tracking-wider">
                                                        Curanmor
                                                    </th>
                                                    <th class="px-4 py-3 text-center text-xs font-medium text-gray-500 uppercase tracking-wider">
                                                        Anirat
                                                    </th>
                                                    <th class="px-4 py-3 text-center text-xs font-medium text-gray-500 uppercase tracking-wider">
                                                        Judi
                                                    </th>
                                                </tr>
                                            </thead>
                                            <tbody class="divide-y divide-gray-200">
                                                @foreach($iteration['centroids'] as $centroidIndex => $centroid)
                                                    @php
                                                        $labels = ['Tinggi', 'Sedang', 'Rendah'];
                                                        $colors = ['text-red-800 bg-red-50', 'text-yellow-800 bg-yellow-50', 'text-green-800 bg-green-50'];
                                                    @endphp
                                                    <tr class="{{ $colors[$centroidIndex] }}">
                                                        <td class="px-4 py-3 text-sm font-medium">
                                                            C{{ $centroidIndex + 1 }} - {{ $labels[$centroidIndex] }}
                                                        </td>
                                                        @foreach($centroid as $value)
                                                            <td class="px-4 py-3 text-sm text-center font-bold">
                                                                {{ $value }}
                                                            </td>
                                                        @endforeach
                                                    </tr>
                                                @endforeach
                                            </tbody>
                                        </table>
                                    </div>
                                </div>

                                <!-- Tabel Detail Perhitungan Jarak - HANYA JIKA BUKAN INITIALIZATION ONLY -->
                                @if(!isset($iteration['is_initialization_only']) || !$iteration['is_initialization_only'])
                                <div class="mb-6">
                                    <h4 class="font-semibold text-gray-800 mb-3">
                                        <i class="fas fa-ruler mr-2 text-purple-600"></i>
                                        Detail Perhitungan Jarak dan Penugasan Cluster
                                    </h4>
                                    <div class="overflow-x-auto">
                                        <table class="min-w-full bg-white border border-gray-200 rounded-lg shadow-sm">
                                            <thead class="bg-gray-50">
                                                <tr>
                                                    <th class="px-3 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                                                        Data
                                                    </th>
                                                    <th class="px-3 py-3 text-center text-xs font-medium text-gray-500 uppercase tracking-wider">
                                                        Bulan
                                                    </th>
                                                    <th class="px-3 py-3 text-center text-xs font-medium text-gray-500 uppercase tracking-wider">
                                                        Jarak ke C1
                                                    </th>
                                                    <th class="px-3 py-3 text-center text-xs font-medium text-gray-500 uppercase tracking-wider">
                                                        Jarak ke C2
                                                    </th>
                                                    <th class="px-3 py-3 text-center text-xs font-medium text-gray-500 uppercase tracking-wider">
                                                        Jarak ke C3
                                                    </th>
                                                    <th class="px-3 py-3 text-center text-xs font-medium text-gray-500 uppercase tracking-wider">
                                                        Jarak Min
                                                    </th>
                                                    <th class="px-3 py-3 text-center text-xs font-medium text-gray-500 uppercase tracking-wider">
                                                        Cluster
                                                    </th>
                                                </tr>
                                            </thead>
                                            <tbody class="divide-y divide-gray-200">
                                                @foreach($iteration['data_with_clusters'] as $item)
                                                    @php
                                                        $clusterColors = [
                                                            1 => 'bg-red-50 text-red-800',
                                                            2 => 'bg-yellow-50 text-yellow-800',
                                                            3 => 'bg-green-50 text-green-800'
                                                        ];
                                                        $clusterLabels = [1 => 'Tinggi', 2 => 'Sedang', 3 => 'Rendah'];
                                                        $monthIndex = $item['data_index'] - 1;
                                                        $month = $months[$monthIndex] ?? 'N/A';
                                                    @endphp
                                                    <tr class="hover:bg-gray-50">
                                                        <td class="px-3 py-3 text-sm font-medium text-gray-900">
                                                            {{ $item['data_index'] }}
                                                        </td>
                                                        <td class="px-3 py-3 text-sm text-center text-gray-700">
                                                            {{ $month }}
                                                        </td>
                                                        @foreach($item['distances'] as $distance)
                                                            <td class="px-3 py-3 text-sm text-center text-gray-700">
                                                                {{ $distance }}
                                                            </td>
                                                        @endforeach
                                                        <td class="px-3 py-3 text-sm text-center font-bold text-blue-600">
                                                            {{ min($item['distances']) }}
                                                        </td>
                                                        <td class="px-3 py-3 text-center">
                                                            <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium {{ $clusterColors[$item['cluster']] }}">
                                                                C{{ $item['cluster'] }} - {{ $clusterLabels[$item['cluster']] }}
                                                            </span>
                                                        </td>
                                                    </tr>
                                                @endforeach
                                            </tbody>
                                        </table>
                                    </div>
                                </div>
                                @else
                                <!-- Pesan untuk iterasi 1 -->
                                <div class="mb-6 p-4 bg-gray-50 border border-gray-200 rounded-lg">
                                    <h4 class="font-semibold text-gray-700 mb-2">
                                        <i class="fas fa-info-circle mr-2"></i>
                                        Belum Ada Perhitungan Jarak
                                    </h4>
                                    <p class="text-sm text-gray-600">
                                        Pada iterasi pertama, belum dilakukan perhitungan jarak dan penugasan cluster. 
                                        Proses clustering dimulai dari iterasi ke-2 menggunakan centroid yang telah diinisialisasi di atas.
                                    </p>
                                </div>
                                @endif
                            </div>
                        </div>
                    </div>
                    @endforeach
                </div>
            </div>

            <!-- Grafik Visualisasi -->
            <div class="bg-white rounded-xl shadow-lg mb-8">
                <div class="p-6 border-b border-gray-200">
                    <h2 class="text-2xl font-bold text-gray-900">
                        <i class="fas fa-chart-bar mr-2 text-purple-600"></i>
                        Visualisasi Hasil Clustering
                    </h2>
                </div>
                
                <div class="p-6">
                    <div class="grid grid-cols-1 lg:grid-cols-2 gap-6">
                        <!-- Chart Distribusi Cluster -->
                        <div class="bg-gray-50 rounded-lg p-4">
                            <h3 class="text-lg font-semibold text-gray-800 mb-4">
                                <i class="fas fa-pie-chart mr-2 text-blue-600"></i>
                                Distribusi Data per Cluster
                            </h3>
                            <canvas id="clusterDistributionChart" width="400" height="300"></canvas>
                        </div>
                        
                        <!-- Chart Rata-rata Kejahatan per Cluster -->
                        <div class="bg-gray-50 rounded-lg p-4">
                            <h3 class="text-lg font-semibold text-gray-800 mb-4">
                                <i class="fas fa-bar-chart mr-2 text-green-600"></i>
                                Rata-rata Kejahatan per Cluster
                            </h3>
                            <canvas id="crimeAverageChart" width="400" height="300"></canvas>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Kesimpulan dan Rekomendasi -->
            <div class="bg-white rounded-xl shadow-lg mb-8">
                <div class="p-6 border-b border-gray-200">
                    <h2 class="text-2xl font-bold text-gray-900">
                        <i class="fas fa-lightbulb mr-2 text-yellow-600"></i>
                        Kesimpulan dan Rekomendasi
                    </h2>
                </div>
                
                <div class="p-6">
                    <div class="grid grid-cols-1 lg:grid-cols-3 gap-6">
                        <!-- Cluster Tinggi -->
                        @php
                            $highClusterCount = count(array_filter($final_clusters[1] ?? [], function($key) { return $key !== 'average'; }, ARRAY_FILTER_USE_KEY));
                            $mediumClusterCount = count(array_filter($final_clusters[2] ?? [], function($key) { return $key !== 'average'; }, ARRAY_FILTER_USE_KEY));
                            $lowClusterCount = count(array_filter($final_clusters[3] ?? [], function($key) { return $key !== 'average'; }, ARRAY_FILTER_USE_KEY));
                        @endphp
                        
                        <div class="bg-red-50 border-l-4 border-red-500 rounded-lg p-6">
                            <div class="flex items-center mb-4">
                                <div class="p-3 bg-red-100 rounded-full">
                                    <i class="fas fa-exclamation-triangle text-red-600 text-xl"></i>
                                </div>
                                <div class="ml-4">
                                    <h3 class="text-lg font-bold text-red-800">Cluster Tinggi</h3>
                                    <p class="text-sm text-red-600">{{ $highClusterCount }} bulan teridentifikasi</p>
                                </div>
                            </div>
                            <div class="space-y-2">
                                <h4 class="font-semibold text-red-800">Karakteristik:</h4>
                                <ul class="text-sm text-red-700 space-y-1">
                                    <li class="flex items-start">
                                        <i class="fas fa-dot-circle mr-2 mt-1 text-xs"></i>
                                        Tingkat kejahatan sangat tinggi
                                    </li>
                                    <li class="flex items-start">
                                        <i class="fas fa-dot-circle mr-2 mt-1 text-xs"></i>
                                        Memerlukan perhatian khusus
                                    </li>
                                    <li class="flex items-start">
                                        <i class="fas fa-dot-circle mr-2 mt-1 text-xs"></i>
                                        Prioritas penanganan tertinggi
                                    </li>
                                </ul>
                                <h4 class="font-semibold text-red-800 mt-4">Rekomendasi:</h4>
                                <ul class="text-sm text-red-700 space-y-1">
                                    <li class="flex items-start">
                                        <i class="fas fa-arrow-right mr-2 mt-1 text-xs"></i>
                                        Tingkatkan patroli keamanan
                                    </li>
                                    <li class="flex items-start">
                                        <i class="fas fa-arrow-right mr-2 mt-1 text-xs"></i>
                                        Implementasi program pencegahan
                                    </li>
                                    <li class="flex items-start">
                                        <i class="fas fa-arrow-right mr-2 mt-1 text-xs"></i>
                                        Koordinasi intensif dengan pihak terkait
                                    </li>
                                </ul>
                            </div>
                        </div>
                        
                        <!-- Cluster Sedang -->
                        <div class="bg-yellow-50 border-l-4 border-yellow-500 rounded-lg p-6">
                            <div class="flex items-center mb-4">
                                <div class="p-3 bg-yellow-100 rounded-full">
                                    <i class="fas fa-minus-circle text-yellow-600 text-xl"></i>
                                </div>
                                <div class="ml-4">
                                    <h3 class="text-lg font-bold text-yellow-800">Cluster Sedang</h3>
                                    <p class="text-sm text-yellow-600">{{ $mediumClusterCount }} bulan teridentifikasi</p>
                                </div>
                            </div>
                            <div class="space-y-2">
                                <h4 class="font-semibold text-yellow-800">Karakteristik:</h4>
                                <ul class="text-sm text-yellow-700 space-y-1">
                                    <li class="flex items-start">
                                        <i class="fas fa-dot-circle mr-2 mt-1 text-xs"></i>
                                        Tingkat kejahatan moderat
                                    </li>
                                    <li class="flex items-start">
                                        <i class="fas fa-dot-circle mr-2 mt-1 text-xs"></i>
                                        Memerlukan monitoring berkala
                                    </li>
                                    <li class="flex items-start">
                                        <i class="fas fa-dot-circle mr-2 mt-1 text-xs"></i>
                                        Potensi peningkatan kejahatan
                                    </li>
                                </ul>
                                <h4 class="font-semibold text-yellow-800 mt-4">Rekomendasi:</h4>
                                <ul class="text-sm text-yellow-700 space-y-1">
                                    <li class="flex items-start">
                                        <i class="fas fa-arrow-right mr-2 mt-1 text-xs"></i>
                                        Patroli rutin dan terjadwal
                                    </li>
                                    <li class="flex items-start">
                                        <i class="fas fa-arrow-right mr-2 mt-1 text-xs"></i>
                                        Program edukasi masyarakat
                                    </li>
                                    <li class="flex items-start">
                                        <i class="fas fa-arrow-right mr-2 mt-1 text-xs"></i>
                                        Sistem pelaporan yang mudah
                                    </li>
                                </ul>
                            </div>
                        </div>
                        
                        <!-- Cluster Rendah -->
                        <div class="bg-green-50 border-l-4 border-green-500 rounded-lg p-6">
                            <div class="flex items-center mb-4">
                                <div class="p-3 bg-green-100 rounded-full">
                                    <i class="fas fa-check-circle text-green-600 text-xl"></i>
                                </div>
                                <div class="ml-4">
                                    <h3 class="text-lg font-bold text-green-800">Cluster Rendah</h3>
                                    <p class="text-sm text-green-600">{{ $lowClusterCount }} bulan teridentifikasi</p>
                                </div>
                            </div>
                            <div class="space-y-2">
                                <h4 class="font-semibold text-green-800">Karakteristik:</h4>
                                <ul class="text-sm text-green-700 space-y-1">
                                    <li class="flex items-start">
                                        <i class="fas fa-dot-circle mr-2 mt-1 text-xs"></i>
                                        Tingkat kejahatan rendah
                                    </li>
                                    <li class="flex items-start">
                                        <i class="fas fa-dot-circle mr-2 mt-1 text-xs"></i>
                                        Kondisi keamanan baik
                                    </li>
                                    <li class="flex items-start">
                                        <i class="fas fa-dot-circle mr-2 mt-1 text-xs"></i>
                                        Model untuk area lain
                                    </li>
                                </ul>
                                <h4 class="font-semibold text-green-800 mt-4">Rekomendasi:</h4>
                                <ul class="text-sm text-green-700 space-y-1">
                                    <li class="flex items-start">
                                        <i class="fas fa-arrow-right mr-2 mt-1 text-xs"></i>
                                        Pertahankan kondisi saat ini
                                    </li>
                                    <li class="flex items-start">
                                        <i class="fas fa-arrow-right mr-2 mt-1 text-xs"></i>
                                        Program community policing
                                    </li>
                                    <li class="flex items-start">
                                        <i class="fas fa-arrow-right mr-2 mt-1 text-xs"></i>
                                        Dokumentasi best practices
                                    </li>
                                </ul>
                            </div>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Export dan Action Buttons -->
            <div class="bg-white rounded-xl shadow-lg">
                <div class="p-6">
                    <h2 class="text-xl font-bold text-gray-900 mb-4">
                        <i class="fas fa-download mr-2 text-indigo-600"></i>
                        Export dan Aksi Lanjutan
                    </h2>
                    
                    <div class="flex flex-wrap gap-4">
                        <button onclick="exportToPDF()" 
                                class="inline-flex items-center px-6 py-3 bg-red-600 text-white rounded-lg hover:bg-red-700 transition duration-200">
                            <i class="fas fa-file-pdf mr-2"></i>
                            Export PDF
                        </button>
                        
                        <button onclick="exportToExcel()" 
                                class="inline-flex items-center px-6 py-3 bg-green-600 text-white rounded-lg hover:bg-green-700 transition duration-200">
                            <i class="fas fa-file-excel mr-2"></i>
                            Export Excel
                        </button>
                        
                        <button onclick="printResults()" 
                                class="inline-flex items-center px-6 py-3 bg-blue-600 text-white rounded-lg hover:bg-blue-700 transition duration-200">
                            <i class="fas fa-print mr-2"></i>
                            Print
                        </button>
                        
                        <a href="{{ route('clustering.index') }}" 
                            class="inline-flex items-center px-6 py-3 bg-gray-600 text-white rounded-lg hover:bg-gray-700 transition duration-200">
                            <i class="fas fa-arrow-left mr-2"></i>
                            Kembali ke Clustering
                        </a>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- JavaScript untuk Interaktivitas -->
    <script>
        // Toggle accordion
        function toggleIteration(index) {
            const content = document.getElementById(`iteration-${index}`);
            const chevron = document.getElementById(`chevron-${index}`);
            
            if (content.classList.contains('hidden')) {
                content.classList.remove('hidden');
                chevron.classList.add('rotate-180');
            } else {
                content.classList.add('hidden');
                chevron.classList.remove('rotate-180');
            }
        }

        // Chart.js untuk visualisasi
        document.addEventListener('DOMContentLoaded', function() {
            // Data untuk chart
            const clusterData = {
                labels: ['Cluster Tinggi', 'Cluster Sedang', 'Cluster Rendah'],
                datasets: [{
                    data: [{{ $highClusterCount }}, {{ $mediumClusterCount }}, {{ $lowClusterCount }}],
                    backgroundColor: ['#EF4444', '#F59E0B', '#10B981'],
                    borderColor: ['#DC2626', '#D97706', '#059669'],
                    borderWidth: 2
                }]
            };

            // Pie chart untuk distribusi cluster
            const ctx1 = document.getElementById('clusterDistributionChart').getContext('2d');
            new Chart(ctx1, {
                type: 'pie',
                data: clusterData,
                options: {
                    responsive: true,
                    plugins: {
                        legend: {
                            position: 'bottom',
                        },
                        title: {
                            display: true,
                            text: 'Distribusi Data Cluster'
                        }
                    }
                }
            });

            // Bar chart untuk rata-rata kejahatan
            @if(isset($final_clusters))
            const averageData = {
                labels: ['Curas', 'Curat', 'Curanmor', 'Anirat', 'Judi'],
                datasets: [
                    @if(isset($final_clusters[1]['average']))
                    {
                        label: 'Cluster Tinggi',
                        data: [{{ implode(',', $final_clusters[1]['average']) }}],
                        backgroundColor: 'rgba(239, 68, 68, 0.7)',
                        borderColor: 'rgb(239, 68, 68)',
                        borderWidth: 1
                    },
                    @endif
                    @if(isset($final_clusters[2]['average']))
                    {
                        label: 'Cluster Sedang',
                        data: [{{ implode(',', $final_clusters[2]['average']) }}],
                        backgroundColor: 'rgba(245, 158, 11, 0.7)',
                        borderColor: 'rgb(245, 158, 11)',
                        borderWidth: 1
                    },
                    @endif
                    @if(isset($final_clusters[3]['average']))
                    {
                        label: 'Cluster Rendah',
                        data: [{{ implode(',', $final_clusters[3]['average']) }}],
                        backgroundColor: 'rgba(16, 185, 129, 0.7)',
                        borderColor: 'rgb(16, 185, 129)',
                        borderWidth: 1
                    }
                    @endif
                ]
            };

            const ctx2 = document.getElementById('crimeAverageChart').getContext('2d');
            new Chart(ctx2, {
                type: 'bar',
                data: averageData,
                options: {
                    responsive: true,
                    plugins: {
                        legend: {
                            position: 'bottom',
                        },
                        title: {
                            display: true,
                            text: 'Rata-rata Kejahatan per Cluster'
                        }
                    },
                    scales: {
                        y: {
                            beginAtZero: true
                        }
                    }
                }
            });
            @endif
        });

        // Fungsi export dan print

        function printResults() {
            window.print();
        }

        // Auto-expand first iteration
        document.addEventListener('DOMContentLoaded', function() {
            if (document.getElementById('iteration-0')) {
                toggleIteration(0);
            }
        });
    </script>

    <!-- Chart.js CDN -->
    <script src="https://cdn.jsdelivr.net/npm/chart.js"></script>

    <!-- Print Styles -->
    <style>
        @media print {
            .no-print {
                display: none !important;
            }
            
            .print-break {
                page-break-before: always;
            }
            
            body {
                -webkit-print-color-adjust: exact;
                color-adjust: exact;
            }
        }
    </style>
    </x-app-layout>