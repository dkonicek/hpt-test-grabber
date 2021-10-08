<?php declare(strict_types=1);
/**
 * @author David Koníček
 */

namespace HPT\Models;

use PHPHtmlParser\Dom;

/**
 *
 */
class CzcProduct {

    /** @var string */
    private $code;

    /** @var Dom */
    private $dom;

    /** @var float */
    private $price = 0;

    /** @var float */
    private $rating = 0;

    /** @var string */
    private $name = '';

    /**
     * @param string $code
     * @param Dom $dom
     * @throws \PHPHtmlParser\Exceptions\ChildNotFoundException
     * @throws \PHPHtmlParser\Exceptions\NotLoadedException
     */
    public function __construct(string $code, Dom $dom) {
        $this->setParams($dom, $code);
    }

    /**
     * @param Dom $dom
     * @param string $productId
     * @throws \PHPHtmlParser\Exceptions\ChildNotFoundException
     * @throws \PHPHtmlParser\Exceptions\NotLoadedException
     */
    private function setParams(Dom $dom, string $productId): void {
        $this->setDom($dom);
        $this->setCode($productId);
        $this->setPrice();
        $this->setName();
        $this->setRating();
    }

    /**
     * @param Dom $dom
     */
    private function setDom(Dom $dom): void {
        $this->dom = $dom;
    }

    /**
     * @param string $code
     */
    private function setCode(string $code): void {
        $this->code = $code;
    }

    /**
     * @throws \PHPHtmlParser\Exceptions\ChildNotFoundException
     * @throws \PHPHtmlParser\Exceptions\NotLoadedException
     */
    private function setPrice(): void {
        if (!$dom = $this->getDom()) {
            return;
        }

        if (!$priceBlocks = $dom->find('span[class=price alone]')) {
            return;
        }

        if (!$priceElement = $priceBlocks[0]->find('span[class=price-vatin]')) {
            return;
        }

        $price = (string)($priceElement[0]->innerText() ?? 0);
        $this->price = (float)str_replace(' ', '', $price);
    }

    /**
     * @return Dom
     */
    public function getDom(): Dom {
        return $this->dom;
    }

    /**
     * @throws \PHPHtmlParser\Exceptions\ChildNotFoundException
     * @throws \PHPHtmlParser\Exceptions\NotLoadedException
     */
    private function setName() {
        if (!$dom = $this->getDom()) {
            return;
        }

        if (!$wrappers = $dom->find('div[class=pd-wrap]')) {
            return;
        }

        if (!$headlines = $wrappers[0]->find('h1')) {
            return;
        }

        $name = trim($headlines[0]->innerText() ?? '');
        $this->name = $name;
    }

    /**
     * @throws \PHPHtmlParser\Exceptions\ChildNotFoundException
     * @throws \PHPHtmlParser\Exceptions\NotLoadedException
     */
    private function setRating() {
        if (!$dom = $this->getDom()) {
            return;
        }

        if (!$ratingBlocks = $dom->find('div[class=pd-header]')) {
            return;
        }

        if (!$ratingElements = $ratingBlocks[0]->find('span[class=rating__label]')) {
            return;
        }

        $rating = (string)($ratingElements[0]->innerText() ?? 0);
        $this->rating = (float)str_replace([' ', '%'], '', $rating);
    }

    /**
     * @return string
     */
    public function getCode(): string {
        return $this->code;
    }

    /**
     * @return float
     */
    public function getPrice(): float {
        return $this->price;
    }

    /**
     * @return float
     */
    public function getRating() {
        return $this->rating;
    }

    /**
     * @return string
     */
    public function getName(): string {
        return $this->name;
    }
}