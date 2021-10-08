<?php

declare(strict_types=1);

namespace HPT;

use Tracy\Debugger;
use Tracy\ILogger;

/**
 *
 */
class Dispatcher
{
    /** @var Grabber */
    private $grabber;

    /** @var Output */
    private $output;

    /**
     * @param Grabber $grabber
     * @param Output $output
     */
    public function __construct(Grabber $grabber, Output $output)
    {
        $this->grabber = $grabber;
        $this->output = $output;
    }

    /**
     * @return string JSON
     */
    public function run(): string
    {
        try {
            $codes = $this->loadCodes();
            $data = $this->processCodes($codes);
        } catch (\Throwable $e) {
            Debugger::log($e->getMessage(), ILogger::ERROR);
        }
        $this->output->data = $data ?? [];
        return $this->output->getJson();
    }

    /**
     * @return array
     * @throws \ErrorException
     */
    private function loadCodes(): array {
        $codes = file(__DIR__ . '/../vstup.txt', FILE_IGNORE_NEW_LINES | FILE_SKIP_EMPTY_LINES);
        if(!$codes){
            throw new \ErrorException("File not exists or is empty!");
        }
        return $codes;
    }

    /**
     * @param array $codes
     * @return array
     */
    private function processCodes(array $codes): array {
        $data = [];
        foreach ($codes as $code){
            try {
                $productParams = [
                    'price' => $this->grabber->getPrice($code) ?: null
                ];
                $data[$code] = $productParams;
            } catch (\InvalidArgumentException $e) {
                $data[$code] = null;
            }
        }
        return $data;
    }
}
