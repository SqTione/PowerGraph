<?php

require 'vendor/autoload.php';

// Настройки
$login = 'demo@demo.demo';
$password = 'demo';
$meterId = 92607;
$periodType = 'hour';
$periodValue = date('Y-m-d\TH:i:sP');
$limit = 5;

// Авторизация
try {
    $auth = new \app\services\AuthService($login, $password);
    $sessionId = $auth->authenticate();
    echo "Успешная авторизация. Session ID: $sessionId\n";
} catch (Exception $e) {
    die("Ошибка авторизации: " . $e->getMessage());
}

// Формирование запроса к API
$apiUrl = 'https://app.yaenergetik.ru/api?v2';
$requestData = [
    'jsonrpc' => '2.0',
    'method' => 'currentValue.data',
    'params' => [
        'meter' => $meterId,
        'name' => 'voltage',
        'period' => [
            'type' => $periodType,
            'value' => $periodValue
        ],
        'limit' => $limit
    ],
    'id' => 1
];

// Выполнение запроса
$client = new GuzzleHttp\Client();
try {
    $response = $client->post($apiUrl, [
        'headers' => [
            'Content-Type' => 'application/json',
            'X-Session-Id' => $sessionId
        ],
        'body' => json_encode($requestData)
    ]);

    $result = json_decode($response->getBody(), true);
    echo "Ответ API:\n";
    print_r($result);

    if (!empty($result['result']['data'])) {
        echo "Данные получены успешно!";
    } else {
        echo "Данные отсутствуют. Проверьте meter_id и временной диапазон.";
    }
} catch (Exception $e) {
    die("Ошибка запроса: " . $e->getMessage());
}