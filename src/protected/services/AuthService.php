<?php

use GuzzleHttp\Client;
use GuzzleHttp\Exception\RequestException;

class AuthService {
    private $client;
    private $apiUrl;

    public function __construct() {
        $this->client = new Client();
        $this->apiUrl = 'https://app.yaenergetik.ru/api?v2';
    }

    /**
     * Авторизация пользователя в сети "яЭнергетик"
     * @param string $login Логин пользователя
     * @param string $password Пароль пользователя
     * @return string Идентификатор пользователя
     * @throws InvalidArgumentException Логин или пароль пустые
     * @throws RuntimeException Произошла ошибка при запросе
     * @throws UnexpectedValueException $sessionId не найден в ответе
     * @throws RuntimeException Произошла ошибка при обработке запроса
     */
    public function authenticate(string $login, string $password, string $verifyCode = null): string
    {
        // Проверка наличия логина и пароля
        if (empty($login) || empty($password)) {
            throw new InvalidArgumentException('Login and password should not be empty');
        }

        // Формирование запроса
        $requestData = [
            'jsonrpc' => '2.0',
            'method' => 'auth.login',
            'params' => [
                'mode' => 'user',
                'user' => $login,
                'password' => $password,
                'verifyCode' => $verifyCode
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
