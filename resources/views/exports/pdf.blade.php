<!DOCTYPE html>
<html>
<head>
    <title>Laporan Stok Obat</title>
    <style>
        body { font-family: sans-serif; font-size: 12px; }
        table { width: 100%; border-collapse: collapse; margin-top: 15px; }
        th, td { border: 1px solid #333; padding: 6px; text-align: left; }
        th { background-color: #f4f4f4; }
        .header-info { margin-bottom: 20px; }
        .header-info p { margin: 2px 0; }
    </style>
</head>
<body>
    <h2>Laporan Stok Obat</h2>
    <div class="header-info">
        <p><strong>Tanggal Laporan:</strong> {{ $date }}</p>
        <p><strong>Jumlah Data:</strong> {{ $total }}</p>
    </div>

    <table>
        <thead>
            <tr>
                <th>Kode</th>
                <th>Nama</th>
                <th>Kategori</th>
                <th>Satuan</th>
                <th>Stok</th>
                <th>Min Stok</th>
                <th>Kedaluwarsa</th>
            </tr>
        </thead>
        <tbody>
            @foreach($medicines as $medicine)
            <tr>
                <td>{{ $medicine->code }}</td>
                <td>{{ $medicine->name }}</td>
                <td>{{ $medicine->category }}</td>
                <td>{{ $medicine->unit }}</td>
                <td>{{ $medicine->stock }}</td>
                <td>{{ $medicine->minimum_stock }}</td>
                <td>{{ $medicine->expired_date }}</td>
            </tr>
            @endforeach
        </tbody>
    </table>
</body>
</html>
