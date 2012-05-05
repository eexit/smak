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
     */
    public function register(Application $app)
    {
        // Checks the presence of required parameter
        if (empty($app['smak.portfolio.content_path'])) {
            throw new SmakServiceProviderException('"smak.portfolio.content_path" parameter is mandatory!');
        }

        // Checks the presence of required parameter
        if (empty($app['smak.portfolio.public_path'])) {
            throw new SmakServiceProviderException('"smak.portfolio.public_path" parameter is mandatory!');
        }

        // Registers the application
        $app['smak.portfolio'] = $app->share(function() use ($app) {
            return new Collection($app['smak.portfolio.content_path']);
        });
    }
}
