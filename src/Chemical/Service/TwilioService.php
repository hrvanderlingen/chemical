<?php

namespace Chemical\Service;

use Twilio\Rest\Client;
use Twilio\Rest\Verify\V2\ServiceContext;
use Twilio\Rest\Verify\V2\Service\VerificationInstance;

class TwilioService implements VerifyServiceInterface
{

    /**
     * Constructor
     *
     * @param array $config
     */
    protected $config = array();

    /**
     *
     * @var Client
     */
    protected $client;

    public function __construct(array $config)
    {
        $this->config = $config;
    }

    /**
     * Create the Twilio client
     * @return void
     */
    protected function createClient(): void
    {
        if (is_null($this->client)) {
            $this->client = new Client(
                $this->config['twilio']['account_sid'], $this->config['twilio']['auth_token']
            );
        }
    }

    public function sendMessage(string $message, string $telNumber)
    {
        $this->createClient();

        $this->client->messages->create($telnumber,
            array(
                'from' => $this->config['twilio']['tel_from'],
                'body' => '$message'
        ));
    }

    /**
     *
     * @return ServiceContext
     */
    protected function getVerify(): ServiceContext
    {
        return $this->client->verify->v2->services($this->config['twilio']['service_sid']);
    }

    /**
     * {@inheritDoc}
     */
    public function sendToken(string $telNumber): string
    {
        $this->createClient();

        $verification = $this->getVerify()
            ->verifications
            ->create($telNumber, "sms");

        return $verification->status;
    }

    /**
     * {@inheritDoc}
     */
    public function verify(string $code, string $telNumber): string
    {
        $this->createClient();

        $verification_check = $this->getVerify()
            ->verificationChecks
            ->create($code, ["to" => $telNumber]
        );

        return $verification_check->status;
    }
}
