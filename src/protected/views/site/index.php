<?php
/* @var $this SiteController */

$this->pageTitle=Yii::app()->name;
?>

<h1>Welcome to <i><?php echo CHtml::encode(Yii::app()->name); ?></i></h1>

<p>Congratulations! You have successfully created your Yii application.</p>

<p>You may change the content of this page by modifying the following two files:</p>
<ul>
	<li>View file: <code><?php echo __FILE__; ?></code></li>
	<li>Layout file: <code><?php echo $this->getLayoutFile('main'); ?></code></li>
</ul>

<p>For more details on how to further develop this application, please read
the <a href="https://www.yiiframework.com/doc/">documentation</a>.
Feel free to ask in the <a href="https://www.yiiframework.com/forum/">forum</a>,
should you have any questions.</p>


<!-- Элемент canvas для графика -->
<canvas id="myChart" width="400" height="200"></canvas>

<!-- Скрипт инициализации графика -->
<script type="text/javascript">
    // Дожидаемся загрузки DOM
    document.addEventListener('DOMContentLoaded', function() {
        // Настройки графика
        const ctx = document.getElementById('myChart').getContext('2d');
        const myChart = new Chart(ctx, {
            type: 'line', // Тип графика: линейный
            data: {
                labels: ['Янв', 'Фев', 'Мар', 'Апр', 'Май', 'Июн'], // Оси X
                datasets: [{
                    label: 'Продажи',
                    data: [12, 19, 3, 5, 2, 10], // Данные для линии
                    backgroundColor: 'rgba(75, 192, 192, 0.2)',
                    borderColor: 'rgba(75, 192, 192, 1)',
                    borderWidth: 2
                }]
            },
            options: {
                responsive: false, // Отключаем адаптивность (для примера)
                scales: {
                    y: {
                        beginAtZero: true // Начинать ось Y с нуля
                    }
                }
            }
        });
    });
</script>