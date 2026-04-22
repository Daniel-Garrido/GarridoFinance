<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <title>GarridoFinance</title>
    
    {{-- CSS GENERAL --}}
    <link rel="stylesheet" href="{{ asset('css/style.css') }}">
    <link rel="stylesheet" href="{{ asset('css/dashboard.css') }}">
    
    <!-- Bootstrap 5 CSS -->
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">

    {{-- libreria de chart js --}}
    <script src="https://cdn.jsdelivr.net/npm/chart.js"></script>

</head>
<body>
   <nav class="content-nav navbar navbar-dark px-3">
    
    <div class="content-nav-container d-flex align-items-center">
        
        <!-- Logo -->
        <img class="content-nav-img" src="{{ asset('images/Logo2.png') }}" alt="Logo">
        <!-- Título de la app -->
        <h2 class="navbar-brand mb-0 ">GarridoFinance</h2>

    </div>
</nav>

    <div class="contenedor">
         @yield('content')
    </div>

    
     <!-- Bootstrap JS -->
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>