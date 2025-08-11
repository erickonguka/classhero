@extends('layouts.admin')

@section('title', 'Analytics')
@section('page-title', 'Analytics')

@section('content')
<div class="space-y-6">
    <!-- Export Actions -->
    <div class="flex justify-end space-x-3">
        <a href="{{ route('admin.analytics.export', ['format' => 'pdf']) }}" 
           class="bg-red-600 hover:bg-red-700 text-white px-4 py-2 rounded-lg">
            Export PDF
        </a>
        <a href="{{ route('admin.analytics.export', ['format' => 'xlsx']) }}" 
           class="bg-green-600 hover:bg-green-700 text-white px-4 py-2 rounded-lg">
            Export Excel
        </a>
    </div>

    <!-- Analytics Content -->
    <div class="grid grid-cols-1 lg:grid-cols-2 gap-6">
        <div class="bg-white dark:bg-gray-800 rounded-xl shadow-lg p-6">
            <h3 class="text-lg font-semibold text-gray-900 dark:text-white mb-4">User Growth</h3>
            <div style="height: 300px; position: relative;">
                <canvas id="userGrowthChart"></canvas>
            </div>
        </div>

        <div class="bg-white dark:bg-gray-800 rounded-xl shadow-lg p-6">
            <h3 class="text-lg font-semibold text-gray-900 dark:text-white mb-4">Revenue Trend</h3>
            <div style="height: 300px; position: relative;">
                <canvas id="revenueChart"></canvas>
            </div>
        </div>
    </div>
</div>

@push('scripts')
<script>
const userCtx = document.getElementById('userGrowthChart').getContext('2d');
new Chart(userCtx, {
    type: 'line',
    data: {
        labels: {!! json_encode($analytics['user_growth']->pluck('date')) !!},
        datasets: [{
            label: 'New Users',
            data: {!! json_encode($analytics['user_growth']->pluck('count')) !!},
            borderColor: 'rgb(59, 130, 246)',
            backgroundColor: 'rgba(59, 130, 246, 0.1)',
            tension: 0.4
        }]
    },
    options: {
        responsive: true,
        maintainAspectRatio: false,
        plugins: { legend: { display: false } },
        scales: { y: { beginAtZero: true } }
    }
});

const revenueCtx = document.getElementById('revenueChart').getContext('2d');
new Chart(revenueCtx, {
    type: 'bar',
    data: {
        labels: {!! json_encode($analytics['revenue_data']->pluck('date')) !!},
        datasets: [{
            label: 'Revenue',
            data: {!! json_encode($analytics['revenue_data']->pluck('total')) !!},
            backgroundColor: 'rgba(147, 51, 234, 0.8)',
            borderColor: 'rgb(147, 51, 234)',
            borderWidth: 1
        }]
    },
    options: {
        responsive: true,
        maintainAspectRatio: false,
        plugins: { legend: { display: false } },
        scales: {
            y: {
                beginAtZero: true,
                ticks: { callback: function(value) { return '$' + value; } }
            }
        }
    }
});
</script>
@endpush
@endsection