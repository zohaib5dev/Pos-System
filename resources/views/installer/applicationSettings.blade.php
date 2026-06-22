@extends('installer.main')

@section('content')
<div class="container mt-5">
    <h2>Application Settings</h2>
    <form action="{{ route('installer.saveApplicationSettings') }}" method="POST">
        @csrf

        <div class="form-group">
            <label for="app_name">Application Name</label>
            <input type="text" class="form-control" name="app_name" id="app_name" value="{{ $data['APP_NAME'] }}">
        </div>

        <div class="form-group">
            <label for="app_env">Application Environment</label>
            <select class="form-control" name="app_env" id="app_env">
                <option value="local" {{ $data['APP_ENV'] == 'local' ? 'selected' : '' }}>Local</option>
                <option value="production" {{ $data['APP_ENV'] == 'production' ? 'selected' : '' }}>Production</option>
            </select>
        </div>

        <div class="form-group">
            <label for="app_debug">Debug Mode</label>
            <select class="form-control" name="app_debug" id="app_debug">
                <option value="true" {{ $data['APP_DEBUG'] == true ? 'selected' : '' }}>True</option>
                <option value="false" {{ $data['APP_DEBUG'] == false ? 'selected' : '' }}>False</option>
            </select>
        </div>

        <div class="form-group">
            <label for="app_key">Application Key</label>
            <input type="text" class="form-control" name="app_key" id="app_key" value="{{ $data['APP_KEY'] }}">
            <small class="form-text text-muted">
                Leave empty to generate automatically after installer.
            </small>
        </div>

        <button type="submit" class="btn btn-primary mt-3">Next: Database Settings</button>
    </form>
</div>
@endsection