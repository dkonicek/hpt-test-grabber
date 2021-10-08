<?php declare(strict_types=1);
/**
 * @author David Koníček
 */

namespace HPT\Models;

use PHPHtmlParser\Dom\Node\HtmlNode;

/**
 *
 */
class CzcProduct {

    /** @var string */
    private $code;

    /** @var null|HtmlNode */
    private $htmlNode = null;

    /**
     * @param string $code
     */
    public function __construct(string $code) {
        $this->setCode($code);
    }

    /**
     * @param string $code
     */
    private function setCode(string $code): void {
        $this->code = $code;
    }

    /**
     * @return string
     */
    public function getCode(): string {
        return $this->code;
    }

    /**
     * @param HtmlNode|null $htmlNode
     */
    public function setHtmlNode(?HtmlNode $htmlNode): void {
        $this->htmlNode = $htmlNode;
    }

    /**
     * @return float
     */
    public function getPrice(): float {
        if ($node = $this->getHtmlNode()) {
            foreach ($node->getAttributes() as $key => $val) {
                if ($key === 'data-ga-impression') {
                    $obj = json_decode($val);
                    return (float)$obj->price;
                }
            }
        }
        return 0;
    }

    /**
     * @return HtmlNode|null
     */
    private function getHtmlNode(): ?HtmlNode {
        return $this->htmlNode;
    }
}