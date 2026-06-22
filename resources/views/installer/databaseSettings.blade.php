@extends('installer.main')

@section('content')
<div class="container mt-5">
    <h2>Database Settings</h2>
    <form action="{{ route('installer.saveDatabaseSettings') }}" method="POST">
        @csrf

        <div class="form-group">
            <label for="db_connection">Database Connection</label>
            <select class="form-control" name="db_connection" id="db_connection">
                <option value="mysql" {{ $data['DB_CONNECTION'] == 'mysql' ? 'selected' : '' }}>MySQL</option>
                <option value="sqlite" {{ $data['DB_CONNECTION'] == 'sqlite' ? 'selected' : '' }}>SQLite</option>
                <option value="pgsql" {{ $data['DB_CONNECTION'] == 'pgsql' ? 'selected' : '' }}>PostgreSQL</option>
            </select>
        </div>

        <div class="form-group">
            <label for="db_host">Database Host</label>
            <input type="text" class="form-control" name="db_host" id="db_host" value="{{ $data['DB_HOST'] }}">
        </div>

        <div class="form-group">
            <label for="db_port">Database Port</label>
            <input type="text" class="form-control" name="db_port" id="db_port" value="{{ $data['DB_PORT'] }}">
        </div>

        <div class="form-group">
            <label for="db_database">Database Name</label>
            <input type="text" class="form-control" name="db_database" id="db_database" value="{{ $data['DB_DATABASE'] }}">
        </div>

        <div class="form-group">
            <label for="db_username">Database Username</label>
            <input type="text" class="form-control" name="db_username" id="db_username" value="{{ $data['DB_USERNAME'] }}">
        </div>

        <div class="form-group">
            <label for="db_password">Database Password</label>
            <input type="password" class="form-control" name="db_password" id="db_password" value="{{ $data['DB_PASSWORD'] }}">
        </div>

        <div class="d-flex justify-content-between mt-4">
            <a href="{{ route('installer.applicationSettings') }}" class="btn btn-secondary">Back</a>
            <button type="submit" class="btn btn-primary">Next: Review Settings</button>
        </div>
    </form>
</div>
@endsection