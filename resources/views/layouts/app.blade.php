<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <title>GarridoFinance</title>
    
    {{-- CSS GENERAL --}}
    <link rel="stylesheet" href="{{ asset('css/style.css') }}">
    
    <!-- Bootstrap 5 CSS -->
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">

</head>
<body>
    <nav class="navbar navbar-dark bg-dark px-3">
        <span class="navbar-brand">GarridoFinance</span>
    </nav>

    <div class="contenedor">
         @yield('content')
    </div>
   
     <!-- Bootstrap JS -->
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>