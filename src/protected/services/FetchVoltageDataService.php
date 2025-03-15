<?php

use GuzzleHttp\Client;
use GuzzleHttp\Exception\RequestException;

Yii::import('application.components.DateFormatting.RFC3339DataFormatting');

class FetchVoltageDataService {
    public $client;
    private $apiUrl;
    private $sessionId;

    public function __construct(string $sessionId) {
        $this->client = new Client();
        $this->apiUrl = 'https://app.yaenergetik.ru/api?v2';
        $this->sessionId = $sessionId;
    }

    /**
     * Получение данных по счётчику за конкретный период
     * @param int $meterId ID счётчика
     * @param string $periodType Тип периода (moment|hour|day|month|year)
     * @param string $periodValue Период (напр. 2017-10-01T00:00:00+03:00)
     * @param int $limit Ограничение количества точек (стан. 1000)(макс. 10000)
     * @throws InvalidArgumentException Один из обязательных параметров пуст
     * @throws UnexpectedValueException Значение $limit больше 10000
     * @throws RuntimeException Произошла ошибка при обработке запроса
     */
    public function fetchVoltageData(int $meterId, string $periodType, string $periodValue, int $limit = 1000) {
        // Проверка наличия id счетчика, типа периода и значения периода
        if (empty($meterId) || empty($periodType) || empty($periodValue)) {
            throw new \InvalidArgumentException("Parameters 'meterId', 'periodType', and 'periodValue' must be set");
        }

        // Валидация periodType
        $allowedPeriodTypes = ['moment', 'hour', 'day', 'month', 'year'];
        if (!in_array($periodType, $allowedPeriodTypes)) {
            throw new \InvalidArgumentException("Invalid period type. Allowed values: " . implode(', ', $allowedPeriodTypes));
        }

        // Форматирование даты
        $dataFormating = new RFC3339DataFormatting();
        $periodValue = $dataFormating->formatDate($periodType, $periodValue);

        // Проверка лимита
        if ($limit > 10000) {
            throw new \UnexpectedValueException('Limit must be lower than or equal to 10000');
        }

        // Формирование запроса
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

        echo "Request data: " . print_r($requestData, true);

        try {
            // Выполнение запроса
            $response = $this->client->post($this->apiUrl, [
                'headers' => [
                    'Content-Type' => 'application/json',
                    'X-Session-Id' => $this->sessionId,
                ],
                'body' => json_encode($requestData)
            ]);

            $result = json_decode($response->getBody(), true);

            // Логирование ответа
            Yii::log(CJSON::encode($result), CLogger::LEVEL_INFO, 'api');
            echo "API raw response: " . print_r($result, true);

            // Проверка наличия ошибок в ответе API
            if (isset($result['error'])) {
                throw new \RuntimeException("API error: {$result['error']['message']}");
            }

            // Проверка наличия данных
            if (empty($result['result']['data'])) {
                throw new \UnexpectedValueException('Voltage data not found in the response');
            }

            // Форматирование полученных данных
            $formatedData = [];
            $headers = $result['result']['headers'];

            foreach ($result['result']['data'] as $row) {
                $timestamp = new \DateTime($row[0]);
                $timestamp->format('Y-m-d H:i:s');

                foreach ($headers as $index => $header) {
                    if ($header['type'] === 'value') {
                        $formatedData[] = [
                            'timestamp' => $timestamp->format('Y-m-d H:i:s'),
                            'phase_type' => $header['name'],
                            'value' => $row[$index] ?? null,
                        ];
                    }
                }
            }

            Yii::log("API response: " . print_r($result, true), CLogger::LEVEL_ERROR);
            echo "Result: " . print_r($formatedData, true);
            return $formatedData;
        } catch (RequestException $e) {
            // Обработка ошибок запроса
            throw new \RuntimeException('An error occurred during HTTP request: ' . $e->getMessage(), $e->getCode(), $e);
        } catch (\Exception $e) {
            // Обработка прочих ошибок
            throw new \RuntimeException('Unexpected error occurred: ' . $e->getMessage(), $e->getCode(), $e);
        }
    }
}