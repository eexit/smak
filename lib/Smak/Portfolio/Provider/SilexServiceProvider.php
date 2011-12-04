<?php

namespace Smak\Porfolio\Provider;

use Smak\Porfolio\Application;
use Silex\ServiceProviderInterface;

/**
 * SilexServiceProvider.php
 * 
 * @author Joris Berthelot <joris@berthelot.tel>
 * @copyright Copyright (c) 2011, Joris Berthelot
 * @license http://www.opensource.org/licenses/mit-license.php MIT License
 */
class SilexServiceProvider implements ServiceProviderInterface
{
    /**
     * Registers the Portfolio application as Silex SP
     * 
     * @param Silex\Application $app The Silex application
     */
    public function register(\Silex\Application $app)
    {
        $app['smak.portfolio'] = $app->share(function() {
            return new Application($app['smak.portfolio.content_path']);
        });
        
        if (isset($app['smak.portfolio.class_path'])) {
            $app['autoloader']->registerNamespace('Smak\\Portfolio', $app['smak.portfolio.class_path']);
        }
    }
}
