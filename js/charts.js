// Load attendance chart data
async function loadAttendanceChart() {
    try {
        const response = await fetch('api/chart_data.php');
        const result = await response.json();
        
        if (result.success) {
            createAttendanceChart(result.data);
        }
    } catch (error) {
        console.error('Error loading chart data:', error);
    }
}

// Create attendance trend chart
function createAttendanceChart(data) {
    const ctx = document.getElementById('attendanceChart');
    
    if (!ctx) return;
    
    new Chart(ctx, {
        type: 'line',
        data: {
            labels: data.labels,
            datasets: [{
                label: 'Present',
                data: data.present,
                borderColor: '#10b981',
                backgroundColor: 'rgba(16, 185, 129, 0.1)',
                tension: 0.4,
                fill: true
            }, {
                label: 'Absent',
                data: data.absent,
                borderColor: '#ef4444',
                backgroundColor: 'rgba(239, 68, 68, 0.1)',
                tension: 0.4,
                fill: true
            }]
        },
        options: {
            responsive: true,
            maintainAspectRatio: true,
            plugins: {
                legend: {
                    display: true,
                    position: 'top'
                },
                tooltip: {
                    mode: 'index',
                    intersect: false
                }
            },
            scales: {
                y: {
                    beginAtZero: true,
                    ticks: {
                        stepSize: 1
                    }
                }
            },
            interaction: {
                mode: 'nearest',
                axis: 'x',
                intersect: false
            }
        }
    });
}

// Initialize on page load
document.addEventListener('DOMContentLoaded', function() {
    if (document.getElementById('attendanceChart')) {
        loadAttendanceChart();
    }
});