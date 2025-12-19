@extends('components.layout')

@section('content')
<div class="pagetitle">
    <h1>Selesaikan Belanja</h1>
    <nav>
        <ol class="breadcrumb">
            <li class="breadcrumb-item"><a href="{{ url('/') }}">Home</a></li>
            <li class="breadcrumb-item"><a href="{{ route('belanja.index') }}">Belanja</a></li>
            <li class="breadcrumb-item active">Proses</li>
        </ol>
    </nav>
</div>

<section class="section">
    <div class="card shadow border-0">
        <div class="card-header bg-success text-white py-3">
            <h6 class="m-0 font-weight-bold"><i class="bi bi-check-circle me-2"></i>Konfirmasi & Upload Bukti</h6>
        </div>
        <div class="card-body pt-4">

            @if(session('error'))
                <div class="alert alert-danger">{{ session('error') }}</div>
            @endif

            <div class="alert alert-info">
                <strong>Kode Request: {{ $belanja->kode }}</strong> created by {{ $belanja->userRequest->username ?? 'Unknown' }}<br>
                Silakan isi data realisasi belanja di bawah ini.
            </div>

            <form action="{{ route('belanja.update', $belanja->id) }}" method="POST" enctype="multipart/form-data" id="completeForm">
                @csrf
                @method('PUT')

                <div class="table-responsive mb-4">
                    <table class="table table-bordered align-middle">
                        <thead class="bg-light">
                            <tr>
                                <th style="width: 30%">Bahan</th>
                                <th style="width: 10%">Satuan</th>
                                <th style="width: 10%">Jml Est</th>
                                <th style="width: 15%">Jml Akhir</th>
                                <th style="width: 20%">Total Harga (Rp)</th>
                                <th style="width: 15%">Expired</th>
                            </tr>
                        </thead>
                        <tbody>
                            @foreach($belanja->details as $d)
                            <tr class="item-row">
                                <td>
                                    {{ $d->bahan->nama }}
                                    <input type="hidden" name="items[{{ $d->id }}][id]" value="{{ $d->id }}">
                                </td>
                                <td>
                                    @php
                                        $satuanBeli = $d->bahan->satuan_beli ?? $d->bahan->satuan;
                                        $factor = $d->bahan->jumlah_satuan ?? 1;
                                    @endphp
                                    
                                    <span class="badge {{ $factor > 1 ? 'bg-info text-dark' : 'bg-secondary' }}">
                                        {{ $satuanBeli }}
                                    </span>
                                    @if($factor > 1)
                                        <div class="small text-muted" style="font-size: 0.7em;">
                                            (Isi {{ $factor }} {{ $d->bahan->satuan }})
                                        </div>
                                    @endif
                                </td>
                                <td>{{ $d->jumlah_estimasi }}</td>
                                <td>
                                    <input type="number" name="items[{{ $d->id }}][jumlah_akhir]" class="form-control jumlah-input" value="{{ old("items.{$d->id}.jumlah_akhir", $d->jumlah_estimasi) }}" min="1" required oninput="calculateTotal()">
                                </td>
                                <td>
                                    <input type="text" name="items[{{ $d->id }}][subtotal_akhir]" class="form-control price-input" value="{{ old("items.{$d->id}.subtotal_akhir", number_format($d->subtotal_estimasi, 0, ',', '.')) }}" required>
                                </td>
                                <td>
                                    <input type="date" name="items[{{ $d->id }}][expired]" class="form-control" value="{{ old("items.{$d->id}.expired") }}">
                                </td>
                            </tr>
                            @endforeach
                        </tbody>
                        <tfoot>
                            <tr class="fw-bold bg-light">
                                <td colspan="4" class="text-end">Total Akhir:</td>
                                <td colspan="2" id="grand-total">Rp 0</td>
                            </tr>
                        </tfoot>
                    </table>
                </div>

                <div class="mb-4">
                    <label for="foto_bukti" class="form-label fw-bold">Upload Foto Struk / Bukti Belanja</label>
                    <input class="form-control" type="file" id="foto_bukti" name="foto_bukti" accept="image/*" required>
                    <div class="form-text">Format: JPG, PNG. Maks 2MB.</div>
                </div>

                <div class="d-grid gap-2 d-md-flex justify-content-md-end">
                    <a href="{{ route('belanja.index') }}" class="btn btn-secondary">Batal</a>
                    <button type="submit" class="btn btn-success"><i class="bi bi-check2-circle me-1"></i> Simpan & Selesai</button>
                </div>
            </form>
        </div>
    </div>
</section>

@endsection

@section('scriptjs')
<script>
    document.addEventListener("DOMContentLoaded", function() {
        calculateTotal();

        // Attach event listeners for formatting
        document.querySelectorAll('.price-input').forEach(input => {
            // Format initial value if exists
            if(input.value) input.value = formatRupiah(input.value);

            input.addEventListener('keyup', function(e) {
                this.value = formatRupiah(this.value);
                calculateTotal();
            });
        });
    });

    function formatRupiah(angka, prefix) {
        var number_string = angka.replace(/[^,\d]/g, '').toString(),
            split = number_string.split(','),
            sisa = split[0].length % 3,
            rupiah = split[0].substr(0, sisa),
            ribuan = split[0].substr(sisa).match(/\d{3}/gi);

        if (ribuan) {
            separator = sisa ? '.' : '';
            rupiah += separator + ribuan.join('.');
        }

        rupiah = split[1] != undefined ? rupiah + ',' + split[1] : rupiah;
        return prefix == undefined ? rupiah : (rupiah ? 'Rp. ' + rupiah : '');
    }

    function calculateTotal() {
        let total = 0;
        document.querySelectorAll('.item-row').forEach(row => {
            // Get raw number from formatted string
            const valStr = row.querySelector('.price-input').value.replace(/\./g, '');
            const subtotal = parseFloat(valStr) || 0;
            total += subtotal;
        });

        const formatted = new Intl.NumberFormat('id-ID').format(total);
        document.getElementById('grand-total').innerText = 'Rp ' + formatted;
    }
</script>
@endsection
