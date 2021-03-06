<?php

namespace App\Services;

use Exception;
use Throwable;
use App\Entity\UserAddress;
use App\Entity\UserPassport;
use Psr\Log\LoggerInterface;

/**
 * @link https://docs.google.com/document/d/1KrqvjpwUxt5Id02bsXilMcYQScDcyxpyKnEpIV5lXe0/edit
 */
class LitemfApiService
{
    private $logger;

    public function __construct(LoggerInterface $logger)
    {
        $this->logger = $logger;
    }

    const API_URL = 'https://api.litemf.com/v2/rpc';

    protected function execute($method, $data)
    {
        $curl = curl_init();
        curl_setopt_array($curl, [
            CURLOPT_URL => self::API_URL,
            CURLOPT_RETURNTRANSFER => true,
            CURLOPT_SSL_VERIFYPEER => false,
            CURLOPT_POST => true,
            CURLOPT_HTTPHEADER => [
                'Content-Type: application/json',
                'X-Auth-Api-Key: ' . getenv("LITEMF_API_KEY"),
                'Cache-Control: no-cache',
            ],
            CURLOPT_POSTFIELDS  => json_encode([
                'id' => uniqid('bty_'),
                'method' => $method,
                'params' => $data
            ]),
        ]);

        try {
            $response = curl_exec($curl);
            $code = curl_getinfo($curl, CURLINFO_HTTP_CODE);
            curl_close($curl);
        } catch (Throwable $e) {
            if (is_resource($curl)) {
                curl_close($curl);
            }
            $this->logger->error('LiteMF API server error: ' . $e->getMessage());
            throw new Exception('(Exception) LiteMF API server error: ' . $e->getMessage(), null, $e);
        }

        $response = json_decode($response, true);
        if (empty($response)) {
            $this->logger->error('LiteMF API invalid json response.', [
                'code' => $code,
                'response' => $response
            ]);
            throw new Exception('(Exception) LiteMF API invalid json response: ' . $response['error']['message']);
        }

        if ($code != 200) {
            $this->logger->error('LiteMF API error: wrong response code', [
                'code' => $code,
                'response' => $response
            ]);
            throw new Exception('(Exception) LiteMF API error: wrong response code: ' . $code);
        }

        if (!array_key_exists('status', $response) || !array_key_exists('result', $response)) {
            $this->logger->error('LiteMF API error: invalid response body', ['response' => $response]);
            throw new Exception('(Exception) LiteMF API error: invalid response body.');
        }

        if ($response['status'] != 'ok') {
            $this->logger->error('LiteMF API response error.', [
                'code' => $response['error']['code'] ?? '',
                'message' => $response['error']['message'] ?? '',
            ]);
            throw new Exception('(Exception) LiteMF API response error: ' . $response['error']['message']);
        }

        return $response['result'];
    }

    // TODO временный метод для тестов
    /**
     * @return array(jsonrpc, id, result, status)
     */
    public function getCountry()
    {
        $params = [
            'filter' => [
                'code' => 'ru'
            ]
        ];

        //$params = [];

        return $this->execute('getCountr', $params);
    }

    public function createAddress(UserAddress $userAddress, UserPassport $userPassport)
    {
        $params = [];

        $data['format'] = 'separated';
        $data['name'] = [
            'last_name'   => $userAddress->getLastName(),
            'first_name'  => $userAddress->getFirstName(),
            'middle_name' => $userAddress->getMiddleName(),
        ];
        $data['delivery_country'] = 3159;
        $data['first_line'] = [
            'street' => $userAddress->getStreet(),
            'house'  => $userAddress->getHouse()
        ];
        $data['flat']     = $userAddress->getFlat();
        $data['city']     = $userAddress->getCity();
        $data['region']   = $userAddress->getRegion();
        $data['zip_code'] = $userAddress->getPostCode();
        $data['phone']    = $userAddress->getPhone();
        $data['email']    = $userAddress->getEmail();

        $data['passport'] = [
            'series'     => $userPassport->getSeries(),
            'number'     => $userPassport->getNumber(),
            'issue_date' => $userPassport->getGiveDate()->format('Y-m-d'),
            'issued_by'  => $userPassport->getGiveBy(),
            'birth_date' => $userPassport->getBirthDate()->format('Y-m-d'),
            'inn'        => $userPassport->getInn(),
            'status'     => 'confirmed',
        ];

        $params['data'] = $data;

        return $this->execute('createAddress', $params);
    }
}
