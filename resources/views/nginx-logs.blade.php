@extends('layouts.app')

@section('content')
<div class="container">
    <div class="row justify-content-center">
        <div class="col-md-12">
            <div class="card">
                <div class="card-header d-flex justify-content-between align-items-center">
                    <span>Nginx Logs</span>
                    <a href="{{ route('nginx.download') }}" class="btn btn-primary">Download Full Log</a>
                </div>

                <div class="card-body">
                    @if(isset($error))
                        <div class="alert alert-danger">{{ $error }}</div>
                    @else
                        <div class="table-responsive">
                            <table class="table table-striped">
                                <thead>
                                    <tr>
                                        <th>Log Entry</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    @foreach($logs ?? [] as $log)
                                        <tr>
                                            <td class="font-monospace">{{ $log }}</td>
                                        </tr>
                                    @endforeach
                                </tbody>
                            </table>
                        </div>
                    @endif
                </div>
            </div>
        </div>
    </div>
</div>
@endsection
