<?php

namespace Chemical\Service;

use GuzzleHttp\Client;
use GuzzleHttp\psr7;
use GuzzleHttp\Exception\ClientException;

class ExistDbService
{

    protected $config = array();
    protected $client;
    protected $collection;
    protected $hasError = false;
    protected $errorMessage;

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
     * Query an eXist-db database
     * @param string $method
     * @param string $queryStr
     * @return string|false The raw query result as a string or false in the event of an error
     */
    public function query($method, $queryStr)
    {

        $path = sprintf("%s/db/%s?_query=%s", $this->config['existdb_Path'], $this->collection, $queryStr);

        try {
            $this->client = new Client([
                'base_uri' => $this->config['existdb_Host'],
                'timeout' => 2.0,
            ]);

            $response = $this->client->request($method, $path, ['auth' => $this->getAuthentication()]);
            return (string) $response->getBody();
        } catch (ClientException $e) {
            $this->hasError = true;
            $this->errorMessage = Psr7\str($e->getMessage());
        } catch (\Exception $e) {
            $this->hasError = true;
            $this->errorMessage = $e->getMessage();
        }
        return false;
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

    function getHasError()
    {
        return $this->hasError;
    }

    function getErrorMessage()
    {
        return $this->errorMessage;
    }
}
