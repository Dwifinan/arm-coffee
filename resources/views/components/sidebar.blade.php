<aside id="sidebar" class="sidebar">

  <ul class="sidebar-nav" id="sidebar-nav">

    <li class="nav-item">
      <a class="nav-link {{ request()->is('dashboard') ? '' : 'collapsed' }}" href="{{ url('dashboard') }}">
        <i class="bi bi-house-heart-fill"></i>
        <span>Dashboard</span>
      </a>
    </li>
@if (auth()->user()->role === 'owner')

<li class="nav-item">
  <a class="nav-link {{ request()->is('users') ? '' : 'collapsed' }}" href="{{ url('users') }}">
    <i class="bi bi-person"></i>
    <span>Users</span>
  </a>
</li>
@endif

    <li class="nav-item">
      <a class="nav-link {{ request()->is('belanja') ? '' : 'collapsed' }}" href="{{ url('belanja') }}">
        <i class="bi bi-box-arrow-up"></i>
        <span>Belanja</span>
      </a>
    </li>

    @if (auth()->user()->role === 'owner')
    <li class="nav-item">
      <a class="nav-link {{ request()->is('bahan') ? '' : 'collapsed' }}" href="{{ url('bahan') }}">
        <i class="bi bi-box"></i>
        <span>Bahan</span>
      </a>
    </li>

    <li class="nav-item">
      <a class="nav-link {{ request()->is('menu') ? '' : 'collapsed' }}" href="{{ url('menu') }}">
        <i class="bi bi-book"></i>
        <span>Menu</span>
      </a>
    </li>

    @endif

    <li class="nav-item">
      <a class="nav-link {{ request()->is('produksi') ? '' : 'collapsed' }}" href="{{ url('produksi') }}">
        <i class="bi bi-boxes"></i>
        <span>Produksi</span>
      </a>
    </li>

  </ul>

</aside><!-- End Sidebar -->
