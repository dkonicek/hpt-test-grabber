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

    /** @var CzcProduct $product */
    private $product;
    /**
     * @var string
     */
    private $baseUrl;
    /**
     * @var string
     */
    private $searchUrl;

    /**
     * @param string $baseUrl
     * @param string $searchUrl
     */
    public function __construct(string $baseUrl, string $searchUrl) {
        $this->baseUrl = $baseUrl;
        $this->searchUrl = $searchUrl;
    }

    /**
     * @param string $productId
     * @return float
     * @throws \PHPHtmlParser\Exceptions\ChildNotFoundException
     * @throws \PHPHtmlParser\Exceptions\CircularException
     * @throws \PHPHtmlParser\Exceptions\ContentLengthException
     * @throws \PHPHtmlParser\Exceptions\LogicalException
     * @throws \PHPHtmlParser\Exceptions\NotLoadedException
     * @throws \PHPHtmlParser\Exceptions\StrictException
     * @throws \PHPHtmlParser\Exceptions\UnknownChildTypeException
     * @throws \Psr\Http\Client\ClientExceptionInterface
     */
    public function getPrice(string $productId): float {
        return $this->getProduct($productId)->getPrice();
    }

    /**
     * @param string $productId
     * @return string
     * @throws \PHPHtmlParser\Exceptions\ChildNotFoundException
     * @throws \PHPHtmlParser\Exceptions\CircularException
     * @throws \PHPHtmlParser\Exceptions\ContentLengthException
     * @throws \PHPHtmlParser\Exceptions\LogicalException
     * @throws \PHPHtmlParser\Exceptions\NotLoadedException
     * @throws \PHPHtmlParser\Exceptions\StrictException
     * @throws \PHPHtmlParser\Exceptions\UnknownChildTypeException
     * @throws \Psr\Http\Client\ClientExceptionInterface
     */
    public function getName(string $productId): string {
        return $this->getProduct($productId)->getName();
    }

    /**
     * @param string $productId
     * @return float
     * @throws \PHPHtmlParser\Exceptions\ChildNotFoundException
     * @throws \PHPHtmlParser\Exceptions\CircularException
     * @throws \PHPHtmlParser\Exceptions\ContentLengthException
     * @throws \PHPHtmlParser\Exceptions\LogicalException
     * @throws \PHPHtmlParser\Exceptions\NotLoadedException
     * @throws \PHPHtmlParser\Exceptions\StrictException
     * @throws \PHPHtmlParser\Exceptions\UnknownChildTypeException
     * @throws \Psr\Http\Client\ClientExceptionInterface
     */
    public function getRating(string $productId): float {
        return $this->getProduct($productId)->getRating();
    }


    /**
     * @param string $productId
     * @return CzcProduct
     * @throws \PHPHtmlParser\Exceptions\ChildNotFoundException
     * @throws \PHPHtmlParser\Exceptions\CircularException
     * @throws \PHPHtmlParser\Exceptions\ContentLengthException
     * @throws \PHPHtmlParser\Exceptions\LogicalException
     * @throws \PHPHtmlParser\Exceptions\NotLoadedException
     * @throws \PHPHtmlParser\Exceptions\StrictException
     * @throws \PHPHtmlParser\Exceptions\UnknownChildTypeException
     * @throws \Psr\Http\Client\ClientExceptionInterface
     */
    private function getProduct(string $productId): CzcProduct
    {
        if($this->product && $this->product->getCode() === $productId){
            return $this->product;
        }

        $links = $this->getLinks($productId);
        $this->product = $this->getCorrectProduct($productId, $links);

        return $this->product;
    }

    /**
     * @param string $productId
     * @return array
     * @throws \PHPHtmlParser\Exceptions\ChildNotFoundException
     * @throws \PHPHtmlParser\Exceptions\CircularException
     * @throws \PHPHtmlParser\Exceptions\ContentLengthException
     * @throws \PHPHtmlParser\Exceptions\LogicalException
     * @throws \PHPHtmlParser\Exceptions\NotLoadedException
     * @throws \PHPHtmlParser\Exceptions\StrictException
     * @throws \Psr\Http\Client\ClientExceptionInterface
     */
    private function getLinks(string $productId): array {
        $dom = new Dom();
        $dom->loadFromUrl(sprintf($this->searchUrl, $productId));

        if(!$items = $dom->find('a[class=tile-link]')){
            throw new \InvalidArgumentException('Product not founded!');
        }

        $links = [];
        /** @var Dom\Node\HtmlNode $item */
        foreach ($items as $item){
            $links[] = $this->baseUrl . $item->getAttribute('href');
        }
        return $links;
    }

    /**
     * @param string $productId
     * @param array $links
     * @return CzcProduct
     * @throws \PHPHtmlParser\Exceptions\ChildNotFoundException
     * @throws \PHPHtmlParser\Exceptions\CircularException
     * @throws \PHPHtmlParser\Exceptions\ContentLengthException
     * @throws \PHPHtmlParser\Exceptions\LogicalException
     * @throws \PHPHtmlParser\Exceptions\NotLoadedException
     * @throws \PHPHtmlParser\Exceptions\StrictException
     * @throws \PHPHtmlParser\Exceptions\UnknownChildTypeException
     * @throws \Psr\Http\Client\ClientExceptionInterface
     */
    private function getCorrectProduct(string $productId, array $links): CzcProduct {
        foreach ($links as $link){

            $dom = new Dom();
            $dom->loadFromUrl($link);

            if(!$categories = $dom->find('div[class=pd-next-in-category clearfix no-print]')){
                continue;
            }
            /** @var Dom\Node\HtmlNode $category */
            foreach ($categories as $category){
                if(!$values = $category->find('span[class=pd-next-in-category__item-value]')){
                    continue;
                }
                /** @var Dom\Node\HtmlNode $value */
                foreach ($values as $value){
                    if($value->innerText() === $productId){
                        return new CzcProduct($productId, $dom);
                    }
                }
            }
        }
        throw new \InvalidArgumentException('Product not founded!');
    }
}