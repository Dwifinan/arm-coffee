@extends('components.layout')

@section('content')
<div class="pagetitle">
    <h1>Users</h1>
    <nav>
        <ol class="breadcrumb">
            <li class="breadcrumb-item"><a href="{{ url('/') }}">Home</a></li>
            <li class="breadcrumb-item active">Users</li>
        </ol>
    </nav>
</div>

<section class="section">
    <div class="card">
        <div class="card-body">

            {{-- TOMBOL TAMBAH USER (SESUAI PERMINTAAN) --}}
            <div class="d-flex justify-content-between align-items-center mb-3">
                <h5 class="card-title mb-0">Data Users</h5>

                {{-- Tombol mengarah ke route users.create --}}
                <a href="{{ route('users.create') }}" class="btn btn-primary btn-sm">
                    <i class="bi bi-plus-circle"></i> Tambah User
                </a>
            </div>

            <p>Halaman ini digunakan untuk mengelola data pengguna sistem.</p>

            {{-- Menampilkan pesan sukses dari Controller (jika ada) --}}
            @if(session('success'))
                <div class="alert alert-success alert-dismissible fade show" role="alert">
                    {{ session('success') }}
                    <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
                </div>
            @endif

            {{-- Menampilkan pesan error dari Controller (jika ada) --}}
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
                        <th>Nama</th>
                        <th>Username</th>
                        <th>Role</th>
                        <th>Aksi</th>
                    </tr>
                </thead>
                <tbody>
                    @php
                        $no=1;
                    @endphp
                    @foreach ($users as $data)
                    <tr>
                        <td>{{ $no++ }}</td>
                        {{-- Catatan: Kolom 'name' tidak ada di DB, tapi di sini diasumsikan data["name"] mungkin merujuk ke data["username"] atau Anda ingin menambahkan kolom "name" di masa depan. Saya pertahankan sesuai kode lama. --}}
                        <td>{{ $data["name"] ?? $data["username"] }}</td>
                        <td>{{ $data["username"] }}</td>
                        <td>
                            {{-- Menampilkan role dengan badge Bootstrap --}}
                            @if($data["role"] == 'owner')
                                <span class="badge bg-danger">{{ ucfirst($data["role"]) }}</span>
                            @elseif($data["role"] == 'staff')
                                <span class="badge bg-info">{{ ucfirst($data["role"]) }}</span>
                            @else
                                <span class="badge bg-secondary">{{ ucfirst($data["role"]) }}</span>
                            @endif
                        </td>
                        <td>
                            {{-- Tombol Edit --}}
                            <a href="{{ route('users.edit', $data->id) }}" class="btn btn-warning btn-sm" title="Edit User">
                                <i class="bi bi-pencil"></i>
                            </a>

                            {{-- Tombol Hapus (Menggunakan Form DELETE) --}}
                            <button type="button" class="btn btn-danger btn-sm delete-btn" data-id="{{ $data->id }}" data-username="{{ $data->username }}" title="Hapus User">
                                <i class="bi bi-trash"></i>
                            </button>

                            {{-- Form tersembunyi untuk proses penghapusan (akan di-submit oleh JavaScript) --}}
                            <form id="delete-form-{{ $data->id }}" action="{{ route('users.destroy', $data->id) }}" method="POST" style="display: none;">
                                @csrf
                                @method('DELETE')
                            </form>
                        </td>
                    </tr>
                    @endforeach
                </tbody>
            </table>
        </div>
    </div>
</section>

{{-- MODAL KONFIRMASI HAPUS (Wajib menggunakan modal, bukan alert/confirm) --}}
<div class="modal fade" id="deleteConfirmationModal" tabindex="-1" aria-labelledby="deleteConfirmationModalLabel" aria-hidden="true">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title" id="deleteConfirmationModalLabel">Konfirmasi Penghapusan</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <div class="modal-body">
                Apakah Anda yakin ingin menghapus user **<span id="modal-username"></span>**?
                Tindakan ini tidak dapat dibatalkan.
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Batal</button>
                <button type="button" class="btn btn-danger" id="confirmDeleteButton">Hapus</button>
            </div>
        </div>
    </div>
</div>

{{-- SCRIPT JAVASCRIPT UNTUK MODAL KONFIRMASI HAPUS --}}
<script>
    document.addEventListener('DOMContentLoaded', function () {
        const deleteModal = document.getElementById('deleteConfirmationModal');
        const confirmDeleteButton = document.getElementById('confirmDeleteButton');
        let formToSubmit = null;

        // Mendengarkan klik pada semua tombol hapus
        document.querySelectorAll('.delete-btn').forEach(button => {
            button.addEventListener('click', function() {
                const userId = this.getAttribute('data-id');
                const username = this.getAttribute('data-username');

                // Set username di modal
                document.getElementById('modal-username').textContent = username;

                // Simpan referensi form yang akan disubmit
                formToSubmit = document.getElementById('delete-form-' + userId);

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
