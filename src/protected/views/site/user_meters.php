<?php
/* @var $this SiteController */

$this->pageTitle = 'Мои счётчики |';
?>

<main class="my-meters container">
    <div class="section__title">
        <h1>Мои счётчики</h1>
        <span class="meters-count"><?php echo count($meters); ?></span>
    </div>
    <hr class="separator">
    <div class="my-meters__list">
        <div class="my-meters__list-container">
            <?php foreach ($meters as $meter): ?>
                <div class="my-meters__meter meter-card" data-id="<?php echo $meter->id; ?>">
                    <div class="meter-card__header">
                        <img class="meter-card__meter-icon"
                             src="<?php echo Yii::app()->request->baseUrl; ?>/images/icons/meter.svg" alt="">
                        <h3 class="meter-card__meter-name"><?php echo CHtml::encode($meter->name); ?></h3>
                    </div>
                    <div class="meter-card__body">
                        <p><?php echo CHtml::encode($meter->description); ?></p>
                    </div>
                </div>
            <?php endforeach; ?>
        </div>
        <button class="button" id="all-meters-button">Все счётчики</button>
    </div>
</main>

<script>
    $(document).ready(function () {
        $('.my-meters__meter').on('click', function () {
            // Получаем ID счётчика из data-атрибута
            const meterId = $(this).data('id');

            // Формируем URL для переадресации
            const url = '<?php echo Yii::app()->createUrl("site/meter"); ?>/' + meterId;

            // Переадресация на страницу
            window.location.href = url;
        });
    });
</script>