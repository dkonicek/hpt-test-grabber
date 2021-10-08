<?php declare(strict_types=1);
/**
 * @author David Koníček
 */

namespace HPT\Workers;

use HPT\Grabber;
use HPT\Models\CzcProduct;
use PHPHtmlParser\Dom;

/**
 *
 */
class CzcGrabber implements Grabber {

    /** @var string $url */
    private $url;

    /** @var null|CzcProduct $product */
    private $product = null;

    /**
     * @param string $url
     */
    public function __construct(string $url) {
        $this->url = $url;
    }

    /**
     * @param string $productId
     * @return CzcProduct
     * @throws \InvalidArgumentException
     * @throws \PHPHtmlParser\Exceptions\ChildNotFoundException
     * @throws \PHPHtmlParser\Exceptions\CircularException
     * @throws \PHPHtmlParser\Exceptions\ContentLengthException
     * @throws \PHPHtmlParser\Exceptions\LogicalException
     * @throws \PHPHtmlParser\Exceptions\NotLoadedException
     * @throws \PHPHtmlParser\Exceptions\StrictException
     * @throws \Psr\Http\Client\ClientExceptionInterface
     */
    private function getProduct(string $productId): CzcProduct
    {
        if($this->product && $this->product->getCode() === $productId){
            return $this->product;
        }

        $this->product = new CzcProduct($productId);

        $dom = new Dom();
        $dom->loadFromUrl(sprintf($this->url, $productId));
        if(!$element = $dom->find('div[class=new-tile]')[0] ?? null){
            throw new \InvalidArgumentException('Product not founded!');
        }
        $this->product->setHtmlNode($element);

        return $this->product;
    }

    /**
     * @param string $productId
     * @return float
     * @throws \InvalidArgumentException
     * @throws \PHPHtmlParser\Exceptions\ChildNotFoundException
     * @throws \PHPHtmlParser\Exceptions\CircularException
     * @throws \PHPHtmlParser\Exceptions\ContentLengthException
     * @throws \PHPHtmlParser\Exceptions\LogicalException
     * @throws \PHPHtmlParser\Exceptions\NotLoadedException
     * @throws \PHPHtmlParser\Exceptions\StrictException
     * @throws \Psr\Http\Client\ClientExceptionInterface
     */
    public function getPrice(string $productId): float {
        return $this->getProduct($productId)->getPrice();
    }
}