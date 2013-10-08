<?php

namespace Smak\Fcms\ContentWrapper;

use Smak\Fcms\ContentWrapperInterface;
use Aptoma\Twig\Extension\MarkdownEngine;

class Markdown implements ContentWrapperInterface
{
    protected $engine;

    protected $content_path;

    protected $index = 'index';

    protected $extensions;

    public function __construct()
    {
        $this->extensions = array('.md', '.markdown');
    }

    public function setEngine(MarkdownEngine $engine)
    {
        $this->engine = $engine;
    }

    public function getEngine()
    {
        return $this->engine;
    }

    public function setContentPath($content_path)
    {
        if (! is_readable($content_path)) {
            throw new \InvalidArgumentException('Provided content path is not found or cannot be readable.');
        }

        $this->content_path = $content_path;
    }

    public function getContentPath()
    {
        return $this->content_path;
    }

    public function setIndexBasename($index)
    {
        if (! is_string($index) || ! strlen($index)) {
            throw new \InvalidArgumentException('Provided index name is not valid.');
        }

        $this->index = $index;
    }

    public function getIndexBasename()
    {
        return $this->index;
    }

    public function setExtension($extension)
    {
        if (! is_string($extension)
            || 2 > strlen($extension)
            || '.' !== $extension[0]) {
            throw new \InvalidArgumentException('Provided extension is not valid.');
        }

        if (! in_array(strtolower($extension), $this->extensions)) {
            $this->extensions[] = strtolower($extension);
        }
    }

    public function setExtensions(array $extensions)
    {
        foreach ($extensions as $extension) {
            $this->setExtension($extension);
        }
    }

    public function getExtensions()
    {
        return $this->extensions;
    }

    public function getIndex()
    {
        return $this->getIndexBasename() . array_shift($this->getExtensions());
    }
}
