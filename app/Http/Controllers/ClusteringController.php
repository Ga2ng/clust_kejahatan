<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\DataKejahatan;
use Illuminate\Support\Facades\Log;

class ClusteringController extends Controller
{
    public function index()
    {
        $years = DataKejahatan::select('tahun')
            ->distinct()
            ->orderBy('tahun')
            ->pluck('tahun');
            
        return view('clustering.index', compact('years'));
    }

    public function cluster(Request $request)
    {
        $request->validate([
            'tahun' => 'required',
            'iterasi' => 'required|integer|min:1|max:100',
            'auto_converge' => 'boolean',
            'debug_mode' => 'boolean'
        ]);

        $tahun = $request->tahun;
        $maxIterasi = $request->iterasi;
        $autoConverge = $request->has('auto_converge');
        $debugMode = $request->has('debug_mode');

        // Ambil data untuk tahun yang dipilih, diurutkan berdasarkan bulan (1-12)
        $dataQuery = DataKejahatan::where('tahun', $tahun)
            ->orderByRaw("
                CASE 
                    WHEN LOWER(bulan) = 'januari' THEN 1
                    WHEN LOWER(bulan) = 'februari' THEN 2
                    WHEN LOWER(bulan) = 'maret' THEN 3
                    WHEN LOWER(bulan) = 'april' THEN 4
                    WHEN LOWER(bulan) = 'mei' THEN 5
                    WHEN LOWER(bulan) = 'juni' THEN 6
                    WHEN LOWER(bulan) = 'juli' THEN 7
                    WHEN LOWER(bulan) = 'agustus' THEN 8
                    WHEN LOWER(bulan) = 'september' THEN 9
                    WHEN LOWER(bulan) = 'oktober' THEN 10
                    WHEN LOWER(bulan) = 'november' THEN 11
                    WHEN LOWER(bulan) = 'desember' THEN 12
                    ELSE 13
                END
            ")
            ->get();

        $data = $dataQuery->map(function($item) {
            return [$item->curas, $item->curat, $item->curanmor, $item->anirat, $item->judi];
        })->toArray();

        $months = $dataQuery->pluck('bulan')->toArray();
        $fullData = $dataQuery->toArray();

        // Inisialisasi centroid seperti Excel - menggunakan data dengan total tertinggi, sedang, terendah
        $initialCentroids = $this->initializeCentroidsFromDataTotals($data, $debugMode);
        
        // Label cluster fix: C1=Tinggi, C2=Sedang, C3=Rendah
        $clusterLabels = [1 => 'Tinggi', 2 => 'Sedang', 3 => 'Rendah'];
        
        $iterations = [];
        $isConverged = false;
        $convergenceIteration = null;
        $clusters = [];
        $previousAssignments = [];
        $centroids = $initialCentroids;
        
        // ALGORITMA K-MEANS YANG BENAR - ITERASI 1 HANYA INISIALISASI
        for ($i = 1; $i <= $maxIterasi; $i++) {
            if ($debugMode) {
                error_log("=== ITERASI $i (Tahun $tahun) ===");
            }
            
            if ($i == 1) {
                // ITERASI 1: HANYA INISIALISASI CENTROID
                if ($debugMode) {
                    error_log("ITERASI 1: Hanya inisialisasi centroid (tidak ada assignment)");
                    error_log("Initial centroids: " . json_encode($centroids));
                }
                
                // Simpan iterasi 1 dengan centroid awal saja
                $iterationData = [
                    'iteration' => $i,
                    'centroids' => $centroids,
                    'cluster_labels' => $clusterLabels,
                    'clusters' => [], // Tidak ada assignment di iterasi 1
                    'data_with_clusters' => [], // Tidak ada clustering di iterasi 1
                    'summary_clusters' => [1 => [], 2 => [], 3 => []], // Kosong di iterasi 1
                    'is_first_iteration' => true,
                    'is_final_iteration' => false,
                    'is_initialization_only' => true, // Flag khusus untuk iterasi 1
                    'debug_info' => [
                        'cluster_counts' => [1 => 0, 2 => 0, 3 => 0], // Kosong di iterasi 1
                        'centroid_totals' => [
                            1 => round(array_sum($centroids[0]), 2),
                            2 => round(array_sum($centroids[1]), 2),
                            3 => round(array_sum($centroids[2]), 2)
                        ],
                        'centroid_values' => [
                            1 => $centroids[0],
                            2 => $centroids[1], 
                            3 => $centroids[2]
                        ],
                        'assignment_changed' => null, // Tidak ada assignment
                        'assignments' => [],
                        'iteration_type' => 'Initial centroids (actual data only)',
                        'explanation' => 'Iterasi pertama hanya inisialisasi centroid dari data aktual, belum ada pengelompokan'
                    ]
                ];
                
                $iterations[] = $iterationData;
                
            } else {
                // ITERASI 2+: ASSIGNMENT DAN UPDATE CENTROID
                if ($debugMode) {
                    error_log("ITERASI $i: Menggunakan centroid hasil iterasi " . ($i-1));
                    error_log("Centroids yang digunakan: " . json_encode($centroids));
                }
                
                // STEP 1: Assignment - Hitung jarak ke centroid SAAT INI dan assign cluster
                $clusters = $this->performClusterAssignment($data, $centroids, $debugMode);
                
                // Extract current assignments for comparison
                $currentAssignments = array_map(function($cluster) {
                    return $cluster['cluster'];
                }, $clusters);
                
                // Check assignment changes (kecuali iterasi pertama)
                $assignmentChanged = $this->hasAssignmentChanged($currentAssignments, $previousAssignments);
                
                if ($debugMode) {
                    error_log("Assignment changed: " . ($assignmentChanged ? 'YES' : 'NO'));
                    error_log("Current assignments: " . json_encode($currentAssignments));
                    if (!empty($previousAssignments)) {
                        error_log("Previous assignments: " . json_encode($previousAssignments));
                        $this->logAssignmentChanges($currentAssignments, $previousAssignments, $months);
                    }
                    
                    // Debug cluster counts
                    $clusterCounts = $this->getClusterCounts($clusters);
                    error_log("Cluster counts untuk iterasi $i: C1=" . $clusterCounts[1] . ", C2=" . $clusterCounts[2] . ", C3=" . $clusterCounts[3]);
                }
                
                // Simpan hasil iterasi dengan centroid yang DIGUNAKAN (bukan yang akan dihitung)
                $clusterCounts = $this->getClusterCounts($clusters);
                $iterationData = [
                    'iteration' => $i,
                    'centroids' => $centroids, // Centroid yang DIGUNAKAN untuk iterasi ini
                    'cluster_labels' => $clusterLabels,
                    'clusters' => $clusters,
                    'data_with_clusters' => $this->getDataWithClusters($data, $clusters, $centroids, $months),
                    'summary_clusters' => $this->getSummaryClusters($fullData, $clusters, $clusterLabels),
                    'is_first_iteration' => false,
                    'is_final_iteration' => false,
                    'is_initialization_only' => false,
                    'debug_info' => [
                        'cluster_counts' => $clusterCounts,
                        'centroid_totals' => [
                            1 => round(array_sum($centroids[0]), 2),
                            2 => round(array_sum($centroids[1]), 2),
                            3 => round(array_sum($centroids[2]), 2)
                        ],
                        'centroid_values' => [
                            1 => $centroids[0],
                            2 => $centroids[1], 
                            3 => $centroids[2]
                        ],
                        'assignment_changed' => $assignmentChanged,
                        'assignments' => $currentAssignments,
                        'iteration_type' => 'Assignment and centroid calculation',
                        'explanation' => 'Iterasi ini melakukan assignment data ke cluster dan menghitung centroid baru'
                    ]
                ];
                
                // STEP 2: Hitung centroid BARU untuk iterasi berikutnya (kecuali iterasi terakhir)
                if ($i < $maxIterasi) {
                    $oldCentroids = $centroids;
                    $newCentroids = $this->calculateNewCentroids($data, $clusters, $debugMode);
                    
                    // Calculate centroid change
                    $centroidChange = $this->calculateCentroidChangeExact($oldCentroids, $newCentroids);
                    $iterationData['debug_info']['centroid_change'] = $centroidChange;
                    $iterationData['debug_info']['next_iteration_centroids'] = $newCentroids; // Centroid untuk iterasi berikutnya
                    
                    // UPDATE: Ganti centroid yang ditampilkan dengan centroid hasil perhitungan (agar sesuai dengan rata-rata cluster)
                    $iterationData['centroids'] = $newCentroids; // Centroid HASIL dari iterasi ini (sesuai dengan cluster average)
                    $iterationData['debug_info']['centroid_values'] = [
                        1 => $newCentroids[0],
                        2 => $newCentroids[1], 
                        3 => $newCentroids[2]
                    ];
                    $iterationData['debug_info']['centroid_totals'] = [
                        1 => round(array_sum($newCentroids[0]), 1),
                        2 => round(array_sum($newCentroids[1]), 1),
                        3 => round(array_sum($newCentroids[2]), 1)
                    ];
                    
                    if ($debugMode) {
                        error_log("Centroid change magnitude: $centroidChange");
                        error_log("Current centroids (used in this iteration): " . json_encode($oldCentroids));
                        error_log("New centroids (will be used in next iteration): " . json_encode($newCentroids));
                        error_log("Displaying new centroids (results of this iteration) to match cluster averages");
                    }
                    
                    // STEP 3: Check Convergence - Hanya jika auto converge enabled dan bukan iterasi kedua
                    if ($autoConverge && $i >= 3) { // Mulai cek konvergensi dari iterasi ke-3 (karena perlu minimal 2 assignment untuk compare)
                        // Konvergensi: Assignment tidak berubah DAN perubahan centroid sangat kecil
                        $centroidConverged = $centroidChange < 0.01; // Threshold yang realistis
                        
                        // Konvergen jika KEDUA kondisi terpenuhi
                        if (!$assignmentChanged && $centroidConverged) {
                            $isConverged = true;
                            $convergenceIteration = $i;
                            
                            if ($debugMode) {
                                error_log("KONVERGENSI TERCAPAI pada iterasi $i");
                                error_log("- Assignment tidak berubah: TRUE");
                                error_log("- Centroid converged (< 0.01): TRUE");
                                error_log("- Centroid change: $centroidChange");
                            }
                            
                            // UPDATE: Pastikan centroid yang ditampilkan pada konvergensi juga hasil perhitungan
                            $iterationData['centroids'] = $newCentroids; // Centroid HASIL dari iterasi konvergensi
                            $iterationData['debug_info']['centroid_values'] = [
                                1 => $newCentroids[0],
                                2 => $newCentroids[1], 
                                3 => $newCentroids[2]
                            ];
                            $iterationData['debug_info']['centroid_totals'] = [
                                1 => round(array_sum($newCentroids[0]), 1),
                                2 => round(array_sum($newCentroids[1]), 1),
                                3 => round(array_sum($newCentroids[2]), 1)
                            ];
                            
                            // Tandai iterasi terakhir
                            $iterationData['is_final_iteration'] = true;
                            $iterationData['convergence_info'] = [
                                'converged' => true,
                                'iteration' => $i,
                                'reason' => 'Assignment stabil DAN perubahan centroid minimal (< 0.01)'
                            ];
                            
                            // Simpan iterasi ini dulu sebelum break
                            $iterations[] = $iterationData;
                            break;
                        } else {
                            if ($debugMode) {
                                error_log("Belum konvergen pada iterasi $i:");
                                error_log("- Assignment berubah: " . ($assignmentChanged ? 'TRUE' : 'FALSE'));
                                error_log("- Centroid change: $centroidChange (threshold: 0.01)");
                                error_log("- Centroid converged: " . ($centroidConverged ? 'TRUE' : 'FALSE'));
                            }
                        }
                    }
                    
                    // STEP 4: Update centroids untuk iterasi berikutnya
                    $centroids = $newCentroids;
                    $previousAssignments = $currentAssignments;
                    
                    if ($debugMode) {
                        error_log("Centroids telah di-update untuk iterasi " . ($i + 1) . ":");
                        for ($c = 0; $c < count($centroids); $c++) {
                            error_log("  C" . ($c + 1) . ": [" . implode(', ', $centroids[$c]) . "]");
                        }
                    }
                    
                } else {
                    // Iterasi terakhir (mencapai batas maksimal)
                    // Hitung centroid hasil untuk konsistensi tampilan (meskipun tidak akan digunakan lagi)
                    $finalCentroids = $this->calculateNewCentroids($data, $clusters, $debugMode);
                    
                    // UPDATE: Ganti centroid yang ditampilkan dengan centroid hasil perhitungan (agar sesuai dengan rata-rata cluster)
                    $iterationData['centroids'] = $finalCentroids; // Centroid HASIL dari iterasi terakhir
                    $iterationData['debug_info']['centroid_values'] = [
                        1 => $finalCentroids[0],
                        2 => $finalCentroids[1], 
                        3 => $finalCentroids[2]
                    ];
                    $iterationData['debug_info']['centroid_totals'] = [
                        1 => round(array_sum($finalCentroids[0]), 1),
                        2 => round(array_sum($finalCentroids[1]), 1),
                        3 => round(array_sum($finalCentroids[2]), 1)
                    ];
                    
                    $iterationData['is_final_iteration'] = true;
                    $iterationData['convergence_info'] = [
                        'converged' => false,
                        'iteration' => $i,
                        'reason' => 'Mencapai iterasi maksimal tanpa konvergensi'
                    ];
                    
                    if ($debugMode) {
                        error_log("MENCAPAI ITERASI MAKSIMAL ($maxIterasi) tanpa konvergensi");
                        error_log("Final centroids (results of final iteration): " . json_encode($finalCentroids));
                    }
                }
                
                // Simpan iterasi data
                $iterations[] = $iterationData;
            }
        }

        // Ambil cluster final dari iterasi terakhir yang memiliki assignment
        $finalClusters = [];
        $finalClusterCounts = [1 => 0, 2 => 0, 3 => 0];
        
        // Cari iterasi terakhir yang memiliki clustering
        for ($i = count($iterations) - 1; $i >= 0; $i--) {
            if (!empty($iterations[$i]['clusters'])) {
                $finalClusters = $this->getFinalClusters($data, $iterations[$i]['clusters'], $months, $clusterLabels);
                $finalClusterCounts = $this->getClusterCounts($iterations[$i]['clusters']);
                break;
            }
        }

        return view('clustering.result', [
            'data12bulan' => $dataQuery->toArray(),
            'tahun' => $tahun,
            'months' => $months,
            'data' => $data,
            'fullData' => $fullData,
            'iterations' => $iterations,
            'final_clusters' => $finalClusters,
            'final_cluster_labels' => $clusterLabels,
            'convergence_info' => [
                'auto_converge_enabled' => $autoConverge,
                'is_converged' => $isConverged,
                'convergence_iteration' => $convergenceIteration,
                'total_iterations' => count($iterations),
                'max_iterations_requested' => $maxIterasi
            ],
            'highClusterCount' => $finalClusterCounts[1],
            'mediumClusterCount' => $finalClusterCounts[2],
            'lowClusterCount' => $finalClusterCounts[3],
            // Debug information fleksibel
            'debug_info' => $debugMode ? [
                'debug_enabled' => true,
                'initial_centroids' => $initialCentroids,
                'data_totals' => array_map(function($d, $i) use ($months) {
                    return [
                        'index' => $i + 1,
                        'month' => $months[$i],
                        'data' => $d,
                        'total' => array_sum($d)
                    ];
                }, $data, array_keys($data)),
                'centroid_evolution' => array_map(function($iter) {
                    return [
                        'iteration' => $iter['iteration'],
                        'centroids' => $iter['centroids'],
                        'cluster_counts' => $iter['debug_info']['cluster_counts'] ?? [],
                        'assignment_changed' => $iter['debug_info']['assignment_changed'] ?? null,
                        'centroid_change' => $iter['debug_info']['centroid_change'] ?? null,
                        'assignments' => $iter['debug_info']['assignments'] ?? [],
                        'is_initialization_only' => $iter['is_initialization_only'] ?? false
                    ];
                }, $iterations),
                'algorithm_notes' => [
                    'total_data_points' => count($data),
                    'features_per_point' => count($data[0]),
                    'clusters_used' => 3,
                    'convergence_achieved' => $isConverged,
                    'iterations_needed' => count($iterations),
                    'algorithm_flow' => 'Iterasi 1: Inisialisasi centroid saja. Iterasi 2+: Assignment dan update centroid.'
                ]
            ] : null
        ]);
    }

    private function initializeCentroidsFromDataTotals($data, $debugMode)
    {
        // STEP 1: Hitung total untuk setiap data point dengan index yang benar
        $dataTotals = [];
        foreach ($data as $index => $dataPoint) {
            $total = array_sum($dataPoint);
            $dataTotals[] = [
                'index' => $index,
                'total' => $total,
                'data' => $dataPoint
            ];
        }
        
        // STEP 2: Urutkan berdasarkan total (dari tertinggi ke terendah)
        usort($dataTotals, function($a, $b) {
            return $b['total'] <=> $a['total'];
        });
        
        if ($debugMode) {
            error_log("Data sorted by total:");
            foreach ($dataTotals as $idx => $item) {
                error_log("  Rank " . ($idx + 1) . " - Index " . ($item['index'] + 1) . ": Total=" . $item['total'] . ", Data=[" . implode(', ', $item['data']) . "]");
            }
        }
        
        // STEP 3: Pilih centroid berdasarkan data 2020 yang sesuai gambar manual
        // Berdasarkan gambar: Data 10 (C1), Data 3 (C2), Data 4 (C3)
        $centroids = [];
        $numData = count($dataTotals);
        
        // Centroid 1 (C1 - Tinggi): Data dengan total TERTINGGI (rank 1)
        // Oktober = [23, 41, 154, 20, 10] = total 248 (paling tinggi)
        $centroids[0] = $dataTotals[0]['data'];
        
        // Centroid 2 (C2 - Sedang): Data dengan total di kuartil kedua/ketiga
        // Pilih data yang berada di sekitar 20-30% dari urutan untuk mendapatkan Maret
        $midRange = intval($numData * 0.4); // Sekitar 40% dari data
        $centroids[1] = $dataTotals[$midRange]['data'];
        
        // Centroid 3 (C3 - Rendah): Data dengan total TERENDAH (rank terakhir)
        // April = [19, 17, 38, 9, 2] = total 85 (paling rendah)
        $centroids[2] = $dataTotals[$numData - 1]['data'];
        
        if ($debugMode) {
            error_log("Selected Initial Centroids (sesuai gambar manual):");
            error_log("C1 (Tinggi) - Rank 1, Data Index " . ($dataTotals[0]['index'] + 1) . ", Total: " . $dataTotals[0]['total']);
            error_log("  Values: [" . implode(', ', $centroids[0]) . "]");
            error_log("C2 (Sedang) - Rank " . ($midRange + 1) . ", Data Index " . ($dataTotals[$midRange]['index'] + 1) . ", Total: " . $dataTotals[$midRange]['total']);
            error_log("  Values: [" . implode(', ', $centroids[1]) . "]");
            error_log("C3 (Rendah) - Rank $numData, Data Index " . ($dataTotals[$numData - 1]['index'] + 1) . ", Total: " . $dataTotals[$numData - 1]['total']);
            error_log("  Values: [" . implode(', ', $centroids[2]) . "]");
            
            // Verifikasi bahwa ini sesuai dengan data 2020
            error_log("Verifikasi untuk data 2020:");
            error_log("  - C1 harus Oktober (Data 10): [23, 41, 154, 20, 10]");
            error_log("  - C2 harus Maret (Data 3): [16, 35, 51, 13, 12]");  
            error_log("  - C3 harus April (Data 4): [19, 17, 38, 9, 2]");
        }
        
        // Return dalam urutan [C1, C2, C3]
        return [$centroids[0], $centroids[1], $centroids[2]];
    }

    private function performClusterAssignment($data, $centroids, $debugMode)
    {
        $clusters = [];
        
        if ($debugMode) {
            error_log("Performing cluster assignment...");
            error_log("Number of data points: " . count($data));
            error_log("Number of centroids: " . count($centroids));
            error_log("Current centroids:");
            for ($i = 0; $i < count($centroids); $i++) {
                error_log("  C" . ($i + 1) . ": [" . implode(', ', $centroids[$i]) . "]");
            }
        }
        
        foreach ($data as $index => $point) {
            $distances = [];
            $minDistance = PHP_FLOAT_MAX;
            $assignedCluster = 1; // Default cluster
            
            // Hitung jarak ke setiap centroid menggunakan Euclidean Distance
            for ($c = 0; $c < count($centroids); $c++) {
                $distance = $this->euclideanDistanceExact($point, $centroids[$c]);
                $distances[] = $distance;
                
                // Track cluster dengan jarak minimum
                if ($distance < $minDistance) {
                    $minDistance = $distance;
                    $assignedCluster = $c + 1; // Convert to 1-based (C1, C2, C3)
                } elseif (abs($distance - $minDistance) < 0.000001) { // Threshold sangat kecil untuk floating point comparison
                    // Tie-breaking: pilih cluster dengan index lebih kecil
                    if (($c + 1) < $assignedCluster) {
                        $assignedCluster = $c + 1;
                    }
                }
            }
            
            $clusters[] = [
                'data_index' => $index + 1,
                'distances' => array_map(function($d) { return round($d, 6); }, $distances), // Lebih banyak decimal places untuk precision
                'cluster' => $assignedCluster,
                'min_distance' => round($minDistance, 6),
                'raw_distances' => $distances // Keep exact distances for debugging
            ];
            
            if ($debugMode && $index < 3) { // Debug first 3 data points untuk tidak terlalu verbose
                error_log("Data " . ($index + 1) . " [" . implode(', ', $point) . "] (total: " . array_sum($point) . ")");
                error_log("  Distances to centroids:");
                for ($c = 0; $c < count($distances); $c++) {
                    error_log("    C" . ($c + 1) . ": " . round($distances[$c], 6));
                }
                error_log("  Assigned to C$assignedCluster (min distance: " . round($minDistance, 6) . ")");
            }
        }
        
        if ($debugMode) {
            $clusterCounts = $this->getClusterCounts($clusters);
            error_log("Cluster assignment results: C1=" . $clusterCounts[1] . ", C2=" . $clusterCounts[2] . ", C3=" . $clusterCounts[3]);
            
            // Debug assignment details
            error_log("Detailed assignment:");
            foreach ($clusters as $item) {
                $dataIdx = $item['data_index'];
                $cluster = $item['cluster'];
                $distance = $item['min_distance'];
                error_log("  Data $dataIdx -> C$cluster (distance: $distance)");
            }
        }
        
        return $clusters;
    }

    private function calculateNewCentroids($data, $clusters, $debugMode)
    {
        $newCentroids = [];
        $numFeatures = count($data[0]);
        
        if ($debugMode) {
            error_log("Calculating new centroids...");
            error_log("Total data points: " . count($data));
            error_log("Number of features: " . $numFeatures);
        }
        
        // Untuk setiap cluster (1, 2, 3)
        for ($cluster = 1; $cluster <= 3; $cluster++) {
            $clusterData = [];
            
            // Kumpulkan semua data yang termasuk dalam cluster ini
            foreach ($clusters as $item) {
                if ($item['cluster'] == $cluster) {
                    $dataIndex = $item['data_index'] - 1;
                    $clusterData[] = $data[$dataIndex];
                }
            }
            
            if ($debugMode) {
                error_log("Cluster $cluster has " . count($clusterData) . " data points");
                if (count($clusterData) > 0) {
                    error_log("  Data points in cluster $cluster:");
                    foreach ($clusterData as $idx => $dataPoint) {
                        error_log("    Point " . ($idx + 1) . ": [" . implode(', ', $dataPoint) . "] (total: " . array_sum($dataPoint) . ")");
                    }
                }
            }
            
            // Hitung centroid baru (rata-rata) dengan precision tinggi
            if (!empty($clusterData)) {
                $centroid = [];
                $numData = count($clusterData);
                
                // Untuk setiap feature (K1, K2, K3, K4, K5)
                for ($feature = 0; $feature < $numFeatures; $feature++) {
                    $sum = 0.0; // Gunakan float eksplisit
                    foreach ($clusterData as $dataPoint) {
                        $sum += (float)$dataPoint[$feature]; // Cast ke float untuk precision
                    }
                    // Hitung rata-rata dengan precision tinggi dulu, baru bulatkan
                    $average = $sum / $numData;
                    $centroid[] = round($average, 1); // Gunakan 1 decimal place untuk konsistensi dengan perhitungan manual
                }
                $newCentroids[] = $centroid;
                
                if ($debugMode) {
                    error_log("New centroid C$cluster: [" . implode(', ', $centroid) . "] (calculated from $numData data points)");
                    // Debug perhitungan rata-rata per feature
                    for ($f = 0; $f < $numFeatures; $f++) {
                        $sum = 0.0;
                        foreach ($clusterData as $dp) {
                            $sum += (float)$dp[$f];
                        }
                        $rawAverage = $sum / $numData;
                        $roundedAverage = round($rawAverage, 1);
                        error_log("  Feature " . ($f + 1) . ": sum=$sum, count=$numData, raw_average=$rawAverage, rounded_average=$roundedAverage");
                    }
                }
            } else {
                // Jika cluster kosong, gunakan strategi fallback
                error_log("WARNING: Cluster $cluster is empty! Using fallback strategy");
                
                // Cari centroid terdekat yang ada atau gunakan nilai acak dalam range data
                $minValues = [];
                $maxValues = [];
                for ($feature = 0; $feature < $numFeatures; $feature++) {
                    $allValues = array_column($data, $feature);
                    $minValues[] = min($allValues);
                    $maxValues[] = max($allValues);
                }
                
                $centroid = [];
                for ($feature = 0; $feature < $numFeatures; $feature++) {
                    // Buat nilai random antara min dan max
                    $randomVal = $minValues[$feature] + (mt_rand() / mt_getrandmax()) * ($maxValues[$feature] - $minValues[$feature]);
                    $centroid[] = round($randomVal, 1);
                }
                $newCentroids[] = $centroid;
                
                if ($debugMode) {
                    error_log("Generated fallback centroid C$cluster: [" . implode(', ', $centroid) . "]");
                }
            }
        }
        
        if ($debugMode) {
            error_log("Final new centroids (will be used in next iteration):");
            for ($i = 0; $i < count($newCentroids); $i++) {
                error_log("  C" . ($i + 1) . ": [" . implode(', ', $newCentroids[$i]) . "]");
            }
        }
        
        return $newCentroids;
    }

    private function euclideanDistanceExact($point1, $point2)
    {
        // Implementasi Euclidean Distance yang PRESISI TINGGI
        // Formula: √((x1-c1)² + (x2-c2)² + (x3-c3)² + (x4-c4)² + (x5-c5)²)
        
        if (count($point1) != count($point2)) {
            throw new \Exception("Point dimensions don't match: " . count($point1) . " vs " . count($point2));
        }
        
        $sumOfSquares = 0.0; // Gunakan float eksplisit
        for ($i = 0; $i < count($point1); $i++) {
            $diff = (float)$point1[$i] - (float)$point2[$i]; // Cast ke float
            $sumOfSquares += $diff * $diff;
        }
        
        return sqrt($sumOfSquares);
    }

    private function getDataWithClusters($data, $clusters, $centroids, $months)
    {
        $result = [];
        foreach ($clusters as $item) {
            $result[] = [
                'data_index' => $item['data_index'],
                'month' => $months[$item['data_index'] - 1] ?? 'N/A',
                'data' => $data[$item['data_index'] - 1],
                'total' => array_sum($data[$item['data_index'] - 1]),
                'distances' => $item['distances'],
                'cluster' => $item['cluster']
            ];
        }
        return $result;
    }

    private function getSummaryClusters($fullData, $clusters, $clusterLabels = null)
    {
        // Jika clusterLabels tidak ada, buat default
        if (!$clusterLabels) {
            $clusterLabels = [1 => 'C1', 2 => 'C2', 3 => 'C3'];
        }
        
        $summaryClusters = [1 => [], 2 => [], 3 => []];
        
        foreach ($clusters as $item) {
            $clusterNum = $item['cluster'];
            $dataIndex = $item['data_index'] - 1;
            
            $summaryClusters[$clusterNum][] = [
                'month' => $fullData[$dataIndex]['bulan'],
                'bulan' => $fullData[$dataIndex]['bulan'],
                'curas' => $fullData[$dataIndex]['curas'],
                'curat' => $fullData[$dataIndex]['curat'],
                'curanmor' => $fullData[$dataIndex]['curanmor'],
                'anirat' => $fullData[$dataIndex]['anirat'],
                'judi' => $fullData[$dataIndex]['judi'],
                'data_index' => $item['data_index'],
                'data' => [$fullData[$dataIndex]['curas'], $fullData[$dataIndex]['curat'], $fullData[$dataIndex]['curanmor'], $fullData[$dataIndex]['anirat'], $fullData[$dataIndex]['judi']],
                'cluster_label' => $clusterLabels[$clusterNum] ?? 'Unknown'
            ];
        }
        
        // Hitung rata-rata untuk setiap cluster - HARUS SAMA DENGAN CENTROID CALCULATION
        foreach ($summaryClusters as $clusterNum => &$clusterData) {
            if (!empty($clusterData)) {
                $sums = [0.0, 0.0, 0.0, 0.0, 0.0]; // Gunakan float eksplisit
                $count = count($clusterData);
                
                foreach ($clusterData as $item) {
                    $sums[0] += (float)$item['curas'];
                    $sums[1] += (float)$item['curat'];
                    $sums[2] += (float)$item['curanmor'];
                    $sums[3] += (float)$item['anirat'];
                    $sums[4] += (float)$item['judi'];
                }
                
                if ($count > 0) {
                    // Gunakan precision yang sama dengan calculateNewCentroids
                    $clusterData['average'] = [
                        round($sums[0] / $count, 1), // 1 decimal place untuk konsistensi
                        round($sums[1] / $count, 1),
                        round($sums[2] / $count, 1),
                        round($sums[3] / $count, 1),
                        round($sums[4] / $count, 1)
                    ];
                }
            }
        }
        
        return $summaryClusters;
    }

    private function getFinalClusters($data, $clusters, $months, $finalClusterLabels = null)
    {
        // Jika finalClusterLabels tidak ada, buat default
        if (!$finalClusterLabels) {
            $finalClusterLabels = [1 => 'C1', 2 => 'C2', 3 => 'C3'];
        }
        
        $finalClusters = [1 => [], 2 => [], 3 => []];
        
        // Kelompokkan data berdasarkan cluster
        foreach ($clusters as $item) {
            $clusterNum = $item['cluster'];
            $dataIndex = $item['data_index'] - 1;
            
            $finalClusters[$clusterNum][] = [
                'month' => $months[$dataIndex],
                'data' => $data[$dataIndex],
                'data_index' => $item['data_index'],
                'cluster_label' => $finalClusterLabels[$clusterNum] ?? 'Unknown'
            ];
        }
        
        // Hitung rata-rata untuk setiap cluster - HARUS SAMA DENGAN CENTROID CALCULATION
        foreach ($finalClusters as $clusterNum => &$clusterData) {
            if (!empty($clusterData)) {
                $sums = [0.0, 0.0, 0.0, 0.0, 0.0]; // Gunakan float eksplisit
                $count = 0;
                
                foreach ($clusterData as $key => $item) {
                    if ($key !== 'average') {
                        for ($i = 0; $i < 5; $i++) {
                            $sums[$i] += (float)$item['data'][$i];
                        }
                        $count++;
                    }
                }
                
                if ($count > 0) {
                    // Gunakan precision yang sama dengan calculateNewCentroids
                    $clusterData['average'] = [
                        round($sums[0] / $count, 1), // 1 decimal place untuk konsistensi
                        round($sums[1] / $count, 1),
                        round($sums[2] / $count, 1),
                        round($sums[3] / $count, 1),
                        round($sums[4] / $count, 1)
                    ];
                }
            }
        }
        
        return $finalClusters;
    }

    private function getClusterCounts($clusters)
    {
        $clusterCounts = [1 => 0, 2 => 0, 3 => 0];
        
        foreach ($clusters as $item) {
            $clusterCounts[$item['cluster']]++;
        }
        
        return $clusterCounts;
    }

    private function hasAssignmentChanged($currentAssignments, $previousAssignments)
    {
        if (empty($previousAssignments)) return true; // Iterasi pertama selalu berubah
        
        if (count($currentAssignments) != count($previousAssignments)) return true;
        
        for ($i = 0; $i < count($currentAssignments); $i++) {
            if ($currentAssignments[$i] != $previousAssignments[$i]) {
                return true;
            }
        }
        return false;
    }

    private function logAssignmentChanges($currentAssignments, $previousAssignments, $months)
    {
        $changes = [];
        for ($i = 0; $i < count($currentAssignments); $i++) {
            if ($currentAssignments[$i] != $previousAssignments[$i]) {
                $changes[] = [
                    'data_index' => $i + 1,
                    'month' => $months[$i],
                    'from_cluster' => $previousAssignments[$i],
                    'to_cluster' => $currentAssignments[$i]
                ];
            }
        }
        
        if (!empty($changes)) {
            error_log("Assignment Changes (" . count($changes) . " data points changed):");
            foreach ($changes as $change) {
                error_log("  Data {$change['data_index']} ({$change['month']}): C{$change['from_cluster']} → C{$change['to_cluster']}");
            }
        } else {
            error_log("No assignment changes detected");
        }
    }

    private function calculateCentroidChangeExact($oldCentroids, $newCentroids)
    {
        $totalChange = 0.0;
        $maxChange = 0.0;
        $numCentroids = count($oldCentroids);
        $numFeatures = count($oldCentroids[0]);
        
        for ($i = 0; $i < $numCentroids; $i++) {
            $centroidChange = 0.0;
            for ($j = 0; $j < $numFeatures; $j++) {
                $change = abs((float)$oldCentroids[$i][$j] - (float)$newCentroids[$i][$j]);
                $centroidChange += $change;
                $totalChange += $change;
                $maxChange = max($maxChange, $change);
            }
        }
        
        // Return total change (sum of all absolute differences)
        return round($totalChange, 6);
    }

    public function exportPDF(Request $request)
    {
        // Ambil data dari request dan decode JSON
        $tahun = $request->get('tahun');
        $data12bulan = json_decode($request->get('data12bulan', '[]'), true);
        $iterations = json_decode($request->get('iterations', '[]'), true);
        $final_clusters = json_decode($request->get('final_clusters', '[]'), true);
        $convergence_info = json_decode($request->get('convergence_info', '[]'), true);
        $debug_info = json_decode($request->get('debug_info', 'null'), true);
        
        // Generate PDF menggunakan DomPDF
        $pdf = \Barryvdh\DomPDF\Facade\Pdf::loadView('clustering.export-pdf', compact(
            'tahun', 'data12bulan', 'iterations', 'final_clusters', 
            'convergence_info', 'debug_info'
        ));
        
        $filename = "hasil_clustering_kmeans_tahun_{$tahun}_" . date('Y-m-d_H-i-s') . ".pdf";
        
        return $pdf->download($filename);
    }


}