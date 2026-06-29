<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>@yield('title', 'Dashboard')</title>

    <!-- Tailwind -->
    <script src="https://cdn.tailwindcss.com?plugins=forms,container-queries"></script>

    <!-- Fonts -->
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@300;400;500;600;700&display=swap" rel="stylesheet">

    <!-- Icons -->
    <script src="https://unpkg.com/lucide@latest"></script>

    <style>
        body {
            font-family: 'Inter', sans-serif;
            background-color: #f1f5f9;
        }
    </style>
</head>

<body class="flex h-screen overflow-hidden">

    @include('partials.sidebar')

    <main class="flex-1 flex flex-col overflow-hidden bg-[#0a1128]">

        @include('partials.navbar')

        <div class="flex-1 overflow-y-auto p-6 bg-slate-100 rounded-tl-3xl shadow-2xl">

            @yield('content')

        </div>

        @include('partials.footer')

    </main>

    <script>
        document.addEventListener("DOMContentLoaded", function() {
            lucide.createIcons();
        });
    </script>
</body>
</html>