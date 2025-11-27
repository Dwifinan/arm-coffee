@extends('components.layout')

@section('content')
<div class="pagetitle">
    <h1>Request & Manajemen Aktif</h1>
    <nav>
        <ol class="breadcrumb">
            <li class="breadcrumb-item"><a href="{{ url('/') }}">Home</a></li>
            <li class="breadcrumb-item"><a href="{{ route('belanja.index') }}">Histori</a></li>
            <li class="breadcrumb-item active">Request Aktif</li>
        </ol>
    </nav>
</div>

<section class="section">
    <div class="row">
        
        {{-- Menampilkan pesan dari controller --}}
        @if(session('success'))
            <div class="col-12">
                <div class="alert alert-success alert-dismissible fade show" role="alert">
                    {{ session('success') }}
                    <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
                </div>
            </div>
        @endif
        @if(session('error'))
            <div class="col-12">
                <div class="alert alert-danger alert-dismissible fade show" role="alert">
                    {{ session('error') }}
                    <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
                </div>
            </div>
        @endif

        {{-- ========================================================= --}}
        {{-- TAMPILAN OWNER (Form Tambah dan Daftar Aktif, 2 Kolom) --}}
        {{-- ========================================================= --}}
        @if(auth()->user()->role === 'owner')
        <div class="col-lg-6">
            <div class="card">
                <div class="card-body">
                    <h5 class="card-title">Tambah Request Belanja</h5>

                    @if ($errors->any())
                        <div class="alert alert-danger">
                            Gagal menambahkan request belanja. Silakan periksa input Anda.
                        </div>
                    @endif

                    {{-- Form Tambah Request --}}
                    <form method="POST" action="{{ route('belanja.tambah') }}">
                        @csrf
                        <div class="mb-3">
                            <label for="bahan_id" class="form-label">Pilih Bahan</label>
                            {{-- Asumsi $bahan dikirim dari Controller create() --}}
                            <select class="form-select @error('bahan_id') is-invalid @enderror" name="bahan_id" required>
                                <option value="">-- Pilih Bahan --</option>
                                {{-- $bahan seharusnya dikirim dari controller request() --}}
                                @foreach($bahan as $b)
                                    <option value="{{ $b->id }}" {{ old('bahan_id') == $b->id ? 'selected' : '' }}>
                                        {{ $b->nama }} (Stok: {{ $b->stok ? $b->stok: '0'}} {{ $b->satuan }})
                                    </option>
                                @endforeach
                            </select>
                            @error('bahan_id') <div class="invalid-feedback">{{ $message }}</div> @enderror
                        </div>

                        <div class="mb-3">
                            <label for="jumlah" class="form-label">Jumlah</label>
                            <input type="number" class="form-control @error('jumlah') is-invalid @enderror" name="jumlah" value="{{ old('jumlah') }}" required min="1">
                            @error('jumlah') <div class="invalid-feedback">{{ $message }}</div> @enderror
                        </div>

                        <button type="submit" class="btn btn-primary">Tambah Request</button>
                    </form>
                </div>
            </div>
        </div>
        
        <div class="col-lg-6">
            <div class="card">
                <div class="card-body">
                    <h5 class="card-title">Daftar Request Aktif (Owner)</h5>
                    <table class="table table-bordered">
                        <thead>
                            <tr>
                                <th>Bahan</th>
                                <th>Jumlah</th>
                                <th>Aksi</th>
                            </tr>
                        </thead>
                        <tbody>
                            {{-- $requestList dikirim dari BelanjaController@request --}}
                            @forelse($requestList as $r)
                                <tr>
                                    <td>{{ $r->bahan->nama }}</td>
                                    <td>{{ $r->jumlah }} {{ $r->bahan->satuan }}</td>
                                    <td>
                                        <form method="POST" action="{{ route('belanja.hapus', $r->id) }}">
                                            @csrf
                                            @method('DELETE')
                                            <button class="btn btn-danger btn-sm" onclick="return confirm('Yakin hapus request ini?');">Hapus</button>
                                        </form>
                                    </td>
                                </tr>
                            @empty
                                <tr><td colspan="3" class="text-center">Belum ada request belanja yang aktif.</td></tr>
                            @endforelse
                        </tbody>
                    </table>
                </div>
            </div>
        </div>
        
        {{-- ========================================================= --}}
        {{-- TAMPILAN STAFF (Daftar Tugas Aktif, 1 Kolom) --}}
        {{-- ========================================================= --}}
        @elseif(auth()->user()->role === 'staff')
        <div class="col-lg-12">
            <div class="card">
                <div class="card-body">
                    <h5 class="card-title">Tugas Belanja yang Belum Selesai</h5>
                    <p class="text-muted">Klik "Selesai" setelah bahan baku berhasil dibeli.</p>
                    <table class="table table-striped">
                        <thead>
                            <tr>
                                <th>Bahan</th>
                                <th>Jumlah</th>
                                <th>Aksi</th>
                            </tr>
                        </thead>
                        <tbody>
                            {{-- $requestList dikirim dari BelanjaController@request --}}
                            @forelse($requestList as $r)
                                <tr>
                                    <td>{{ $r->bahan->nama }}</td>
                                    <td>{{ $r->jumlah }} {{ $r->bahan->satuan }}</td>
                                    <td>
                                        <form method="POST" action="{{ route('belanja.selesai', $r->id) }}">
                                            @csrf
                                            <button class="btn btn-success btn-sm" onclick="return confirm('Selesaikan belanja dan tambahkan stok {{ $r->bahan->nama }}?');">Selesai</button>
                                        </form>
                                    </td>
                                </tr>
                            @empty
                                <tr><td colspan="3" class="text-center">Tidak ada tugas belanja dari owner.</td></tr>
                            @endforelse
                        </tbody>
                    </table>
                </div>
            </div>
        </div>
        @endif

    </div>
</section>
@endsection