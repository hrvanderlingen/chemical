<?php

namespace Chemical\Service;

class TreeService
{

    protected $maxDepth = 3;
    protected $category = array();
    protected $errorMessages = array();
    protected $error = 0;

    /**
     * Use form post input to generate and return a array tree
     * @param array $nodeData
     * @return array
     * @throws \Exception
     */
    public function getTree($nodeData)
    {
        /**
         * Errors can be generated in the factory
         * workaround to have the exception generated at this level
         */
        if ($this->error == 1) {
            throw new \Exception(implode(" ", $this->getErrorMessages()));
        }

        $node = filter_var($nodeData['node'], FILTER_SANITIZE_STRING);
        $key = $node;
        $this->iterateChildren($node, $key);
        $tree = $this->explodeTree($this->category, "/", true);
        return $tree;
    }

    /**
     * With a parent find all children, store the results in the
     * category property.
     * @param string $parent
     * @param string $key
     */
    protected function iterateChildren($parent, $key)
    {

        if (substr_count($key, "/") < $this->maxDepth) {
            $children = $this->getChildren($parent);

            foreach ($children as $child) {
                $childStr = (is_array($child) && $child['name']) ? $child['name'] : $child;
                $newKey = $key . "/" . $childStr;
                $this->category[$newKey] = $child;
                $this->iterateChildren($childStr, $newKey);
            }
        }
    }

    /**
     * Return true if the json string is valid
     * @param string $json_string
     * @return bool
     */
    protected function isValidJson($json_string)
    {
        $regex = "/[^,:{}\\[\\]0-9.\\-+Eaeflnr-u \\n\\r\\t]/";
        $regex2 = '/"(\\.|[^"\\\\])*"/';
        return !preg_match($regex, preg_replace($regex2, '', $json_string));
    }

    /**
     * Convert a flat tree to an array tree
     * Based on http://kvz.io/blog/2007/10/03/convert-anything-to-tree-structures-in-php/
     * @param array $array
     * @param string $delimiter
     * @param string $baseval
     * @return boolean|array
     */
    protected function explodeTree($array, $delimiter = '_', $baseval = false)
    {
        if (!is_array($array)) {
            return false;
        }
        $splitRE = '/' . preg_quote($delimiter, '/') . '/';
        $returnArr = array();
        foreach ($array as $key => $val) {
            // Get parent parts and the current leaf
            $parts = preg_split($splitRE, $key, -1, PREG_SPLIT_NO_EMPTY);
            $leafPart = array_pop($parts);

            // Build parent structure
            // Might be slow for really deep and large structures
            $parentArr = &$returnArr;
            foreach ($parts as $part) {
                if (!isset($parentArr[$part])) {
                    $parentArr[$part] = array();
                } elseif (!is_array($parentArr[$part])) {
                    $parentArr[$part] = array();
                }
                $parentArr = &$parentArr[$part];
            }

            // Add the final part to the structure
            if (empty($parentArr[$leafPart])) {
                $parentArr[$leafPart] = $val;
            } elseif ($baseval && is_array($parentArr[$leafPart])) {
                $parentArr[$leafPart]['__base_val'] = $val;
            }
        }
        return $returnArr;
    }

    /**
     * Add error message to eror message array
     * @param string $message
     */
    public function setErrorMessage($message)
    {
        $this->errorMessages[] = $message;
    }

    /**
     * Return if tree has generated an error
     * @param bool $bError
     */
    public function hasError($bError)
    {
        $this->error = $bError;
    }

    /**
     * Return an array of eror messages
     * @return array
     */
    public function getErrorMessages()
    {
        return $this->errorMessages;
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
