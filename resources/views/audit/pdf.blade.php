<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="utf-8">
    <title>Laporan Audit Trail</title>
    <style>
        body {
            font-family: DejaVu Sans, sans-serif;
            font-size: 12px;
            color: #222;
        }

        h1 {
            text-align: center;
            color: #7a3d00;
            margin-bottom: 20px;
        }

        table {
            width: 100%;
            border-collapse: collapse;
        }

        th,
        td {
            border: 1px solid #555;
            padding: 8px;
            text-align: left;
        }

        th {
            background: #7a3d00;
            color: #fff;
        }

        tr:nth-child(even) {
            background: #f7f3ee;
        }
    </style>
</head>
<body>
    <h1>Laporan Audit Trail</h1>

    <table>
        <thead>
            <tr>
                <th>No</th>
                <th>Waktu</th>
                <th>Pengguna</th>
                <th>Aktivitas</th>
                <th>Modul</th>
                <th>IP</th>
            </tr>
        </thead>
        <tbody>
            @forelse ($audits as $audit)
                <tr>
                    <td>{{ $loop->iteration }}</td>
                    <td>{{ $audit->waktu_aksi?->format('d-m-Y H:i:s') }}</td>
                    <td>{{ $audit->pengguna?->nama_lengkap ?? '-' }}</td>
                    <td>{{ $audit->aksi }}</td>
                    <td>{{ $audit->modul }}</td>
                    <td>{{ $audit->ip_address ?? '-' }}</td>
                </tr>
            @empty
                <tr>
                    <td colspan="6" style="text-align: center;">Belum ada data audit trail.</td>
                </tr>
            @endforelse
        </tbody>
    </table>
</body>
</html>
