<?php

namespace App\Helpers;

use App\Entity\UserAddress;
use App\Entity\UserPassport;

class LitemfApiService
{
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

        $output = curl_exec($curl);

        return $output;
    }

    // TODO временный метод для тестов
    public function getCountry()
    {
        $params = [
            'filter' => [
                'code' => 'ru'
            ]
        ];

        return $this->execute('getCountry', $params);
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
