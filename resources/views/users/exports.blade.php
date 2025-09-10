<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Exported Files - Laravel Dusk</title>
    <style>
        body {
            font-family: Arial, sans-serif;
            background-color: #f4f4f4;
            margin: 0;
            padding: 20px;
        }
        .container {
            max-width: 1200px;
            margin: 0 auto;
            background: white;
            padding: 2rem;
            border-radius: 8px;
            box-shadow: 0 2px 10px rgba(0,0,0,0.1);
        }
        h1 {
            color: #333;
            margin-bottom: 2rem;
        }
        .btn {
            padding: 0.5rem 1rem;
            background-color: #007bff;
            color: white;
            text-decoration: none;
            border-radius: 4px;
            display: inline-block;
            margin-bottom: 1rem;
        }
        .btn:hover {
            background-color: #0056b3;
        }
        .btn-success {
            background-color: #28a745;
        }
        .btn-success:hover {
            background-color: #1e7e34;
        }
        .btn-warning {
            background-color: #ffc107;
            color: #212529;
        }
        .btn-warning:hover {
            background-color: #e0a800;
        }
        .btn-danger {
            background-color: #dc3545;
        }
        .btn-danger:hover {
            background-color: #c82333;
        }
        table {
            width: 100%;
            border-collapse: collapse;
            margin-top: 1rem;
        }
        th, td {
            padding: 0.75rem;
            text-align: left;
            border-bottom: 1px solid #ddd;
        }
        th {
            background-color: #f8f9fa;
            font-weight: bold;
        }
        .alert {
            padding: 0.75rem;
            margin-bottom: 1rem;
            border-radius: 4px;
        }
        .alert-success {
            background-color: #d4edda;
            color: #155724;
            border: 1px solid #c3e6cb;
        }
        .alert-info {
            background-color: #d1ecf1;
            color: #0c5460;
            border: 1px solid #bee5eb;
        }
        .no-exports {
            text-align: center;
            color: #666;
            font-style: italic;
            padding: 2rem;
        }
        .file-size {
            font-family: monospace;
            font-size: 0.9em;
        }
        .storage-path {
            background-color: #f8f9fa;
            padding: 1rem;
            border-radius: 4px;
            margin-bottom: 1rem;
            font-family: monospace;
            border-left: 4px solid #007bff;
        }
    </style>
</head>
<body>
    <div class="container">
        <h1>Exported Files</h1>
        
        @if(session('success'))
            <div class="alert alert-success">
                {{ session('success') }}
            </div>
        @endif

        <div class="storage-path">
            <strong>Storage Location:</strong><br>
            <code>{{ storage_path('app/exports') }}</code><br>
            <small>Files are stored in the Laravel storage/app/exports directory</small>
        </div>

        <a href="{{ route('users.index') }}" class="btn">Back to Users</a>
        <a href="{{ route('users.export') }}" class="btn btn-success">Export New File</a>

        @if(count($exports) > 0)
            <table>
                <thead>
                    <tr>
                        <th>Filename</th>
                        <th>Size</th>
                        <th>Created At</th>
                        <th>Actions</th>
                    </tr>
                </thead>
                <tbody>
                    @foreach($exports as $export)
                        <tr>
                            <td>{{ $export['filename'] }}</td>
                            <td class="file-size">{{ number_format($export['size'] / 1024, 2) }} KB</td>
                            <td>{{ $export['created_at'] }}</td>
                            <td>
                                <a href="{{ route('users.export') }}" class="btn btn-warning">Download</a>
                            </td>
                        </tr>
                    @endforeach
                </tbody>
            </table>
        @else
            <div class="no-exports">
                No exported files found. <a href="{{ route('users.export') }}">Create your first export</a>
            </div>
        @endif

        <div class="alert alert-info">
            <strong>Note:</strong> Export files are automatically generated when you click "Export Users" and are stored in the server's storage directory. You can download them directly from this page.
        </div>
    </div>
</body>
</html>

