<?php

namespace Chemical\Service;

interface VerifyServiceInterface
{

    /**
     * Send a token to the verification provider
     * @param string $telNumber
     * @return string
     */
    public function sendToken(string $telNumber): string;

    /**
     * Very the token with the verification provider
     * @param string $code
     * @param string $telNumber
     * @return string
     */
    public function verify(string $code, string $telNumber): string;
}
