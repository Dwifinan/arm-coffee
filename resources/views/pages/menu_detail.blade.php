@extends('components.layout')

@section('content')
<div class="pagetitle">
    <h1>Detail Menu: {{ $menu->nama }}</h1>
    <nav>
        <ol class="breadcrumb">
            <li class="breadcrumb-item"><a href="{{ url('/') }}">Home</a></li>
            <li class="breadcrumb-item"><a href="{{ route('menu.index') }}">Menu</a></li>
            <li class="breadcrumb-item active">Detail</li>
        </ol>
    </nav>
</div>

<section class="section">
    <div class="card">
        <div class="card-body">
            <h5 class="card-title">Informasi Dasar</h5>

            <dl class="row">
                <dt class="col-sm-3">Nama Menu</dt>
                <dd class="col-sm-9">{{ $menu->nama }}</dd>

                <dt class="col-sm-3">Harga Jual</dt>
                <dd class="col-sm-9">Rp {{ number_format($menu->harga, 0, ',', '.') }}</dd>

                <dt class="col-sm-3">Tanggal Dibuat</dt>
                <dd class="col-sm-9">{{ $menu->created_at->format('d M Y H:i') }}</dd>
            </dl>

            <h5 class="card-title mt-4">Bahan Baku yang Dibutuhkan</h5>

            <div class="table-responsive">
                <table class="table table-bordered">
                    <thead>
                        <tr>
                            <th style="width: 5%">#</th>
                            <th style="width: 50%">Bahan Baku</th>
                            <th style="width: 25%">Jumlah</th>
                            <th style="width: 20%">Satuan</th>
                        </tr>
                    </thead>
                    <tbody>
                        @forelse ($menu->komposisi as $detail)
                        <tr>
                            <td>{{ $loop->iteration }}</td>
                            <td>{{ $detail->bahan->nama }} ({{ $detail->bahan->kode }})</td>
                            <td>{{ $detail->jumlah_bahan }}</td>
                            <td>{{ $detail->bahan->satuan }}</td>
                        </tr>
                        @empty
                        <tr>
                            <td colspan="4" class="text-center">Tidak ada bahan baku yang terdaftar untuk menu ini.</td>
                        </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>

            <div class="mt-4">
                <a href="{{ route('menu.edit', $menu->id) }}" class="btn btn-warning"><i class="bi bi-pencil"></i> Edit Menu</a>
                <a href="{{ route('menu.index') }}" class="btn btn-secondary">Kembali ke Daftar Menu</a>
            </div>

        </div>
    </div>
</section>
@endsection
