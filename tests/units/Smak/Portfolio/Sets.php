<?php
namespace Smak\Portfolio\tests\units;

use mageekguy\atoum;
use Smak\Portfolio;

require_once __DIR__ . '/../../../bootstrap.php';

class Sets extends atoum\test
{
    public function testBuildSets()
    {
        $sets = $this->_bootstrap();
        $this->assert->object($sets)->isInstanceOf('Symfony\Component\Finder\Finder');
        $this->assert->object($sets)->isInstanceOf('Countable');
    }
    
    public function testGetSets()
    {
        $sets = $this->_bootstrap();
        $this->assert->object($sets->getSets())->isInstanceOf('Iterator');
    }
    
    public function testCount()
    {
        $sets = $this->_bootstrap();
        $this->assert->integer($sets->count())->isEqualTo(5);
    }
    
    public function testGetSet()
    {
        $sets = $this->_bootstrap();
        $this->assert->object($sets->getSet('Chile'))->isInstanceOf('SplFileInfo');
    }
    
    protected function _bootstrap()
    {
        return new \Smak\Portfolio\Sets($this->_getFs());
    }
    
    protected function _getFs()
    {
        return __DIR__ . '/../../../fs';
    }
}