<?php

namespace Chemical\Service;

use Chemical\Entity\User;

interface UserServiceInterface
{

    /**
     * Authenticate a user
     * @param string $username
     * @param string $password
     * @return User
     */
    public function authenticate(string $username, string $password): ?User;

    /**
     * Find a user by user name
     * @param string $username
     * @return User|null
     */
    public function find(string $username): ?User;
}
