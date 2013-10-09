<?php

namespace Smak\Fcms\Engine;

use Silex\Application;
use Smak\Fcms\Engine;
use Eexit\Twig\ContextParser\ContextParser;
use Smak\Fcms\ContentWrapperInterface;

class MarkdownEngine extends Engine
{
    public function __construct(Application $app, ContextParser $context_parser, ContentWrapperInterface $content_wrapper)
    {
        parent::__construct($app, $context_parser);

        $this->content_wrapper = $content_wrapper;
    }
}
