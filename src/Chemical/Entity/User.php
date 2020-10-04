<?php

namespace Chemical\Entity;

class User
{

    protected $username;
    protected $firstname;
    protected $lastname;
    protected $role;
    protected $phone;
    protected $token;
    protected $hash;
    protected $twofactorstatus;

    public function getArrayCopy(): array
    {
        return get_object_vars($this);
    }

    public function hydrate(array $data): User
    {
        foreach ($data as $key => $value) {
            if (property_exists($this, $key)) {
                $this->$key = $value;
            }
        }
        return $this;
    }

    function getUsername()
    {
        return $this->username;
    }

    function getFirstnbame()
    {
        return $this->firstnbame;
    }

    function getLastname()
    {
        return $this->lastname;
    }

    function getRole()
    {
        return $this->role;
    }

    function getPhone()
    {
        return $this->phone;
    }

    function getToken()
    {
        return $this->token;
    }

    function getHash()
    {
        return $this->hash;
    }

    function getTwofactorstatus()
    {
        return $this->twofactorstatus;
    }

    function setTwofactorstatus($twofactorstatus): void
    {
        $this->twofactorstatus = $twofactorstatus;
    }

    function setToken($token): void
    {
        $this->token = $token;
    }
}
