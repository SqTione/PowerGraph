<?php /* @var $this Controller */
$this->pageTitle = '';
?>

<!DOCTYPE html>
<html lang="ru">
<head>
  <meta charset="UTF-8">
  <meta name="viewport"
        content="width=device-width, user-scalable=no, initial-scale=1.0, maximum-scale=1.0, minimum-scale=1.0">
  <meta http-equiv="X-UA-Compatible" content="ie=edge">
  <meta name="language" content="ru">

  <link rel="icon" href="<?php echo Yii::app()->request->baseUrl; ?>/images/icons/meter.svg">
  <link rel="shortcut icon" href="<?php echo Yii::app()->request->baseUrl; ?>/images/icons/meter.svg">

  <!-- CSS -->
  <link rel="stylesheet" href="<?php echo Yii::app()->request->baseUrl; ?>/css/style.css" type="text/css">

  <title><?php echo CHtml::encode($this->pageTitle); ?> PowerGraph </title>
</head>
<body>
<div id="page">
  <!-- Header -->
  <div class="container" id="header">
    <a href="#" class="logo">
      <img src="<?php Yii::app()->request->baseUrl; ?>/images/logo.svg" alt="">
    </a>
    <a href="#" class="logout">
      <img src="<?php echo Yii::app()->request->baseUrl; ?>/images/icons/logout.svg" alt="">
    </a>
  </div>

  <!-- Page content -->
  <?php echo $content; ?>

  <!-- Footer -->
  <div class="container" id="footer">
    <div class="footer__info">
      <a href="#" class="logo">
        <img src="<?php Yii::app()->request->baseUrl; ?>/images/logo.svg" alt="">
      </a>
      <p>Данный проект предназначен для удобного отслеживания мгновенных показателей со счётчиков по фазам A, B и C.
        Проект разработан в рамках производственной практики для "ООО Технологии энергоучёта".</p>
    </div>
    <div class="footer__group">
      <nav class="footer__nav">
        <ul>
          <li><a href="<?php echo Yii::app()->createUrl('site/usermeters'); ?>">Мои счётчики</a></li>
          <li><a href="#">Выйти из аккаунта</a></li>
        </ul>
      </nav>
      <div class="footer__contacts-container">
        <ul class="footer__contacts">
          <li>
            <a href="mailto:powergraph@mail.ru">
              <img src="<?php Yii::app()->request->baseUrl; ?>/images/icons/email.svg" alt="">
              powergraph@mail.ru
            </a>
          </li>
          <li>
            <a href="tel:+79123456789">
              <img src="<?php Yii::app()->request->baseUrl; ?>/images/icons/phone.svg" alt="">
              +7 (912) 345-67-89
            </a>
          </li>
        </ul>
        <ul class="footer__socials">
          <li>
            <a href="http://t.me/sqtione">
              <img
                src="<?php Yii::app()->request->baseUrl; ?>/images/icons/telegram.svg"
                alt="">
            </a>
          </li>
          <li>
            <a href="https://github.com/SqTione/PowerGraph">
              <img
                src="<?php Yii::app()->request->baseUrl; ?>/images/icons/github.svg"
                alt="">
            </a>
          </li>
        </ul>
      </div>
    </div>
    <div class="footer__copyright">
      <p>PowerGraph &copy; <?php echo date('Y'); ?></p>
      <p class="footer__designer">Designed by <a href="#">SqTione</a></p>
    </div>
  </div>
</div>
</body>
</html>