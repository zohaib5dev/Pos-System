@extends('installer.main')

@section('content')
<div class="container mt-5">
    <h2>Review Settings</h2>

    <p>Please review your application and database settings before finalizing the installation:</p>

    <ul class="list-group mb-4">
        <li class="list-group-item"><strong>Application Name:</strong> {{ $data['APP_NAME'] }}</li>
        <li class="list-group-item"><strong>Application Environment:</strong> {{ $data['APP_ENV'] }}</li>
        <li class="list-group-item"><strong>Debug Mode:</strong> {{ $data['APP_DEBUG'] }}</li>
        <li class="list-group-item"><strong>Application Key:</strong> {{ $data['APP_KEY'] }}</li>
        <li class="list-group-item"><strong>Database Connection:</strong> {{ $data['DB_CONNECTION'] }}</li>
        <li class="list-group-item"><strong>Database Host:</strong> {{ $data['DB_HOST'] }}</li>
        <li class="list-group-item"><strong>Database Port:</strong> {{ $data['DB_PORT'] }}</li>
        <li class="list-group-item"><strong>Database Name:</strong> {{ $data['DB_DATABASE'] }}</li>
        <li class="list-group-item"><strong>Database Username:</strong> {{ $data['DB_USERNAME'] }}</li>
        <li class="list-group-item"><strong>Database Password:</strong> {{ str_repeat('*', strlen($data['DB_PASSWORD'] ?? '')) }}</li>
    </ul>

    <form action="{{ route('installer.finalizeSetup') }}" method="POST">
        @csrf
        <div class="d-flex justify-content-between">
            <a href="{{ route('installer.showDatabaseSettings') }}" class="btn btn-secondary">Back</a>
            <button type="submit" class="btn btn-success">Finalize Installation</button>
        </div>
    </form>
</div>
@endsection