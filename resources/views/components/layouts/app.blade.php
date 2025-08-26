<!DOCTYPE html>
<html lang="id" class="scroll-smooth">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta name="services-json" content="{{ url('/api/services.json') }}">
    <title>{{ $title ?? 'Sertifikasi & ISO' }}</title>
    @stack('head')

    {{-- Vite assets --}}
    @vite(['resources/css/app.css','resources/js/app.js'])

    {{-- Fonts & Icons (opsional tetap CDN) --}}
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@300;400;500;600;700;800&display=swap" rel="stylesheet">
    <link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css" rel="stylesheet">
    @stack('head')
</head>
<body class="font-sans bg-gray-50 text-slate-800 antialiased">
    {{ $slot }}
</body>
</html>
