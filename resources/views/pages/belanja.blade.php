@extends('components.layout')

@section('content')
<div class="pagetitle">
    <h1>Histori Belanja Bahan</h1>
    <nav>
        <ol class="breadcrumb">
            <li class="breadcrumb-item"><a href="{{ url('/') }}">Home</a></li>
            <li class="breadcrumb-item active">Histori</li>
        </ol>
    </nav>
</div>

<section class="section">
    <div class="card">
        <div class="card-body">
            
            <div class="d-flex justify-content-between align-items-center mb-3">
                <h5 class="card-title">Histori Pembelian Bahan Baku (Selesai)</h5>
                {{-- Tombol untuk kembali ke Daftar Request Aktif --}}
                <a href="{{ route('belanja.request') }}" class="btn btn-secondary btn-sm">
                    <i class="bi bi-list-check"></i> Lihat Request Aktif
                </a>
            </div>

            @if(session('success'))
                <div class="alert alert-success alert-dismissible fade show" role="alert">
                    {{ session('success') }}
                    <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
                </div>
            @endif

            <table class="table table-striped">
                <thead>
                    <tr>
                        <th>#</th>
                        <th>Bahan</th>
                        <th>Jumlah</th>
                        <th>Satuan</th>
                        <th>Total Harga</th>
                        <th>Tanggal Beli</th>
                        <th>Expired</th>
                    </tr>
                </thead>
                <tbody>
                    @forelse($historiBelanja as $index => $r)
                        <tr>
                            <td>{{ $index + 1 }}</td>
                            {{-- Asumsi relasi bahan sudah ter-load --}}
                            <td>{{ $r->bahan->nama }} ({{ $r->bahan->kode }})</td>
                            <td>{{ $r->jumlah }}</td>
                            <td>{{ $r->bahan->satuan }}</td>
                            <td>Rp {{ number_format($r->total_harga, 0, ',', '.') }}</td>
                            <td>{{ $r->created_at->format('d M Y') }}</td>
                            <td>{{ $r->expired ? $r->expired->format('d M Y') : '-' }}</td>
                        </tr>
                    @empty
                        <tr><td colspan="7" class="text-center">Belum ada histori pembelian bahan baku.</td></tr>
                    @endforelse
                </tbody>
            </table>

        </div>
    </div>
</section>
@endsection