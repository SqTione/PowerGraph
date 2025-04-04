<?php
/* @var $this SiteController */

$this->pageTitle = 'Мои счётчики |';
?>

<main class="my-meters container">
    <div class="section__title">
        <h1>Мои счётчики</h1>
        <span class="meters-count">10</span>
    </div>
    <hr class="separator">
    <div class="my-meters__list">
        <div class="my-meters__list-container">
            <div class="my-meters__meter meter-card">
                <div class="meter-card__header">
                    <img class="meter-card__meter-icon"
                         src="<?php echo Yii::app()->request->baseUrl; ?>/images/icons/meter.svg" alt="">
                    <h3 class="meter-card__meter-name">Счётчик 92248</h3>
                </div>
                <div class="meter-card__body">
                    <p>Счётчик трансформатора ТП-5</p>
                </div>
            </div>
            <div class="my-meters__meter meter-card">
                <div class="meter-card__header">
                    <img class="meter-card__meter-icon"
                         src="<?php echo Yii::app()->request->baseUrl; ?>/images/icons/meter.svg" alt="">
                    <h3 class="meter-card__meter-name">Счётчик 24512</h3>
                </div>
                <div class="meter-card__body">
                    <p>Счётчик генератора ГР-12</p>
                </div>
            </div>
            <div class="my-meters__meter meter-card">
                <div class="meter-card__header">
                    <img class="meter-card__meter-icon"
                         src="<?php echo Yii::app()->request->baseUrl; ?>/images/icons/meter.svg" alt="">
                    <h3 class="meter-card__meter-name">Счётчик 97214</h3>
                </div>
                <div class="meter-card__body">
                    <p>Счётчик распределительного щита ЩР-7</p>
                </div>
            </div>
            <div class="my-meters__meter meter-card">
                <div class="meter-card__header">
                    <img class="meter-card__meter-icon"
                         src="<?php echo Yii::app()->request->baseUrl; ?>/images/icons/meter.svg" alt="">
                    <h3 class="meter-card__meter-name">Счётчик 58913</h3>
                </div>
                <div class="meter-card__body">
                    <p>Счётчик компрессора КМ-8</p>
                </div>
            </div>
            <div class="my-meters__meter meter-card">
                <div class="meter-card__header">
                    <img class="meter-card__meter-icon"
                         src="<?php echo Yii::app()->request->baseUrl; ?>/images/icons/meter.svg" alt="">
                    <h3 class="meter-card__meter-name">Тестовый счётчик 58913</h3>
                </div>
                <div class="meter-card__body">
                    <p>Счётчик, предназначенный для тестирования системы</p>
                </div>
            </div>
            <div class="my-meters__meter meter-card">
                <div class="meter-card__header">
                    <img class="meter-card__meter-icon"
                         src="<?php echo Yii::app()->request->baseUrl; ?>/images/icons/meter.svg" alt="">
                    <h3 class="meter-card__meter-name">Счётчик 24612</h3>
                </div>
                <div class="meter-card__body">
                    <p>Счётчик вентиляционной системы ВС-10</p>
                </div>
            </div>
            <div class="my-meters__meter meter-card">
                <div class="meter-card__header">
                    <img class="meter-card__meter-icon"
                         src="<?php echo Yii::app()->request->baseUrl; ?>/images/icons/meter.svg" alt="">
                    <h3 class="meter-card__meter-name">Тестовый счётчик 58913</h3>
                </div>
                <div class="meter-card__body">
                    <p>Счётчик, предназначенный для тестирования системы</p>
                </div>
            </div>
            <div class="my-meters__meter meter-card">
                <div class="meter-card__header">
                    <img class="meter-card__meter-icon"
                         src="<?php echo Yii::app()->request->baseUrl; ?>/images/icons/meter.svg" alt="">
                    <h3 class="meter-card__meter-name">Счётчик 24612</h3>
                </div>
                <div class="meter-card__body">
                    <p>Счётчик вентиляционной системы ВС-10</p>
                </div>
            </div>
        </div>
        <button class="button" id="all-meters-button">Все счётчики</button>
    </div>
</main>