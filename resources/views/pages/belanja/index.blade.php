@extends('components.layout')

@section('content')
<div class="pagetitle">
    <h1>Daftar Request Belanja</h1>
    <nav>
        <ol class="breadcrumb">
            <li class="breadcrumb-item"><a href="{{ url('/') }}">Home</a></li>
            <li class="breadcrumb-item active">Belanja</li>
        </ol>
    </nav>
</div>

<section class="section">
    <div class="row">

        @if(session('success'))
        <div class="col-12">
            <div class="alert alert-success alert-dismissible fade show" role="alert">
                {{ session('success') }}
                <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
            </div>
        </div>
        @endif

        @if(auth()->user()->role === 'owner')
        <div class="col-12 mb-3">
            <a href="{{ route('belanja.create') }}" class="btn btn-primary">
                <i class="bi bi-plus-circle me-1"></i> Buat Request Baru
            </a>
        </div>
        @endif

        <div class="col-12">
            <div class="card shadow-sm border-0">
                <div class="card-body">
                    <h5 class="card-title">Riwayat & Request Aktif</h5>
                    <div class="table-responsive">
                        <table class="table table-hover align-middle">
                            <thead class="bg-light">
                                <tr>
                                    <th>Kode</th>
                                    <th>Tanggal</th>
                                    <th>Pembuat</th>
                                    <th>Jml Item</th>
                                    <th>Total Estimasi</th>
                                    <th>Status</th>
                                    <th class="text-end">Aksi</th>
                                </tr>
                            </thead>
                            <tbody>
                                @forelse ($belanja as $b)
                                <tr>
                                    <td class="fw-bold">{{ $b->kode }}</td>
                                    <td>{{ $b->created_at->format('d M Y') }}</td>
                                    <td>{{ $b->userRequest->username ?? 'Unknown' }}</td>
                                    <td>
                                        <ul class="list-unstyled mb-0 small">
                                            @foreach($b->details->take(3) as $d)
                                                @php
                                                    $sBeli = $d->bahan->satuan_beli ?? $d->bahan->satuan;
                                                    // $d->jumlah_estimasi is already in Buy Unit due to Controller Logic
                                                @endphp
                                                <li>- {{ $d->bahan->nama }}: {{ $d->jumlah_estimasi }} {{ $sBeli }}</li>
                                            @endforeach
                                            @if($b->details->count() > 3)
                                                <li class="text-muted">+ {{ $b->details->count() - 3 }} more...</li>
                                            @endif
                                        </ul>
                                    </td>
                                    <td>Rp {{ number_format($b->total_estimasi, 0, ',', '.') }}</td>
                                    <td>
                                        @if($b->status == 'pending')
                                            <span class="badge bg-warning text-dark">Pending</span>
                                        @else
                                            <span class="badge bg-success">Selesai</span>
                                        @endif
                                    </td>
                                    <td class="text-end">
                                        <a href="{{ route('belanja.show', $b->id) }}" class="btn btn-info btn-sm text-white" title="Lihat Detail">
                                            <i class="bi bi-eye"></i>
                                        </a>

                                        @if(auth()->user()->role === 'staff' && $b->status == 'pending')
                                            <a href="{{ route('belanja.edit', $b->id) }}" class="btn btn-success btn-sm" title="Selesaikan">
                                                <i class="bi bi-check-lg"></i> Proses
                                            </a>
                                        @endif

                                        @if(auth()->user()->role === 'owner' && $b->status == 'pending')
                                            <button type="button" class="btn btn-danger btn-sm delete-btn" data-id="{{ $b->id }}" title="Hapus">
                                                <i class="bi bi-trash"></i>
                                            </button>
                                            <form id="delete-form-{{ $b->id }}" action="{{ route('belanja.destroy', $b->id) }}" method="POST" style="display: none;">
                                                @csrf
                                                @method('DELETE')
                                            </form>
                                        @endif
                                    </td>
                                </tr>
                                @empty
                                <tr>
                                    <td colspan="7" class="text-center text-muted py-4">Belum ada data request belanja.</td>
                                </tr>
                                @endforelse
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>
        </div>

    </div>
</section>

<script>
    document.addEventListener('DOMContentLoaded', function() {
        // Delete Confirmation
        document.querySelectorAll('.delete-btn').forEach(btn => {
            btn.addEventListener('click', function() {
                const id = this.getAttribute('data-id');
                Swal.fire({
                    title: 'Hapus Request?',
                    text: "Data yang dihapus tidak bisa dikembalikan!",
                    icon: 'warning',
                    showCancelButton: true,
                    confirmButtonColor: '#d33',
                    cancelButtonColor: '#3085d6',
                    confirmButtonText: 'Ya, Hapus!',
                    cancelButtonText: 'Batal'
                }).then((result) => {
                    if (result.isConfirmed) {
                        document.getElementById('delete-form-' + id).submit();
                    }
                });
            });
        });

        // Success Alert
        @if(session('success'))
            Swal.fire({
                icon: 'success',
                title: 'Berhasil',
                text: "{{ session('success') }}",
                timer: 3000,
                showConfirmButton: false
            });
        @endif

        // Error Alert
        @if(session('error'))
            Swal.fire({
                icon: 'error',
                title: 'Gagal',
                text: "{{ session('error') }}"
            });
        @endif
    });
</script>
@endsection
