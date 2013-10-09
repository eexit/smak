<?php

namespace Smak\Fcms\ContentWrapper;

use Smak\Fcms\ContentWrapperInterface;
use Aptoma\Twig\Extension\MarkdownEngineInterface;

class Markdown implements ContentWrapperInterface
{
    protected $engine;

    protected $content_path;

    protected $index = 'index';

    protected $extensions;

    public function __construct($content_path = null)
    {
        if ($content_path) {
            $this->setContentPath($content_path);    
        }
        
        $this->extensions = array('.md', '.markdown');
    }

    public function setEngine(MarkdownEngineInterface $engine)
    {
        $this->engine = $engine;

        return $this;
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

        return $this;
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

        return $this;
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

        return $this;
    }

    public function setExtensions(array $extensions)
    {
        foreach ($extensions as $extension) {
            $this->setExtension($extension);
        }

        return $this;
    }

    public function getExtensions()
    {
        return $this->extensions;
    }

    public function getIndex()
    {
        $extensions = $this->getExtensions();

        return $this->getIndexBasename() . array_shift($extensions);
    }
}
