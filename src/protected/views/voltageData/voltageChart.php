<h1>График напряжения</h1>

<div style="margin: 20px 0">
    <?php echo CHtml::dropDownList(
        'meter_select',
        $selectedMeter->id,
        CHtml::listData($meters, 'id', 'name'),
        [
            'prompt' => 'Выберите счётчик',
            'class' => 'form-control',
            'onchange' => 'updateChart()'
        ]
    ); ?>

    <?php echo CHtml::dropDownList(
        'period_select',
        $period,
        [
            'today' => 'Сегодня',
            'yesterday' => 'Вчера',
            'week' => 'Неделя',
            'month' => 'Месяц',
            'custom' => 'Произвольный период'
        ],
        [
            'class' => 'form-control',
            'onchange' => 'updateChart()'
        ]
    ); ?>

    <div id="date-range" style="display: <?php echo $period === 'custom' ? 'block' : 'none'; ?>; margin: 10px 0">
        <?php echo CHtml::textField('start_date', $startDate, ['class' => 'form-control datepicker', 'placeholder' => 'Начальная дата']); ?>
        <?php echo CHtml::textField('end_date', $endDate, ['class' => 'form-control datepicker', 'placeholder' => 'Конечная дата']); ?>
    </div>

    <button class="btn btn-primary" onclick="updateChart()">Обновить график</button>
</div>

<!-- Элемент для отображения графика -->
<div id="chart-container">
    <?php $this->renderPartial('_voltageChart', ['chartData' => $chartData]); ?>
</div>