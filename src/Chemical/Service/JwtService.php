<?php

namespace Chemical\Service;

use Firebase\JWT\JWT;

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
        return array(
            'Access-Control-Allow-Origin' => $this->config['Access-Control-Allow-Origin'],
            'Access-Control-Allow-Methods' => 'POST,GET',
            'Access-Control-Allow-Credentials' => true,
            'Access-Control-Allow-Headers' => 'Origin,X-Requested-With,Content-Type, '
            . 'Accept,Authorization, Accept-Encoding',
        );
    }

    /**
     * Extract the payload from the authorization string
     * @param string $authorization
     * @return \stdClass
     *
     */
    public function extractPayload(string $authorization): \stdClass
    {
        list($jwt_token) = sscanf($authorization, 'Bearer %s');
        $secretKey = $this->config['jwt_secret'];
        return JWT::decode($jwt_token, $secretKey, array('HS512'));
    }
}
