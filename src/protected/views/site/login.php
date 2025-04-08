<?php
/* @var $this SiteController */

$this->pageTitle = Yii::app()->name . ' Вход |';
?>

<main class="auth container">
    <div class="auth__form form" id="login-form">
        <div class="form__header">
            <h1>Вход</h1>
            <p>Ещё нет аккаунта?</p>
            <a href="<?php echo Yii::app()->createUrl('site/register'); ?>">Зарегистрируйтесь!</a>
            <hr class="separator">
        </div>
        <div class="form__body">
            <div class="form__field">
                <label for="login-form__email">Email:</label>
                <input
                    type="email"
                    name="login-form__email"
                    id="login-form__email"
                    placeholder="email@mail.ru">
            </div>
            <div class="form__field">
                <label for="login-form__password">Пароль:</label>
                <input
                    type="password"
                    name="login-form__password"
                    id="login-form__password"
                    placeholder="Ваш пароль">
            </div>
            <div class="form__checkbox">
                <label class="custom-checkbox">
                    <input type="checkbox" name="login-form__password" id="login-form__checkbox">
                    <span class="checkmark"></span>
                    Запомнить меня
                </label>
            </div>
        </div>
        <div class="form__footer">
            <button type="submit" class="form__submit button">Войти</button>
        </div>
    </div>
</main>