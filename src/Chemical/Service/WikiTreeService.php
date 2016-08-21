<?php

namespace Chemical\Service;

class WikiTreeService extends TreeService
{

    /**
     * The base url of the wikipedia API
     */
    const URL = 'https://en.wikipedia.org/w/api.php';

    /**
     * Get children based on the parent node
     * @param string $node
     * @return array
     */
    public function getChildren($node)
    {
	sleep(1);
	$output = $this->getRawTree($node);
	$output = $this->cleanRawTree($output);
	$tree = json_decode($output);

	if (!$tree || !$tree->categorytree) {
	    return array();
	}

	$content = $tree->categorytree->content;
	if (strlen($content) < 1) {
	    return array();
	}

	$elements = $this->getTreeElements($content);

	if (!is_null($elements)) {
	    foreach ($elements as $element) {
		$href = $element->getAttribute('href');
		$nodes = $element->childNodes;
		foreach ($nodes as $node) {

		    $utf8string = utf8_decode($node->nodeValue);

		    $category[] = array('name' => $utf8string, 'href' => $href);
		}
	    }
	}
	return $category;
    }

    /**
     * Extract nodes from DOM tree using Xpath
     * @param string $content
     * @return DOMNodeList
     */
    protected function getTreeElements($content)
    {
	$doc = new \DOMDocument();
	$doc->loadHTML($content);
	$xpath = new \DOMXpath($doc);
	$elements = $xpath->query("//a[contains(@class, 'CategoryTreeLabel')]");
	return $elements;
    }

    /**
     * Query the Wikipedia API and return a json data string
     * @param string $node
     * @return string
     * @throws \Exception
     */
    protected function getRawTree($node)
    {
	$url = self::URL . '?action=categorytree&rvprop=content&category=' . urlencode($node) . '&format=json';
	$ch = curl_init();
	curl_setopt($ch, CURLOPT_URL, $url);
	curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
	curl_setopt($ch, CURLOPT_USERAGENT, $_SERVER['HTTP_USER_AGENT']);
	curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, false);
	$output = curl_exec($ch);

	// @TODO  get a working json validator
	if (false && !$this->isValidJson($output)) {
	    throw new \Exception('Not valid json');
	}

	curl_close($ch);
	return $output;
    }

    /**
     * The raw json output contains garbage, clean it up
     * @param string $output
     * @return string
     */
    protected function cleanRawTree($output)
    {
	$output = str_replace("*", "content", $output);
	$output = str_replace("\n", "", $output);
	$output = str_replace("\t", "", $output);
	$output = str_replace("\u25ba", "", $output);

	/**
	 * convert en-dash to world-readable minus sign
	 */
	$output = str_replace("\u2013", "-", $output);
	return $output;
    }

}
