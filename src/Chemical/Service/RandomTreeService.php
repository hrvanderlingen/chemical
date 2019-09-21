<?php

namespace Chemical\Service;

class RandomTreeService extends TreeService implements TreeServiceInterface
{

    /**
     * {@inheritdoc}
     */
    public function getChildren($node = null)
    {
        $category = array(
            $this->generateRandomString(),
            $this->generateRandomString(),
            $this->generateRandomString());
        return $category;
    }

    public function generateRandomString($length = 9)
    {
        $characters = '0123456789abcdefghijklmnopqrstuvwxyzABCDEFGHIJKLMNOPQRSTUVWXYZ';
        $charactersLength = strlen($characters);
        $randomString = '';
        for ($i = 0; $i < $length; $i++) {
            $randomString .= $characters[rand(0, $charactersLength - 1)];
        }
        return $randomString;
    }
}
