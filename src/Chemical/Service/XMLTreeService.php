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
    protected $maxDepth = 3;

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
        $parent = new \SimpleXMLElement('<products/>');
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

        $attributes = $parent->attributes();
        $code = $attributes['code'];

        if (substr_count($code, "-") < $this->maxDepth) {

            $children = $this->getChildren();
            if (count($children) == 0) {
                $this->addEndNode($parent);
            } else {
                $newParent = $parent->addChild('products');
                foreach ($children as $child) {
                    $value = $this->count;
                    $newKey = $code . "-" . $value;
                    $node = $newParent->addChild('product' . $value);
                    $this->count++;
                    $node->addAttribute('code', $newKey);
                    $node->addChild('productCode', $child);
                    $this->iterateChildren($node, $newKey);
                }
            }
        } else {
            $this->addEndNode($parent);
        }
    }

    /**
     * Add an example node
     * @param \Chemical\Service\SimpleXMLElement $parent
     */
    protected function addEndNode($parent)
    {
        $node = $parent->addChild('product' . $this->count);
        $this->count++;
        $node->addChild('description', 'software');
        $node->addChild('version', rand(2, 500));
        $node->addChild('price', rand(200, 500));
        $node->addChild('productCode', $this->generateRandomString());
        $node = $parent->addChild('product' . $this->count);
        $this->count++;
        $node->addChild('description', 'hardware');
        $node->addChild('weight', rand(2, 500));
        $node->addChild('price', rand(200, 500));
        $node->addChild('productCode', $this->generateRandomString());
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

    public function getNode($data)
    {
        if ($is_null($data)) {

        } else {
            $node = $data['node'];
        }

        $file = __DIR__ . "../../../../data/products.xml";

        if (file_exists($file)) {
            $xml = simplexml_load_file($file) or die("Error: Cannot create object");
        } else {
            throw new \Exception('xml file not foud');
        }

        print_r($xml->products->product[0]->productCode);

        $products = $xml->products->product[0]->products;


        // create a random xml file.
        $parent = new \SimpleXMLElement('<products/>');
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

}