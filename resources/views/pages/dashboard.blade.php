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
                            const salesData = [120, 190, 300, 250, 220, 400, 350];
                            const days = ['Senin', 'Selasa', 'Rabu', 'Kamis', 'Jumat', 'Sabtu', 'Minggu'];

                            new Chart(ctx, {
                                type: 'line',
                                data: {
                                    labels: days,
                                    datasets: [{
                                        label: 'Jumlah Penjualan',
                                        data: salesData,
                                        borderColor: '#a95e13',
                                        backgroundColor: 'rgba(169, 94, 19, 0.3)',
                                        fill: true,
                                        tension: 0.3
                                    }]
                                },
                                options: {
                                    responsive: true,
                                    plugins: {
                                        legend: {
                                            position: 'top',
                                        },
                                        title: {
                                            display: true,
                                            text: 'Statistik Penjualan 1 Minggu Terakhir'
                                        }
                                    },
                                    scales: {
                                        y: {
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

{{-- SCRIPT PERINGATAN STOK KRITIS (Dari kode sebelumnya) --}}
@if ($stokKritis->count() > 0)
<script>
    document.addEventListener('DOMContentLoaded', function() {
        // Daftar bahan yang stoknya kritis
        // const kritisItems = @json($stokKritis->pluck('nama', 'stok')); // Variabel ini tidak diperlukan di JS

        let htmlList = '<p>Bahan-bahan berikut memiliki stok di bawah batas minimum:</p><ul>';

        @foreach($stokKritis as $item)
            htmlList += `<li><strong>{{ $item->nama }}</strong> (Stok: {{ $item->stok }}, Min: {{ $item->stok_minimal }} {{ $item->satuan }})</li>`;
        @endforeach

        htmlList += '</ul><p>Mohon segera belanja.</p>';

        // Tampilkan peringatan menggunakan SweetAlert
        Swal.fire({
            title: 'Stok Bahan Kritis!',
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
