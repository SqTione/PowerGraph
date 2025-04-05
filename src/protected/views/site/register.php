<?php
/* @var $this SiteController */

$this->pageTitle = Yii::app()->name . ' Регистрация |';
?>

<main class="auth container">
    <div class="auth__form form" id="registration-form">
        <div class="form__header">
            <h1>Регистрация</h1>
            <p>Уже есть аккаунт?</p>
            <a href="<?php echo Yii::app()->createUrl('site/index') ?>">Войдите!</a>
            <hr class="separator">
        </div>
        <div class="form__body">
            <div class="form__field">
                <label for="registration-form__email">Email:</label>
                <input
                    type="email"
                    name="registration-form__email"
                    id="registration-form__email"
                    placeholder="email@mail.ru">
            </div>
            <div class="form__field">
                <label for="registration-form__password">Пароль:</label>
                <input
                    type="password"
                    name="registration-form__password"
                    id="registration-form__password"
                    placeholder="Ваш пароль">
            </div>
            <div class="form__field">
                <label for="registration-form__password-repeat">Пароль:</label>
                <input
                    type="password"
                    name="registration-form__password-repeat"
                    id="registration-form__password-repeat"
                    placeholder="Ваш пароль">
            </div>
            <div class="form__checkbox">
                <label class="custom-checkbox">
                    <input type="checkbox" name="registration-form__password" id="registration-form__checkbox">
                    <span class="checkmark"></span>
                    Запомнить меня
                </label>
            </div>
        </div>
        <div class="form__footer">
            <button type="submit" class="form__submit button">Зарегистрироваться</button>
        </div>
    </div>
</main>