<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Import Users - Laravel Dusk</title>
    <style>
        body {
            font-family: Arial, sans-serif;
            background-color: #f4f4f4;
            margin: 0;
            padding: 20px;
        }
        .container {
            max-width: 800px;
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
            border: none;
            cursor: pointer;
        }
        .btn:hover {
            background-color: #0056b3;
        }
        .btn-secondary {
            background-color: #6c757d;
        }
        .btn-secondary:hover {
            background-color: #545b62;
        }
        .form-group {
            margin-bottom: 1.5rem;
        }
        label {
            display: block;
            margin-bottom: 0.5rem;
            font-weight: bold;
            color: #333;
        }
        input[type="file"] {
            width: 100%;
            padding: 0.5rem;
            border: 1px solid #ddd;
            border-radius: 4px;
            font-size: 1rem;
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
        .alert-danger {
            background-color: #f8d7da;
            color: #721c24;
            border: 1px solid #f5c6cb;
        }
        .help-text {
            font-size: 0.9rem;
            color: #666;
            margin-top: 0.5rem;
        }
        .csv-format {
            background-color: #f8f9fa;
            padding: 1rem;
            border-radius: 4px;
            margin-top: 1rem;
            font-family: monospace;
            font-size: 0.9rem;
        }
        .error-list {
            background-color: #f8d7da;
            color: #721c24;
            padding: 1rem;
            border-radius: 4px;
            margin-top: 1rem;
        }
        .error-list ul {
            margin: 0;
            padding-left: 1.5rem;
        }
    </style>
</head>
<body>
    <div class="container">
        <h1>Import Users from CSV</h1>
        
        @if(session('success'))
            <div class="alert alert-success">
                {{ session('success') }}
            </div>
        @endif

        @if(session('error'))
            <div class="alert alert-danger">
                {{ session('error') }}
            </div>
        @endif

        @if($errors->any())
            <div class="alert alert-danger">
                <ul>
                    @foreach($errors->all() as $error)
                        <li>{{ $error }}</li>
                    @endforeach
                </ul>
            </div>
        @endif

        <form action="{{ route('users.import') }}" method="POST" enctype="multipart/form-data">
            @csrf
            
            <div class="form-group">
                <label for="csv_file">CSV File</label>
                <input type="file" 
                       id="csv_file" 
                       name="csv_file" 
                       accept=".csv,.txt"
                       required
                       dusk="csv-file-input">
                <div class="help-text">
                    Select a CSV file to import users. Maximum file size: 2MB.
                </div>
            </div>

            <button type="submit" class="btn" dusk="import-submit">
                Import Users
            </button>
            
            <a href="{{ route('users.index') }}" class="btn btn-secondary">
                Back to Users
            </a>
        </form>

        <div class="csv-format">
            <h3>CSV Format</h3>
            <p>Your CSV file should have the following format:</p>
            <pre>name,email,password
John Doe,john@example.com,password123
Jane Smith,jane@example.com,password456</pre>
            
            <p><strong>Required fields:</strong></p>
            <ul>
                <li><strong>name</strong> - User's full name</li>
                <li><strong>email</strong> - User's email address (must be unique)</li>
                <li><strong>password</strong> - User's password (optional, defaults to 'password123')</li>
            </ul>
            
            <p><strong>Notes:</strong></p>
            <ul>
                <li>First row should contain column headers</li>
                <li>Email addresses must be unique and valid</li>
                <li>If password is not provided, 'password123' will be used</li>
                <li>Duplicate emails will be skipped with an error message</li>
            </ul>
        </div>
    </div>
</body>
</html>
