
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
                            <th>Tanggal Produksi</th>
                            <th>Total Item Terjual</th>
                            <th>Total Omset</th>
                            <th>Aksi</th>
                        </tr>
                    </thead>
                    <tbody></tbody>
                </table>
            </div>
        </div>
    </div>
</section>

{{-- MODAL DETAIL BATCH --}}
<div class="modal fade" id="modalDetailBatch" tabindex="-1">
    <div class="modal-dialog modal-lg">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title">Detail Produksi Batch</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
            </div>
            <div class="modal-body">
                <table class="table table-bordered table-sm">
                    <thead>
                        <tr>
                            <th>Menu</th>
                            <th>Jumlah</th>
                            <th>Total Harga</th>
                            {{-- <th>Kode TRX</th> --}}
                        </tr>
                    </thead>
                    <tbody id="detailBatchBody">
                        <tr><td colspan="3" class="text-center">Loading...</td></tr>
                    </tbody>
                </table>
            </div>
        </div>
    </div>
</div>

{{-- MODAL INPUT (Existing) --}}
<div class="modal fade" id="addProduksiModal" tabindex="-1" aria-labelledby="addProduksiModalLabel" aria-hidden="true">
    <div class="modal-dialog modal-lg">
        <div class="modal-content">
             {{-- ... existing content ... --}}
            <div class="modal-header">
                <h5 class="modal-title" id="addProduksiModalLabel">Catat Produksi Baru (Batch)</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <form id="addProduksiForm">
                @csrf
                <div class="modal-body">
                    
                    <p class="small text-muted mb-3">Tambahkan menu yang diproduksi (terjual) di bawah ini.</p>

                    <div class="table-responsive">
                        <table class="table table-bordered table-sm" id="produksiTableInput">
                            <thead class="bg-light">
                                <tr>
                                    <th width="45%">Menu</th>
                                    <th width="20%">Jml</th>
                                    <th width="30%">Total (Rp)</th>
                                    <th width="5%"></th>
                                </tr>
                            </thead>
                            <tbody>
                                <!-- Rows added via JS -->
                            </tbody>
                            <tfoot>
                                <tr>
                                    <td colspan="4" class="text-end">
                                        <button type="button" class="btn btn-sm btn-success" id="addRowBtn">
                                            <i class="bi bi-plus-circle"></i> Tambah Item
                                        </button>
                                    </td>
                                </tr>
                            </tfoot>
                        </table>
                    </div>

                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Tutup</button>
                    <button type="submit" class="btn btn-primary" id="saveProduksiBtn">Simpan Semua</button>
                </div>
            </form>
        </div>
    </div>
</div>

{{-- Master Option for Cloning --}}
<div style="display: none;">
    <select id="masterMenuSelect">
        <option value="">-- Pilih Menu --</option>
    </select>
</div>

@endsection

@section('scriptjs')
<script>
    let menuData = [];

    $(document).ready(function() {

        // --- 1. DATATABLES ---
        const table = $('#penjualanAsProduksiTable').DataTable({
            "processing": false,
            "serverSide": false,
            "ajax": {
                "url": "{{ url('api/penjualan-produksi') }}",
                "type": "GET",
                "dataSrc": "data",
                "error": function(xhr, error, code) {
                    Swal.fire({ icon: 'error', title: 'Gagal', text: 'Gagal memuat data: ' + code });
                }
            },
            "columns": [
                { "data": "tgl", "title": "Tanggal Produksi",
                    "render": function(data) { 
                        return data ? new Date(data).toLocaleDateString('id-ID', { weekday: 'long', day: 'numeric', month: 'long', year: 'numeric' }) : ''; 
                    }
                },
                { "data": "total_items", "title": "Total Item Terjual" },
                { "data": "total_pendapatan", "title": "Total Omset", "render": $.fn.dataTable.render.number('.', ',', 0, 'Rp ') },
                {
                    "data": null, "title": "Aksi",
                    "render": function(data, type, row) {
                        if(!row.tgl) return '-';
                        return `
                            <button class="btn btn-sm btn-info text-white btnDetailBatch me-1" data-date="${row.tgl}"><i class="bi bi-eye"></i> Detail</button>
                            <button class="btn btn-sm btn-danger btnHapusBatch" data-date="${row.tgl}"><i class="bi bi-trash"></i> Void Harian</button>
                        `;
                    }
                }
            ],
            "order": [[0, 'desc']]
        });

        // --- DETAIL BUTTON CLICK ---
        $(document).on('click', '.btnDetailBatch', function() {
            let date = $(this).data('date');
            
            // Format date for Header
            let fmtDate = new Date(date).toLocaleDateString('id-ID', { weekday: 'long', day: 'numeric', month: 'long', year: 'numeric' });
            $('#modalDetailBatch .modal-title').text('Detail Produksi: ' + fmtDate);

            $('#modalDetailBatch').modal('show');
            $('#detailBatchBody').html('<tr><td colspan="4" class="text-center">Loading Data...</td></tr>');

            $.ajax({
                url: "{{ url('api/penjualan-produksi') }}/" + date,
                method: 'GET',
                success: function(res) {
                    if(!res.data || res.data.length === 0) {
                        $('#detailBatchBody').html('<tr><td colspan="3" class="text-center text-danger">Data tidak ditemukan di database.</td></tr>');
                        return;
                    }

                    let rows = '';
                    let totalDaily = 0;
                    res.data.forEach(item => {
                        let total = parseFloat(item.total_harga);
                        totalDaily += total;
                        let menu = item.menu ? item.menu.nama : 'Menu Terhapus/Unknown';
                        
                        // Time
                        let time = new Date(item.created_at).toLocaleTimeString('id-ID', {hour: '2-digit', minute:'2-digit'});

                        rows += `<tr>
                            <td>
                                <strong>${menu}</strong> <br>
                                <span class="badge bg-light text-dark border">${item.kode_transaksi || '-'}</span>
                                <small class="text-muted ms-2">${time}</small>
                            </td>
                            <td>${item.jumlah}</td>
                            <td>Rp ${total.toLocaleString('id-ID')}</td>
                        </tr>`;
                    });
                    
                    // Add Total Row
                    rows += `
                        <tr class="table-light fw-bold">
                            <td colspan="2" class="text-end">Total Hari Ini:</td>
                            <td>Rp ${totalDaily.toLocaleString('id-ID')}</td>
                        </tr>
                    `;

                    $('#detailBatchBody').html(rows);
                },
                error: function(xhr) {
                    $('#detailBatchBody').html(`<tr><td colspan="3" class="text-center text-danger">Error: ${xhr.status} - ${xhr.statusText}</td></tr>`);
                }
            });
        });

        // --- HAPUS / VOID BATCH (DAILY) ---
        $(document).on('click', '.btnHapusBatch', function() {
            let date = $(this).data('date');
            let fmtDate = new Date(date).toLocaleDateString('id-ID', { weekday: 'long', day: 'numeric', month: 'long', year: 'numeric' });

            Swal.fire({
                title: 'Void Produksi Harian?',
                html: `Anda akan menghapus <b>SEMUA</b> data produksi tanggal <b>${fmtDate}</b>.<br>Stok bahan akan dikembalikan.`,
                icon: 'warning',
                showCancelButton: true,
                confirmButtonColor: '#d33',
                cancelButtonColor: '#3085d6',
                confirmButtonText: 'Ya, Void Semua',
                cancelButtonText: 'Batal'
            }).then((result) => {
                if (result.isConfirmed) {
                    $.ajax({
                        url: "{{ url('api/penjualan-produksi') }}/" + date,
                        method: 'DELETE',
                        data: { _token: "{{ csrf_token() }}" },
                        success: function(res) {
                            Swal.fire('Berhasil!', res.message, 'success');
                            table.ajax.reload();
                        },
                        error: function(xhr) {
                            let msg = xhr.responseJSON ? xhr.responseJSON.message : 'Gagal menghapus data.';
                            Swal.fire('Gagal!', msg, 'error');
                        }
                    });
                }
            });
        });


        // --- 2. LOAD MENU LIST ---
        function fetchMenuList() {
            $.ajax({
                url: "{{ url('api/menu-list') }}",
                method: 'GET',
                success: function(response) {
                    menuData = response.data;
                    let options = '<option value="">-- Pilih Menu --</option>';
                    menuData.forEach(menu => {
                        options += `<option value="${menu.id}" data-harga="${menu.harga}">${menu.nama}</option>`;
                    });
                    $('#masterMenuSelect').html(options);

                    // Sync to existing rows (Fix Race Condition)
                    $('.menu-select').each(function() {
                        let val = $(this).val();
                        $(this).html(options);
                        $(this).val(val).trigger('change'); // Trigger change for Select2 update
                    });
                },
                error: function(xhr) {
                    console.error("Failed to load menus");
                }
            });
        }
        fetchMenuList();


        // --- 3. DYNAMIC TABLE LOGIC ---
        let rowIdx = 0;

        function addRow() {
            let i = rowIdx++;
            let rowHtml = `
                <tr id="row-${i}">
                    <td>
                        <select name="items[${i}][menu_id]" class="form-control form-select-sm menu-select" required>
                            ${$('#masterMenuSelect').html()}
                        </select>
                    </td>
                    <td>
                        <input type="number" name="items[${i}][jumlah]" class="form-control form-control-sm jumlah-input" min="1" value="1" required>
                    </td>
                    <td>
                        <input type="text" class="form-control form-control-sm total-display" readonly>
                        <input type="hidden" name="items[${i}][total_harga]" class="total-hidden">
                    </td>
                    <td>
                        <button type="button" class="btn btn-sm btn-danger remove-row" data-id="${i}"><i class="bi bi-trash"></i></button>
                    </td>
                </tr>
            `;
            $('#produksiTableInput tbody').append(rowHtml);
            
            // Initialize Select2 on the new element
            $(`#row-${i} .menu-select`).select2({
                dropdownParent: $('#addProduksiModal'),
                theme: 'bootstrap-5',
                width: '100%',
                placeholder: "-- Pilih Menu --",
            });

            // Trigger calculation for initial distinct value
            calculateRow(i);
        }

        // Add first row on modal open if empty
        $('#addProduksiModal').on('show.bs.modal', function(){
            if($('#produksiTableInput tbody tr').length === 0){
                addRow();
            }
        });

        $('#addRowBtn').click(addRow);

        $(document).on('click', '.remove-row', function(){
            let id = $(this).data('id');
            $(`#row-${id}`).remove();
        });

        // Calculation Logic
        $(document).on('change', '.menu-select', function(){
            let row = $(this).closest('tr');
            calculateRowFromTr(row);
        });

        $(document).on('input', '.jumlah-input', function(){
            let row = $(this).closest('tr');
            calculateRowFromTr(row);
        });

        function calculateRow(idx) {
            let row = $(`#row-${idx}`);
            calculateRowFromTr(row);
        }

        function calculateRowFromTr(row) {
            let price = parseFloat(row.find('.menu-select option:selected').data('harga')) || 0;
            let qty = parseInt(row.find('.jumlah-input').val()) || 0;
            let total = price * qty;

            row.find('.total-display').val(total.toLocaleString('id-ID'));
            row.find('.total-hidden').val(total);
        }


        // --- 4. SUBMIT FORM ---
        $('#addProduksiForm').on('submit', function(e) {
            e.preventDefault();

            const formData = $(this).serialize();

            Swal.fire({
                title: 'Sedang Memproses...',
                text: 'Mencatat produksi dan mengurangi stok...',
                didOpen: () => Swal.showLoading()
            });

            $.ajax({
                url: "{{ url('api/penjualan') }}",
                method: 'POST',
                data: formData,
                success: function(response) {
                    Swal.fire('Berhasil!', response.message, 'success');
                    $('#addProduksiModal').modal('hide');
                    table.ajax.reload();
                    $('#produksiTableInput tbody').empty(); // Clear rows
                    addRow(); // Add fresh row
                },
                error: function(xhr) {
                    let msg = xhr.responseJSON ? xhr.responseJSON.message : 'Terjadi kesalahan server.';
                    Swal.fire('Gagal!', msg, 'error');
                }
            });
        });

    });
</script>
@endsection
