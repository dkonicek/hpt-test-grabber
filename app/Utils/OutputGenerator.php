<?php declare(strict_types=1);
/**
 * @author David Koníček
 */

namespace HPT\Utils;

use HPT\Output;

/**
 *
 */
class OutputGenerator implements Output {

    /** @var array */
    public $data = [];

    /**
     * @return string
     */
    public function getJson(): string {
        header('Content-Type: application/json');
        return json_encode($this->data);
    }
}