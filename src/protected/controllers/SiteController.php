<?php

use Couchbase\Meter;

class SiteController extends Controller
{
	/**
	 * Declares class-based actions.
	 */
	public function actions()
	{
		return array(
			// captcha action renders the CAPTCHA image displayed on the contact page
			'captcha'=>array(
				'class'=>'CCaptchaAction',
				'backColor'=>0xFFFFFF,
			),
			// page action renders "static" pages stored under 'protected/views/site/pages'
			// They can be accessed via: index.php?r=site/page&view=FileName
			'page'=>array(
				'class'=>'CViewAction',
			),
		);
	}

	/**
	 * Отрисовывает основную страницу(вход в аккаунт)
	 */
	public function actionIndex()
	{
		// renders the view file 'protected/views/site/index.php'
		// using the default layout 'protected/views/layouts/main.php'
		$this->render('login');
	}

    /**
     * Отрисовывает страницу регистрации
    */
    public function actionRegister() {
        $this->render('register');
    }

    /**
     * Отрисовывает страницу мои счётчики
    */
    public function actionUserMeters() {
        // Получаем все счётчики из БД
        $meters = Meters::model()->findAll();

        // Передаем данные в представление
        $this->render('user_meters', [
            'meters' => $meters,
        ]);
    }

    public function actionUpdateUserMeter()
    {
        if (Yii::app()->request->isAjaxRequest && isset($_POST['id'])) {
            $id = (int)$_POST['id'];

            // Проверяем существование счётчика
            $meter = Meters::model()->findByPk($id);
            if (!$meter) {
                echo CJSON::encode(['success' => false, 'message' => 'Счётчик не найден.']);
                Yii::app()->end();
            }

            // Обновляем данные
            $meter->name = $_POST['name'];
            $meter->description = $_POST['description'];

            // Сохраняем изменения
            if ($meter->save()) {
                header('Content-Type: application/json');
                echo CJSON::encode(['success' => true, 'message' => 'Данные успешно обновлены.']);
            } else {
                header('Content-Type: application/json');
                echo CJSON::encode(['success' => false, 'message' => 'Ошибка при сохранении данных.']);
            }

            Yii::app()->end();
        } else {
            header('Content-Type: application/json');
            echo CJSON::encode(['success' => false, 'message' => 'Неверный запрос.']);
            Yii::app()->end();
        }
    }

    /**
     * Отрисовывает страницу счётчика
    */
    public function actionMeter($id) {
        // Получаем данные счётчика

        $meter = Meters::model()->findByPk($id);
        if (!$meter) {
            throw new CHttpException(404, 'Счётчик не найден.');
        }

        // Устанавливаем фиксированный период "сегодня" для начальной загрузки
        $period = 'today';

        // Получаем данные для графика
        $service = new PrepareVoltageDataService($id, $period);
        $chartData = $service->prepareVoltageData();

        // Передаем данные в представление
        $this->render('meter', [
            'meter' => $meter,
            'chartData' => $chartData['chartData'],
        ]);
    }

	/**
	 * This is the action to handle external exceptions.
	 */
	public function actionError()
	{
		if($error=Yii::app()->errorHandler->error)
		{
			if(Yii::app()->request->isAjaxRequest)
				echo $error['message'];
			else
				$this->render('error', $error);
		}
	}

	/**
	 * Displays the contact page
	 */
	public function actionContact()
	{
		$model=new ContactForm;
		if(isset($_POST['ContactForm']))
		{
			$model->attributes=$_POST['ContactForm'];
			if($model->validate())
			{
				$name='=?UTF-8?B?'.base64_encode($model->name).'?=';
				$subject='=?UTF-8?B?'.base64_encode($model->subject).'?=';
				$headers="From: $name <{$model->email}>\r\n".
					"Reply-To: {$model->email}\r\n".
					"MIME-Version: 1.0\r\n".
					"Content-Type: text/plain; charset=UTF-8";

				mail(Yii::app()->params['adminEmail'],$subject,$model->body,$headers);
				Yii::app()->user->setFlash('contact','Thank you for contacting us. We will respond to you as soon as possible.');
				$this->refresh();
			}
		}
		$this->render('contact',array('model'=>$model));
	}

	/**
	 * Displays the login page
	 */
	public function actionLogin()
	{
		$model=new LoginForm;

		// if it is ajax validation request
		if(isset($_POST['ajax']) && $_POST['ajax']==='login-form')
		{
			echo CActiveForm::validate($model);
			Yii::app()->end();
		}

		// collect user input data
		if(isset($_POST['LoginForm']))
		{
			$model->attributes=$_POST['LoginForm'];
			// validate user input and redirect to the previous page if valid
			if($model->validate() && $model->login())
				$this->redirect(Yii::app()->user->returnUrl);
		}
		// display the login form
		$this->render('login',array('model'=>$model));
	}

	/**
	 * Logs out the current user and redirect to homepage.
	 */
	public function actionLogout()
	{
		Yii::app()->user->logout();
		$this->redirect(Yii::app()->homeUrl);
	}
}