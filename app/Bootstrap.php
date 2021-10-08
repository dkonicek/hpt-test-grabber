<?php declare(strict_types=1);
/**
 * @author David Koníček
 */

namespace HPT;

use Nette\Bootstrap\Configurator;

/**
 *
 */
class Bootstrap {

    /**
     * @return Configurator
     */
    public static function boot(): Configurator
    {
        $appDir = dirname(__DIR__);
        $configurator = new Configurator;
        $configurator->setDebugMode(false);
        $configurator->enableTracy($appDir . '/log');
        $configurator->setTempDirectory($appDir . '/temp');
        $configurator->createRobotLoader()
            ->addDirectory(__DIR__)
            ->register();
        $configurator->addConfig($appDir . '/config/config.neon');
        return $configurator;
    }
}