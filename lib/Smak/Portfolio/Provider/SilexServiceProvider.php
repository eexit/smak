<?php

namespace Smak\Portfolio\Provider;

use Smak\Portfolio\Application;
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
        $app['smak.portfolio'] = $app->share(function() use ($app) {
            return new Application($app['smak.portfolio.content_repository']);
        });
    }
}
