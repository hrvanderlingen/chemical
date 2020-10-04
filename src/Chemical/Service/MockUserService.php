<?php

namespace Chemical\Service;

use Chemical\Entity\User;

class MockUserService implements UserServiceInterface
{

    /**
     * Constructor
     *
     * @param array $config
     */
    protected $config = array();

    public function __construct(array $config)
    {
        $this->config = $config;
    }

    protected function returnMockCredentials()
    {
        return [
            [
                'email' => 'test@example.com',
                'firstname' => 'Peter',
                'lastname' => 'Smith',
                'role' => 'Admin',
                'phone' => $this->config['twilio']['debug_tel_to'],
                'hash' => password_hash('notsosecret', PASSWORD_DEFAULT)
            ]
        ];
    }

    /**
     *
     * {@inheritDoc}
     *
     */
    public function authenticate(string $username, string $password): ?User
    {
        foreach ($this->returnMockCredentials() as $credential) {
            if ($username === $credential['email'] &&
                password_verify($password, $credential['hash'])) {
                $user = new User;
                return $user->hydrate($credential);
            }
        }
        return null;
    }

    /**
     *
     * {@inheritDoc}
     *
     */
    public function find(string $username): ?User
    {
        foreach ($this->returnMockCredentials() as $credential) {
            if ($username === $credential['email']) {
                $user = new User;
                return $user->hydrate($credential);
            }
        }
        return null;
    }
}
