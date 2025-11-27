@extends('components.layout')

@section('content')
<div class="pagetitle">
  <h1>Produksi</h1>
  <nav>
    <ol class="breadcrumb">
      <li class="breadcrumb-item"><a href="{{ url('/') }}">Home</a></li>
      <li class="breadcrumb-item active">Produksi</li>
    </ol>
  </nav>
</div>

<section class="section">
  <div class="card">
    <div class="card-body">
      <h5 class="card-title">Data Produksi Harian</h5>
      <p>Contoh data hasil produksi minuman kopi harian.</p>

      <table class="table table-hover">
        <thead>
          <tr>
            <th>#</th>
            <th>Tanggal</th>
            <th>Menu</th>
            <th>Jumlah Diproduksi</th>
          </tr>
        </thead>
        <tbody>
          <tr>
            <td>1</td>
            <td>2025-10-14</td>
            <td>Espresso</td>
            <td>30 gelas</td>
          </tr>
          <tr>
            <td>2</td>
            <td>2025-10-14</td>
            <td>Latte</td>
            <td>45 gelas</td>
          </tr>
        </tbody>
      </table>
    </div>
  </div>
</section>
@endsection
