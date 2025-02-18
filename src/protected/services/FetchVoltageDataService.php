<?php
use GuzzleHttp\Client;
use GuzzleHttp\Exception\RequestException;

class FetchVoltageDataService {
    private $client;
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
            throw new InvalidArgumentException("Parameters 'meterId', 'periodType', and 'periodValue' must be set");
        }

        // Проверка и преобразование формата periodValue
        try {
            $dateTime = new DateTime($periodValue);
            $periodValue = $dateTime->format('Y-m-d\TH:i:sP'); // Формат ISO 8601
        } catch (Exception $e) {
            throw new InvalidArgumentException("Invalid period value format. Expected format: YYYY-MM-DDTHH:MM:SS±HH:MM");
        }

        if ($limit > 10000) {
            throw new UnexpectedValueException('Limit must be lower than or equal to 10000');
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

            // Проверка наличия ошибок в ответе API
            if (isset($result['error'])) {
                throw new RuntimeException("API error: {$result['error']['message']}");
            }

            // Вывод нашего запроса (удалить)
            echo CJSON::encode($result);

            // Проверка наличия данных
            if (empty($result['result']['data'])) {
                throw new UnexpectedValueException('Voltage data not found in the response');
            }

            return $result['result']['data'];
        } catch (RequestException $e) {
            // Обработка ошибок запроса
            throw new RuntimeException('An error occurred during HTTP request: ' . $e->getMessage(), $e->getCode(), $e);
        } catch (Exception $e) {
            // Обработка прочих ошибок
            throw new RuntimeException('Unexpected error occurred: ' . $e->getMessage(), $e->getCode(), $e);
        }
    }
}