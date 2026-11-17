<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>{{ $title }} | Mpek-Mpek Bu Titin</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
</head>
<body>
<nav class="navbar navbar-dark bg-dark px-3">
    <span class="navbar-brand">Mpek-Mpek Bu Titin</span>
    <form method="POST" action="{{ route('logout') }}" class="d-flex align-items-center gap-3">
        @csrf
        <span class="text-white">{{ auth()->user()->name }} ({{ auth()->user()->role }})</span>
        <button class="btn btn-outline-light btn-sm">Logout</button>
    </form>
</nav>
<div class="container py-5">
    <h2>{{ $title }}</h2>
    <p class="text-muted">Halaman sementara. Akan diisi pada tahap M3.</p>
</div>
</body>
</html>
