<?php

namespace Smak\Portfolio\Provider;

use Smak\Portfolio\Application;
use Silex\ServiceProviderInterface;

/**
 * SilexServiceProvider.php
 * 
 * @author Joris Berthelot <joris@berthelot.tel>
 * @copyright Copyright (c) 2012, Joris Berthelot
 * @license http://www.opensource.org/licenses/mit-license.php MIT License
 */
class SilexServiceProvider implements ServiceProviderInterface
{
    /**
     * Registers the Portfolio application as Silex SP
     * 
     * @param Silex\Application $app The Silex application
     * @throws Smak\Portfolio\SilexServiceProviderException
     */
    public function register(\Silex\Application $app)
    {

        // Checks the presence of required parameter
        if (empty($app['smak.portfolio.content_path'])) {
            throw new SilexServiceProviderException('"smak.portfolio.content_path" parameter is mandatory!');
        }

        // Checks the presence of required parameter
        if (empty($app['smak.portfolio.public_path'])) {
            throw new SilexServiceProviderException('"smak.portfolio.public_path" parameter is mandatory!');
        }

        // Checks the presence of required parameter
        if (empty($app['smak.portfolio.view_path'])) {
            throw new SilexServiceProviderException('"smak.portfolio.view_path" parameter is mandatory!');
        }

        // Registers the application
        $app['smak.portfolio'] = $app->share(function() use ($app) {
            return new Application($app['smak.portfolio.content_path']);
        });

        // Application view loader
        $app['smak.portfolio.view_loader'] = $app->protect(function($view_name) use ($app) {

            $original_view_file = $app['smak.portfolio.content_path'] . $view_name;
            $production_view_file = $app['smak.portfolio.view_path'] . $view_name;
            
            if (!is_file($original_view_file)) {
                return false;
            }
            
            $original_view_fingerprint = sha1_file($original_view_file);

            if (!is_file($production_view_file)
                || (sha1_file($production_view_file) !== $view_fingerprint)) {
                
                if (!copy($original_view_file, $production_view_file) {
                    throw new SilexServiceProviderException(sprintf('Unable to copy view file: "%s!"', $original_view_file));
                }
            }

            return $production_view_file;
        });
    }
}
