<?php

namespace Chemical\Service;

use GuzzleHttp\Client;
use GuzzleHttp\Exception\ClientException;
use GuzzleHttp\psr7;

class RscService
{

    protected $config = array();

    /**
     * Constructor
     *
     * @param array $config
     */
    public function __construct(array $config)
    {
        $this->config = $config;
    }

    /**
     * Call RSC and return data as array
     * @return array
     */
    public function getSources()
    {
        $client = new Client([
            'base_uri' => 'https://api.rsc.org',
            'timeout' => 2.0,
        ]);

        $headers = [
            'apikey' => $this->config['rsc_key'],
            'content-type' => ''
        ];

        try {
            $response = $client->request('GET', '/compounds/v1/lookups/datasources', ['headers' => $headers]);
        } catch (ClientException $e) {

            $data = array(
                'success' => false,
                'message' => Psr7\str($e->getResponse()),
            );
            return ['body' => $data, 'code' => $e->getCode()];
        }

        $code = $response->getStatusCode();

        if ($code == '200') {
            $dataString = (string) $response->getBody();
            $sources = json_decode($dataString, true);
            $data = [];
            foreach ($sources['dataSources'] as $source) {
                $data[] = ['title' => $source];
            }
        } else {
            $data = array(
                'success' => false,
                'message' => $response->getReasonPhrase(),
            );
        }
        return ['body' => $data, 'code' => $code];
    }
}
