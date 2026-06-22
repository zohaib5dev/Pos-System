@extends('installer.main')

@section('content')
<div class="container mt-5 text-center">
    <h2>Installation Complete!</h2>
    <p>Your POS application has been successfully installed.</p>
    <a href="{{ url('/login') }}" class="btn btn-primary mt-3">Go to Application</a>
</div>
@endsection