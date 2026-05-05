@extends('layouts.app')

@section('content')
<div class="container">
    <div class="row justify-content-center">
        <div class="col-md-10">
            <div class="card">
                <div class="card-header">{{ __('User Statistics by Day') }}</div>

                <div class="card-body">
                    @if ($dailyStats->isEmpty())
                        <p>No statistics data available yet.</p>
                    @else
                        <table class="table table-striped table-bordered">
                            <thead>
                                <tr>
                                    <th>Date</th>
                                    <th>Count of Users</th>
                                </tr>
                            </thead>
                            <tbody>
                                @foreach ($dailyStats as $stat)
                                    <tr>
                                        <td>{{ \Carbon\Carbon::parse($stat->date)->format('Y-m-d') }}</td>
                                        <td>{{ $stat->count }}</td>
                                    </tr>
                                @endforeach
                            </tbody>
                        </table>
                        
                        <div class="mt-3">
                            <strong>Total Days:</strong> {{ $dailyStats->count() }}<br>
                            <strong>Total Users:</strong> {{ $dailyStats->sum('count') }}
                        </div>
                    @endif
                </div>
            </div>
        </div>
    </div>
</div>
@endsection
