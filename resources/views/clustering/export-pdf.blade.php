<!DOCTYPE html>
<html>
<head>
    <meta charset="utf-8">
    <title>Hasil Clustering K-Means Tahun {{ $tahun }}</title>
    <style>
        body {
            font-family: 'Arial', sans-serif;
            margin: 0;
            padding: 20px;
            background-color: #f8f9fa;
        }
        .container {
            max-width: 800px;
            margin: 0 auto;
            background: white;
            padding: 30px;
            border-radius: 10px;
            box-shadow: 0 0 20px rgba(0,0,0,0.1);
        }
        .header {
            text-align: center;
            margin-bottom: 30px;
            border-bottom: 3px solid #3498db;
            padding-bottom: 20px;
        }
        .header h1 {
            color: #2c3e50;
            font-size: 28px;
            margin: 0 0 10px 0;
        }
        .header p {
            color: #7f8c8d;
            font-size: 16px;
            margin: 0;
        }
        .section {
            margin-bottom: 25px;
            padding: 20px;
            border-radius: 8px;
            background: #f8f9fa;
        }
        .section h2 {
            color: #2c3e50;
            font-size: 20px;
            margin: 0 0 15px 0;
            border-left: 4px solid #3498db;
            padding-left: 15px;
        }
        .info-grid {
            display: grid;
            grid-template-columns: repeat(3, 1fr);
            gap: 15px;
            margin-bottom: 20px;
        }
        .info-card {
            background: white;
            padding: 15px;
            border-radius: 8px;
            border-left: 4px solid #e74c3c;
            box-shadow: 0 2px 5px rgba(0,0,0,0.1);
        }
        .info-card.medium {
            border-left-color: #f39c12;
        }
        .info-card.low {
            border-left-color: #27ae60;
        }
        .info-card h3 {
            margin: 0 0 10px 0;
            font-size: 16px;
            color: #2c3e50;
        }
        .info-card .count {
            font-size: 24px;
            font-weight: bold;
            color: #34495e;
        }
        .table-container {
            margin: 20px 0;
            overflow-x: auto;
        }
        table {
            width: 100%;
            border-collapse: collapse;
            margin: 15px 0;
            background: white;
            border-radius: 8px;
            overflow: hidden;
            box-shadow: 0 2px 5px rgba(0,0,0,0.1);
        }
        th {
            background: #3498db;
            color: white;
            padding: 12px;
            text-align: left;
            font-weight: bold;
        }
        td {
            padding: 10px 12px;
            border-bottom: 1px solid #ecf0f1;
        }
        tr:nth-child(even) {
            background: #f8f9fa;
        }
        .cluster-high {
            background: #ffebee !important;
            color: #c62828;
        }
        .cluster-medium {
            background: #fff3e0 !important;
            color: #ef6c00;
        }
        .cluster-low {
            background: #e8f5e8 !important;
            color: #2e7d32;
        }
        .summary-box {
            background: white;
            padding: 20px;
            border-radius: 8px;
            margin: 15px 0;
            box-shadow: 0 2px 5px rgba(0,0,0,0.1);
        }
        .summary-box h3 {
            margin: 0 0 15px 0;
            color: #2c3e50;
            font-size: 18px;
        }
        .month-list {
            display: flex;
            flex-wrap: wrap;
            gap: 8px;
        }
        .month-tag {
            background: #3498db;
            color: white;
            padding: 5px 10px;
            border-radius: 15px;
            font-size: 12px;
        }
        .footer {
            margin-top: 30px;
            text-align: center;
            color: #7f8c8d;
            font-size: 12px;
            border-top: 1px solid #ecf0f1;
            padding-top: 20px;
        }
        .convergence-info {
            background: #e3f2fd;
            padding: 15px;
            border-radius: 8px;
            margin: 15px 0;
            border-left: 4px solid #2196f3;
        }
        .iteration-summary {
            background: white;
            padding: 15px;
            border-radius: 8px;
            margin: 10px 0;
            border-left: 4px solid #9c27b0;
        }
        
        .subsection {
            margin: 20px 0;
            padding: 15px;
            background: #f8f9fa;
            border-radius: 8px;
        }
        
        .subsection h3 {
            color: #2c3e50;
            font-size: 18px;
            margin-bottom: 15px;
            border-bottom: 2px solid #3498db;
            padding-bottom: 8px;
        }
        
        .subsection h4 {
            color: #34495e;
            font-size: 16px;
            margin: 15px 0 10px 0;
        }
        
        .centroid-detail {
            background: white;
            padding: 15px;
            border-radius: 8px;
            margin: 15px 0;
            border-left: 4px solid #e74c3c;
        }
        
        .distance-calculation {
            background: white;
            padding: 15px;
            border-radius: 8px;
            margin: 15px 0;
            border-left: 4px solid #f39c12;
        }
        
        .calculation-note {
            background: #e3f2fd;
            padding: 10px;
            border-radius: 5px;
            margin: 10px 0;
            font-family: 'Courier New', monospace;
            font-size: 14px;
        }
        
        .algorithm-explanation {
            background: #f1f8e9;
            padding: 20px;
            border-radius: 8px;
            margin: 20px 0;
            border-left: 4px solid #4caf50;
        }
        
        .explanation-content h4 {
            color: #2e7d32;
            font-size: 16px;
            margin: 15px 0 8px 0;
        }
        
        .explanation-content ol,
        .explanation-content ul {
            margin: 10px 0;
            padding-left: 20px;
        }
        
        .explanation-content li {
            margin: 5px 0;
            line-height: 1.5;
        }
    </style>
</head>
<body>
    <div class="container">
        <!-- Header -->
        <div class="header">
            <h1>Hasil Clustering K-Means</h1>
            <p>Analisis Data Kejahatan Tahun {{ $tahun }}</p>
            <p>Dibuat pada: {{ date('d/m/Y H:i:s') }}</p>
        </div>

        <!-- Informasi Konvergensi -->
        <div class="section">
            <h2>Informasi Konvergensi</h2>
            <div class="convergence-info">
                <strong>Total Iterasi:</strong> {{ $convergence_info['total_iterations'] ?? 0 }} dari {{ $convergence_info['max_iterations_requested'] ?? 0 }} diminta<br>
                <strong>Status Konvergensi:</strong> {{ $convergence_info['is_converged'] ?? false ? 'Konvergen' : 'Belum Konvergen' }}<br>
                @if(isset($convergence_info['convergence_iteration']))
                <strong>Konvergen di Iterasi:</strong> {{ $convergence_info['convergence_iteration'] }}
                @endif
            </div>
        </div>

        <!-- Data Lengkap -->
        <div class="section">
            <h2>Data Lengkap Kejahatan Tahun {{ $tahun }}</h2>
            <div class="table-container">
                <table>
                    <thead>
                        <tr>
                            <th>No</th>
                            <th>Bulan</th>
                            <th>Pencurian dengan Kekerasan</th>
                            <th>Pencurian dengan Pemberatan</th>
                            <th>Pencurian Kendaraan Bermotor</th>
                            <th>Pencurian dengan Aniaya</th>
                            <th>Perjudian</th>
                            <th>Total</th>
                        </tr>
                    </thead>
                    <tbody>
                        @foreach($data12bulan as $index => $item)
                        @php
                            $total = $item['curas'] + $item['curat'] + $item['curanmor'] + $item['anirat'] + $item['judi'];
                        @endphp
                        <tr>
                            <td>{{ $index + 1 }}</td>
                            <td>{{ $item['bulan'] }}</td>
                            <td>{{ $item['curas'] }}</td>
                            <td>{{ $item['curat'] }}</td>
                            <td>{{ $item['curanmor'] }}</td>
                            <td>{{ $item['anirat'] }}</td>
                            <td>{{ $item['judi'] }}</td>
                            <td><strong>{{ $total }}</strong></td>
                        </tr>
                        @endforeach
                    </tbody>
                </table>
            </div>
        </div>

        <!-- Hasil Clustering -->
        <div class="section">
            <h2>Hasil Pengelompokan Cluster</h2>
            <div class="info-grid">
                @php
                    $highClusterCount = count(array_filter($final_clusters[1] ?? [], function($key) { return $key !== 'average'; }, ARRAY_FILTER_USE_KEY));
                    $mediumClusterCount = count(array_filter($final_clusters[2] ?? [], function($key) { return $key !== 'average'; }, ARRAY_FILTER_USE_KEY));
                    $lowClusterCount = count(array_filter($final_clusters[3] ?? [], function($key) { return $key !== 'average'; }, ARRAY_FILTER_USE_KEY));
                @endphp
                
                <div class="info-card">
                    <h3>Cluster Tinggi</h3>
                    <div class="count">{{ $highClusterCount }} bulan</div>
                    <div class="month-list">
                        @if(isset($final_clusters[1]))
                            @foreach($final_clusters[1] as $key => $item)
                                @if($key !== 'average')
                                <span class="month-tag">{{ $item['month'] }}</span>
                                @endif
                            @endforeach
                        @endif
                    </div>
                </div>
                
                <div class="info-card medium">
                    <h3>Cluster Sedang</h3>
                    <div class="count">{{ $mediumClusterCount }} bulan</div>
                    <div class="month-list">
                        @if(isset($final_clusters[2]))
                            @foreach($final_clusters[2] as $key => $item)
                                @if($key !== 'average')
                                <span class="month-tag">{{ $item['month'] }}</span>
                                @endif
                            @endforeach
                        @endif
                    </div>
                </div>
                
                <div class="info-card low">
                    <h3>Cluster Rendah</h3>
                    <div class="count">{{ $lowClusterCount }} bulan</div>
                    <div class="month-list">
                        @if(isset($final_clusters[3]))
                            @foreach($final_clusters[3] as $key => $item)
                                @if($key !== 'average')
                                <span class="month-tag">{{ $item['month'] }}</span>
                                @endif
                            @endforeach
                        @endif
                    </div>
                </div>
            </div>
        </div>

        <!-- Detail per Cluster -->
        <div class="section">
            <h2>Detail Rata-rata per Cluster</h2>
            <div class="table-container">
                <table>
                    <thead>
                        <tr>
                            <th>Cluster</th>
                            <th>Pencurian dengan Kekerasan</th>
                            <th>Pencurian dengan Pemberatan</th>
                            <th>Pencurian Kendaraan Bermotor</th>
                            <th>Pencurian dengan Aniaya</th>
                            <th>Perjudian</th>
                            <th>Total Rata-rata</th>
                        </tr>
                    </thead>
                    <tbody>
                        @foreach([1 => 'Tinggi', 2 => 'Sedang', 3 => 'Rendah'] as $clusterNum => $clusterLabel)
                            @if(isset($final_clusters[$clusterNum]['average']))
                            @php
                                $average = $final_clusters[$clusterNum]['average'];
                                $totalAvg = array_sum($average);
                                $rowClass = $clusterNum == 1 ? 'cluster-high' : ($clusterNum == 2 ? 'cluster-medium' : 'cluster-low');
                            @endphp
                            <tr class="{{ $rowClass }}">
                                <td><strong>Cluster {{ $clusterNum }} - {{ $clusterLabel }}</strong></td>
                                <td>{{ $average[0] }}</td>
                                <td>{{ $average[1] }}</td>
                                <td>{{ $average[2] }}</td>
                                <td>{{ $average[3] }}</td>
                                <td>{{ $average[4] }}</td>
                                <td><strong>{{ $totalAvg }}</strong></td>
                            </tr>
                            @endif
                        @endforeach
                    </tbody>
                </table>
            </div>
        </div>

        <!-- Detail Perhitungan -->
        @if(isset($iterations) && count($iterations) > 0)
        <div class="section">
            <h2>Detail Perhitungan Algoritma K-Means</h2>
            
            <!-- Iterasi Summary -->
            <div class="subsection">
                <h3>Ringkasan Iterasi</h3>
                @foreach($iterations as $index => $iteration)
                <div class="iteration-summary">
                    <h4>Iterasi {{ $iteration['iteration'] }}</h4>
                    @if(isset($iteration['debug_info']['cluster_counts']))
                    <p><strong>Jumlah Data per Cluster:</strong></p>
                    <ul>
                        <li>Cluster Tinggi: {{ $iteration['debug_info']['cluster_counts'][1] ?? 0 }} data</li>
                        <li>Cluster Sedang: {{ $iteration['debug_info']['cluster_counts'][2] ?? 0 }} data</li>
                        <li>Cluster Rendah: {{ $iteration['debug_info']['cluster_counts'][3] ?? 0 }} data</li>
                    </ul>
                    @endif
                    @if(isset($iteration['debug_info']['assignment_changed']))
                    <p><strong>Assignment Berubah:</strong> {{ $iteration['debug_info']['assignment_changed'] ? 'Ya' : 'Tidak' }}</p>
                    @endif
                    @if(isset($iteration['debug_info']['centroid_change']))
                    <p><strong>Perubahan Centroid:</strong> {{ $iteration['debug_info']['centroid_change'] }}</p>
                    @endif
                </div>
                @endforeach
            </div>

            <!-- Detail Centroid -->
            <div class="subsection">
                <h3>Detail Centroid per Iterasi</h3>
                @foreach($iterations as $index => $iteration)
                <div class="centroid-detail">
                    <h4>Iterasi {{ $iteration['iteration'] }}</h4>
                    @if(isset($iteration['centroids']) && is_array($iteration['centroids']))
                    <div class="table-container">
                        <table>
                            <thead>
                                <tr>
                                    <th>Centroid</th>
                                    <th>Pencurian dengan Kekerasan</th>
                                    <th>Pencurian dengan Pemberatan</th>
                                    <th>Pencurian Kendaraan Bermotor</th>
                                    <th>Pencurian dengan Aniaya</th>
                                    <th>Perjudian</th>
                                    <th>Total</th>
                                </tr>
                            </thead>
                            <tbody>
                                @foreach($iteration['centroids'] as $centroidIndex => $centroid)
                                @php
                                    $labels = ['Tinggi', 'Sedang', 'Rendah'];
                                    $colors = ['cluster-high', 'cluster-medium', 'cluster-low'];
                                    $total = array_sum($centroid);
                                @endphp
                                <tr class="{{ $colors[$centroidIndex] }}">
                                    <td><strong>C{{ $centroidIndex + 1 }} - {{ $labels[$centroidIndex] }}</strong></td>
                                    <td>{{ $centroid[0] ?? 0 }}</td>
                                    <td>{{ $centroid[1] ?? 0 }}</td>
                                    <td>{{ $centroid[2] ?? 0 }}</td>
                                    <td>{{ $centroid[3] ?? 0 }}</td>
                                    <td>{{ $centroid[4] ?? 0 }}</td>
                                    <td><strong>{{ $total }}</strong></td>
                                </tr>
                                @endforeach
                            </tbody>
                        </table>
                    </div>
                    @endif
                </div>
                @endforeach
            </div>

            <!-- Detail Perhitungan Jarak -->
            @if(isset($iterations) && count($iterations) > 1)
            <div class="subsection">
                <h3>Detail Perhitungan Jarak (Iterasi 2+)</h3>
                @foreach($iterations as $index => $iteration)
                @if($iteration['iteration'] > 1 && isset($iteration['data_with_clusters']))
                <div class="distance-calculation">
                    <h4>Iterasi {{ $iteration['iteration'] }} - Perhitungan Jarak Euclidean</h4>
                    <p class="calculation-note">
                        <strong>Formula:</strong> √((x₁-c₁)² + (x₂-c₂)² + (x₃-c₃)² + (x₄-c₄)² + (x₅-c₅)²)
                    </p>
                    <div class="table-container">
                        <table>
                            <thead>
                                <tr>
                                    <th>Data</th>
                                    <th>Bulan</th>
                                    <th>Jarak ke C1</th>
                                    <th>Jarak ke C2</th>
                                    <th>Jarak ke C3</th>
                                    <th>Jarak Min</th>
                                    <th>Cluster</th>
                                </tr>
                            </thead>
                            <tbody>
                                @foreach($iteration['data_with_clusters'] as $item)
                                @php
                                    $clusterColors = [
                                        1 => 'cluster-high',
                                        2 => 'cluster-medium',
                                        3 => 'cluster-low'
                                    ];
                                    $clusterLabels = [1 => 'Tinggi', 2 => 'Sedang', 3 => 'Rendah'];
                                @endphp
                                <tr>
                                    <td>{{ $item['data_index'] }}</td>
                                    <td>{{ $item['month'] }}</td>
                                    <td>{{ $item['distances'][0] ?? 'N/A' }}</td>
                                    <td>{{ $item['distances'][1] ?? 'N/A' }}</td>
                                    <td>{{ $item['distances'][2] ?? 'N/A' }}</td>
                                    <td><strong>{{ min($item['distances']) }}</strong></td>
                                    <td class="{{ $clusterColors[$item['cluster']] }}">
                                        <strong>C{{ $item['cluster'] }} - {{ $clusterLabels[$item['cluster']] }}</strong>
                                    </td>
                                </tr>
                                @endforeach
                            </tbody>
                        </table>
                    </div>
                </div>
                @endif
                @endforeach
            </div>
            @endif

            <!-- Penjelasan Algoritma -->
            <div class="algorithm-explanation">
                <h3>Penjelasan Algoritma K-Means</h3>
                <div class="explanation-content">
                    <h4>Langkah-langkah Algoritma:</h4>
                    <ol>
                        <li><strong>Inisialisasi Centroid:</strong> Pilih 3 data sebagai centroid awal (tertinggi, sedang, terendah)</li>
                        <li><strong>Assignment:</strong> Hitung jarak Euclidean setiap data ke centroid, assign ke cluster terdekat</li>
                        <li><strong>Update Centroid:</strong> Hitung rata-rata data dalam setiap cluster sebagai centroid baru</li>
                        <li><strong>Iterasi:</strong> Ulangi langkah 2-3 sampai konvergen atau mencapai iterasi maksimal</li>
                    </ol>
                    
                    <h4>Kriteria Konvergensi:</h4>
                    <ul>
                        <li>Assignment data tidak berubah antar iterasi</li>
                        <li>Perubahan centroid sangat kecil (< 0.01)</li>
                        <li>Mencapai iterasi maksimal yang ditentukan</li>
                    </ul>
                    
                    <h4>Interpretasi Hasil:</h4>
                    <ul>
                        <li><strong>Cluster Tinggi:</strong> Bulan dengan tingkat kejahatan sangat tinggi, memerlukan perhatian khusus</li>
                        <li><strong>Cluster Sedang:</strong> Bulan dengan tingkat kejahatan moderat, perlu monitoring berkala</li>
                        <li><strong>Cluster Rendah:</strong> Bulan dengan tingkat kejahatan rendah, kondisi keamanan baik</li>
                    </ul>
                </div>
            </div>
        </div>
        @endif

        <!-- Footer -->
        <div class="footer">
            <p>Dokumen ini dibuat secara otomatis oleh sistem Clustering K-Means</p>
            <p>© {{ date('Y') }} - Sistem Analisis Data Kejahatan</p>
        </div>
    </div>
</body>
</html> 