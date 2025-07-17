<x-app-layout>
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <x-slot name="header">
        <h2 class="font-semibold text-xl text-gray-800 leading-tight">
            {{ __('Data Kejahatan') }}
        </h2>
    </x-slot>

    <div class="py-12">
        <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">
            <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg">
                <div class="p-6 text-gray-900">
                    <!-- Header Section -->
                    <div class="mb-6">
                        <h3 class="text-lg font-semibold text-gray-800 mb-2">Data Tindak Kriminalitas</h3>
                        <p class="text-gray-600">Data statistik kejahatan berdasarkan tahun dan bulan dengan kategori tindak kriminalitas.</p>
                    </div>

                    <!-- Add Data Button -->
                    <div class="mb-6">
                        <button onclick="openModal('add')" class="bg-blue-500 hover:bg-blue-700 text-white font-bold py-2 px-4 rounded">
                            Tambah Data
                        </button>
                    </div>

                    <!-- Filter Section -->
                    <div class="mb-6 flex flex-wrap gap-4">
                        <div>
                            <label for="year-filter" class="block text-sm font-medium text-gray-700 mb-1">Filter Tahun:</label>
                            <select id="year-filter" class="appearance-none border border-gray-300 rounded-md px-3 py-2 focus:outline-none focus:ring-2 focus:ring-blue-500 bg-white pr-8">
                                <option value="">Semua Tahun</option>
                                @foreach($years as $year)
                                    <option value="{{ $year }}">{{ $year }}</option>
                                @endforeach
                            </select>
                        </div>
                        <div>
                            <label for="crime-filter" class="block text-sm font-medium text-gray-700 mb-1">Filter Jenis Kejahatan:</label>
                            <select id="crime-filter" class="appearance-none border border-gray-300 rounded-md px-3 py-2 focus:outline-none focus:ring-2 focus:ring-blue-500 bg-white pr-8">
                                <option value="">Semua Jenis</option>
                                <option value="curas">Pencurian dengan Kekerasan</option>
                                <option value="curat">Pencurian dengan Pemberatan</option>
                                <option value="curanmor">Pencurian Kendaraan Bermotor</option>
                                <option value="anirat">Pencurian dengan Aniaya</option>
                                <option value="judi">Perjudian</option>
                            </select>
                        </div>
                    </div>

                    <!-- Statistics Cards -->
                    <div class="grid grid-cols-1 md:grid-cols-5 gap-4 mb-6">
                        <div class="bg-red-50 border border-red-200 rounded-lg p-4">
                            <h4 class="text-sm font-medium text-red-600 mb-1">Pencurian dengan Kekerasan</h4>
                            <p class="text-2xl font-bold text-red-700" id="total-curas">{{ number_format($totals['curas']) }}</p>
                            <p class="text-xs text-red-500">Total Kasus</p>
                            <p class="text-xs text-gray-500 mt-1">(Curas)</p>
                        </div>
                        <div class="bg-orange-50 border border-orange-200 rounded-lg p-4">
                            <h4 class="text-sm font-medium text-orange-600 mb-1">Pencurian dengan Pemberatan</h4>
                            <p class="text-2xl font-bold text-orange-700" id="total-curat">{{ number_format($totals['curat']) }}</p>
                            <p class="text-xs text-orange-500">Total Kasus</p>
                            <p class="text-xs text-gray-500 mt-1">(Curat)</p>
                        </div>
                        <div class="bg-yellow-50 border border-yellow-200 rounded-lg p-4">
                            <h4 class="text-sm font-medium text-yellow-600 mb-1">Pencurian Kendaraan Bermotor</h4>
                            <p class="text-2xl font-bold text-yellow-700" id="total-curanmor">{{ number_format($totals['curanmor']) }}</p>
                            <p class="text-xs text-yellow-500">Total Kasus</p>
                            <p class="text-xs text-gray-500 mt-1">(Curanmor)</p>
                        </div>
                        <div class="bg-blue-50 border border-blue-200 rounded-lg p-4">
                            <h4 class="text-sm font-medium text-blue-600 mb-1">Pencurian dengan Aniaya</h4>
                            <p class="text-2xl font-bold text-blue-700" id="total-anirat">{{ number_format($totals['anirat']) }}</p>
                            <p class="text-xs text-blue-500">Total Kasus</p>
                            <p class="text-xs text-gray-500 mt-1">(Anirat)</p>
                        </div>
                        <div class="bg-purple-50 border border-purple-200 rounded-lg p-4">
                            <h4 class="text-sm font-medium text-purple-600 mb-1">Perjudian</h4>
                            <p class="text-2xl font-bold text-purple-700" id="total-judi">{{ number_format($totals['judi']) }}</p>
                            <p class="text-xs text-purple-500">Total Kasus</p>
                            <p class="text-xs text-gray-500 mt-1">(Judi)</p>
                        </div>
                    </div>

                    <!-- Data Table -->
                    <div class="overflow-x-auto">
                        <table class="min-w-full divide-y divide-gray-200" id="crime-table">
                            <thead class="bg-gray-50">
                                <tr>
                                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Tahun</th>
                                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Bulan</th>
                                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Pencurian dengan Kekerasan</th>
                                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Pencurian dengan Pemberatan</th>
                                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Pencurian Kendaraan Bermotor</th>
                                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Pencurian dengan Aniaya</th>
                                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Perjudian</th>
                                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Total</th>
                                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Aksi</th>
                                </tr>
                            </thead>
                            <tbody class="bg-white divide-y divide-gray-200" id="table-body">
                                <!-- Data akan diisi via AJAX -->
                            </tbody>
                        </table>
                    </div>

                    <!-- Export Section -->
                    <div class="mt-6 flex justify-between items-center">
                        <div class="text-sm text-gray-600">
                            Menampilkan <span id="showing-count">0</span> data dari total <span id="total-count">{{ $totalData }}</span> entri
                        </div>
                        <div class="flex gap-2">
                            <button class="bg-green-600 hover:bg-green-700 text-white px-4 py-2 rounded-md text-sm font-medium transition duration-150 ease-in-out">
                                Export Excel
                            </button>
                            <button class="bg-red-600 hover:bg-red-700 text-white px-4 py-2 rounded-md text-sm font-medium transition duration-150 ease-in-out">
                                Export PDF
                            </button>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Modal CRUD -->
    <div id="crud-modal" class="hidden fixed inset-0 bg-gray-600 bg-opacity-50 overflow-y-auto h-full w-full">
        <div class="relative top-20 mx-auto p-5 border w-11/12 md:w-1/2 lg:w-1/3 shadow-lg rounded-md bg-white">
            <div class="flex justify-between items-center mb-4">
                <h3 class="text-lg font-semibold text-gray-800" id="modal-title">Tambah Data Kejahatan</h3>
                <button onclick="closeModal()" class="text-gray-400 hover:text-gray-500">
                    <svg class="h-6 w-6" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12" />
                    </svg>
                </button>
            </div>
            
            <form action="{{ route('dashboard.store') }}" method="POST" id="data-form">
                @csrf
                <input type="hidden" id="data-id">
                
                <div class="mb-4">
                    <label for="tahun" class="block text-sm font-medium text-gray-700 mb-1">Tahun</label>
                    <input type="number" id="tahun" name="tahun" class="w-full px-3 py-2 border border-gray-300 rounded-md focus:outline-none focus:ring-2 focus:ring-blue-500" required>
                </div>
                
                <div class="mb-4">
                    <label for="bulan" class="block text-sm font-medium text-gray-700 mb-1">Bulan</label>
                    <select id="bulan" name="bulan" class="w-full px-3 py-2 border border-gray-300 rounded-md focus:outline-none focus:ring-2 focus:ring-blue-500" required>
                        <option value="">Pilih Bulan</option>
                        <option value="Januari">Januari</option>
                        <option value="Februari">Februari</option>
                        <option value="Maret">Maret</option>
                        <option value="April">April</option>
                        <option value="Mei">Mei</option>
                        <option value="Juni">Juni</option>
                        <option value="Juli">Juli</option>
                        <option value="Agustus">Agustus</option>
                        <option value="September">September</option>
                        <option value="Oktober">Oktober</option>
                        <option value="November">November</option>
                        <option value="Desember">Desember</option>
                    </select>
                </div>
                
                <div class="mb-4">
                    <label for="curas" class="block text-sm font-medium text-gray-700 mb-1">Pencurian dengan Kekerasan</label>
                    <input type="number" id="curas" name="curas" min="0" class="w-full px-3 py-2 border border-gray-300 rounded-md focus:outline-none focus:ring-2 focus:ring-blue-500" required>
                </div>
                
                <div class="mb-4">
                    <label for="curat" class="block text-sm font-medium text-gray-700 mb-1">Pencurian dengan Pemberatan</label>
                    <input type="number" id="curat" name="curat" min="0" class="w-full px-3 py-2 border border-gray-300 rounded-md focus:outline-none focus:ring-2 focus:ring-blue-500" required>
                </div>
                
                <div class="mb-4">
                    <label for="curanmor" class="block text-sm font-medium text-gray-700 mb-1">Pencurian Kendaraan Bermotor</label>
                    <input type="number" id="curanmor" name="curanmor" min="0" class="w-full px-3 py-2 border border-gray-300 rounded-md focus:outline-none focus:ring-2 focus:ring-blue-500" required>
                </div>
                
                <div class="mb-4">
                    <label for="anirat" class="block text-sm font-medium text-gray-700 mb-1">Pencurian dengan Aniaya</label>
                    <input type="number" id="anirat" name="anirat" min="0" class="w-full px-3 py-2 border border-gray-300 rounded-md focus:outline-none focus:ring-2 focus:ring-blue-500" required>
                </div>
                
                <div class="mb-4">
                    <label for="judi" class="block text-sm font-medium text-gray-700 mb-1">Perjudian</label>
                    <input type="number" id="judi" name="judi" min="0" class="w-full px-3 py-2 border border-gray-300 rounded-md focus:outline-none focus:ring-2 focus:ring-blue-500" required>
                </div>
                
                <div class="flex justify-end gap-3 mt-6">
                    <button type="button" onclick="closeModal()" class="px-4 py-2 border border-gray-300 rounded-md text-sm font-medium text-gray-700 hover:bg-gray-50">
                        Batal
                    </button>
                    <button type="submit" class="px-4 py-2 bg-blue-600 text-white rounded-md text-sm font-medium hover:bg-blue-700">
                        Simpan
                    </button>
                </div>
            </form>
        </div>
    </div>

    <!-- Delete Confirmation Modal -->
    <div id="delete-modal" class="hidden fixed inset-0 bg-gray-600 bg-opacity-50 overflow-y-auto h-full w-full">
        <div class="relative top-20 mx-auto p-5 border w-11/12 md:w-1/3 shadow-lg rounded-md bg-white">
            <div class="flex justify-between items-center mb-4">
                <h3 class="text-lg font-semibold text-gray-800">Konfirmasi Hapus Data</h3>
                <button onclick="closeDeleteModal()" class="text-gray-400 hover:text-gray-500">
                    <svg class="h-6 w-6" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12" />
                    </svg>
                </button>
            </div>
            
            <p class="mb-6">Apakah Anda yakin ingin menghapus data ini? Data yang sudah dihapus tidak dapat dikembalikan.</p>
            
            <div class="flex justify-end gap-3">
                <button onclick="closeDeleteModal()" class="px-4 py-2 border border-gray-300 rounded-md text-sm font-medium text-gray-700 hover:bg-gray-50">
                    Batal
                </button>
                <button id="confirm-delete" class="px-4 py-2 bg-red-600 text-white rounded-md text-sm font-medium hover:bg-red-700">
                    Hapus
                </button>
            </div>
        </div>
    </div>

    <script>
    // Variabel global
    let currentAction = '';
    let currentId = '';

    // Fungsi untuk memuat data
    function loadData() {
        const year = $('#year-filter').val();
        
        $.get('/dashboard/data', { year: year }, function(data) {
            const tableBody = $('#table-body');
            tableBody.empty();
            
            let showingCount = 0;
            
            data.forEach((item, index) => {
                const total = item.curas + item.curat + item.curanmor + item.anirat + item.judi;
                
                const row = `
                    <tr class="hover:bg-gray-50 ${index % 2 === 0 ? 'bg-white' : 'bg-gray-50'}" 
                        data-year="${item.tahun}" 
                        data-curas="${item.curas}" 
                        data-curat="${item.curat}" 
                        data-curanmor="${item.curanmor}" 
                        data-anirat="${item.anirat}" 
                        data-judi="${item.judi}">
                        <td class="px-6 py-4 whitespace-nowrap text-sm font-medium text-gray-900">${item.tahun}</td>
                        <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-900">${item.bulan}</td>
                        <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-900">
                            <span class="bg-red-100 text-red-800 px-2 py-1 rounded-full text-xs font-medium">${item.curas}</span>
                        </td>
                        <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-900">
                            <span class="bg-orange-100 text-orange-800 px-2 py-1 rounded-full text-xs font-medium">${item.curat}</span>
                        </td>
                        <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-900">
                            <span class="bg-yellow-100 text-yellow-800 px-2 py-1 rounded-full text-xs font-medium">${item.curanmor}</span>
                        </td>
                        <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-900">
                            <span class="bg-blue-100 text-blue-800 px-2 py-1 rounded-full text-xs font-medium">${item.anirat}</span>
                        </td>
                        <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-900">
                            <span class="bg-purple-100 text-purple-800 px-2 py-1 rounded-full text-xs font-medium">${item.judi}</span>
                        </td>
                        <td class="px-6 py-4 whitespace-nowrap text-sm font-medium text-gray-900">
                            <span class="bg-gray-100 text-gray-800 px-2 py-1 rounded-full text-xs font-medium">${total}</span>
                        </td>
                        <td class="px-6 py-4 whitespace-nowrap text-sm font-medium text-gray-900">
                            <button onclick="openModal('edit', ${item.id})" class="text-blue-600 hover:text-blue-900 mr-2">Edit</button>
                            <button onclick="openDeleteModal(${item.id})" class="text-red-600 hover:text-red-900">Hapus</button>
                        </td>
                    </tr>
                `;
                
                tableBody.append(row);
                showingCount++;
            });
            
            $('#showing-count').text(showingCount);
            updateStatistics();
        });
    }

    // Fungsi untuk membuka modal
    function openModal(action, id = null) {
        currentAction = action;
        currentId = id;
        
        const modal = $('#crud-modal');
        const form = $('#data-form')[0];
        
        if (action === 'add') {
            $('#modal-title').text('Tambah Data Kejahatan');
            form.reset();
            $('#data-id').val('');
        } else if (action === 'edit' && id) {
            $('#modal-title').text('Edit Data Kejahatan');
            
            // Ambil data dari server
            $.ajax({
                url: "/dashboard/show/" + id,
                type: 'GET',
                success: function(data) {
                    $('#data-id').val(data.id);
                    $('#tahun').val(data.tahun);
                    $('#bulan').val(data.bulan);
                    $('#curas').val(data.curas);
                    $('#curat').val(data.curat);
                    $('#curanmor').val(data.curanmor);
                    $('#anirat').val(data.anirat);
                    $('#judi').val(data.judi);
                },
                error: function(xhr, status, error) {
                    console.error('Error fetching data:', error);
                    alert('Gagal mengambil data untuk diedit. Silakan coba lagi.');
                }
            });
        }
        
        modal.removeClass('hidden');
    }

    // Fungsi untuk menutup modal
    function closeModal() {
        $('#crud-modal').addClass('hidden');
    }

    // Fungsi untuk membuka modal hapus
    function openDeleteModal(id) {
        currentId = id;
        $('#delete-modal').removeClass('hidden');
    }

    // Fungsi untuk menutup modal hapus
    function closeDeleteModal() {
        $('#delete-modal').addClass('hidden');
    }

    // Fungsi untuk memperbarui statistik
    function updateStatistics() {
        const visibleRows = $('#table-body tr').filter(function() {
            return $(this).css('display') !== 'none';
        });
        
        let totalCuras = 0, totalCurat = 0, totalCuranmor = 0, totalAnirat = 0, totalJudi = 0;

        visibleRows.each(function() {
            const row = $(this);
            totalCuras += parseInt(row.data('curas'));
            totalCurat += parseInt(row.data('curat'));
            totalCuranmor += parseInt(row.data('curanmor'));
            totalAnirat += parseInt(row.data('anirat'));
            totalJudi += parseInt(row.data('judi'));
        });

        $('#total-curas').text(totalCuras.toLocaleString());
        $('#total-curat').text(totalCurat.toLocaleString());
        $('#total-curanmor').text(totalCuranmor.toLocaleString());
        $('#total-anirat').text(totalAnirat.toLocaleString());
        $('#total-judi').text(totalJudi.toLocaleString());
    }

    // Event saat dokumen siap
    $(document).ready(function() {
        // Muat data awal
        loadData();

        // Filter tahun
        $('#year-filter').on('change', function() {
            loadData();
        });

        // Filter jenis kejahatan (hanya untuk highlight)
        $('#crime-filter').on('change', function() {
            const crimeType = $(this).val();
            
            $('#table-body tr').each(function() {
                const row = $(this);
                if (crimeType && row.data(crimeType)) {
                    // Reset semua highlight
                    row.find('span').removeClass('bg-yellow-200');
                    
                    // Highlight kolom yang sesuai
                    if (crimeType === 'curas') {
                        row.find('td:nth-child(3) span').addClass('bg-yellow-200');
                    } else if (crimeType === 'curat') {
                        row.find('td:nth-child(4) span').addClass('bg-yellow-200');
                    } else if (crimeType === 'curanmor') {
                        row.find('td:nth-child(5) span').addClass('bg-yellow-200');
                    } else if (crimeType === 'anirat') {
                        row.find('td:nth-child(6) span').addClass('bg-yellow-200');
                    } else if (crimeType === 'judi') {
                        row.find('td:nth-child(7) span').addClass('bg-yellow-200');
                    }
                } else {
                    row.find('span').removeClass('bg-yellow-200');
                }
            });
        });

        // Submit form
        $('#data-form').on('submit', function(e) {
            e.preventDefault();

            const formData = $(this).serialize();
            const url = currentAction === 'add' ? "{{ route('dashboard.store') }}" : `/dashboard/${currentId}`;
            const method = currentAction === 'add' ? 'POST' : 'PUT';
            
            $.ajax({
                url: url,
                type: method,
                data: formData,
                headers: {
                    'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
                },
                success: function(response) {
                    closeModal();
                    loadData();
                    alert(response.success);
                },
                error: function(xhr) {
                    if (xhr.status === 422) {
                        console.log('Validation Error:', xhr.responseJSON.errors);
                        alert('Data tidak valid. Periksa input Anda.');
                    } else if (xhr.status === 409) {
                        alert(xhr.responseJSON.error);
                    } else {
                        console.error(xhr.responseText);
                        alert('Terjadi kesalahan. Silakan coba lagi.');
                    }
                }
            });
        });

        // Konfirmasi hapus
        $('#confirm-delete').on('click', function() {
            $.ajax({
                url: `/dashboard/${currentId}`,
                type: 'DELETE',
                headers: {
                    'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
                },
                success: function(response) {
                    closeDeleteModal();
                    loadData();
                    alert(response.success);
                },
                error: function() {
                    alert('Terjadi kesalahan. Silakan coba lagi.');
                }
            });
        });
    });
    </script>
</x-app-layout>