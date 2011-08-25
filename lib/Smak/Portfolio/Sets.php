<?php
namespace Smak\Portfolio;
use Symfony\Component\Finder;

class Sets extends Finder\Finder implements \Countable
{    
    public function __construct($dir = __DIR__)
    {
        parent::create();
        $this->directories()->in($dir);
    }
    
    public function count()
    {
        
    }
}