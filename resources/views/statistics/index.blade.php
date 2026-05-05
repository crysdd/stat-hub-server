@extends('layouts.app')

@push('styles')
<style>
    .chart-container {
        position: relative;
        height: 700px;
        width: 100%;
    }
</style>
@endpush

@section('content')
    <div class="container">
        <div class="row justify-content-center">
            <div class="col-md-12">
                @if (!$dailyStats->isEmpty())
                    <!-- Chart Section -->
                    <div class="card mb-4">
                        <div class="card-header">{{ __('User Statistics Chart') }}</div>
                        <div class="chart-container">
                            <canvas id="statsChart"></canvas>
                        </div>
                    </div>

                    <!-- Month Navigation -->
                    <div class="d-flex justify-content-between align-items-center mb-3">
                        @if ($dailyStats->previous_month)
                            <a href="{{ route('statistics.index', ['month' => $dailyStats->previous_month]) }}"
                               class="btn btn-outline-primary">
                                &larr; {{ Illuminate\Support\Carbon::parse($dailyStats->previous_month . '-01')->format('F Y') }}
                            </a>
                        @else
                            <span></span> <!-- Spacer -->
                        @endif

                        <h4>{{ Illuminate\Support\Carbon::parse($dailyStats->current_month)->format('F Y') }}</h4>

                        @if ($dailyStats->next_month && $dailyStats->next_month <= Illuminate\Support\Carbon::now()->format('Y-m'))
                            <a href="{{ route('statistics.index', ['month' => $dailyStats->next_month]) }}"
                               class="btn btn-outline-primary">
                                {{ Illuminate\Support\Carbon::parse($dailyStats->next_month . '-01')->format('F Y') }} &rarr;
                            </a>
                        @else
                            <span></span> <!-- Spacer -->
                        @endif
                    </div>
                @endif

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
                                        <th>Count of Hit</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    @foreach ($dailyStats->reverse() as $stat)
                                        <tr>
                                            <td>{{ Illuminate\Support\Carbon::parse($stat->date)->format('Y-m-d') }}</td>
                                            <td>{{ $stat->count }}</td>
                                            <td>{{ $stat->count_hit }}</td>
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

<script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
<script>
document.addEventListener('DOMContentLoaded', function() {
    const ctx = document.getElementById('statsChart').getContext('2d');

    // Extract data from the stats
    const dates = @json($dailyStats->pluck('date')->map(fn($date) => Illuminate\Support\Carbon::parse($date)->format('Y-m-d')));
    const userCounts = @json($dailyStats->pluck('count'));
    const hitCounts = @json($dailyStats->pluck('count_hit'));

    // Create chart
    new Chart(ctx, {
        type: 'line',
        data: {
            labels: dates,
            datasets: [
                {
                    label: 'Users',
                    data: userCounts,
                    borderColor: '#36a2eb', // Blue color for users
                    backgroundColor: 'rgba(54, 162, 235, 0.2)',
                    tension: 0.3,
                    fill: false
                },
                {
                    label: 'Hits',
                    data: hitCounts,
                    borderColor: '#ffcd56', // Yellow/orange color for hits
                    backgroundColor: 'rgba(255, 205, 86, 0.2)',
                    tension: 0.3,
                    fill: false
                }
            ]
        },
        options: {
            responsive: true,
            maintainAspectRatio: false,
            scales: {
                x: {
                    title: {
                        display: true,
                        text: 'Date'
                    }
                },
                y: {
                    beginAtZero: true,
                    title: {
                        display: true,
                        text: 'Count'
                    }
                }
            },
            plugins: {
                legend: {
                    position: 'top'
                },
                tooltip: {
                    mode: 'index',
                    intersect: false
                }
            }
        }
    });
});
</script>
@endsection

