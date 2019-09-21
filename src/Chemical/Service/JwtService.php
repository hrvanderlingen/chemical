<?php

namespace Chemical\Service;

/**
 * Class for JSON web tokens
 * inspired by https://www.sitepoint.com/php-authorization-jwt-json-web-tokens/
 */
class JwtService
{

    protected $config = array();

    /**
     *
     * @var array
     */
    protected $user;

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
     * Configure and return data array
     * @return array
     */
    public function setJwtData()
    {
        if (is_null($this->user)) {
            throw new \Exception('Set a validated user');
        }
        $tokenId = base64_encode(random_bytes(5));
        $issuedAt = time();
        $notBefore = $issuedAt - 10;
        $expire = $notBefore + 60 * 60;
        $serverName = $this->config['jwt_servername'];
        $data = array(
            'iat' => $issuedAt,
            'jti' => $tokenId,
            'iss' => $serverName,
            'nbf' => $notBefore,
            'exp' => $expire,
            'data' => $this->user
        );
        return $data;
    }

    /**
     * Getter
     * @return array
     */
    public function getUser()
    {
        return $this->user;
    }

    /**
     * Setter
     * @param array $user
     */
    public function setUser($user)
    {
        $this->user = $user;
    }

    /**
     * Return an array of access control headers
     * @return array
     */
    public function getAccessControlHeaders()
    {
        return $headers = array(
            'Access-Control-Allow-Origin' => '*',
            'Access-Control-Allow-Methods' => 'POST,GET',
            'Access-Control-Allow-Headers' => 'Origin, X-Requested-With, Content-Type, '
            . 'Accept, Authorization, Access-Control-Allow-Origin',
        );
    }
}
