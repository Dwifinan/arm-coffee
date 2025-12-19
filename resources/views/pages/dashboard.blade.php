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
                    <canvas id="salesChart" style="max-height: 400px;"></canvas>

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
</section>

{{-- SCRIPT PERINGATAN STOK KRITIS (Owner Only) --}}
@if (Auth::user()->role === 'owner' && ($stokKritis->count() > 0 || $expiringItems->count() > 0))
<script>
    document.addEventListener('DOMContentLoaded', function() {
        let htmlList = '';

        @if($stokKritis->count() > 0)
            htmlList += '<p><strong>Stok Kritis:</strong></p><ul>';
            @foreach($stokKritis as $item)
                htmlList += `<li><strong>{{ $item->nama }}</strong> (Stok: {{ $item->stok }}, Min: {{ $item->stok_minimal }} {{ $item->satuan }})</li>`;
            @endforeach
            htmlList += '</ul>';
        @endif

        @if($expiringItems->count() > 0)
            htmlList += '<hr><p><strong>Mendekati Expired (2 Hari):</strong></p><ul>';
            @foreach($expiringItems as $item)
                htmlList += `<li><strong>{{ $item->bahan->nama }}</strong> (Exp: {{ $item->expired->format('d M Y') }})</li>`;
            @endforeach
            htmlList += '</ul>';
        @endif
        
        htmlList += '<p>Mohon segera tindak lanjuti.</p>';

        // Tampilkan peringatan menggunakan SweetAlert
        Swal.fire({
            title: 'Peringatan Stok!',
            icon: 'warning',
            html: htmlList,
            showCancelButton: true,
            confirmButtonText: 'Lihat Daftar Bahan',
            cancelButtonText: 'Tutup',
        }).then((result) => {
            if (result.isConfirmed) {
                // Arahkan ke halaman Bahan jika tombol diklik
                window.location.href = '{{ route('bahan.index') }}';
            }
        });
    });
</script>
@endif

@endsection
