<?php

namespace App\Helpers;

use App\Entity\UserAddress;
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

    public function createAddress(UserAddress $userAddrss)
    {
        $params = [];

        $this->call('getCountry', $params);
    }
}
