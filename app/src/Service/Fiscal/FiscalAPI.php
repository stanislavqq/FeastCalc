<?php

namespace App\Service\Fiscal;

use GuzzleHttp\Client;
use Symfony\Component\DependencyInjection\Attribute\Autowire;
use Symfony\Component\HttpFoundation\Response;

class FiscalAPI
{
    private Client $client;
    public function __construct(
        #[Autowire(env: "FISCAL_URI")]
        private readonly string $baseUri,
        #[Autowire(env: "FISCAL_TOKEN")]
        private readonly string $apiToken,
    )
    {
        $this->client = new Client(["base_uri" => $this->baseUri]);
    }


    public function loadFiscalData(string $qrData) : array
    {
        $response = $this->client->request('POST', '/api/v1/check/get', [
            "form_params" => [
                "qrraw" => $qrData,
                "token" => $this->apiToken,
            ]
        ]);


        if ($response->getStatusCode() === Response::HTTP_OK) {
            $responseData = json_decode($response->getBody()->getContents(), true);
            return $responseData["data"]["json"];
        }

        return [];

        //        curl --location 'https://proverkacheka.com/api/v1/check/get'
//    --header 'Cookie: ENGID=1.1'
//    --form 'qrraw="<То что ты засканишь на чеке>"'
//    --form 'token="<Твой Токен на сайте>'
    }
}
