
@extends('components.layout')

@section('content')

<div class="pagetitle">
    <h1>Daftar Menu</h1>
    <nav>
        <ol class="breadcrumb">
            <li class="breadcrumb-item"><a href="{{ url('/') }}">Home</a></li>
            <li class="breadcrumb-item active">Menu</li>
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
        <h5 class="m-0">Daftar Menu</h5>

        {{-- Mengarahkan ke form tambah menu (route menu.create) --}}
        <a href="{{ route('menu.create') }}" class="btn btn-primary">
            <i class="bi bi-plus-circle"></i> Tambah Menu Baru
        </a>
    </div>

    <div class="card-body p-3">
        <table id="menuTable" class="display table table-bordered" style="width: 100%">
            <thead class="table-light">
                <tr>
                    <th>ID</th>
                    <th>Nama</th>
                    <th>Harga</th>
                    <th>Tersedia</th>
                    <th>Aksi</th>
                </tr>
            </thead>
            <tbody></tbody>
        </table>
    </div>
</div>

@endsection


@section('scriptjs')
<script>
$(document).ready(function () {

    // ======================================
    //  DATATABLE
    // ======================================
    let table = $('#menuTable').DataTable({
        // FIX: Route ini sekarang menunjuk ke MenuController@getMenuData
        ajax: "{{ url('api/menu') }}",
        columns: [
            { data: "id", visible: false },
            { data: "nama" },
            {
                data: "harga",
                render: val => `Rp ${parseInt(val).toLocaleString()}`
            },
            {
                data: "tersedia",
                render: val => `
                    <span class="badge bg-${val ? 'success':'danger'}">
                        ${val ? 'Tersedia':'Habis'}
                    </span>`
            },
            {
                data: null,
                render: row => {
                    // Membuat URL menggunakan route helper Laravel
                    const detailUrl = "{{ route('menu.show', ':id') }}".replace(':id', row.id);
                    const editUrl = "{{ route('menu.edit', ':id') }}".replace(':id', row.id);

                    return `
                        <a href="${detailUrl}" class="btn btn-info btn-sm me-1" title="Detail Menu"><i class="bi bi-eye"></i></a>
                        <a href="${editUrl}" class="btn btn-warning btn-sm me-1" title="Edit Menu"><i class="bi bi-pencil"></i></a>
                        <button class="btn btn-danger btn-sm btnHapus" data-id="${row.id}" title="Hapus Menu"><i class="bi bi-trash"></i></button>
                    `;
                }
            }
        ]
    });


    // ==================================================
    // HAPUS (Menggunakan API route)
    // ==================================================
    $(document).on('click', '.btnHapus', function(){
        let id = $(this).data('id');

        Swal.fire({
            title: "Yakin ingin menghapus menu?",
            text: "Penghapusan akan menghapus menu dan komposisi bahannya.",
            icon: "warning",
            showCancelButton: true,
            confirmButtonText: "Hapus"
        }).then(result => {
            if(result.isConfirmed) {

                $.ajax({
                    // Menggunakan rute delete-menu API
                    url: "{{ url('api/delete-menu') }}/" + id,
                    type: "DELETE",
                    data: { '_token': '{{ csrf_token() }}' },
                    success: (res) => {
                        Swal.fire("Terhapus!", "Menu berhasil dihapus", "success");
                        table.ajax.reload();
                    },
                    error: (err) => {
                        // Menampilkan pesan error dari server
                        Swal.fire("Gagal!", err.responseJSON.message || "Terjadi kesalahan saat menghapus menu.", "error");
                    }
                });

            }
        });

    });
});
</script>
@endsection
