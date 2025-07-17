<?php

namespace App\Http\Controllers;

use App\Models\DataKejahatan;
use Illuminate\Http\Request;

class DashboardController extends Controller
{
    public function index()
    {
        $years = DataKejahatan::select('tahun')->distinct()->orderBy('tahun')->pluck('tahun');
        $totalData = DataKejahatan::count();
        
        // Hitung total untuk setiap jenis kejahatan
        $totals = [
            'curas' => DataKejahatan::sum('curas'),
            'curat' => DataKejahatan::sum('curat'),
            'curanmor' => DataKejahatan::sum('curanmor'),
            'anirat' => DataKejahatan::sum('anirat'),
            'judi' => DataKejahatan::sum('judi'),
        ];

        return view('dashboard', compact('years', 'totals', 'totalData'));
    }

    public function getData(Request $request)
    {
        $query = DataKejahatan::query();

        if ($request->has('year') && $request->year != '') {
            $query->where('tahun', $request->year);
        }

        $data = $query->orderBy('tahun')->orderByRaw("FIELD(bulan, 'Januari', 'Februari', 'Maret', 'April', 'Mei', 'Juni', 'Juli', 'Agustus', 'September', 'Oktober', 'November', 'Desember')")->get();

        return response()->json($data);
    }

    public function show($id)
    {
        $data = DataKejahatan::findOrFail($id);
        return response()->json($data);
    }

    public function store(Request $request)
    {
        // Validasi data
        // dd($request->all());
        $validated = $request->validate([
            'tahun'     => 'required|string:4',
            'bulan'     => 'required|string|max:10',
            'curas'     => 'required|integer|min:0',
            'curat'     => 'required|integer|min:0',
            'curanmor'  => 'required|integer|min:0',
            'anirat'    => 'required|integer|min:0',
            'judi'      => 'required|integer|min:0',
        ]);
    
        // Cek apakah data untuk tahun dan bulan tersebut sudah ada
        $existing = DataKejahatan::where('tahun', $validated['tahun'])
                                  ->where('bulan', $validated['bulan'])
                                  ->first();
    
        if ($existing) {
            return response()->json([
                'error' => 'Data untuk bulan dan tahun tersebut sudah ada'
            ], 409); // Conflict
        }
    
        // Simpan data
        // $data = DataKejahatan::create($validated);
        $data = DataKejahatan::create([
          'tahun'     => $request->tahun,
          'bulan'     => $request->bulan,
          'curas'     => $request->curas,
          'curat'     => $request->curat,
          'curanmor'  => $request->curanmor,
          'anirat'    => $request->anirat,
          'judi'      => $request->judi,
      ]);
      
    
        // Respon sukses
        return response()->json([
            'success' => 'Data berhasil ditambahkan',
            'data' => $data
        ]);
    }

    public function update(Request $request, $id)
    {
        $request->validate([
            'tahun' => 'required|digits:4',
            'bulan' => 'required|string',
            'curas' => 'required|integer|min:0',
            'curat' => 'required|integer|min:0',
            'curanmor' => 'required|integer|min:0',
            'anirat' => 'required|integer|min:0',
            'judi' => 'required|integer|min:0',
        ]);

        $data = DataKejahatan::findOrFail($id);
        $data->update($request->all());

        return response()->json(['success' => 'Data berhasil diperbarui', 'data' => $data]);
    }

    public function destroy($id)
    {
        $data = DataKejahatan::findOrFail($id);
        $data->delete();

        return response()->json(['success' => 'Data berhasil dihapus']);
    }
}