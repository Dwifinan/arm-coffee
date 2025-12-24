@extends('components.layout')

@section('content')

<div class="pagetitle">
    <h1>Data Bahan Baku</h1>
    <nav>
        <ol class="breadcrumb">
            <li class="breadcrumb-item"><a href="{{ url('/') }}">Home</a></li>
            <li class="breadcrumb-item active">Bahan</li>
        </ol>
    </nav>
</div>

<style>
    div.dataTables_filter {
        margin-bottom: 18px !important;
    }
</style>

<div class="card">
    <div class="card-title d-flex justify-content-between align-items-center px-3 pt-3">
        <h5 class="m-0">Daftar Bahan</h5>

        <button type="button" class="btn btn-primary" data-bs-toggle="modal" data-bs-target="#modalTambahBahan">
            Tambah Bahan Baru
        </button>
    </div>

    <div class="card-body p-3">
        <div class="table-responsive">
            <table id="bahanTable" class="display table table-bordered" style="width: 100%">
                <thead class="table-light">
                    <tr>
                        <th>ID</th>
                        <th>Kode</th>
                        <th>Nama</th>
                        <th>Satuan</th>
                        <th>Harga</th>
                        <th>Stok</th>
                        <th>Stok Minimal</th>
                        <th>Status</th>
                        <th>Aksi</th>
                    </tr>
                </thead>
                <tbody></tbody>
            </table>
        </div>
    </div>
</div>

<!-- =================================================================== -->
<!--                        MODAL TAMBAH BAHAN                           -->
<!-- =================================================================== -->
<div class="modal fade" id="modalTambahBahan">
  <div class="modal-dialog modal-lg">
    <div class="modal-content">

      <div class="modal-header">
        <h5 class="modal-title">Tambah Bahan Baru</h5>
        <button class="btn-close" data-bs-dismiss="modal"></button>
      </div>

      <div class="modal-body">

        <form id="formTambahBahan">
            @csrf

            <div class="row mb-3">
                <div class="col-md-6">
                    <label>Kode Bahan <span class="text-danger">*</span></label>
                    <input type="text" name="kode" class="form-control" autocomplete="off" required placeholder="BHN-001">
                </div>
                <div class="col-md-6">
                    <label>Nama Bahan <span class="text-danger">*</span></label>
                    <input type="text" name="nama" class="form-control" autocomplete="off" required>
                </div>
            </div>

            <div class="row mb-3">
                <div class="col-md-6">
                    <label>Satuan <span class="text-danger">*</span></label>
                    <select name="satuan" id="selectSatuan" class="form-control" required>
                        <option value="">Pilih...</option>
                        <option value="gram">gram</option>
                        <option value="liter">liter</option>
                        <option value="ml">ml</option>
                        <option value="pcs">pcs</option>
                        <option value="pack">pack</option>
                    </select>
                </div>
                <div class="col-md-6">
                    <label>Stok Minimal <span class="text-danger">*</span></label>
                    <input type="number" min="0" name="stok_minimal" class="form-control" autocomplete="off" required>
                </div>
            </div>

            <div class="row mb-3">
                <div class="col-md-6">
                    <label>Harga <span class="text-danger">*</span></label>
                    <input type="number" min="0" name="harga_persatuan" class="form-control" autocomplete="off" required>
                </div>
                <div class="col-md-6">
                    <label>Per <span class="text-danger">*</span></label>
                    <div class="input-group">
                        <input type="number" name="jumlah_satuan" min="1" value="1" class="form-control" autocomplete="off" required>
                        <span class="input-group-text" id="labelSatuan">-</span>
                    </div>
                </div>
            </div>
            <div class="row mb-3">
            <div class="col-md-6">
                    <label>Satuan Beli <span class="text-danger">*</span></label>
                    <input type="text" min="0" name="satuan_beli" class="form-control" autocomplete="off" required>
                </div>
            </div>


        </form>

      </div>

      <div class="modal-footer">
        <button class="btn btn-secondary" data-bs-dismiss="modal">Tutup</button>
        <button class="btn btn-primary" id="btnSimpanBahan">Simpan</button>
      </div>

    </div>
  </div>
</div>


<!-- =================================================================== -->
<!--                         MODAL EDIT BAHAN                            -->
<!-- =================================================================== -->
<div class="modal fade" id="modalEditBahan">
  <div class="modal-dialog modal-lg">
    <div class="modal-content">

      <div class="modal-header">
        <h5 class="modal-title">Edit Bahan</h5>
        <button class="btn-close" data-bs-dismiss="modal"></button>
      </div>

      <div class="modal-body">
        <form id="formEditBahan">
            @csrf

            <input type="hidden" name="id" id="edit_id">

            <div class="row mb-3">
                <div class="col-md-6">
                    <label>Kode <span class="text-danger">*</span></label>
                    <input type="text" name="kode" id="edit_kode" class="form-control" autocomplete="off" required>
                </div>
                <div class="col-md-6">
                    <label>Nama Bahan <span class="text-danger">*</span></label>
                    <input type="text" name="nama" id="edit_nama" class="form-control" autocomplete="off" required>
                </div>
            </div>

            <div class="row mb-3">
                <div class="col-md-6">
                    <label>Satuan <span class="text-danger">*</span></label>
                    <select name="satuan" id="edit_satuan" class="form-control" required>
                        <option value="">Pilih...</option>
                        <option value="gram">gram</option>
                        <option value="liter">liter</option>
                        <option value="ml">ml</option>
                        <option value="pcs">pcs</option>
                        <option value="pack">pack</option>
                    </select>
                </div>
                <div class="col-md-6">
                    <label>Stok Minimal <span class="text-danger">*</span></label>
                    <input type="number" name="stok_minimal" id="edit_stok_minimal" class="form-control" autocomplete="off" required>
                </div>
            </div>

            <div class="row mb-3">
                <div class="col-md-6">
                    <label>Harga <span class="text-danger">*</span></label>
                    <input type="number" name="harga_persatuan" id="edit_harga" class="form-control" autocomplete="off" required>
                </div>
                <div class="col-md-6">
                    <label>Per <span class="text-danger">*</span></label>
                    <div class="input-group">
                        <input type="number" name="jumlah_satuan" id="edit_jumlah_per" class="form-control" autocomplete="off" required>
                        <span class="input-group-text" id="edit_labelSatuan">-</span>
                    </div>
                </div>
            </div>
            <div class="row mb-3">
                <div class="col-md-6">
                    <label>Satuan Beli <span class="text-danger">*</span></label>
                    <input type="text" min="0" name="satuan_beli" id="edit_satuan_beli" class="form-control" autocomplete="off" required>
                </div>
            </div>


        </form>
      </div>

      <div class="modal-footer">
        <button class="btn btn-secondary" data-bs-dismiss="modal">Tutup</button>
        <button class="btn btn-primary" id="btnUpdateBahan">Update</button>
      </div>

    </div>
  </div>
</div>

<!-- =================================================================== -->
<!--                        MODAL DETAIL BATCH (EXPIRY)                  -->
<!-- =================================================================== -->
<div class="modal fade" id="modalDetailBatch">
  <div class="modal-dialog modal-lg">
    <div class="modal-content">

      <div class="modal-header">
        <h5 class="modal-title">Detail Batch & Expired</h5>
        <button class="btn-close" data-bs-dismiss="modal"></button>
      </div>

      <div class="modal-body">

        <ul class="nav nav-tabs mb-3" id="detailTabs" role="tablist">
            <li class="nav-item" role="presentation">
                <button class="nav-link active" id="fefo-tab" data-bs-toggle="tab" data-bs-target="#fefo" type="button" role="tab">Batch & Expiry (FEFO)</button>
            </li>
            <li class="nav-item" role="presentation">
                <button class="nav-link" id="history-tab" data-bs-toggle="tab" data-bs-target="#history" type="button" role="tab">Kartu Stok (Riwayat)</button>
            </li>
        </ul>

        <div class="tab-content" id="detailTabsContent">
            <!-- TAB 1: FEFO BATCHES -->
            <div class="tab-pane fade show active" id="fefo" role="tabpanel">
                <div class="table-responsive">
                    <table class="table table-bordered table-hover align-middle">
                        <thead class="table-light">
                            <tr>
                                <th>Tgl Masuk</th>
                                <th>Expired</th>
                                <th>Awal</th>
                                <th>Sisa (FEFO)</th>
                                <th>Status</th>
                                <th>Aksi</th>
                            </tr>
                        </thead>
                        <tbody id="bodyFefo">
                            <!-- Loaded via AJAX -->
                        </tbody>
                    </table>
                </div>
                <div class="alert alert-info py-2 small">
                    <i class="bi bi-info-circle me-1"></i> Sistem akan memotong stok dari <b>Expired Terdekat</b> (Baris Paling Atas) yang masih memiliki Sisa > 0.
                </div>
            </div>

            <!-- TAB 2: HISTORY -->
            <div class="tab-pane fade" id="history" role="tabpanel">
                <div class="table-responsive">
                    <table class="table table-bordered table-striped" id="tableBatch">
                        <thead>
                            <tr>
                                <th>Waktu</th>
                                <th>Tipe</th>
                                <th>Jumlah</th>
                                <th>Keterangan</th>
                                <th>Ref</th>
                            </tr>
                        </thead>
                        <tbody id="bodyBatch">
                            <!-- Data loaded via AJAX -->
                        </tbody>
                    </table>
                </div>
                <p class="text-muted small mt-2">* Data stok ditampilkan berdasarkan riwayat belanja (Bahan Masuk).</p>
            </div>
        </div>

      </div>

      <div class="modal-footer">
        <button class="btn btn-secondary" data-bs-dismiss="modal">Tutup</button>
      </div>

    </div>
  </div>
</div>

@endsection


@section('scriptjs')
<script>
$(document).ready(function () {

    // ======================================
    //  SELECT2
    // ======================================
    $('#selectSatuan').select2({
        dropdownParent: $('#modalTambahBahan'),
        theme: 'bootstrap-5',
        placeholder: "Pilih atau ketik satuan",
        tags: true,
        width: '100%'
    });

    $('#edit_satuan').select2({
        dropdownParent: $('#modalEditBahan'),
        theme: 'bootstrap-5',
        placeholder: "Pilih atau ketik satuan",
        tags: true,
        width: '100%'
    });

    $('#selectSatuan').change(function(){
        $('#labelSatuan').text($(this).val());
    });

    $('#edit_satuan').change(function(){
        $('#edit_labelSatuan').text($(this).val());
    });


    // ======================================
    //  DATATABLE
    // ======================================
    // ======================================
    //  DATATABLE
    // ======================================
    let table = $('#bahanTable').DataTable({
        // responsive: true, // Dinonaktifkan agar scroll horizontal
        ajax: "{{ url('api/bahan') }}",
        columns: [
            { data: "id", visible: false },
            { data: "kode" },
            { data: "nama" },
            { data: "satuan" },
            {
                data: "harga_persatuan",
                render: function(data, type, row) {
                    let price = parseInt(data) || 0;
                    let fmt = price.toLocaleString('id-ID');
                    let unit = row.satuan_beli ? row.satuan_beli : row.satuan; // Use Buy Unit if avail
                    return `Rp ${fmt} <small class="text-muted">/ ${unit}</small>`;
                }
            },
            { data: "stok" },
            { data: "stok_minimal" },
            {
                data: null,
                render: row => {
                    return `<span class="badge bg-${row.stok <= row.stok_minimal ? 'danger':'success'}">
                        ${row.stok <= row.stok_minimal ? 'Kurang':'Aman'}
                    </span>`;
                }
            },
            {
                data: null,
                render: row => `
                    <button class="btn btn-info btn-sm btnDetail me-1" data-id="${row.id}" title="Lihat Batch"><i class="bi bi-eye"></i></button>
                    <button class="btn btn-warning btn-sm btnEdit me-1" data-id="${row.id}">Edit</button>
                    <button class="btn btn-danger btn-sm btnHapus" data-id="${row.id}">Hapus</button>
                `
            }
        ],
        initComplete: function(settings, json) {
            // --- AUTO OPEN FROM URL PARAM (MOVED HERE FOR ROBUSTNESS) ---
            const urlParams = new URLSearchParams(window.location.search);
            const openDetailId = urlParams.get('open_detail');

            if (openDetailId) {
                // 1. Filter table to show ONLY this specific ID (column 0 is hidden ID)
                //    This ensures the row is present in the DOM even if it was on page 2
                table.column(0).search('^' + openDetailId + '$', true, false).draw();

                // 2. Click the button once drawn
                setTimeout(() => {
                    let btn = $(`.btnDetail[data-id='${openDetailId}']`);
                    if (btn.length) {
                        btn.click();

                        // Clean URL so refresh doesn't trigger it again
                        const newUrl = window.location.pathname;
                        window.history.replaceState({}, document.title, newUrl);
                    } else {
                        console.warn("Target row not found for ID:", openDetailId);
                        // Reset filter if not found
                        table.column(0).search('').draw();
                    }
                }, 500); // Small delay to allow draw to finish
            }
        }
    });

    // Reset Filter when modal is closed so user sees all data again
    $('#modalDetailBatch').on('hidden.bs.modal', function () {
        // Clear the specific ID search on column 0
        table.column(0).search('').draw();
    });


    // ==================================================
    // SIMPAN DATA
    // ==================================================
    $('#btnSimpanBahan').click(function () {
        $.post("{{ url('api/add-bahan') }}", $('#formTambahBahan').serialize(), function () {
            Swal.fire("Berhasil!", "Data berhasil ditambahkan", "success");
            table.ajax.reload();
            $('#modalTambahBahan').modal('hide');
            $('#formTambahBahan')[0].reset();
        }).fail(() => {
            Swal.fire("Gagal!", "Periksa kembali data!", "error");
        });
    });


    // ==================================================
    // EDIT DATA → OPEN MODAL
    // ==================================================
    $(document).on('click', '.btnEdit', function(){
        let id = $(this).data('id');

        $.get("{{ url('api/bahan') }}/" + id, function(res){

            $('#edit_id').val(res.data.id);
            $('#edit_kode').val(res.data.kode);
            $('#edit_nama').val(res.data.nama);
            $('#edit_satuan').val(res.data.satuan).trigger('change');
            $('#edit_stok_minimal').val(res.data.stok_minimal);
            $('#edit_harga').val(res.data.harga_persatuan);
            $('#edit_jumlah_per').val(res.data.jumlah_satuan);
            $('#edit_labelSatuan').text(res.data.satuan);
            $('#edit_satuan_beli').val(res.data.satuan_beli);

            $('#modalEditBahan').modal('show');
        });

    });


    // ==================================================
    // UPDATE DATA
    // ==================================================
    $('#btnUpdateBahan').click(function(){
        let id = $('#edit_id').val();

        $.ajax({
            url: "{{ url('api/update-bahan') }}/" + id,
            type: "POST",
            data: $('#formEditBahan').serialize(),
            success: function(){
                Swal.fire("Berhasil!", "Data berhasil diupdate!", "success");
                table.ajax.reload();
                $('#modalEditBahan').modal('hide');
            }
        });
    });


    // ==================================================
    // HAPUS DATA
    // ==================================================
    $(document).on('click', '.btnHapus', function(){
        let id = $(this).data('id');

        Swal.fire({
            title: "Yakin ingin menghapus?",
            icon: "warning",
            showCancelButton: true,
            confirmButtonText: "Hapus"
        }).then(result => {
            if(result.isConfirmed) {

                $.ajax({
                    url: "{{ url('api/delete-bahan') }}/" + id,
                    data: {
                        '_token': '{{ csrf_token() }}'
                    },
                    type: "DELETE",
                    success: () => {
                        Swal.fire("Terhapus!", "Data berhasil dihapus", "success");
                        table.ajax.reload();
                    }
                });

            }
        });

    });

    // ==================================================
    // DETAIL BATCH (History)
    // ==================================================
    // ==================================================
    // DETAIL BATCH (History / KARTU STOK)
    // ==================================================
    // ==================================================
    // DETAIL BATCH (History / KARTU STOK & FEFO)
    // ==================================================
    $(document).on('click', '.btnDetail', function(){
        let id = $(this).data('id');
        $('#bodyBatch').html('<tr><td colspan="5" class="text-center">Loading Riwayat...</td></tr>');
        $('#bodyFefo').html('<tr><td colspan="6" class="text-center">Loading Batch...</td></tr>');

        $('#modalDetailBatch .modal-title').text('Detail & Validasi Stok (FEFO)');
        $('#modalDetailBatch').modal('show');

        // 1. LOAD HISTORY (Existing)
        $.get("{{ url('api/bahan') }}/" + id + "/history", function(res){
            let rows = '';
            if(res.data.length === 0){
                rows = '<tr><td colspan="5" class="text-center">Belum ada riwayat stok.</td></tr>';
            } else {
                res.data.forEach(item => {
                    let isMasuk = item.type === 'Masuk';
                    let typeBadge = isMasuk ? '<span class="badge bg-success">Masuk</span>' : '<span class="badge bg-danger">Keluar</span>';
                    let d = new Date(item.created_at);
                    let tgl = d.toLocaleDateString('id-ID', {day: 'numeric', month: 'short', year: 'numeric', hour: '2-digit', minute:'2-digit'});
                    let bgClass = isMasuk ? '' : 'table-light';

                    rows += `<tr class="${bgClass}">
                        <td>${tgl}</td>
                        <td>${typeBadge}</td>
                        <td><span class="${isMasuk ? 'text-success fw-bold' : 'text-danger fw-bold'}">${isMasuk ? '+' : '-'}${item.jumlah}</span></td>
                        <td>${item.keterangan || '-'}</td>
                        <td><small class="text-muted"><i class="bi bi-clock"></i></small></td>
                    </tr>`;
                });
            }
            $('#bodyBatch').html(rows);
        });

        // 2. LOAD ACTIVE BATCHES (FEFO PROOF)
        $.get("{{ url('api/bahan') }}/" + id + "/batches", function(res){
            let rows = '';
            let today = new Date();
            today.setHours(0,0,0,0);

            if(res.data.length === 0){
                rows = '<tr><td colspan="6" class="text-center">Belum ada data batch pembelian.</td></tr>';
            } else {
                res.data.forEach(batch => {
                    let dMasuk = new Date(batch.created_at);
                    let tglMasuk = dMasuk.toLocaleDateString('id-ID', {day: 'numeric', month: 'short', year: '2-digit'});

                    let dExp = new Date(batch.expired);
                    let tglExp = dExp.toLocaleDateString('id-ID', {day: 'numeric', month: 'short', year: 'numeric'});

                    // Logic Status
                    let isExpired = dExp < today;
                    let isEmpty = batch.sisa_stok <= 0;

                    let statusBadge = '';
                    let rowClass = '';

                    if (isEmpty) {
                        statusBadge = '<span class="badge bg-secondary">Habis Terpakai</span>';
                        rowClass = 'table-secondary opacity-75';
                    } else if (isExpired) {
                        statusBadge = '<span class="badge bg-danger">EXPIRED</span>';
                        rowClass = 'table-danger'; // Highlight Expired
                    } else {
                        // Cek Near Expired (3 days)
                        let diffTime = dExp - today;
                        let diffDays = Math.ceil(diffTime / (1000 * 60 * 60 * 24));
                        if (diffDays <= 3 && diffDays >= 0) {
                            statusBadge = '<span class="badge bg-warning text-dark">Hampir Expired (' + diffDays + ' hr)</span>';
                            rowClass = 'table-warning';
                        } else {
                            statusBadge = '<span class="badge bg-success">Active</span>';
                        }
                    }

                    // Resolve Button (Only if Expired/Issues and Not Resolved?)
                    // Logic: You can resolve manually if you want to trash it
                    let btnResolve = '';
                    if (!isEmpty) {
                        btnResolve = `<button class="btn btn-sm btn-outline-danger btnResolve" data-id="${batch.id}" data-stok="${batch.sisa_stok}" title="Atur / Buang"><i class="bi bi-trash"></i></button>`;
                    }

                    rows += `<tr class="${rowClass}">
                        <td>${tglMasuk}</td>
                        <td class="fw-bold">${tglExp}</td>
                        <td>${batch.jumlah}</td>
                        <td><span class="fw-bold" style="font-size:1.1em">${batch.sisa_stok}</span></td>
                        <td>${statusBadge}</td>
                        <td>${btnResolve}</td>
                    </tr>`;
                });
            }
            $('#bodyFefo').html(rows);
        });
    });

    // --- HANDLE RESOLVE BUTTON ---
    $(document).on('click', '.btnResolve', function() {
        let batchId = $(this).data('id');
        let currentStock = $(this).data('stok'); // Optional: show max logic

        Swal.fire({
            title: 'Atur Barang Expired',
            html: `
                <div class="text-start">
                    <p class="mb-2">Barang ini sudah kadaluarsa.</p>
                    <label class="form-label fw-bold">Berapa banyak yang dibuang?</label>
                    <input type="number" id="qtyDisposed" class="form-control form-control-lg border-primary" value="0" min="0">
                    <div class="form-text text-muted mt-1">
                        <small>
                        <i class="bi bi-info-circle"></i> Isi <b>0</b> jika barang sudah habis terpakai.<br>
                        <i class="bi bi-trash"></i> Isi <b>Angka</b> jika barang fisik dibuang (Stok akan berkurang).
                        </small>
                    </div>
                </div>
            `,
            showCancelButton: true,
            confirmButtonText: 'Simpan',
            cancelButtonText: 'Batal',
            focusConfirm: false, // Prevent auto-focus on Confirm button, allowing input focus interaction if needed
            didOpen: () => {
                const input = Swal.getPopup().querySelector('#qtyDisposed');
                input.focus();
                input.select(); // Select all text so user can type immediately
            },
            preConfirm: () => {
                return document.getElementById('qtyDisposed').value;
            }
        }).then((result) => {
            if (result.isConfirmed) {
                let qty = result.value;
                $.ajax({
                    url: "{{ url('api/bahan-masuk') }}/" + batchId + "/resolve",
                    method: 'POST',
                    data: {
                        _token: "{{ csrf_token() }}",
                        qty_disposed: qty
                    },
                    success: function(res) {
                        Swal.fire('Berhasil', 'Status expired telah diselesaikan.', 'success');
                        $('#modalDetailBatch').modal('hide');
                        table.ajax.reload();
                    },
                    error: function(xhr) {
                        Swal.fire('Error', 'Gagal memproses data.', 'error');
                    }
                });
            }
        });
    });

    // (Old auto-open logic removed)


});
</script>
@endsection
