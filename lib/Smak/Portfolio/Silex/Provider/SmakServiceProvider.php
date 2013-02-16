<?php

namespace Smak\Portfolio\Silex\Provider;

use Smak\Portfolio\Collection;
use Silex\Application;
use Silex\ServiceProviderInterface;

/**
 * SmakServiceProvider.php
 * 
 * @author Joris Berthelot <joris@berthelot.tel>
 * @copyright Copyright (c) 2012, Joris Berthelot
 * @license http://www.opensource.org/licenses/mit-license.php MIT License
 */
class SmakServiceProvider implements ServiceProviderInterface
{
    /**
     * Registers the Portfolio application as Silex SP
     * 
     * @param Silex\Application $app The Silex application
     * @throws Smak\Portfolio\Silex\Provider\SmakServiceProviderException
     *
     * Service parameters:
     *  - smak.portfolio.content_path: The system root directory of the portfolio content
     *  - smak.portfolio.public_path: The public (web) root directory of the portfolio content
     */
    public function register(Application $app)
    {
        // Registers the application
        $app['smak.portfolio'] = $app->share(function() use ($app) {
            return new Collection($app['smak.portfolio.content_path']);
        });
    }

    /**
     * Service bootstrap (called once service is registered)
     *
     * @param Silex\Application $app The Silex application
     */
    public function boot(Application $app)
    {
    }
}
