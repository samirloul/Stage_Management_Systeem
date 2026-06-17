@props(['title' => 'Stage Management Systeem'])

<!doctype html>
<html lang="nl">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <title>{{ $title }}</title>
    <link rel="stylesheet" href="{{ asset('assets/css/stage.css') }}">
    <script defer src="{{ asset('assets/js/stage.js') }}"></script>
</head>
<body data-page="{{ request()->segment(1) }}">
<header class="topbar">
    <div class="container topbar-inner">
        <a class="brand" href="{{ route('dashboard') }}">StageMS</a>

        <button id="menuToggle" class="menu-toggle" type="button">Menu</button>

        <nav id="mainNav" class="nav-links">
            <a href="{{ route('dashboard') }}">Dashboard</a>
            <a href="{{ route('internships.index') }}">Stages</a>
            <a href="{{ route('reviews.index') }}">Beoordelingen</a>

            @if(auth()->user()?->hasRole('admin'))
                <a href="{{ route('students.index') }}">Studenten</a>
                <a href="{{ route('companies.index') }}">Bedrijven</a>
            @endif

            <span class="role-pill">Rol: {{ auth()->user()?->role?->label ?? 'Geen rol' }}</span>

            <form method="POST" action="{{ route('logout') }}">
                @csrf
                <button type="submit" class="btn btn-ghost">Uitloggen</button>
            </form>
        </nav>
    </div>
</header>

<main class="container page-content">
    @if(session('success'))
        <div class="alert success auto-dismiss">{{ session('success') }}</div>
    @endif

    @if($errors->has('general'))
        <div class="alert error">{{ $errors->first('general') }}</div>
    @endif

    @if($errors->any())
        <div class="alert error">
            <strong>Controleer de invoer:</strong>
            <ul>
                @foreach($errors->all() as $error)
                    <li>{{ $error }}</li>
                @endforeach
            </ul>
        </div>
    @endif

    {{ $slot }}
</main>
</body>
</html>
