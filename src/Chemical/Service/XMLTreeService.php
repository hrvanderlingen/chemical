<?php

namespace Chemical\Service;

/**
 * Class for the creation of a random XML tree for a fictional product line
 */
class XMLTreeService extends TreeService implements TreeServiceInterface
{

    /**
     * Limit to the number of child nodes
     * @var int
     */
    protected $maxDepth = 5;

    /**
     * Internal counter
     * @var int
     */
    protected $count = 0;

    /**
     * {@inheritdoc}
     */
    public function getTree($data)
    {

        // create a random xml file.
        $parent = new \SimpleXMLElement('<collection/>');
        $key = 'product';
        $parent->addAttribute('code', $this->count);
        $this->count++;
        $this->iterateChildren($parent, $key);
        $doc = new \DomDocument('1.0');
        $doc->preserveWhiteSpace = false;
        $doc->formatOutput = true;
        $doc->loadXML($parent->asXML());
        return $doc->saveXML();
    }

    /**
     * {@inheritdoc}
     */
    protected function iterateChildren($parent, $key)
    {
        if (!$parent) {
            return false;
        }

        $attributes = $parent->attributes();
        $code = $attributes['code'];
        if (substr_count($code, "-") < $this->maxDepth) {
            $children = $this->getChildren();
            if (count($children) == 0) {
                $this->addEndNodes($parent);
            } else {
                $newParent = $parent->addChild('products');
                foreach ($children as $child) {
                    $value = $this->count;
                    $newKey = $code . "-" . $value;
                    $node = $newParent->addChild('product');
                    $this->count++;
                    $node->addAttribute('ID', $value);
                    $node->addAttribute('code', $newKey);
                    $node->addChild('productCode', $child);
                    $node->addChild('description', 'hardware');
                    $node->addChild('version', rand(2, 500));
                    $this->iterateChildren($node, $newKey);
                }
            }
        } else {
            $this->addEndNodes($parent);
        }
    }

    /**
     * Add example nodes
     * @param \Chemical\Service\SimpleXMLElement $parent
     */
    protected function addEndNodes($parent)
    {
        $max = rand(2, 5);
        for ($i = 0; $i < $max; $i++) {
            $this->endNode($parent);
        }
    }

    /**
     * {@inheritdoc}
     */
    public function getChildren($node = null)
    {
        $childCount = rand(0, 5);
        $category = array();
        for ($i = 1; $i <= $childCount; ++$i) {
            $category[] = $this->generateRandomString();
        }

        return $category;
    }

    /**
     * Add example node
     * @param \Chemical\Service\SimpleXMLElement $parent
     */
    protected function endNode($parent)
    {
        $descriptions = ['software', 'parts', 'assembly', 'compliance', 'certification'];
        $node = $parent->addChild('product');
        $node->addChild('description', $descriptions[array_rand($descriptions)]);
        $node->addChild('version', rand(2, 500));
        $node->addChild('price', rand(200, 500));
        $node->addChild('productCode', $this->generateRandomString());
        $this->count++;
    }
}
