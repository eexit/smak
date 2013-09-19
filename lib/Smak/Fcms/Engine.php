<?php

namespace Smak\Fcms;

use Smak\Fcms\LoaderInterface;
use Smak\Fcms\ParserInterface;

/**
 * Engine.php
 *
 * @author Joris Berthelot <joris@berthelot.tel>
 * @copyright Copyright (c) 2013, Joris Berthelot
 * @license http://www.opensource.org/licenses/mit-license.php MIT License
 */
class Engine
{
    protected $_loader;

    protected $_parser;

    public function __construct(LoaderInterface $loader, RenderInterface $parser, array $options = [])
    {
        $this->_loader = $loader;
        $this->_parser = $parser;
    }

    public function parseUri($uri)
    {
        return str_replace('/', DIRECTORY_SEPARATOR, trim($uri, '/'));
    }

    public function loadContent($content_uri)
    {
        return $this->_loader->find($content_uri);
    }

    public function renderContent(\SplFileInfo $content_file)
    {
        return $this->_parser->render($content_file);
    }
}
