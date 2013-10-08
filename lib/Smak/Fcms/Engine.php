<?php

namespace Smak\Fcms;

use Smak\Fcms\Response;
use Silex\Application;
use Eexit\Twig\ContextParser\ContextParser;
use Symfony\Component\Finder\Finder;

/**
 * Engine.php
 *
 * @author Joris Berthelot <joris@berthelot.tel>
 * @copyright Copyright (c) 2013, Joris Berthelot
 * @license http://www.opensource.org/licenses/mit-license.php MIT License
 */
class Engine
{
    protected $app;

    protected $context = array();

    protected $context_parser;

    protected $content_wrapper;

    public function __construct(Application $app, ContextParser $context_parser = null)
    {
        $this->app = $app;
        $this->context_parser = $context_parser;
    }

    public function doResponse($uri)
    {
        $extension = array_shift($this->content_wrapper->getExtensions());
        $path = sprintf('%s%s', $uri, $extension);

        if ($this->app['twig.loader']->exists($path)) {
            $template = $this->app['twig']->loadTemplate($path);
        } else {
            $path = sprintf('%s%s%s', $uri, DIRECTORY_SEPARATOR, $this->content_wrapper->getIndex());
            $template = $this->app['twig']->loadTemplate($path);
        }

        $response           = new Response();
        $response->template = $template;
        $response->context  = $this->getContext();
        $response->metadata = $this->getMetadata();

        return $response;
    }

    public function getContext(\Twig_Template $template)
    {
        if (! $this->context_parser) {
            return;
        }

        $source = $this->app['twig.loader']->getSource($template->getTemplateName());
        $node = $this->app['twig']->parse($this->app['twig']->tokenize($source));
        $this->context = $this->context_parser->parse($node)->getContext();
        $this->enrichMetdata($template);

        return $this->context;
    }

    protected function enrichMetdata(\Twig_Template $template)
    {
        $this->context['metadata']['uri'] = $this->app['request']->getRequestUri();
        $this->context['metadata']['lastmod'] = new DateTime(sprintf('@%d', filemtime($this->app['twig']->getCacheFilename($template->getTemplateName()))));
    }

    public function getMetadata()
    {
        return $this->context['metadata']);
    }

    public function getTemplates()
    {
        $finder = Finder::create();
        $finder->files()
               ->ignoreDotFiles(true)
               ->in($this->content_wrapper->getContentPath())
               ->name(array_shift($this->content_wrapper->getExtensions()));

        var_dump($finder);
    }
}
