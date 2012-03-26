<?php

namespace Smak\Portfolio\Silex\Provider;

use Smak\Portfolio\Application;
use Smak\Portfolio\Set;
use Silex\Application as SilexApplication;
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
    public function register(SilexApplication $app)
    {
        // Checks the presence of required parameter
        if (empty($app['smak.portfolio.content_path'])) {
            throw new SmakServiceProviderException('"smak.portfolio.content_path" parameter is mandatory!');
        }

        // Checks the presence of required parameter
        if (empty($app['smak.portfolio.public_path'])) {
            throw new SmakServiceProviderException('"smak.portfolio.public_path" parameter is mandatory!');
        }

        // Checks the presence of required parameter
        if (empty($app['smak.portfolio.view_path'])) {
            throw new SmakServiceProviderException('"smak.portfolio.view_path" parameter is mandatory!');
        }

        // Registers the application
        $app['smak.portfolio'] = $app->share(function() use ($app) {
            return new Application($app['smak.portfolio.content_path']);
        });

        // Set loader Silex helper
        $app['smak.portfolio.load'] = $app->protect(function($set_name, $view_path) use ($app) {
            $set = $app['smak.portfolio']->getSet($set_name);

            if (is_null($set)) {
                return false;
            }

            if (is_null($set->getInfo())) {
                return false;
            }

            $final_view = $app['smak.portfolio.view_path']
                . DIRECTORY_SEPARATOR
                . $set->getInfo()->getBasename();

            // Checks if the templace view file exists in the destination view path
            // or checks if not outdated
            if (!is_file($final_view)
                || (sha1_file($set->getInfo()->getRealPath()) !== sha1_file($final_view))) {
                
                // Tries to copy the view file
                if (!copy($set->getInfo()->getRealPath(), $final_view)) {
                    throw new SmakServiceProviderException(sprintf('Unable to copy view file: "%s!"', $set->getInfo()->getBasename()));
                }
            }

            $set->view = substr($final_view, strlen($view_path));
            $set->formatted_name = preg_replace('/[^[:alnum:]]/', ' ', $set_name);

            return $set;
        });
    }
}
