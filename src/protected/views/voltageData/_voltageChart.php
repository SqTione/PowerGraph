<canvas id="voltageChart" style="width: 100%; margin: 0 auto"></canvas>

<script type="text/javascript">
    let voltageChartInstance = null;

    document.addEventListener('DOMContentLoaded', function() {
        createChart(<?php echo CJSON::encode($chartData); ?>);
    });


    function createChart(chartData) {
        const ctx = document.getElementById('voltageChart').getContext('2d');

        // Если график уже создан, уничтожаем его перед созданием нового
        if (voltageChartInstance !== null) {
            voltageChartInstance.destroy();
        }

        voltageChartInstance = new Chart(ctx, {
            type: 'line',
            data: {
                labels: chartData.labels,
                datasets: [
                    {
                        label: 'Фаза A',
                        data: chartData.dataA,
                        borderColor: '#FF6B6B',
                        fill: false
                    },
                    {
                        label: 'Фаза B',
                        data: chartData.dataB,
                        borderColor: '#4ECDC4',
                        fill: false
                    },
                    {
                        label: 'Фаза C',
                        data: chartData.dataC,
                        borderColor: '#45B7D1',
                        fill: false
                    }
                ]
            },
            options: {
                responsive: true,
                plugins: {
                    title: {
                        display: true,
                        text: 'График напряжения по фазам'
                    }
                },
                scales: {
                    x: {
                        title: { display: true, text: 'Время' }
                    },
                    y: {
                        title: { display: true, text: 'Напряжение, В' },
                        beginAtZero: false
                    }
                }
            }
        });
    }

    function updateChart() {
        const meterId = document.getElementById('meter_select').value;
        const period = document.getElementById('period_select').value;
        const startDate = document.getElementById('start_date').value;
        const endDate = document.getElementById('end_date').value;

        $.ajax({
            url: `/voltage-data?meter_id=${meterId}&period=${period}&start_date=${startDate}&end_date=${endDate}`,
            method: 'GET',
            dataType: 'json',
            success: function(response) {
                createChart(response);
            }
        });
    }

</script>