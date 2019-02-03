<?php

namespace App\Helpers;

use App\Entity\UserAddress;
use App\Entity\UserPassport;
use Zend\Http\Client;
use Zend\Http\Request;

class LitemfApiService
{
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

    public function createAddress(UserAddress $userAddrss, UserPassport $userPassport)
    {
        $params = [];

        $data['format'] = 'separated';
        $data['name'] = [
            'last_name'   => $userAddrss->getLastName(),
            'first_name'  => $userAddrss->getFirstName(),
            'middle_name' => $userAddrss->getMiddleName(),
        ];
        $data['delivery_country'] = 3159;
        $data['first_line'] = [
            'street' => $userAddrss->getStreet(),
            'house'  => $userAddrss->getHouse()
        ];
        $data['flat']     = $userAddrss->getFlat();
        $data['city']     = $userAddrss->getCity();
        $data['region']   = $userAddrss->getRegion();
        $data['zip_code'] = $userAddrss->getPostCode();
        $data['phone']    = $userAddrss->getPhone();
        $data['email']    = $userAddrss->getEmail();

        $data['passport'] = [
            'series'     => $userPassport->getSeries(),
            'number'     => $userPassport->getNumber(),
            'issue_date' => $userPassport->getGiveDate()->format('Y-m-d'),
            'issued_by'  => $userPassport->getGiveBy(),
            'inn'        => $userPassport->getInn(),
        ];

        $params['data'] = $data;
        
        //dump($params); die('ok');

        $this->call('createAddress', $params);
    }
}
