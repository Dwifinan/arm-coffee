@extends('components.layout')

@section('content')
<div class="pagetitle">
    <h1>Tambah Request Belanja</h1>
    <nav>
        <ol class="breadcrumb">
            <li class="breadcrumb-item"><a href="{{ url('/') }}">Home</a></li>
            <li class="breadcrumb-item"><a href="{{ route('belanja.index') }}">Belanja</a></li>
            <li class="breadcrumb-item active">Tambah</li>
        </ol>
    </nav>
</div>

<section class="section">
    <div class="card shadow-sm border-0">
        <div class="card-header bg-primary text-white py-3">
            <h6 class="m-0 font-weight-bold"><i class="bi bi-cart-plus me-2"></i>Form Request Belanja</h6>
        </div>
        <div class="card-body pt-4">

            @if(session('error'))
                <div class="alert alert-danger">{{ session('error') }}</div>
            @endif

            <form action="{{ route('belanja.store') }}" method="POST" id="belanjaForm">
                @csrf

                <div class="table-responsive mb-3">
                    <table class="table table-bordered align-middle" id="items-table">
                        <thead class="bg-light">
                            <tr>
                                <th style="width: 40%">Bahan Baku</th>
                                <th style="width: 20%">Jumlah Order</th>
                                <th style="width: 15%">Satuan</th>
                                <th style="width: 20%">Estimasi Harga (Rp)</th>
                                <th style="width: 5%">Aksi</th>
                            </tr>
                        </thead>
                        <tbody>
                            {{-- Rows added via JS --}}
                        </tbody>
                        <tfoot>
                            <tr class="fw-bold bg-light">
                                <td colspan="3" class="text-end">Total Estimasi:</td>
                                <td id="grand-total">Rp 0</td>
                                <td></td>
                            </tr>
                        </tfoot>
                    </table>
                </div>

                <button type="button" class="btn btn-success btn-sm mb-4" id="add-row-btn">
                    <i class="bi bi-plus-circle me-1"></i> Tambah Item
                </button>

                <div class="d-grid gap-2 d-md-flex justify-content-md-end">
                    <a href="{{ route('belanja.index') }}" class="btn btn-secondary">Batal</a>
                    <button type="submit" class="btn btn-primary"><i class="bi bi-save me-1"></i> Simpan Request</button>
                </div>
            </form>
        </div>
    </div>
</section>




{{-- Master Options for Select2 (Visible Select inside Hidden Div) --}}
<div style="display: none;">
    <select id="unique-bahan-options">
        <option value="">-- Pilih Bahan --</option>
        @forelse ($bahan as $b)
            <option value="{{ $b->id }}" 
                data-satuan="{{ $b->satuan }}" 
                data-satuan-beli="{{ $b->satuan_beli ?? $b->satuan }}"
                data-jumlah-satuan="{{ $b->jumlah_satuan ?? 1 }}"
                data-harga="{{ $b->harga_satuan ?? 0 }}">
                {{ $b->nama }} (Stok: {{ $b->stok }} {{ $b->satuan }})
            </option>
        @empty
            <option value="">-- TIDAK ADA DATA BAHAN (Hubungi Admin) --</option>
        @endforelse
    </select>
</div>

@endsection

@section('scriptjs')
<script>
    $(document).ready(function() {
        const tableBody = document.querySelector('#items-table tbody');
        const addBtn = document.getElementById('add-row-btn');
        const grandTotalEl = document.getElementById('grand-total');
        let rowCount = 0;

        // Init first row
        addRow();

        addBtn.addEventListener('click', addRow);

        function addRow() {
            const index = rowCount++;
            const tr = document.createElement('tr');
            tr.id = `row-${index}`;
            
            tr.innerHTML = `
                <td>
                    <select name="items[${index}][bahan_id]" class="form-select bahan-select select2-dynamic" required>
                        <!-- Options will be cloned here -->
                    </select>
                </td>
                <td>
                    <input type="number" name="items[${index}][jumlah]" class="form-control jumlah-input" min="1" required oninput="calculateRow(${index})">
                </td>
                <td><span class="satuan-badge badge bg-secondary">--</span></td>
                <td><input type="text" class="form-control-plaintext text-end est-price" value="0" readonly></td>
                <td>
                    <button type="button" class="btn btn-danger btn-sm" onclick="removeRow(${index})"><i class="bi bi-trash"></i></button>
                </td>
            `;
            tableBody.appendChild(tr);

            // Populate Options by Cloning
            const masterSelect = document.getElementById('unique-bahan-options');
            const newSelect = tr.querySelector('.select2-dynamic');
            
            if (masterSelect) {
                Array.from(masterSelect.options).forEach(opt => {
                    newSelect.add(opt.cloneNode(true));
                });
            }

            // Init Select2
            $(newSelect).select2({
                theme: 'bootstrap-5',
                width: '100%',
                placeholder: "-- Pilih Bahan --",
                allowClear: true
            });

            // Bind change event to call calculateRow
            $(newSelect).on('change', function() {
                calculateRow(index);
            });
        }

        window.removeRow = function(index) {
            const row = document.getElementById(`row-${index}`);
            if (!row) return;

            // Destroy Select2
            if ($(row).find('.select2-dynamic').data('select2')) {
                $(row).find('.select2-dynamic').select2('destroy');
            }
            
            if (tableBody.children.length > 1) {
                row.remove();
                calculateGrandTotal();
            } else {
                Swal.fire({
                    icon: 'warning',
                    title: 'Perhatian',
                    text: 'Minimal satu item harus ada.'
                });
            }
        }

        window.calculateRow = function(index) {
            const row = document.getElementById(`row-${index}`);
            if (!row) return;

            const select = row.querySelector('.bahan-select');
            const jumlahInput = row.querySelector('.jumlah-input');
            const satuanBadge = row.querySelector('.satuan-badge');
            const estPriceInput = row.querySelector('.est-price');

            const option = select.options[select.selectedIndex];
            
            // Update Satuan & Hint
            if (option.value) {
                let satuanMain = option.getAttribute('data-satuan'); // e.g. ml
                let satuanBeli = option.getAttribute('data-satuan-beli'); // e.g. Galon
                let factor = parseInt(option.getAttribute('data-jumlah-satuan')) || 1;

                if (factor > 1) {
                    satuanBadge.innerHTML = `<span class="badge bg-info text-dark">${satuanBeli}</span><br><small class="text-muted" style="font-size: 0.65em;">1 ${satuanBeli} = ${factor} ${satuanMain}</small>`;
                } else {
                    satuanBadge.innerHTML = `<span class="badge bg-secondary">${satuanMain}</span>`;
                }
                
                // Price Calculation (Input is in Buy Unit)
                // Existing data-harga is Price Per Main Unit (e.g. per ml)
                // So Price Per input = Price Per Main Unit * Factor
                const pricePerSmall = parseFloat(option.getAttribute('data-harga')) || 0;
                const pricePerBuy = pricePerSmall * factor;
                const jumlah = parseFloat(jumlahInput.value) || 0;
                const subtotal = pricePerBuy * jumlah;

                estPriceInput.value = formatRupiah(subtotal);
                estPriceInput.setAttribute('data-raw', subtotal); 

            } else {
                satuanBadge.innerText = '--';
                estPriceInput.value = 0;
                estPriceInput.setAttribute('data-raw', 0);
            }

            calculateGrandTotal();
        }

        function calculateGrandTotal() {
            let total = 0;
            document.querySelectorAll('.est-price').forEach(input => {
                total += parseFloat(input.getAttribute('data-raw')) || 0;
            });
            grandTotalEl.innerText = 'Rp ' + formatRupiah(total);
        }

        function formatRupiah(number) {
            return new Intl.NumberFormat('id-ID').format(number);
        }
    });
</script>
@endsection
