@extends('components.layout')

@section('content')
<div class="pagetitle">
    <h1>Edit Menu: {{ $menu->nama }}</h1>
    <nav>
        <ol class="breadcrumb">
            <li class="breadcrumb-item"><a href="{{ url('/') }}">Home</a></li>
            <li class="breadcrumb-item"><a href="{{ route('menu.index') }}">Menu</a></li>
            <li class="breadcrumb-item active">Edit</li>
        </ol>
    </nav>
</div>

<section class="section">
    <div class="card">
        <div class="card-body">
            <h5 class="card-title">Edit Definisi Menu dan Bahan Baku</h5>

            @if(session('error'))
                <div class="alert alert-danger alert-dismissible fade show" role="alert">
                    {{ session('error') }}
                    <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
                </div>
            @endif

            {{-- Form utama --}}
            <form action="{{ route('menu.update', $menu->id) }}" method="POST" class="row g-3">
                @csrf
                @method('PUT')

                {{-- Bagian 1: Data Menu --}}
                <div class="col-md-6">
                    <label for="nama" class="form-label">Nama Menu</label>
                    <input type="text" class="form-control @error('nama') is-invalid @enderror" id="nama" name="nama" value="{{ old('nama', $menu->nama) }}" required>
                    @error('nama')
                        <div class="invalid-feedback">{{ $message }}</div>
                    @enderror
                </div>

                <div class="col-md-6">
                    <label for="harga" class="form-label">Harga Jual (Rp)</label>
                    <input type="number" class="form-control @error('harga') is-invalid @enderror" id="harga" name="harga" value="{{ old('harga', $menu->harga) }}" required min="1000">
                    @error('harga')
                        <div class="invalid-feedback">{{ $message }}</div>
                    @enderror
                </div>

                <div class="col-12 mt-4">
                    <h6>Bahan Baku yang Digunakan</h6>
                    <p class="text-muted">Perbarui bahan baku dan jumlahnya untuk membuat satu porsi menu ini.</p>
                </div>

                {{-- Bagian 2: Detail Bahan (Tabel Dinamis) --}}
                <div class="col-12">
                    <table class="table table-bordered" id="bahan-table">
                        <thead>
                            <tr>
                                <th style="width: 50%">Bahan Baku</th>
                                <th style="width: 30%">Jumlah Digunakan</th>
                                <th style="width: 10%">Satuan</th>
                                <th style="width: 10%">Aksi</th>
                            </tr>
                        </thead>
                        <tbody>
                            @php
                                $detailIndex = 0;
                            @endphp
                            {{-- Loop data detail menu yang sudah ada --}}
                            @forelse ($menu->details as $detail)
                            <tr id="bahan-row-{{ $detailIndex }}">
                                <td>
                                    <select name="bahan_id[{{ $detailIndex }}]" class="form-select bahan-select @error('bahan_id.'.$detailIndex) is-invalid @enderror" data-index="{{ $detailIndex }}" required>
                                        <option value="">Pilih Bahan...</option>
                                        @foreach ($bahans as $bahan)
                                            <option value="{{ $bahan->id }}" data-satuan="{{ $bahan->satuan }}"
                                                    {{ old('bahan_id.'.$detailIndex, $detail->bahan_id) == $bahan->id ? 'selected' : '' }}>
                                                {{ $bahan->nama }} ({{ $bahan->kode }})
                                            </option>
                                        @endforeach
                                    </select>
                                    @error('bahan_id.'.$detailIndex)
                                        <div class="invalid-feedback">{{ $message }}</div>
                                    @enderror
                                </td>
                                <td>
                                    <input type="number" name="jumlah_bahan[{{ $detailIndex }}]" class="form-control @error('jumlah_bahan.'.$detailIndex) is-invalid @enderror"
                                           value="{{ old('jumlah_bahan.'.$detailIndex, $detail->jumlah_bahan) }}" min="1" required>
                                    @error('jumlah_bahan.'.$detailIndex)
                                        <div class="invalid-feedback">{{ $message }}</div>
                                    @enderror
                                </td>
                                <td>
                                    <span class="satuan-display" data-index="{{ $detailIndex }}">{{ $detail->bahan->satuan }}</span>
                                </td>
                                <td>
                                    <button type="button" class="btn btn-danger btn-sm" onclick="removeBahanRow({{ $detailIndex }})"><i class="bi bi-trash"></i></button>
                                </td>
                            </tr>
                            @php $detailIndex++; @endphp
                            @empty
                            {{-- Jika tidak ada detail, tampilkan baris kosong seperti create --}}
                            <tr id="bahan-row-0">
                                <td>
                                    <select name="bahan_id[0]" class="form-select bahan-select @error('bahan_id.0') is-invalid @enderror" data-index="0" required>
                                        <option value="">Pilih Bahan...</option>
                                        @foreach ($bahans as $bahan)
                                            <option value="{{ $bahan->id }}" data-satuan="{{ $bahan->satuan }}" {{ old('bahan_id.0') == $bahan->id ? 'selected' : '' }}>
                                                {{ $bahan->nama }} ({{ $bahan->kode }})
                                            </option>
                                        @endforeach
                                    </select>
                                    @error('bahan_id.0')
                                        <div class="invalid-feedback">{{ $message }}</div>
                                    @enderror
                                </td>
                                <td>
                                    <input type="number" name="jumlah_bahan[0]" class="form-control @error('jumlah_bahan.0') is-invalid @enderror" value="{{ old('jumlah_bahan.0') }}" min="1" required>
                                    @error('jumlah_bahan.0')
                                        <div class="invalid-feedback">{{ $message }}</div>
                                    @enderror
                                </td>
                                <td>
                                    <span class="satuan-display" data-index="0">--</span>
                                </td>
                                <td>
                                    <button type="button" class="btn btn-danger btn-sm" onclick="removeBahanRow(0)" disabled><i class="bi bi-trash"></i></button>
                                </td>
                            </tr>
                            @php $detailIndex = 1; @endphp
                            @endforelse
                        </tbody>
                    </table>
                    <button type="button" class="btn btn-success btn-sm" id="add-bahan-btn">
                        <i class="bi bi-plus-circle"></i> Tambah Bahan
                    </button>
                </div>

                {{-- Tombol Simpan --}}
                <div class="col-12 mt-4">
                    <button type="submit" class="btn btn-primary">Simpan Perubahan</button>
                    <a href="{{ route('menu.index') }}" class="btn btn-secondary">Batal</a>
                </div>
            </form>

        </div>
    </div>
</section>

{{-- SCRIPT JAVASCRIPT UNTUK FUNGSI DINAMIS --}}
<script>
    let rowCount = {{ $detailIndex }}; // Mulai dari jumlah detail yang ada
    const bahanTableBody = document.querySelector('#bahan-table tbody');
    const bahansData = @json($bahans);

    document.getElementById('add-bahan-btn').addEventListener('click', function() {
        addBahanRow();
    });

    function addBahanRow() {
        const newIndex = rowCount++;

        let selectOptions = '<option value="">Pilih Bahan...</option>';
        bahansData.forEach(bahan => {
            selectOptions += `<option value="${bahan.id}" data-satuan="${bahan.satuan}">${bahan.nama} (${bahan.kode})</option>`;
        });

        const newRow = document.createElement('tr');
        newRow.id = `bahan-row-${newIndex}`;
        newRow.innerHTML = `
            <td>
                <select name="bahan_id[${newIndex}]" class="form-select bahan-select" data-index="${newIndex}" required>
                    ${selectOptions}
                </select>
            </td>
            <td>
                <input type="number" name="jumlah_bahan[${newIndex}]" class="form-control" min="1" required>
            </td>
            <td>
                <span class="satuan-display" data-index="${newIndex}">--</span>
            </td>
            <td>
                <button type="button" class="btn btn-danger btn-sm" onclick="removeBahanRow(${newIndex})"><i class="bi bi-trash"></i></button>
            </td>
        `;
        bahanTableBody.appendChild(newRow);

        // Tambahkan event listener untuk baris baru
        newRow.querySelector('.bahan-select').addEventListener('change', updateSatuanDisplay);
    }

    function removeBahanRow(index) {
        const row = document.getElementById(`bahan-row-${index}`);
        if (row) {
            row.remove();
        }
    }

    function updateSatuanDisplay() {
        const select = this;
        const index = select.getAttribute('data-index');
        const selectedOption = select.options[select.selectedIndex];
        const displayElement = document.querySelector(`.satuan-display[data-index="${index}"]`);

        if (selectedOption && selectedOption.value) {
            const satuan = selectedOption.getAttribute('data-satuan');
            displayElement.textContent = satuan;
        } else {
            displayElement.textContent = '--';
        }
    }

    // Initialize event listeners for all existing rows
    document.querySelectorAll('.bahan-select').forEach(select => {
        select.addEventListener('change', updateSatuanDisplay);
        // Trigger change event to initialize satuan display based on existing data
        select.dispatchEvent(new Event('change'));
    });
</script>
@endsection
