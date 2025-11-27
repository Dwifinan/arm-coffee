@extends('components.layout')

@section('content')

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
                    <input type="text" name="kode" class="form-control" required placeholder="BHN-001">
                </div>
                <div class="col-md-6">
                    <label>Nama Bahan <span class="text-danger">*</span></label>
                    <input type="text" name="nama" class="form-control" required>
                </div>
            </div>

            <div class="row mb-3">
                <div class="col-md-6">
                    <label>Satuan <span class="text-danger">*</span></label>
                    <select name="satuan" id="selectSatuan" class="form-control" required>
                        <option value="">Pilih...</option>
                        <option value="kg">kg</option>
                        <option value="gram">gram</option>
                        <option value="liter">liter</option>
                        <option value="ml">ml</option>
                        <option value="pcs">pcs</option>
                        <option value="pack">pack</option>
                    </select>
                </div>
                <div class="col-md-6">
                    <label>Stok Minimal <span class="text-danger">*</span></label>
                    <input type="number" min="0" name="stok_minimal" class="form-control" required>
                </div>
            </div>

            <div class="row mb-3">
                <div class="col-md-6">
                    <label>Harga <span class="text-danger">*</span></label>
                    <input type="number" min="0" name="harga_persatuan" class="form-control" required>
                </div>
                <div class="col-md-6">
                    <label>Per <span class="text-danger">*</span></label>
                    <div class="input-group">
                        <input type="number" name="jumlah_satuan" min="1" value="1" class="form-control">
                        <span class="input-group-text" id="labelSatuan">-</span>
                    </div>
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
                    <input type="text" name="kode" id="edit_kode" class="form-control">
                </div>
                <div class="col-md-6">
                    <label>Nama Bahan <span class="text-danger">*</span></label>
                    <input type="text" name="nama" id="edit_nama" class="form-control">
                </div>
            </div>

            <div class="row mb-3">
                <div class="col-md-6">
                    <label>Satuan <span class="text-danger">*</span></label>
                    <select name="satuan" id="edit_satuan" class="form-control" required>
                        <option value="">Pilih...</option>
                        <option value="kg">kg</option>
                        <option value="gram">gram</option>
                        <option value="liter">liter</option>
                        <option value="ml">ml</option>
                        <option value="pcs">pcs</option>
                        <option value="pack">pack</option>
                    </select>
                </div>
                <div class="col-md-6">
                    <label>Stok Minimal <span class="text-danger">*</span></label>
                    <input type="number" name="stok_minimal" id="edit_stok_minimal" class="form-control">
                </div>
            </div>

            <div class="row mb-3">
                <div class="col-md-6">
                    <label>Harga <span class="text-danger">*</span></label>
                    <input type="number" name="harga_persatuan" id="edit_harga" class="form-control">
                </div>
                <div class="col-md-6">
                    <label>Per <span class="text-danger">*</span></label>
                    <div class="input-group">
                        <input type="number" name="jumlah_satuan" id="edit_jumlah_per" class="form-control">
                        <span class="input-group-text" id="edit_labelSatuan">-</span>
                    </div>
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

@endsection


@section('scriptjs')
<script>
$(document).ready(function () {

    // ======================================
    //  SELECT2
    // ======================================
    // $('#selectSatuan, #edit_satuan').select2({
    //     dropdownParent: $('#modalTambahBahan, #modalEditBahan'),
    //     tags: true,
    //     placeholder: "Pilih atau ketik satuan"
    // });

    $('#selectSatuan').change(function(){
        $('#labelSatuan').text($(this).val());
    });

    $('#edit_satuan').change(function(){
        $('#edit_labelSatuan').text($(this).val());
    });


    // ======================================
    //  DATATABLE
    // ======================================
    let table = $('#bahanTable').DataTable({
        ajax: "{{ url('api/bahan') }}",
        columns: [
            { data: "id", visible: false },
            { data: "kode" },
            { data: "nama" },
            { data: "satuan" },
            { data: "harga_satuan" },
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
                    <button class="btn btn-warning btn-sm btnEdit" data-id="${row.id}">Edit</button>
                    <button class="btn btn-danger btn-sm btnHapus" data-id="${row.id}">Hapus</button>
                `
            }
        ]
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

});
</script>
@endsection
