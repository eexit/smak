<?php

namespace Smak\Portfolio\Silex\Provider;

use Smak\Portfolio\Set;
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

        // Checks the presence of required parameter
        if (empty($app['smak.portfolio.view_path'])) {
            throw new SmakServiceProviderException('"smak.portfolio.view_path" parameter is mandatory!');
        }

        // Twig MUST be registered BEFORE
        if (empty($app['twig.path'])) {
            throw new SmakServiceProviderException('Twig is required for Smak Portfolio to run properly!');
        }

        // Registers the application
        $app['smak.portfolio'] = $app->share(function() use ($app) {
            return new Collection($app['smak.portfolio.content_path']);
        });

        // Set loader Silex helper
        // Set loader Silex helper
        $app['smak.portfolio.load'] = $app->protect(function($set) use ($app) {
            if (null == $set->getTemplate()) {
                return false;
            }

            $set->helpers['smak_subpath'] = dirname(substr($set->getSplInfo()->getRealPath(), strlen(realpath($app['smak.portfolio.content_path']))));

            $final_view = $app['smak.portfolio.view_path']
                . $set->helpers['smak_subpath']
                . DIRECTORY_SEPARATOR
                . $set->getTemplate()->getBasename();

            // Checks if the templace view file exists in the destination view path
            // or checks if not outdated
            if (!is_file($final_view)
                || (sha1_file($set->getTemplate()->getRealPath()) !== sha1_file($final_view))) {

                if (!is_dir($app['smak.portfolio.view_path'] . $set->helpers['smak_subpath'])) {
                    // Tries the create the parents directories for the view file
                    if (!mkdir($app['smak.portfolio.view_path'] . $set->helpers['smak_subpath'], 0700, true)) {
                        throw new SmakServiceProviderException(sprintf('Unable to create the directory for view file: "%s!"', $set->getTemplate()->getBasename()));
                    }
                }

                // Tries to copy the view file
                if (!copy($set->getTemplate()->getRealPath(), $final_view)) {
                    throw new SmakServiceProviderException(sprintf('Unable to copy view file: "%s!"', $set->getTemplate()->getBasename()));
                }
            }

            $twig_view_path = sprintf('%s%s', substr($app['smak.portfolio.view_path'], strlen($app['twig.path'])), $set->helpers['smak_subpath']);
            $set->helpers['twig_subpath'] = sprintf('%s%s%s', $twig_view_path, DIRECTORY_SEPARATOR, $set->getTemplate()->getFilename());
            return $set;
        });
    }
}
