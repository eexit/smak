<?php
namespace Smak\Portfolio\tests\units;
use mageekguy\atoum;
use Smak\Portfolio;
//use Symfony\Component\Finder\Finder as Finder;

require_once __DIR__ . '/../../../bootstrap.php';

class Sets extends atoum\test
{   
    public function testIsClassWellFormed()
    {   
        $sets = new \Smak\Portfolio\Sets();
        $this->assert->object($sets)->isInstanceOf('Symfony\Component\Finder\Finder');
        $this->assert->object($sets)->isInstanceOf('Countable');
    }
    
    public function testGetAll()
    {
        $sets = new \Smak\Portfolio\Sets($this->_getFs());        
        //$this->assert->object($sets->getAll())->isInstanceOf('Iterator');
    }
    
    protected function _getFs()
    {
        return __DIR__ . '/../../../fs';
    }
}