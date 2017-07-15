<?php

namespace Chemical\Service;

interface TreeServiceInterface
{

    /**
     * Get children based on the parent node
     * @param string $node
     * @return array
     */
    public function getChildren($node);
}
