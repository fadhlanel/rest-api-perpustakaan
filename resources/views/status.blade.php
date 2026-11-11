<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>Pustaka Digital — API Status</title>
    <style>
        body { font-family: system-ui, sans-serif; max-width: 720px; margin: 40px auto; padding: 0 20px; color: #1e293b; }
        h1 { font-size: 22px; margin: 0 0 4px; }
        .muted { color: #64748b; font-size: 13px; }
        .cards { display: flex; gap: 12px; margin: 20px 0; }
        .card { flex: 1; padding: 16px; border: 1px solid #e2e8f0; border-radius: 12px; }
        .ok { color: #059669; font-weight: 600; }
        .down { color: #dc2626; font-weight: 600; }
        table { width: 100%; border-collapse: collapse; font-size: 13px; }
        th, td { text-align: left; padding: 6px 8px; border-bottom: 1px solid #f1f5f9; }
        th { color: #64748b; font-weight: 600; }
        code { background: #f8fafc; padding: 1px 4px; border-radius: 4px; }
    </style>
</head>
<body>
    <h1>Pustaka Digital — API Status</h1>
    <p class="muted">REST API Perpustakaan SMK Negeri 1 Surabaya</p>

    <div class="cards">
        <div class="card">
            Status Aplikasi<br>
            <span class="{{ $databaseOk ? 'ok' : 'down' }}">{{ $databaseOk ? 'OK' : 'ERROR' }}</span>
        </div>
        <div class="card">
            Database<br>
            <span class="{{ $databaseOk ? 'ok' : 'down' }}">{{ $databaseOk ? 'Terhubung' : 'Tidak tersedia' }}</span>
        </div>
        <div class="card">
            Endpoint<br>
            <code>{{ url('api/v1/buku') }}</code>
        </div>
    </div>

    <h2 style="font-size: 16px;">Log Endpoint Terakhir</h2>
    @forelse($logs as $log)
        <table>
            <tr>
                <td style="width: 150px;">{{ $log['time'] }}</td>
                <td style="width: 60px;"><strong>{{ $log['method'] }}</strong></td>
                <td><code>{{ $log['path'] }}</code></td>
                <td style="width: 50px;">{{ $log['status'] }}</td>
            </tr>
        </table>
    @empty
        <p class="muted">Belum ada request.</p>
    @endforelse
</body>
</html>
