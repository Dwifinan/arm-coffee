@extends('components.layout')

@section('content')
<div class="pagetitle">
    <h1>Menu Produk</h1>
    <nav>
        <ol class="breadcrumb">
            <li class="breadcrumb-item"><a href="{{ url('/') }}">Home</a></li>
            <li class="breadcrumb-item active">Menu</li>
        </ol>
    </nav>
</div>

<section class="section">
    <div class="card">
        <div class="card-body">

            <div class="d-flex justify-content-between align-items-center mb-3">
                <h5 class="card-title mb-0">Daftar Menu</h5>

                {{-- Tombol Tambah Menu --}}
                <a href="{{ route('menu.create') }}" class="btn btn-primary btn-sm">
                    <i class="bi bi-plus-circle"></i> Tambah Menu
                </a>
            </div>

            <p>Halaman ini menampilkan semua menu yang tersedia dan harga jualnya.</p>

            @if(session('success'))
                <div class="alert alert-success alert-dismissible fade show" role="alert">
                    {{ session('success') }}
                    <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
                </div>
            @endif

            @if(session('error'))
                <div class="alert alert-danger alert-dismissible fade show" role="alert">
                    {{ session('error') }}
                    <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
                </div>
            @endif

            <table class="table table-striped">
                <thead>
                    <tr>
                        <th>#</th>
                        <th>Nama Menu</th>
                        <th>Harga Jual</th>
                        <th style="width: 15%">Aksi</th>
                    </tr>
                </thead>
                <tbody>
                    @forelse ($menus as $menu)
                    <tr>
                        <td>{{ $loop->iteration }}</td>
                        <td>{{ $menu->nama }}</td>
                        <td>Rp {{ number_format($menu->harga, 0, ',', '.') }}</td>
                        <td>
                            {{-- Tombol Detail --}}
                            <a href="{{ route('menu.show', $menu->id) }}" class="btn btn-info btn-sm text-white" title="Lihat Detail">
                                <i class="bi bi-eye"></i>
                            </a>

                            {{-- Tombol Edit --}}
                            <a href="{{ route('menu.edit', $menu->id) }}" class="btn btn-warning btn-sm" title="Edit Menu">
                                <i class="bi bi-pencil"></i>
                            </a>

                            {{-- Tombol Hapus (Trigger Modal) --}}
                            <button type="button" class="btn btn-danger btn-sm delete-menu-btn" data-id="{{ $menu->id }}" data-nama="{{ $menu->nama }}" title="Hapus Menu">
                                <i class="bi bi-trash"></i>
                            </button>

                            {{-- Form tersembunyi untuk proses penghapusan --}}
                            <form id="delete-form-{{ $menu->id }}" action="{{ route('menu.destroy', $menu->id) }}" method="POST" style="display: none;">
                                @csrf
                                @method('DELETE')
                            </form>
                        </td>
                    </tr>
                    @empty
                    <tr>
                        <td colspan="4" class="text-center">Belum ada data Menu. Silakan tambahkan menu baru.</td>
                    </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
    </div>
</section>

{{-- MODAL KONFIRMASI HAPUS --}}
<div class="modal fade" id="deleteMenuConfirmationModal" tabindex="-1" aria-labelledby="deleteMenuConfirmationModalLabel" aria-hidden="true">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title" id="deleteMenuConfirmationModalLabel">Konfirmasi Penghapusan Menu</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <div class="modal-body">
                Apakah Anda yakin ingin menghapus menu **<span id="modal-menu-nama"></span>**?
                Semua detail bahan terkait juga akan dihapus. Tindakan ini tidak dapat dibatalkan.
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Batal</button>
                <button type="button" class="btn btn-danger" id="confirmDeleteMenuButton">Hapus Permanen</button>
            </div>
        </div>
    </div>
</div>

{{-- SCRIPT JAVASCRIPT UNTUK MODAL KONFIRMASI HAPUS --}}
<script>
    document.addEventListener('DOMContentLoaded', function () {
        const deleteModal = document.getElementById('deleteMenuConfirmationModal');
        const confirmDeleteButton = document.getElementById('confirmDeleteMenuButton');
        let formToSubmit = null;

        // Mendengarkan klik pada semua tombol hapus
        document.querySelectorAll('.delete-menu-btn').forEach(button => {
            button.addEventListener('click', function() {
                const menuId = this.getAttribute('data-id');
                const menuNama = this.getAttribute('data-nama');

                // Set nama menu di modal
                document.getElementById('modal-menu-nama').textContent = menuNama;

                // Simpan referensi form yang akan disubmit
                formToSubmit = document.getElementById('delete-form-' + menuId);

                // Tampilkan modal
                const modal = new bootstrap.Modal(deleteModal);
                modal.show();
            });
        });

        // Mendengarkan klik pada tombol 'Hapus' di modal
        confirmDeleteButton.addEventListener('click', function() {
            if (formToSubmit) {
                formToSubmit.submit();
            }
            // Tutup modal
            const modalInstance = bootstrap.Modal.getInstance(deleteModal);
            if (modalInstance) {
                modalInstance.hide();
            }
        });
    });
</script>
@endsection
