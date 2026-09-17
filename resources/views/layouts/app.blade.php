<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <title>Manajemen Stok Obat</title>

    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@400;500;600;700;800&display=swap" rel="stylesheet">

    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.1/font/bootstrap-icons.css">

    <style>
        :root {
            --brand: #4f46e5;
            --brand-dark: #3730a3;
            --brand-light: #eef2ff;
            --bg: #f4f6fb;
            --text-muted: #6b7280;
            --radius-lg: 1rem;
            --radius-md: 0.65rem;
            --shadow-soft: 0 4px 20px rgba(15, 23, 42, 0.06);
        }

        * { font-family: 'Inter', 'Segoe UI', system-ui, sans-serif; }

        html, body { height: 100%; }

        body {
            background: var(--bg);
            min-height: 100vh;
        }

        .navbar {
            background: linear-gradient(90deg, var(--brand-dark), var(--brand)) !important;
            box-shadow: 0 2px 12px rgba(55, 48, 163, 0.25);
        }

        .navbar-brand {
            font-weight: 700;
            letter-spacing: 0.2px;
            display: flex;
            align-items: center;
            gap: 0.55rem;
            font-size: 1.15rem;
        }

        .navbar-brand i { font-size: 1.3rem; }

        .card {
            border-radius: var(--radius-lg);
        }

        .shadow-soft { box-shadow: var(--shadow-soft) !important; }

        .btn {
            border-radius: var(--radius-md);
            font-weight: 500;
        }

        .btn-primary { background-color: var(--brand); border-color: var(--brand); }
        .btn-primary:hover, .btn-primary:focus { background-color: var(--brand-dark); border-color: var(--brand-dark); }

        .form-control, .form-select {
            border-radius: var(--radius-md);
        }

        .form-control:focus, .form-select:focus {
            border-color: var(--brand);
            box-shadow: 0 0 0 0.2rem rgba(79, 70, 229, 0.15);
        }

        .table-responsive { min-height: 400px; }

        .auto-save-indicator {
            font-size: 0.78rem;
            font-weight: 600;
            margin-left: 8px;
            transition: opacity 0.2s ease;
        }
        .status-saving { color: #f39c12; }
        .status-saved { color: #27ae60; }
        .status-failed { color: #e74c3c; }

        ::-webkit-scrollbar { height: 8px; width: 8px; }
        ::-webkit-scrollbar-thumb { background: #c7ccd8; border-radius: 10px; }
    </style>
    @stack('styles')
</head>
<body>

    <nav class="navbar navbar-expand-lg navbar-dark mb-4 sticky-top">
        <div class="container">
            <a class="navbar-brand" href="#">
                <i class="bi bi-capsule"></i>
                Sistem Stok Obat
            </a>
        </div>
    </nav>

    <div class="container pb-5">
        @yield('content')
    </div>

    <script src="https://code.jquery.com/jquery-3.7.1.min.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/js/bootstrap.bundle.min.js"></script>
    @stack('scripts')
</body>
</html>
