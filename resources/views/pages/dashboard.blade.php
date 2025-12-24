@extends('components.layout')

@section('content')
<div class="pagetitle">
    <h1>Dashboard Penjualan</h1>
    <nav>
        <ol class="breadcrumb">
            <li class="breadcrumb-item"><a href="{{ url('/') }}">Home</a></li>
            <li class="breadcrumb-item active">Dashboard</li>
        </ol>
    </nav>
</div><!-- End Page Title -->

<section class="section dashboard">
    <div class="row">

        <div class="col-lg-12">
            <div class="card">
                <div class="card-body">
                    <h5 class="card-title">Grafik Penjualan Mingguan</h5>

                    <!-- Chart -->
                    <div style="position: relative; height: 350px; width: 100%">
                        <canvas id="salesChart"></canvas>
                    </div>

                    <script>
                        document.addEventListener("DOMContentLoaded", () => {
                            // Cek apakah Chart.js tersedia sebelum mencoba menggunakannya
                            if (typeof Chart === 'undefined') {
                                console.error("Chart.js library not found. Please ensure it is loaded.");
                                return;
                            }

                            const ctx = document.querySelector('#salesChart');
                            if (!ctx) return;

                            // Data penjualan (contoh data dummy)
                            // Data dari controller
                            const datasets = @json($chartDatasets);
                            const days = @json($chartDates);

                            new Chart(ctx, {
                                type: 'bar',
                                data: {
                                    labels: days,
                                    datasets: datasets
                                },
                                options: {
                                    responsive: true,
                                    maintainAspectRatio: false, // Force height
                                    interaction: {
                                        mode: 'index',
                                        intersect: false,
                                    },
                                    plugins: {
                                        legend: {
                                            position: 'top',
                                        },
                                        title: {
                                            display: true,
                                            text: 'Statistik Penjualan Per Menu (1 Minggu Terakhir)'
                                        },
                                        tooltip: {
                                            callbacks: {
                                                footer: function(tooltipItems) {
                                                    let total = 0;
                                                    tooltipItems.forEach(function(tooltipItem) {
                                                        total += tooltipItem.parsed.y;
                                                    });
                                                    return 'Total: ' + total;
                                                }
                                            }
                                        }
                                    },
                                    scales: {
                                        x: {
                                            stacked: true,
                                        },
                                        y: {
                                            stacked: true,
                                            beginAtZero: true
                                        }
                                    }
                                }
                            });
                        });
                    </script>
                    <!-- End Chart -->

                </div>
            </div>
        </div>
    </div>

    {{-- New Cards Row --}}
    <div class="row mt-4">
        {{-- Best Selling Menu Card --}}
        <div class="col-lg-6">
            <div class="card h-100">
                <div class="card-body">
                    <h5 class="card-title">Menu Terlaris (7 Hari Terakhir)</h5>
                    <div class="table-responsive">
                        <table class="table table-hover">
                            <thead>
                                <tr>
                                    <th scope="col">#</th>
                                    <th scope="col">Menu</th>
                                    <th scope="col" class="text-center">Terjual</th>
                                </tr>
                            </thead>
                            <tbody>
                                @forelse($bestSelling as $index => $item)
                                    <tr>
                                        <th scope="row">{{ $index + 1 }}</th>
                                        <td>{{ $item->menu->nama ?? 'Unknown' }}</td>
                                        <td class="text-center"><span class="badge bg-success">{{ $item->total_sold }}</span></td>
                                    </tr>
                                @empty
                                    <tr>
                                        <td colspan="3" class="text-center text-muted">Belum ada penjualan minggu ini.</td>
                                    </tr>
                                @endforelse
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>
        </div>

        {{-- (Owner Only) --}}
        @if (Auth::user()->role === 'owner' && ($stokKritis->count() > 0 || $expiringItems->count() > 0 || (isset($expiredItems) && $expiredItems->count() > 0)))
        {{-- Critical Stock Card --}}
        <div class="col-lg-6">
            <div class="card h-100">
                <div class="card-body">
                    <h5 class="card-title">Stok Kritis <span class="badge bg-danger">{{ $stokKritis->count() }} Item</span></h5>
                    <div class="table-responsive">
                        <table class="table table-hover">
                            <thead>
                                <tr>
                                    <th scope="col">Bahan</th>
                                    <th scope="col" class="text-center">Stok</th>
                                    <th scope="col" class="text-center">Min</th>
                                    <th scope="col" class="text-end">Aksi</th>
                                </tr>
                            </thead>
                            <tbody>
                                @forelse($stokKritis->take(5) as $item)
                                    <tr>
                                        <td><span class="text-danger fw-bold">{{ $item->nama }}</span></td>
                                        <td class="text-center">{{ $item->stok }} {{ $item->satuan }}</td>
                                        <td class="text-center">{{ $item->stok_minimal }} {{ $item->satuan }}</td>
                                        <td class="text-end">
                                             <a href="{{ route('belanja.create') }}" class="btn btn-sm btn-outline-primary">Belanja</a>
                                        </td>
                                    </tr>
                                @empty
                                    <tr>
                                        <td colspan="4" class="text-center text-success">Stok aman. Tidak ada stok kritis.</td>
                                    </tr>
                                @endforelse
                            </tbody>
                        </table>
                        @if($stokKritis->count() > 5)
                            <div class="text-center mt-2">
                                <a href="{{ route('bahan.index') }}" class="small">Lihat Semua stok kritis...</a>
                            </div>
                        @endif
                    </div>
                </div>
            </div>
        </div>
    </div>
       
       
       <script>
    document.addEventListener('DOMContentLoaded', function() {
        let htmlList = '';

        @if(isset($expiredItems) && $expiredItems->count() > 0)
            htmlList += '<p class="text-danger"><strong>SUDAH KADALUARSA (Expired):</strong></p><ul>';
            @foreach($expiredItems as $item)
                htmlList += `<li class="text-danger d-flex justify-content-between align-items-center">
                    <span><strong>{{ $item->bahan->nama }}</strong> (Exp: {{ $item->expired->format('d M Y') }})</span>
                    <a href="{{ route('bahan.index') }}?open_detail={{ $item->bahan_id }}" class="btn btn-sm btn-outline-danger ms-2" style="font-size: 0.7rem; padding: 2px 5px;">ATUR</a>
                </li>`;
            @endforeach
            htmlList += '</ul><hr>';
        @endif

        @if($stokKritis->count() > 0)
            htmlList += '<p><strong>Stok Kritis:</strong></p><ul>';
            @foreach($stokKritis as $item)
                htmlList += `<li><strong>{{ $item->nama }}</strong> (Stok: {{ $item->stok }}, Min: {{ $item->stok_minimal }} {{ $item->satuan }})</li>`;
            @endforeach
            htmlList += '</ul>';
        @endif

        @if($expiringItems->count() > 0)
            htmlList += '<hr><p><strong>Mendekati Expired (7 Hari):</strong></p><ul>';
            @foreach($expiringItems as $item)
                @php
                    $daysLeft = \Carbon\Carbon::now()->startOfDay()->diffInDays($item->expired->startOfDay(), false);
                @endphp
                htmlList += `<li><strong>{{ $item->bahan->nama }}</strong> (Exp: {{ $item->expired->format('d M Y') }} - {{ $daysLeft }} hari lagi)</li>`;
            @endforeach
            htmlList += '</ul>';
        @endif
        
        htmlList += '<p>Mohon segera tindak lanjuti.</p>';

        // Tampilkan peringatan menggunakan SweetAlert
        // Tampilkan peringatan menggunakan SweetAlert
        Swal.fire({
            title: 'Peringatan Stok!',
            icon: 'warning',
            html: htmlList,
            showCancelButton: true,
            confirmButtonText: 'Buat Request Otomatis',
            cancelButtonText: 'Tutup',
            confirmButtonColor: '#28a745', // Green for positive action
            cancelButtonColor: '#6c757d',
        }).then((result) => {
            if (result.isConfirmed) {
                // AJAX call to auto-request endpoint
                Swal.fire({
                    title: 'Memproses...',
                    text: 'Sedang membuat request belanja...',
                    allowOutsideClick: false,
                    didOpen: () => {
                        Swal.showLoading();
                    }
                });

                fetch('{{ route('belanja.auto-request') }}', {
                    method: 'POST',
                    headers: {
                        'X-CSRF-TOKEN': '{{ csrf_token() }}',
                        'Content-Type': 'application/json'
                    }
                })
                .then(response => response.json())
                .then(data => {
                    if (data.success) {
                        Swal.fire({
                            title: 'Berhasil!',
                            text: data.message,
                            icon: 'success'
                        }).then(() => {
                            window.location.href = '{{ route('belanja.index') }}';
                        });
                    } else {
                        Swal.fire('Gagal!', data.message, 'error');
                    }
                })
                .catch(error => {
                    console.error('Error:', error);
                    Swal.fire('Error!', 'Terjadi kesalahan sistem.', 'error');
                });
            }
        });
    });
</script>
@endif

@endsection
