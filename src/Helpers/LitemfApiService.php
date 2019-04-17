<?php

namespace App\Helpers;

use App\Entity\UserAddress;
use App\Entity\UserPassport;
use Zend\Http\Client;
use Zend\Http\Request;

class LitemfApiService
{
    // TODO переделать на cURL
    // https://stackoverflow.com/questions/5356075/how-to-get-an-option-previously-set-with-curl-setopt
    // https://github.com/sergeiavdeev/EasyWayAPI/blob/master/EasyWay/API/EWConnector.php
    protected function call($method, $params)
    {
        $request = new Request();
        $request->getHeaders()->addHeaders([
            'Content-Type' => 'application/json',
            'X-Auth-Api-Key' => getenv("LITEMF_API_KEY")
        ]);
        $request->setMethod(Request::METHOD_POST)
                ->setUri(getenv("LITEMF_API_HOST"))
                ->setContent(json_encode([
                    'id' => uniqid(),
                    'method' => $method,
                    'params' => $params
                ]));

        $client = new Client();
        $response = $client->send($request);

        echo $response->getBody(); die('ok');
    }

    // TODO временный метод для тестов
    public function getCountry(UserAddress $userAddrss)
    {
        $params = [];

        $this->call('getCountry', $params);
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
        
        //dump($params); die('ok');

        $this->call('createAddress', $params);
    }
}
