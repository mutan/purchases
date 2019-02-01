<?php

namespace App\Helpers;

use Zend\Http\Client;
use Zend\Http\Request;

class ApiService
{
    const LITEMF_HOST = 'https://api.litemf.com/v2/rpc';

    public function call()
    {
        $request = new Request();
        $request->setMethod(Request::METHOD_POST);
        $request->setUri(self::LITEMF_HOST);
        $request->getHeaders()->addHeaders([
            'Content-Type'   => 'application/json',
            'X-Auth-Api-Key' => 'hhhhhhhhhhhhhhhhhhhhhhhhhhhhhhhhhhhhhhhh'
        ]);
        $request->setContent(json_encode([
            'id' => 1,
            'method' => 'getCountry',
            'params' => []
        ]));

        $client = new Client();
        $response = $client->send($request);

        echo $response->getBody(); die('ok');
        
        
    }
}
