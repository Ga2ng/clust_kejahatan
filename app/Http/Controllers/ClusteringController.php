<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\DataKejahatan;

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
        $centroids = $this->initializeCentroidsFromDataTotals($data, $debugMode);
        
        // Label cluster fix: C1=Tinggi, C2=Sedang, C3=Rendah
        $clusterLabels = [1 => 'Tinggi', 2 => 'Sedang', 3 => 'Rendah'];
        
        $iterations = [];
        $isConverged = false;
        $convergenceIteration = null;
        $clusters = [];
        $previousAssignments = [];
        
        // ALGORITMA K-MEANS YANG BENAR SESUAI EXCEL
        for ($i = 1; $i <= $maxIterasi; $i++) {
            if ($debugMode) {
                error_log("=== ITERASI $i (Tahun $tahun) ===");
                error_log("Centroids saat ini: " . json_encode($centroids));
            }
            
            // STEP 1: Assignment - Hitung jarak dan assign cluster dengan perhitungan yang tepat
            $clusters = $this->performClusterAssignment($data, $centroids, $debugMode);
            
            // Extract current assignments for comparison
            $currentAssignments = array_map(function($cluster) {
                return $cluster['cluster'];
            }, $clusters);
            
            // Check assignment changes dengan toleransi yang tepat
            $assignmentChanged = $this->hasAssignmentChanged($currentAssignments, $previousAssignments);
            
            if ($debugMode) {
                error_log("Assignment changed: " . ($assignmentChanged ? 'YES' : 'NO'));
                error_log("Current assignments: " . json_encode($currentAssignments));
                if (!empty($previousAssignments)) {
                    error_log("Previous assignments: " . json_encode($previousAssignments));
                    $this->logAssignmentChanges($currentAssignments, $previousAssignments, $months);
                }
            }
            
            // Simpan hasil iterasi
            $clusterCounts = $this->getClusterCounts($clusters);
            $iterations[] = [
                'iteration' => $i,
                'centroids' => $centroids,
                'cluster_labels' => $clusterLabels,
                'clusters' => $clusters,
                'data_with_clusters' => $this->getDataWithClusters($data, $clusters, $centroids, $months),
                'summary_clusters' => $this->getSummaryClusters($fullData, $clusters, $clusterLabels),
                'is_first_iteration' => $i === 1,
                'is_final_iteration' => false,
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
                    'assignments' => $currentAssignments
                ]
            ];
            
            // STEP 2: Update Centroids (kecuali iterasi terakhir)
            if ($i < $maxIterasi) {
                $oldCentroids = $centroids;
                $newCentroids = $this->calculateNewCentroids($data, $clusters, $debugMode);
                
                // Calculate centroid change
                $centroidChange = $this->calculateCentroidChangeExact($oldCentroids, $newCentroids);
                $iterations[count($iterations) - 1]['debug_info']['centroid_change'] = $centroidChange;
                
                if ($debugMode) {
                    error_log("Centroid change magnitude: $centroidChange");
                    error_log("Old centroids: " . json_encode($oldCentroids));
                    error_log("New centroids: " . json_encode($newCentroids));
                }
                
                // STEP 3: Check Convergence - Lebih realistis untuk data 12x5
                if ($autoConverge && $i >= 3) { // Mulai cek konvergensi dari iterasi ke-3
                    // Untuk data 12x5, konvergensi biasanya tercapai setelah beberapa iterasi
                    // Convergence: Assignment tidak berubah DAN perubahan centroid sangat kecil
                    $centroidConverged = $centroidChange < 0.001; // Threshold sangat ketat
                    
                    // Hanya konvergen jika KEDUA kondisi terpenuhi
                    if (!$assignmentChanged && $centroidConverged) {
                        $isConverged = true;
                        $convergenceIteration = $i;
                        
                        if ($debugMode) {
                            error_log("KONVERGENSI TERCAPAI pada iterasi $i");
                            error_log("- Assignment tidak berubah: TRUE");
                            error_log("- Centroid converged (< 0.001): TRUE");
                            error_log("- Centroid change: $centroidChange");
                        }
                        
                        // Tandai iterasi terakhir
                        $iterations[count($iterations) - 1]['is_final_iteration'] = true;
                        $iterations[count($iterations) - 1]['convergence_info'] = [
                            'converged' => true,
                            'iteration' => $i,
                            'reason' => 'Assignment stabil DAN perubahan centroid minimal (< 0.001)'
                        ];
                        
                        break;
                    } else {
                        if ($debugMode) {
                            error_log("Belum konvergen pada iterasi $i:");
                            error_log("- Assignment berubah: " . ($assignmentChanged ? 'TRUE' : 'FALSE'));
                            error_log("- Centroid change: $centroidChange (threshold: 0.001)");
                            error_log("- Centroid converged: " . ($centroidConverged ? 'TRUE' : 'FALSE'));
                        }
                    }
                }
                
                // Update centroids untuk iterasi berikutnya
                $centroids = $newCentroids;
                $previousAssignments = $currentAssignments;
                
            } else {
                // Iterasi terakhir (mencapai batas maksimal)
                $iterations[count($iterations) - 1]['is_final_iteration'] = true;
                $iterations[count($iterations) - 1]['convergence_info'] = [
                    'converged' => false,
                    'iteration' => $i,
                    'reason' => 'Mencapai iterasi maksimal tanpa konvergensi'
                ];
                
                if ($debugMode) {
                    error_log("MENCAPAI ITERASI MAKSIMAL ($maxIterasi) tanpa konvergensi");
                }
            }
        }

        return view('clustering.result', [
            'data12bulan' => $dataQuery->toArray(),
            'tahun' => $tahun,
            'months' => $months,
            'data' => $data,
            'fullData' => $fullData,
            'iterations' => $iterations,
            'final_clusters' => $this->getFinalClusters($data, $clusters, $months, $clusterLabels),
            'final_cluster_labels' => $clusterLabels,
            'convergence_info' => [
                'auto_converge_enabled' => $autoConverge,
                'is_converged' => $isConverged,
                'convergence_iteration' => $convergenceIteration,
                'total_iterations' => count($iterations),
                'max_iterations_requested' => $maxIterasi
            ],
            'highClusterCount' => $clusterCounts[1],
            'mediumClusterCount' => $clusterCounts[2],
            'lowClusterCount' => $clusterCounts[3],
            // Debug information fleksibel
            'debug_info' => $debugMode ? [
                'debug_enabled' => true,
                'initial_centroids' => $this->initializeCentroidsFromDataTotals($data, false),
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
                        'assignments' => $iter['debug_info']['assignments'] ?? []
                    ];
                }, $iterations),
                'algorithm_notes' => [
                    'total_data_points' => count($data),
                    'features_per_point' => count($data[0]),
                    'clusters_used' => 3,
                    'convergence_achieved' => $isConverged,
                    'iterations_needed' => count($iterations)
                ]
            ] : null
        ]);
    }

    private function initializeCentroidsFromDataTotals($data, $debugMode)
    {
        // STEP 1: Hitung total untuk setiap data point
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
        
        // STEP 3: Pilih centroid dengan strategi yang lebih baik
        $centroids = [];
        $numData = count($dataTotals);
        
        // Centroid 1 (C1 - Tinggi): Data dengan total TERTINGGI (rank 1)
        $centroids[0] = $dataTotals[0]['data'];
        
        // Centroid 3 (C3 - Rendah): Data dengan total TERENDAH (rank terakhir)
        $centroids[2] = $dataTotals[$numData - 1]['data'];
        
        // Centroid 2 (C2 - Sedang): Data dengan total di kuartil kedua atau ketiga
        // Pilih data yang berada di sekitar 25%-75% dari urutan
        $midRange = intval($numData * 0.4); // Sekitar 40% dari data (lebih representatif)
        $centroids[1] = $dataTotals[$midRange]['data'];
        
        if ($debugMode) {
            error_log("Selected Initial Centroids:");
            error_log("C1 (Tinggi) - Rank 1, Data Index " . ($dataTotals[0]['index'] + 1) . ", Total: " . $dataTotals[0]['total']);
            error_log("  Values: [" . implode(', ', $centroids[0]) . "]");
            error_log("C2 (Sedang) - Rank " . ($midRange + 1) . ", Data Index " . ($dataTotals[$midRange]['index'] + 1) . ", Total: " . $dataTotals[$midRange]['total']);
            error_log("  Values: [" . implode(', ', $centroids[1]) . "]");
            error_log("C3 (Rendah) - Rank $numData, Data Index " . ($dataTotals[$numData - 1]['index'] + 1) . ", Total: " . $dataTotals[$numData - 1]['total']);
            error_log("  Values: [" . implode(', ', $centroids[2]) . "]");
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
        }
        
        foreach ($data as $index => $point) {
            $distances = [];
            $minDistance = PHP_FLOAT_MAX;
            $assignedCluster = 1; // Default cluster
            
            // Hitung jarak ke setiap centroid menggunakan Euclidean Distance
            for ($c = 0; $c < count($centroids); $c++) {
                $distance = $this->euclideanDistanceExact($point, $centroids[$c]);
                $distances[] = $distance;
                
                // Track cluster dengan jarak minimum (dengan toleransi untuk tie-breaking)
                if ($distance < $minDistance) {
                    $minDistance = $distance;
                    $assignedCluster = $c + 1; // Convert to 1-based (C1, C2, C3)
                } elseif (abs($distance - $minDistance) < 0.0001) {
                    // Tie-breaking: pilih cluster dengan index lebih kecil
                    if (($c + 1) < $assignedCluster) {
                        $assignedCluster = $c + 1;
                    }
                }
            }
            
            $clusters[] = [
                'data_index' => $index + 1,
                'distances' => array_map(function($d) { return round($d, 4); }, $distances),
                'cluster' => $assignedCluster,
                'min_distance' => round($minDistance, 4),
                'raw_distances' => $distances // Keep exact distances for debugging
            ];
            
            if ($debugMode && $index < 5) { // Debug first 5 data points
                error_log("Data " . ($index + 1) . " [" . implode(', ', $point) . "] assigned to C$assignedCluster");
                error_log("  Distances: C1=" . round($distances[0], 4) . ", C2=" . round($distances[1], 4) . ", C3=" . round($distances[2], 4));
                error_log("  Min distance: " . round($minDistance, 4));
            }
        }
        
        if ($debugMode) {
            $clusterCounts = $this->getClusterCounts($clusters);
            error_log("Cluster assignment results: C1=" . $clusterCounts[1] . ", C2=" . $clusterCounts[2] . ", C3=" . $clusterCounts[3]);
        }
        
        return $clusters;
    }

    private function calculateNewCentroids($data, $clusters, $debugMode)
    {
        $newCentroids = [];
        $numFeatures = count($data[0]);
        
        if ($debugMode) {
            error_log("Calculating new centroids...");
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
                if (count($clusterData) > 0 && count($clusterData) <= 3) {
                    error_log("  Data points in cluster $cluster:");
                    foreach ($clusterData as $idx => $dataPoint) {
                        error_log("    " . ($idx + 1) . ": [" . implode(', ', $dataPoint) . "]");
                    }
                }
            }
            
            // Hitung centroid baru (rata-rata)
            if (!empty($clusterData)) {
                $centroid = [];
                $numData = count($clusterData);
                
                // Untuk setiap feature (K1, K2, K3, K4, K5)
                for ($feature = 0; $feature < $numFeatures; $feature++) {
                    $sum = 0.0; // Gunakan float untuk presisi
                    foreach ($clusterData as $dataPoint) {
                        $sum += (float)$dataPoint[$feature];
                    }
                    $average = $sum / $numData;
                    $centroid[] = round($average, 1); // Round to 1 decimal place seperti Excel
                }
                $newCentroids[] = $centroid;
                
                if ($debugMode) {
                    error_log("New centroid C$cluster: [" . implode(', ', $centroid) . "]");
                    error_log("  Calculated from " . $numData . " data points");
                }
            } else {
                // Jika cluster kosong (seharusnya tidak terjadi dengan inisialisasi yang benar)
                error_log("WARNING: Cluster $cluster is empty! Using previous centroid or default values");
                
                // Coba gunakan centroid sebelumnya atau buat centroid random yang realistis
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
                    $randomVal = $minValues[$feature] + (rand() / getrandmax()) * ($maxValues[$feature] - $minValues[$feature]);
                    $centroid[] = round($randomVal, 3);
                }
                $newCentroids[] = $centroid;
                
                if ($debugMode) {
                    error_log("Generated random centroid C$cluster: [" . implode(', ', $centroid) . "]");
                }
            }
        }
        
        return $newCentroids;
    }

    private function euclideanDistanceExact($point1, $point2)
    {
        // Implementasi Euclidean Distance yang PERSIS seperti Excel
        // Formula: √((x1-c1)² + (x2-c2)² + (x3-c3)² + (x4-c4)² + (x5-c5)²)
        
        if (count($point1) != count($point2)) {
            throw new \Exception("Point dimensions don't match");
        }
        
        $sumOfSquares = 0;
        for ($i = 0; $i < count($point1); $i++) {
            $diff = $point1[$i] - $point2[$i];
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
        
        // Hitung rata-rata untuk setiap cluster
        foreach ($summaryClusters as $clusterNum => &$clusterData) {
            if (!empty($clusterData)) {
                $sums = [0, 0, 0, 0, 0];
                $count = count($clusterData);
                
                foreach ($clusterData as $item) {
                    $sums[0] += $item['curas'];
                    $sums[1] += $item['curat'];
                    $sums[2] += $item['curanmor'];
                    $sums[3] += $item['anirat'];
                    $sums[4] += $item['judi'];
                }
                
                if ($count > 0) {
                    $clusterData['average'] = [
                        round($sums[0] / $count, 1),
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
        
        // Hitung rata-rata untuk setiap cluster
        foreach ($finalClusters as $clusterNum => &$clusterData) {
            if (!empty($clusterData)) {
                $sums = [0, 0, 0, 0, 0];
                $count = 0;
                
                foreach ($clusterData as $key => $item) {
                    if ($key !== 'average') {
                        for ($i = 0; $i < 5; $i++) {
                            $sums[$i] += $item['data'][$i];
                        }
                        $count++;
                    }
                }
                
                if ($count > 0) {
                    $clusterData['average'] = [
                        round($sums[0] / $count, 1),
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
        $totalChange = 0;
        $maxChange = 0;
        
        for ($i = 0; $i < count($oldCentroids); $i++) {
            for ($j = 0; $j < count($oldCentroids[$i]); $j++) {
                $change = abs($oldCentroids[$i][$j] - $newCentroids[$i][$j]);
                $totalChange += $change;
                $maxChange = max($maxChange, $change);
            }
        }
        
        return round($totalChange, 4);
    }
}