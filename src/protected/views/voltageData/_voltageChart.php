<div class="chart">
    <div class="chart__box">
        <div class="chart__container">
            <div class="chart__container-body">
                <canvas id="voltage-chart"></canvas>
            </div>
        </div>
    </div>
</div>

<style>
    .chart__box {
        overflow: hidden;
    }

    .chart__container {
        width: 100%;
        overflow-x: scroll;
    }

    .chart__container-body {
        height: 600px;
    }
</style>

<script type="text/javascript">
    let voltageChartInstance = null;

    document.addEventListener('DOMContentLoaded', function() {
        createChart(<?php echo CJSON::encode($chartData); ?>);
        calculateChartWidth();
    });


    // Создает и настраивает новый график
    function createChart(chartData) {
        const ctx = document.getElementById('voltage-chart').getContext('2d');

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
                        borderColor: '#e68b49',
                        fill: false
                    }
                ]
            },
            options: {
                responsive: true,
                maintainAspectRatio: false,
                animation: false,
                plugins: {
                    title: {
                        display: true,
                        text: 'График напряжения по фазам'
                    }
                },
                scales: {
                    x: {
                        title: { display: true, text: 'Время' },
                    },
                    y: {
                        title: { display: true, text: 'Напряжение, В' },
                        beginAtZero: false
                    }
                }
            }
        });
    }

    // Обновляет график
    function updateChart() {
        const period = document.getElementById('period_select').value;
        const dateRangeDiv = document.getElementById('date-range');

        // Показываем поля для ввода дат, если выбран "Произвольный период"
        if (period === 'custom') {
            dateRangeDiv.style.display = 'block';
        } else {
            dateRangeDiv.style.display = 'none';
        }

        const meterId = document.getElementById('meter_select').value;
        const startDate = document.getElementById('start_date').value;
        const endDate = document.getElementById('end_date').value;

        $.ajax({
            url: `/voltage-data?meter_id=${meterId}&period=${period}&start_date=${startDate}&end_date=${endDate}`,
            method: 'GET',
            dataType: 'json',
            success: function(response) {
                createChart(response);
                calculateChartWidth();
            }
        });
    }

    // Высчитывает ширину контейнера графика
    function calculateChartWidth() {
        const chartContainerBody = document.querySelector('.chart__container-body');
        const totalLabels = voltageChartInstance.data.labels.length;

        if(totalLabels > 20) {
            const newWidth = 1000 + ((totalLabels - 20) * 20);
            chartContainerBody.style.width = `${newWidth}px`;
        } else {
            chartContainerBody.style.width = '100%';
        }
    }
</script>