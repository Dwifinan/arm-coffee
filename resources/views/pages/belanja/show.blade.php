@extends('components.layout')

@section('content')
<div class="pagetitle">
    <h1>Detail Request Belanja</h1>
    <nav>
        <ol class="breadcrumb">
            <li class="breadcrumb-item"><a href="{{ url('/') }}">Home</a></li>
            <li class="breadcrumb-item"><a href="{{ route('belanja.index') }}">Belanja</a></li>
            <li class="breadcrumb-item active">{{ $belanja->kode }}</li>
        </ol>
    </nav>
</div>

<section class="section">
    <div class="row">
        <div class="col-lg-12">
            <div class="card shadow-sm border-0">
                <div class="card-header py-3 {{ $belanja->status == 'selesai' ? 'bg-success' : 'bg-primary' }} text-white">
                    <div class="d-flex justify-content-between align-items-center">
                        <h6 class="m-0 font-weight-bold">
                            Kode: {{ $belanja->kode }} 
                            <span class="badge bg-white text-dark ms-2">{{ ucfirst($belanja->status) }}</span>
                        </h6>
                        <small>{{ $belanja->created_at->format('d M Y') }}</small>
                    </div>
                </div>
                <div class="card-body pt-4">

                    {{-- Informasi Header --}}
                    <div class="row mb-4">
                        <div class="col-md-6">
                            <table class="table table-borderless table-sm">
                                <tr>
                                    <td style="width: 150px" class="fw-bold">Direquest Oleh</td>
                                    <td>: {{ $belanja->userRequest->username ?? 'Unknown' }}</td>
                                </tr>
                                <tr>
                                    <td class="fw-bold">Total Estimasi</td>
                                    <td>: Rp {{ number_format($belanja->total_estimasi, 0, ',', '.') }}</td>
                                </tr>
                            </table>
                        </div>
                        <div class="col-md-6">
                            @if($belanja->status == 'selesai')
                            <table class="table table-borderless table-sm">
                                <tr>
                                    <td style="width: 150px" class="fw-bold">Diselesaikan Oleh</td>
                                    <td>: {{ $belanja->userSelesai->username ?? '-' }}</td>
                                </tr>
                                <tr>
                                    <td class="fw-bold">Total Akhir</td>
                                    <td>: Result: Rp {{ number_format($belanja->total_akhir, 0, ',', '.') }}</td>
                                </tr>
                                <tr>
                                    <td class="fw-bold">Waktu Selesai</td>
                                    <td>: {{ $belanja->updated_at->format('d M Y') }}</td>
                                </tr>
                            </table>
                            @endif
                        </div>
                    </div>

                    {{-- Foto Bukti --}}
                    @if($belanja->foto_bukti)
                    <div class="mb-4 text-center">
                        <img src="{{ asset($belanja->foto_bukti) }}" alt="Bukti Belanja" class="img-fluid rounded border p-1" style="max-height: 300px">
                        <div class="text-muted small mt-1">Bukti Foto Belanja</div>
                    </div>
                    @endif

                    {{-- Tabel Detail --}}
                    <div class="table-responsive">
                        <table class="table table-bordered table-striped align-middle">
                            <thead class="bg-light">
                                <tr>
                                    <th>No</th>
                                    <th>Bahan</th>
                                    <th>Estimasi (Qty x Harga)</th>
                                    @if($belanja->status == 'selesai')
                                    <th>Realisasi (Qty x Harga)</th>
                                    <th>Expired</th>
                                    @endif
                                </tr>
                            </thead>
                            <tbody>
                                @foreach($belanja->details as $index => $d)
                                <tr>
                                    <td>{{ $index + 1 }}</td>
                                    <td>
                                        @php
                                            $sBeli = $d->bahan->satuan_beli ?? $d->bahan->satuan;
                                        @endphp
                                        <strong>{{ $d->bahan->nama }}</strong> <br>
                                        <span class="badge bg-light text-dark border">{{ $sBeli }}</span>
                                    </td>
                                    <td>
                                        {{ $d->jumlah_estimasi }} x {{ number_format($d->harga_satuan_estimasi, 0, ',', '.') }}<br>
                                        <strong>= Rp {{ number_format($d->subtotal_estimasi, 0, ',', '.') }}</strong>
                                    </td>
                                    @if($belanja->status == 'selesai')
                                    <td>
                                        {{ $d->jumlah_akhir }} x {{ number_format($d->harga_satuan_akhir, 0, ',', '.') }}<br>
                                        <strong>= Rp {{ number_format($d->subtotal_akhir, 0, ',', '.') }}</strong>
                                    </td>
                                    <td>
                                        @if($d->expired)
                                            {{ \Carbon\Carbon::parse($d->expired)->format('d M Y') }}
                                        @else
                                            -
                                        @endif
                                    </td>
                                    @endif
                                </tr>
                                @endforeach
                            </tbody>
                        </table>
                    </div>

                    <div class="mt-4">
                        <a href="{{ route('belanja.index') }}" class="btn btn-secondary">Kembali</a>
                        @if(auth()->user()->role === 'staff' && $belanja->status == 'pending')
                            <a href="{{ route('belanja.edit', $belanja->id) }}" class="btn btn-success">Selesaikan Request Ini</a>
                        @endif
                    </div>

                </div>
            </div>
        </div>
    </div>
</section>
@endsection
