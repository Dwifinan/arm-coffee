@extends('components.layout')

@section('content')
<div class="pagetitle">
    <h1>Tambah Menu Baru</h1>
    <nav>
        <ol class="breadcrumb">
            <li class="breadcrumb-item"><a href="{{ url('/') }}">Home</a></li>
            <li class="breadcrumb-item"><a href="{{ route('menu.index') }}">Menu</a></li>
            <li class="breadcrumb-item active">Tambah</li>
        </ol>
    </nav>
</div>

<section class="section">
    <div class="card">
        <div class="card-body">
            <h5 class="card-title">Definisi Menu dan Bahan Baku</h5>

            @if(session('error'))
                <div class="alert alert-danger alert-dismissible fade show" role="alert">
                    {{ session('error') }}
                    <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
                </div>
            @endif

            {{-- Form utama --}}
            <form action="{{ route('menu.store') }}" method="POST" class="row g-3">
                @csrf

                {{-- Bagian 1: Data Menu --}}
                <div class="col-md-6">
                    <label for="nama" class="form-label">Nama Menu</label>
                    <input type="text" class="form-control @error('nama') is-invalid @enderror" id="nama" name="nama" value="{{ old('nama') }}" autocomplete="off" required>
                    @error('nama')
                        <div class="invalid-feedback">{{ $message }}</div>
                    @enderror
                </div>

                <div class="col-md-6">
                    <label for="harga" class="form-label">Harga Jual (Rp)</label>
                    <input type="number" class="form-control @error('harga') is-invalid @enderror" id="harga" name="harga" value="{{ old('harga') }}" autocomplete="off" required min="1000">
                    @error('harga')
                        <div class="invalid-feedback">{{ $message }}</div>
                    @enderror
                </div>

                <div class="col-12 mt-4">
                    <h6>Bahan Baku yang Digunakan</h6>
                    <p class="text-muted">Tambahkan semua bahan baku dan jumlahnya untuk membuat satu porsi menu ini.</p>
                </div>

                {{-- Bagian 2: Detail Bahan (Tabel Dinamis) --}}
                <div class="col-12">
                    <div class="table-responsive">
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
                                {{-- Baris Bahan Pertama (Template) --}}
                                <tr id="bahan-row-0">
                                    <td>
                                        <select name="bahan_id[0]" class="form-select bahan-select select2 @error('bahan_id.0') is-invalid @enderror" data-index="0" required>
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
                                        <input type="number" name="jumlah_bahan[0]" class="form-control @error('jumlah_bahan.0') is-invalid @enderror" value="{{ old('jumlah_bahan.0') }}" min="1" autocomplete="off" required>
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
                            </tbody>
                        </table>
                    </div>
                    <button type="button" class="btn btn-success btn-sm" id="add-bahan-btn">
                        <i class="bi bi-plus-circle"></i> Tambah Bahan
                    </button>
                </div>

                {{-- Tombol Simpan --}}
                <div class="col-12 mt-4">
                    <button type="submit" class="btn btn-primary">Simpan Menu</button>
                    <a href="{{ route('menu.index') }}" class="btn btn-secondary">Batal</a>
                </div>
            </form>

        </div>
    </div>
</section>

{{-- Master Options for Select2 (Visible Select inside Hidden Div) --}}
<div style="display: none;">
    <select id="menu-bahan-options">
        <option value="">Pilih Bahan...</option>
        @foreach ($bahans as $bahan)
            <option value="{{ $bahan->id }}" data-satuan="{{ $bahan->satuan }}">
                {{ $bahan->nama }} ({{ $bahan->kode }})
            </option>
        @endforeach
    </select>
</div>

{{-- SCRIPT JAVASCRIPT UNTUK FUNGSI DINAMIS --}}
<script>
    let rowCount = 1;
    const bahanTableBody = document.querySelector('#bahan-table tbody');

    document.getElementById('add-bahan-btn').addEventListener('click', function() {
        addBahanRow();
    });

    function addBahanRow() {
        const newIndex = rowCount++;
        
        const newRow = document.createElement('tr');
        newRow.id = `bahan-row-${newIndex}`;
        newRow.innerHTML = `
            <td>
                <select name="bahan_id[${newIndex}]" class="form-select bahan-select select2-dynamic" data-index="${newIndex}" required>
                    <!-- Options will be cloned here -->
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

        // Populate Options by Cloning
        const masterSelect = document.getElementById('menu-bahan-options');
        const newSelect = newRow.querySelector('.select2-dynamic');
        
        if (masterSelect) {
            Array.from(masterSelect.options).forEach(opt => {
                newSelect.add(opt.cloneNode(true));
            });
        }

        // Init Select2 on new element
        $(newSelect).select2({
            theme: 'bootstrap-5',
            width: '100%',
            placeholder: "Pilih Bahan...",
            allowClear: true
        });

        // Add event listener (using jQuery for Select2 compatibility)
        $(newRow).find('.bahan-select').on('change', updateSatuanDisplay);
    }

    function removeBahanRow(index) {
        const row = document.getElementById(`bahan-row-${index}`);
        if (row) {
            row.remove();
        }
    }

    function updateSatuanDisplay() {
        // For Select2, 'this' refers to the select element
        const select = this;
        const index = select.getAttribute('data-index');
        // Get selected option using Select2/jQuery way or standard way (Select2 updates the DOM)
        const selectedOption = select.options[select.selectedIndex];
        const displayElement = document.querySelector(`.satuan-display[data-index="${index}"]`);

        if (selectedOption && selectedOption.value) {
            const satuan = selectedOption.getAttribute('data-satuan');
            displayElement.textContent = satuan;
        } else {
            displayElement.textContent = '--';
        }
    }

    // Initialize event listener for the first row
    $('.bahan-select').on('change', updateSatuanDisplay);

    // Initial check (in case of validation error return)
    $('.bahan-select').trigger('change');
</script>
@endsection
