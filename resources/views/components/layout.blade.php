<!DOCTYPE html>
<html lang="en">

<head>
    @include('components.head')
</head>

<body>

  <!-- ======= Header ======= -->
    @include('components.header')
    @yield('head')

  <!-- ======= Sidebar ======= -->
  @include('components.sidebar')

  <main id="main" class="main">

    @yield('content')

  </main><!-- End #main -->

  <!-- ======= Footer ======= -->
  @include('components.footer')

  <a href="#" class="back-to-top d-flex align-items-center justify-content-center"><i class="bi bi-arrow-up-short"></i></a>

  <!-- Vendor JS Files -->
 @include('components.script')
 @yield('scriptjs')

</body>

</html>
