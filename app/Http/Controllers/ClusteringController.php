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
            'iterasi' => 'required|integer|min:1',
            'auto_converge' => 'boolean'
        ]);

        $tahun = $request->tahun;
        $maxIterasi = $request->iterasi;
        $autoConverge = $request->has('auto_converge');

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
        
        // Ambil data lengkap untuk summary cards
        $fullData = $dataQuery->toArray();

        // Hitung jumlah data untuk setiap kategori (tinggi, sedang, rendah)
        $totalData = count($data);
        $highCount = ceil($totalData * 0.2);    // 20% data tertinggi
        $mediumCount = ceil($totalData * 0.3);  // 30% data sedang
        $lowCount = $totalData - $highCount - $mediumCount; // 50% data terendah
        
        // Inisialisasi centroid berdasarkan jumlah data
        $centroids = $this->initializeCentroids($data, $highCount, $mediumCount, $lowCount);
        // dd($data);
        
        $iterations = [];
        $isConverged = false;
        $convergenceIteration = null;
        
        for ($i = 1; $i <= $maxIterasi; $i++) {
            // Hitung jarak dan assign cluster
            $clusters = $this->assignClusters($data, $centroids);
            
            // Simpan hasil iterasi dengan data lengkap
            $iterations[] = [
                'iteration' => $i,
                'centroids' => $centroids,
                'clusters' => $clusters,
                'data_with_clusters' => $this->getDataWithClusters($data, $clusters, $centroids, $months),
                'summary_clusters' => $this->getSummaryClusters($fullData, $clusters),
                'is_first_iteration' => $i === 1,
                'is_final_iteration' => false  // akan di-update nanti
            ];
            
            // Update centroid untuk iterasi berikutnya (kecuali iterasi terakhir)
            if ($i < $maxIterasi) {
                $newCentroids = $this->updateCentroids($data, $clusters);
                
                // Check convergence jika auto_converge aktif (mulai dari iterasi ke-2)
                if ($autoConverge && $i >= 2 && $this->hasConverged($centroids, $newCentroids)) {
                    $isConverged = true;
                    $convergenceIteration = $i;
                    
                    // Tandai iterasi terakhir
                    $iterations[count($iterations) - 1]['is_final_iteration'] = true;
                    $iterations[count($iterations) - 1]['convergence_info'] = [
                        'converged' => true,
                        'iteration' => $i,
                        'reason' => 'Auto-convergence tercapai'
                    ];
                    
                    break;
                }
                
                $centroids = $newCentroids;
            } else {
                // Tandai iterasi terakhir jika mencapai maksimal iterasi
                $iterations[count($iterations) - 1]['is_final_iteration'] = true;
                $iterations[count($iterations) - 1]['convergence_info'] = [
                    'converged' => false,
                    'iteration' => $i,
                    'reason' => 'Mencapai iterasi maksimal'
                ];
            }
        }

        return view('clustering.result', [
            'data12bulan' => $dataQuery->toArray(),
            'tahun' => $tahun,
            'months' => $months,
            'data' => $data,
            'fullData' => $fullData,
            'iterations' => $iterations,
            'final_clusters' => $this->getFinalClusters($data, $clusters, $months),
            'convergence_info' => [
                'auto_converge_enabled' => $autoConverge,
                'is_converged' => $isConverged,
                'convergence_iteration' => $convergenceIteration,
                'total_iterations' => count($iterations),
                'max_iterations_requested' => $maxIterasi
            ]
        ]);
    }

    private function initializeCentroids($data, $highCount, $mediumCount, $lowCount)
    {
        // Hitung total untuk setiap data point
        $dataTotals = [];
        foreach ($data as $index => $dataPoint) {
            $total = array_sum($dataPoint);
            $dataTotals[] = [
                'index' => $index,
                'total' => $total,
                'data' => $dataPoint
            ];
        }
        
        // Urutkan berdasarkan total (dari tertinggi ke terendah)
        usort($dataTotals, function($a, $b) {
            return $b['total'] <=> $a['total'];
        });
        
        // Ambil centroid berdasarkan jumlah data
        $centroids = [];
        
        // Centroid tinggi (data awal)
        if ($highCount > 0 && isset($dataTotals[0])) {
            $centroids[] = $dataTotals[0]['data'];
        }
        
        // Centroid sedang (data tengah)
        $midIndex = min(floor(count($dataTotals)/2), count($dataTotals)-1);
        if ($mediumCount > 0 && isset($dataTotals[$midIndex])) {
            $centroids[] = $dataTotals[$midIndex]['data'];
        }
        
        // Centroid rendah (data akhir)
        if ($lowCount > 0 && isset($dataTotals[count($dataTotals)-1])) {
            $centroids[] = $dataTotals[count($dataTotals)-1]['data'];
        }
        
        // Jika kurang dari 3 centroid, isi dengan data yang ada
        while (count($centroids) < 3) {
            $centroids[] = $dataTotals[0]['data'];
        }
        
        return $centroids;
    }

    private function assignClusters($data, $centroids)
    {
        $clusters = [];
        
        foreach ($data as $index => $point) {
            $distances = [];
            
            // Hitung jarak ke setiap centroid
            foreach ($centroids as $centroidIndex => $centroid) {
                $distance = $this->euclideanDistance($point, $centroid);
                $distances[] = round($distance, 4);
            }
            
            // Assign ke cluster dengan jarak terdekat
            $minDistance = min($distances);
            $clusterIndex = array_search($minDistance, $distances);
            
            $clusters[] = [
                'data_index' => $index + 1,
                'distances' => $distances,
                'cluster' => $clusterIndex + 1,
                'min_distance' => $minDistance
            ];
        }
        
        return $clusters;
    }

    private function updateCentroids($data, $clusters)
    {
        $newCentroids = [[], [], []];
        $numFeatures = count($data[0]);
        
        // Untuk setiap cluster
        for ($cluster = 1; $cluster <= 3; $cluster++) {
            $clusterData = [];
            
            // Ambil data yang termasuk dalam cluster ini
            foreach ($clusters as $item) {
                if ($item['cluster'] == $cluster) {
                    $clusterData[] = $data[$item['data_index'] - 1];
                }
            }
            
            // Hitung rata-rata untuk setiap feature
            if (!empty($clusterData)) {
                for ($feature = 0; $feature < $numFeatures; $feature++) {
                    $sum = array_sum(array_column($clusterData, $feature));
                    $newCentroids[$cluster - 1][] = round($sum / count($clusterData), 1);
                }
            } else {
                // Jika cluster kosong, gunakan centroid lama
                for ($feature = 0; $feature < $numFeatures; $feature++) {
                    $newCentroids[$cluster - 1][] = 0;
                }
            }
        }
        
        return $newCentroids;
    }

    private function euclideanDistance($point1, $point2)
    {
        $sum = 0;
        for ($i = 0; $i < count($point1); $i++) {
            $sum += pow($point1[$i] - $point2[$i], 2);
        }
        return sqrt($sum);
    }

    private function hasConverged($oldCentroids, $newCentroids, $threshold = 0.001)
    {
        $maxChange = 0;
        $totalChanges = 0;
        $changeCount = 0;
        
        for ($i = 0; $i < count($oldCentroids); $i++) {
            for ($j = 0; $j < count($oldCentroids[$i]); $j++) {
                $change = abs($oldCentroids[$i][$j] - $newCentroids[$i][$j]);
                $maxChange = max($maxChange, $change);
                $totalChanges += $change;
                $changeCount++;
                
                // Jika ada perubahan yang melebihi threshold, belum konvergen
                if ($change > $threshold) {
                    return false;
                }
            }
        }
        
        // Tambahan: rata-rata perubahan juga harus kecil (untuk memastikan stabilitas keseluruhan)
        $avgChange = $totalChanges / $changeCount;
        
        // Log informasi konvergensi (optional, bisa dihapus jika tidak perlu)
        // \Log::info("Convergence check: Max change = {$maxChange}, Avg change = {$avgChange}, Threshold = {$threshold}");
        
        return true;
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

    private function getSummaryClusters($fullData, $clusters)
    {
        $summaryClusters = [1 => [], 2 => [], 3 => []];
        
        foreach ($clusters as $item) {
            $clusterNum = $item['cluster'];
            $dataIndex = $item['data_index'] - 1;
            
            $summaryClusters[$clusterNum][] = [
                'bulan' => $fullData[$dataIndex]['bulan'],
                'curas' => $fullData[$dataIndex]['curas'],
                'curat' => $fullData[$dataIndex]['curat'],
                'curanmor' => $fullData[$dataIndex]['curanmor'],
                'anirat' => $fullData[$dataIndex]['anirat'],
                'judi' => $fullData[$dataIndex]['judi'],
                'data_index' => $item['data_index']
            ];
        }
        
        return $summaryClusters;
    }

    private function getFinalClusters($data, $clusters, $months)
    {
        $finalClusters = [1 => [], 2 => [], 3 => []];
        
        // Kelompokkan data berdasarkan cluster
        foreach ($clusters as $item) {
            $clusterNum = $item['cluster'];
            $dataIndex = $item['data_index'] - 1;
            
            $finalClusters[$clusterNum][] = [
                'month' => $months[$dataIndex],
                'data' => $data[$dataIndex],
                'data_index' => $item['data_index']
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
}