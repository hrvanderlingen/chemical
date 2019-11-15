<?php

namespace Chemical\Service;

use GuzzleHttp\Client;
use GuzzleHttp\Exception\ClientException;
use GuzzleHttp\psr7;

class ExistDbService
{

    protected $config = array();
    protected $client;
    protected $collection;

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
     * Initiate a http client
     * @return $this
     */
    public function connect()
    {
        $this->client = new Client([
            'base_uri' => $this->config['existdb_Host'],
            'timeout' => 2.0,
        ]);
        return $this;
    }

    /**
     * Query an eXist-db database
     * @param string $method
     * @param string $queryStr
     * @return string The raw query result as a string
     */
    public function query($method, $queryStr)
    {

        $path = sprintf("%s/db/%s?_query=%s", $this->config['existdb_Path'], $this->collection, $queryStr);

        try {
            $response = $this->client->request($method, $path, ['auth' => $this->getAuthentication()]);
        } catch (ClientException $e) {

            $data = array(
                'success' => false,
                'message' => Psr7\str($e->getResponse()),
            );
            return ['body' => $data, 'code' => $e->getCode()];
        }

        return (string) $response->getBody();
    }

    public function setCollection($collection)
    {
        $this->collection = $collection;
        return $this;
    }

    protected function getAuthentication()
    {
        return [
            $this->config['existdb_User'],
            $this->config['existdb_Pass']
        ];
    }
}
