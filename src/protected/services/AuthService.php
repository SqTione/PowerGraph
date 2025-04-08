<?php

use GuzzleHttp\Client;
use GuzzleHttp\Exception\RequestException;

class AuthService {
    private $client;
    private $apiUrl;
    private $login;
    private $password;
    private $verifyCode;

    public function __construct(string $login, string $password, string $verifyCode = null) {
        $this->client = new Client();
        $this->apiUrl = 'https://app.yaenergetik.ru/api?v2';
        $this->login = $login;
        $this->password = $password;
        $this->verifyCode = $verifyCode;
    }

    /**
     * Авторизация пользователя в сети "яЭнергетик"
     * @return string Идентификатор пользователя
     * @throws InvalidArgumentException Логин или пароль пустые
     * @throws RuntimeException Произошла ошибка при запросе
     * @throws UnexpectedValueException $sessionId не найден в ответе
     * @throws RuntimeException Произошла ошибка при обработке запроса
     */
    public function authenticate(): string
    {
        // Проверка наличия логина и пароля
        if (empty($this->login) || empty($this->password)) {
            throw new InvalidArgumentException('Login and password should not be empty');
        }

        // Формирование запроса
        $requestData = [
            'jsonrpc' => '2.0',
            'method' => 'auth.login',
            'params' => [
                'mode' => 'user',
                'user' => $this->login,
                'password' => $this->password,
                'verifyCode' => $this->verifyCode
            ],
            'id' => 1
        ];

        try {
            // Выполнение запроса
            $response = $this->client->post($this->apiUrl, [
                'headers' => ['Content-Type' => 'application/json'],
                'body' => json_encode($requestData)
            ]);

            $result = json_decode($response->getBody(), true);
            Yii::log("Auth response: " . print_r($result, true), CLogger::LEVEL_INFO);

            $sessionId = $result['result'];

            if ($result['error']['code'] === 10105) {
                // Проверка входа в аккаунт
                throw new InvalidArgumentException('Login or password is incorrect');
            } elseif (!isset($sessionId)) {
                // Проверка наличия sessionId в ответе
                throw new UnexpectedValueException('Failed to retrieve session ID from the response');
            }

            return $sessionId;
        } catch (RequestException $e) {
            // Обработка ошибок запроса
            throw new RuntimeException('An error occurred during HTTP request: ' . $e->getMessage(), $e->getCode(), $e);
        } catch (Exception $e) {
            // Обработка прочих ошибок
            throw new RuntimeException('Unexpected error occurred: ' . $e->getMessage(), $e->getCode(), $e);
        }
    }
}
