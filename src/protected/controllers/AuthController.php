<?php

use GuzzleHttp\Client;

class AuthController extends CController {
    private $authService;

    public function __construct($id, $module = null)
    {
        parent::__construct($id, $module);

        // Инициализация сервиса авторизации
        $client = new Client();
        $apiUrl = 'https://app.yaenergetik.ru/api?v2';
        $this->authService = new AuthService($client, $apiUrl);
    }

    /**
     * Метод для авторизации
    */
    public function actionAuthenticate() {
        $login = Yii::app()->request->getParam('login');
        $password = Yii::app()->request->getParam('password');
        $verifyCode = Yii::app()->request->getParam('verifyCode') ?? null;

        try {
            $sessionId = $this->authService->authenticate($login, $password, $verifyCode);

            // Сохраняем sessionId в сессии
            Yii::app()->session['sessionId'] = $sessionId;

            echo CJSON::encode(['sessionId' => $sessionId]);
        } catch (InvalidArgumentException $e) {
            echo CJSON::encode(['error' => $e->getMessage()]);
        } catch (RuntimeException $e) {
            echo CJSON::encode(['error' => $e->getMessage()]);
        } catch (Exception $e) {
            echo CJSON::encode(['error' => 'An unexpected error occurred' . $e->getMessage()]);
        }
    }
}
