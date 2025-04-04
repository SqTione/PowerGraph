<?php
/* @var $this SiteController */
/* @var $meter Meters */
/* @var $chartData array */

$this->pageTitle = "Счётчик {$meter->name} |";
?>

<main class="container" id="meter">
    <div class="section__title">
        <h1><?php echo CHtml::encode($meter->name) ?></h1>
        <button type="button" id="edit-button">
            <img src="<?php echo Yii::app()->request->baseUrl; ?>/images/icons/pencil.svg" alt="">
        </button>
    </div>
    <p class="meter__description">Счётчик распределительного щита ЩР-7</p>
    <hr class="separator">
    <div class="graph">
        <div class="graph__settings">
            <p class="graph__label">Период отображения:</p>
            <div class="graph__period-radio radio">
                <div class="radio__container">
                    <div class="radio__element">
                        <input type="radio" name="graph__period" id="graph__period--today" value="today" checked>
                        <label for="graph__period--today">Сегодня</label>
                    </div>
                    <div class="radio__element">
                        <input type="radio" name="graph__period" id="graph__period--yesterday" value="yesterday">
                        <label for="graph__period--yesterday">Вчера</label>
                    </div>
                    <div class="radio__element">
                        <input type="radio" name="graph__period" id="graph__period--week" value="week">
                        <label for="graph__period--week">Неделя</label>
                    </div>
                    <div class="radio__element">
                        <input type="radio" name="graph__period" id="graph__period--month" value="month">
                        <label for="graph__period--month">Месяц</label>
                    </div>
                    <div class="radio__element">
                        <input type="radio" name="graph__period" id="graph__period--custom" value="custom">
                        <label for="graph__period--custom">Произвольно</label>
                    </div>
                </div>
            </div>
            <!-- Поля для ввода дат -->
            <div id="date-range" style="display: none; margin: 10px 0">
                <?php echo CHtml::textField('start_date', '', ['class' => 'form-control datepicker', 'placeholder' => 'Начальная дата']); ?>
                <?php echo CHtml::textField('end_date', '', ['class' => 'form-control datepicker', 'placeholder' => 'Конечная дата']); ?>
            </div>
        </div>
        <div class="graph__container">
            <p class="graph__label">График:</p>
            <!-- Элемент для отображения графика -->
            <div id="chart-container">
                <?php $this->renderPartial('_voltageChart', ['chartData' => $chartData]); ?>
            </div>
        </div>
        <div class="graph__legend legend">
            <div class="legend__item">
                <div class="legend__image"><img src="<?php Yii::app()->request->baseUrl; ?>/images/icons/phase_a.svg" alt=""></div>
                <p class="legend__label">Фаза A</p>
            </div>
            <div class="legend__item">
                <div class="legend__image"><img src="<?php Yii::app()->request->baseUrl; ?>/images/icons/phase_b.svg" alt=""></div>
                <p class="legend__label">Фаза B</p>
            </div>
            <div class="legend__item">
                <div class="legend__image"><img src="<?php Yii::app()->request->baseUrl; ?>/images/icons/phase_c.svg" alt=""></div>
                <p class="legend__label">Фаза C</p>
            </div>
        </div>
    </div>
</main>

<script type="text/javascript">
    let voltageChartInstance = null;

    document.addEventListener('DOMContentLoaded', function() {
        createChart(<?php echo CJSON::encode($chartData); ?>);
        calculateChartWidth();

        // Обработчик для радио кнопок
        document.querySelectorAll('input[name="graph__period"]').forEach(radio => {
            radio.addEventListener('change', handlePeriodChange);
        });

        // Обработчики для полей ввода дат
        const startDateInput = document.getElementById('start_date');
        const endDateInput = document.getElementById('end_date');

        if (startDateInput && endDateInput) {
            startDateInput.addEventListener('change', handleCustomDateChange);
            endDateInput.addEventListener('change', handleCustomDateChange);
        }
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
                        borderColor: '#66FF6B',
                        backgroundColor: 'rgba(102, 255, 177, 0.4)',
                        borderWidth: 2,
                        tension: 0.4,
                        fill: false
                    },
                    {
                        label: 'Фаза B',
                        data: chartData.dataB,
                        borderColor: '#F8A757',
                        borderWidth: 2,
                        tension: 0.4,
                        fill: false
                    },
                    {
                        label: 'Фаза C',
                        data: chartData.dataC,
                        borderColor: '#66FFED',
                        borderWidth: 2,
                        tension: 0.4,
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
                        display: false,
                        text: 'График напряжения по фазам'
                    },
                    legend: {
                        display: false,
                    }
                },
                scales: {
                    x: {
                        title: { display: false, text: 'Время' },
                        grid: {
                            display: false,
                            drawBorder: false,
                        },
                        ticks: {
                            font: {
                                family: 'Inter',
                                size: 14
                            },
                            color: '#827D7A',
                            maxRotation: 0,
                            minRotation: 0
                        },
                    },
                    y: {
                        title: { display: false, text: 'Напряжение, В' },
                        beginAtZero: false,
                        grid: {
                            display: false,
                            drawBorder: false,
                        },
                        ticks: {
                            font: {
                                family: 'Inter',
                                size: 14
                            },
                            color: '#827D7A',
                            maxRotation: 0,
                            minRotation: 0
                        },
                    }
                }
            }
        });
    }

    // Обновляет график
    function updateChart(selectedPeriod) {
        const dateRangeDiv = document.getElementById('date-range');

        // Показываем или скрываем поля для ввода дат
        if (selectedPeriod === 'custom') {
            dateRangeDiv.style.display = 'flex';
        } else {
            dateRangeDiv.style.display = 'none';
        }

        // Получаем ID счётчика (передаём его через PHP)
        const meterId = <?php echo $meter->id; ?>;

        // Получаем значения дат, если они заполнены
        const startDateInput = document.getElementById('start_date');
        const endDateInput = document.getElementById('end_date');
        const startDate = selectedPeriod === 'custom' ? startDateInput?.value || '' : '';
        const endDate = selectedPeriod === 'custom' ? endDateInput?.value || '' : '';

        // Формируем URL только с заполненными параметрами
        let url = `/voltage-data?meter_id=${meterId}&period=${selectedPeriod}`;
        if (selectedPeriod === 'custom' && startDate && endDate) {
            url += `&start_date=${encodeURIComponent(startDate)}&end_date=${encodeURIComponent(endDate)}`;
        }

        $.ajax({
            url: url,
            method: 'GET',
            dataType: 'json',
            success: function (response) {
                if (!response || !response.labels || !response.dataA || !response.dataB || !response.dataC) {
                    console.error('Некорректные данные для графика:', response);
                    return;
                }
                createChart(response);
                calculateChartWidth();
            },
            error: function (xhr, status, error) {
                console.error('Ошибка при загрузке данных:', error);
                alert('Ошибка при загрузке данных. Проверьте консоль для деталей.');
            }
        });
    }

    // Обрабатывает изменение периода
    function handlePeriodChange(event) {
        const selectedPeriod = event.target.value;
        updateChart(selectedPeriod);
    }

    // Обрабатывает изменение произвольной даты
    function handleCustomDateChange() {
        const selectedPeriod = 'custom';
        updateChart(selectedPeriod);
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