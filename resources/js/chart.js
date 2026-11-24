// Tunggu sampai halaman dimuat
document.addEventListener('DOMContentLoaded', function() {
    const ctx = document.getElementById('canvasActivity');
    
    // Ambil data dari variable global
    const yearActivity = window.yearActivity || [];
    
    // Siapkan data untuk chart
    const data = {
        labels: yearActivity.map(item => item.month),
        datasets: [{
            label: 'Work Duration (%)',
            data: yearActivity.map(item => item.count),
            fill: false,
            borderColor: '#616161',
            tension: 0.4,
            pointBackgroundColor: '#616161',
            pointBorderColor: '#616161',
            pointRadius: 4,
            pointHoverRadius: 6
        }]
    };
    
    // Buat chart
    new Chart(ctx, {
        type: 'line',
        data: data,
        options: {
            responsive: true,
            maintainAspectRatio: false,
            plugins: {
                legend: {
                    display: false
                }
            },
            scales: {
                y: {
                    beginAtZero: true,
                    grid: {
                        display: true,
                        color: '#f0f0f0'
                    },
                    ticks: {
                        callback: function(value) {
                            return value + '%';
                        }
                    }
                },
                x: {
                    grid: {
                        display: false
                    }
                }
            }
        }
    });
});