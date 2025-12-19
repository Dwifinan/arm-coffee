
@extends('components.layout')

{{-- Pastikan layout Anda mengimpor JQuery, DataTables, SweetAlert2, dan Bootstrap 5 Modal --}}

@section('content')
<div class="pagetitle">
    <h1>Produksi</h1>
    <nav>
        <ol class="breadcrumb">
            <li class="breadcrumb-item"><a href="{{ url('/') }}">Home</a></li>
            <li class="breadcrumb-item active">Produksi</li>
        </ol>
    </nav>
</div>

<section class="section">
    <div class="card shadow mb-4">
        <div class="card-header py-3 d-flex justify-content-between align-items-center">
            <h5 class="m-0">Daftar Produksi</h5>
            {{-- Tombol untuk memicu modal input --}}
            <button class="btn btn-primary" data-bs-toggle="modal" data-bs-target="#addProduksiModal">
                <i class="bi bi-plus-circle"></i> Catat Produksi Baru
            </button>
        </div>
        <div class="card-body">

            <div class="table-responsive">
                <table id="penjualanAsProduksiTable" class="table table-bordered table-striped" width="100%" cellspacing="0">
                    <thead>
                        <tr>
                            <th>ID Transaksi</th>
                            <th>Tanggal Produksi</th>
                            <th>Menu Produk</th>
                            <th>Jumlah Diproduksi (Terjual)</th>
                            <th>Total Pendapatan</th>
                        </tr>
                    </thead>
                    <tbody>
                        {{-- Data akan dimuat oleh DataTables melalui AJAX --}}
                    </tbody>
                </table>
            </div>
        </div>
    </div>
</section>


{{-- ====================================================================== --}}
{{-- MODAL UNTUK MENAMBAH PRODUKSI/PENJUALAN BARU --}}
{{-- ====================================================================== --}}
<div class="modal fade" id="addProduksiModal" tabindex="-1" aria-labelledby="addProduksiModalLabel" aria-hidden="true">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title" id="addProduksiModalLabel">Catat Penjualan/Produksi Baru</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <form id="addProduksiForm">
                @csrf
                <div class="modal-body">

                    <div class="mb-3">
                        <label for="menu_select" class="form-label">Menu Produk</label>
                        <select class="form-select select2" id="menu_select" name="menu_id" required>
                            <option value="">-- Pilih Menu --</option>
                            {{-- Opsi akan diisi oleh JavaScript --}}
                        </select>
                    </div>

                    <div class="mb-3">
                        <label for="jumlah_input" class="form-label">Jumlah Produksi (Terjual)</label>
                        <input type="number" class="form-control" id="jumlah_input" name="jumlah" min="1" required>
                    </div>

                    <div class="mb-3">
                        <label for="total_harga_display" class="form-label">Total Harga (Otomatis)</label>
                        <input type="text" class="form-control" id="total_harga_display" readonly>
                        {{-- Hidden field untuk mengirim nilai total harga --}}
                        <input type="hidden" name="total_harga" id="total_harga_hidden">
                    </div>

                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Tutup</button>
                    <button type="submit" class="btn btn-primary" id="saveProduksiBtn">Catat & Simpan</button>
                </div>
            </form>
        </div>
    </div>
</div>

@endsection

{{-- --- --}}

@section('scriptjs')
<script>
    // Menyimpan daftar menu dan harganya
    let menuData = [];

    $(document).ready(function() {

        // --- 1. INISIALISASI DATATABLES ---
        const table = $('#penjualanAsProduksiTable').DataTable({
            "processing": false,
            "serverSide": false,
            "ajax": {
                // Endpoint API yang mengambil data PENJUALAN yang sudah di-JOIN dengan MENU
                "url": "{{ url('api/penjualan-produksi') }}",
                "type": "GET",
                "dataSrc": "data",
                "error": function(xhr, error, code) {
                    Swal.fire({ icon: 'error', title: 'Gagal Memuat Data', text: 'Terjadi kesalahan saat mengambil data laporan: ' + code });
                }
            },

            "columns": [
                { "data": "kode_transaksi", "title": "ID Transaksi", "defaultContent": "-",
                    "render": function(data, type, row) {
                        return data ? data : 'TRX-' + row.id;
                    }
                },
                { "data": "created_at", "title": "Tanggal Produksi",
                    "render": function(data) {
                        return data ? new Date(data).toLocaleString('id-ID', { day: 'numeric', month: 'long', year: 'numeric' }) : '';
                    }
                },
                { "data": "menu_nama", "title": "Menu Produk" },
                { "data": "jumlah", "title": "Jumlah Diproduksi (Terjual)" },
                {
                    "data": "total_harga", "title": "Total Pendapatan",
                    "render": function(data) {
                        return 'Rp ' + parseFloat(data).toLocaleString('id-ID');
                    }
                }
            ],
            "dom": 'lBfrtip',
        });


        // --- 2. FUNGSI UNTUK MENGISI DROPDOWN MENU ---
        function fetchMenuList() {
            $.ajax({
                url: "{{ url('api/menu-list') }}", // Anda harus membuat endpoint ini
                method: 'GET',
                success: function(response) {
                    menuData = response.data; // Simpan data menu
                    let options = '<option value="">-- Pilih Menu --</option>';
                    menuData.forEach(menu => {
                        // Tambahkan data harga ke opsi untuk memudahkan perhitungan
                        options += `<option value="${menu.id}" data-harga="${menu.harga}">${menu.nama} (Rp ${parseFloat(menu.harga).toLocaleString('id-ID')})</option>`;
                    });
                    $('#menu_select').html(options);
                },
                error: function() {
                    console.error('Gagal mengambil daftar menu.');
                }
            });
        }

        // Panggil fungsi saat dokumen siap
        fetchMenuList();


        // --- 3. LOGIKA PERHITUNGAN HARGA OTOMATIS ---
        function calculateTotalPrice() {
            const selectedOption = $('#menu_select option:selected');
            const hargaSatuan = parseFloat(selectedOption.data('harga')) || 0;
            const jumlah = parseInt($('#jumlah_input').val()) || 0;
            const total = hargaSatuan * jumlah;

            $('#total_harga_hidden').val(total);
            $('#total_harga_display').val('Rp ' + total.toLocaleString('id-ID'));
        }

        // Panggil perhitungan saat menu atau jumlah berubah
        $('#menu_select, #jumlah_input').on('change keyup', calculateTotalPrice);


        // --- 4. SUBMIT FORM (CATAT PRODUKSI) ---
        $('#addProduksiForm').on('submit', function(e) {
            e.preventDefault();

            const formData = $(this).serialize();

            Swal.fire({
                title: 'Sedang Menyimpan...',
                icon: 'info',
                allowOutsideClick: false,
                showConfirmButton: false,
                didOpen: () => { Swal.showLoading(); }
            });

            $.ajax({
                url: "{{ url('api/penjualan') }}", // Endpoint untuk menyimpan data penjualan/produksi
                method: 'POST',
                data: formData,
                success: function(response) {
                    Swal.fire({
                        icon: 'success',
                        title: 'Berhasil!',
                        text: 'Catatan produksi/penjualan berhasil ditambahkan dan stok bahan baku sudah dikurangi.',
                        timer: 2000
                    });

                    $('#addProduksiModal').modal('hide');
                    table.ajax.reload(null, false); // Muat ulang DataTables
                    $('#addProduksiForm')[0].reset(); // Reset form
                    $('#total_harga_display').val(''); // Kosongkan display harga
                },
                error: function(xhr) {
                    let errorMessage = 'Gagal menyimpan data.';
                    if (xhr.responseJSON && xhr.responseJSON.message) {
                        errorMessage += '<br>' + xhr.responseJSON.message;
                    }
                    Swal.fire({
                        icon: 'error',
                        title: 'Kesalahan!',
                        html: errorMessage,
                    });
                }
            });
        });

    });
</script>
@endsection
