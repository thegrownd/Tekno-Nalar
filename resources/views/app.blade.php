<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta name="description" content="TeknoNalar - Platform edukasi teknologi, cyber security, dan pemrograman.">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <title>TeknoNalar - Edukasi & Analisis Teknologi</title>
    @vite(['resources/css/app.css','resources/js/app.js'])
</head>
<body class="min-h-screen bg-gray-50 text-gray-900">
    <div id="app"></div>
</body>
</html>

