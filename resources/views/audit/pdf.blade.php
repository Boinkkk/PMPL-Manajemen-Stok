<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <title>Laporan Audit Trail</title>
    <style>
        body {
            color: #222;
            font-family: DejaVu Sans, sans-serif;
            font-size: 12px;
        }

        h1 {
            color: #7a3d00;
            font-size: 20px;
            margin-bottom: 18px;
            text-align: center;
        }

        table {
            border-collapse: collapse;
            width: 100%;
        }

        th,
        td {
            border: 1px solid #999;
            padding: 7px;
            text-align: left;
        }

        th {
            background-color: #7a3d00;
            color: #fff;
        }

        tr:nth-child(even) td {
            background-color: #f7f2ec;
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
                    <td>{{ $audit->ip_address }}</td>
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
