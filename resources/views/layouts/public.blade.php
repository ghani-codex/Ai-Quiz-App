<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="utf-8">
  <meta content="width=device-width, initial-scale=1.0" name="viewport">
  <title>@yield('title', config('app.name', 'AI Quiz App')) - Smart Quizzes from Any Lecture</title>
  <meta name="description" content="@yield('description', 'AI-powered e-learning platform that auto-generates quizzes from lecture content and gives students personalised feedback.')">
  <meta name="csrf-token" content="{{ csrf_token() }}">

  <link href="{{ asset('flexstart/img/favicon.png') }}" rel="icon">
  <link href="{{ asset('flexstart/img/apple-touch-icon.png') }}" rel="apple-touch-icon">

  <link rel="preconnect" href="https://fonts.googleapis.com">
  <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
  <link href="https://fonts.googleapis.com/css2?family=Roboto:ital,wght@0,300;0,400;0,500;0,700;0,900&family=Poppins:ital,wght@0,400;0,500;0,600;0,700&family=Nunito:ital,wght@0,400;0,600;0,700;0,800&display=swap" rel="stylesheet">

  <link href="{{ asset('flexstart/vendor/bootstrap/css/bootstrap.min.css') }}" rel="stylesheet">
  <link href="{{ asset('flexstart/vendor/bootstrap-icons/bootstrap-icons.css') }}" rel="stylesheet">
  <link href="{{ asset('flexstart/vendor/aos/aos.css') }}" rel="stylesheet">
  <link href="{{ asset('flexstart/vendor/glightbox/css/glightbox.min.css') }}" rel="stylesheet">
  <link href="{{ asset('flexstart/vendor/swiper/swiper-bundle.min.css') }}" rel="stylesheet">

  <link href="{{ asset('flexstart/css/main.css') }}" rel="stylesheet">

  @stack('head')
</head>
<body class="@yield('body_class', 'index-page')">

  @include('partials.flexstart.header')

  <main class="main">
    @yield('content')
  </main>

  @include('partials.flexstart.footer')

  <a href="#" id="scroll-top" class="scroll-top d-flex align-items-center justify-content-center"><i class="bi bi-arrow-up-short"></i></a>

  <script src="{{ asset('flexstart/vendor/bootstrap/js/bootstrap.bundle.min.js') }}"></script>
  <script src="{{ asset('flexstart/vendor/aos/aos.js') }}"></script>
  <script src="{{ asset('flexstart/vendor/glightbox/js/glightbox.min.js') }}"></script>
  <script src="{{ asset('flexstart/vendor/purecounter/purecounter_vanilla.js') }}"></script>
  <script src="{{ asset('flexstart/vendor/imagesloaded/imagesloaded.pkgd.min.js') }}"></script>
  <script src="{{ asset('flexstart/vendor/isotope-layout/isotope.pkgd.min.js') }}"></script>
  <script src="{{ asset('flexstart/vendor/swiper/swiper-bundle.min.js') }}"></script>
  <script src="{{ asset('flexstart/js/main.js') }}"></script>

  @stack('scripts')
</body>
</html>
