<?php
namespace Smak\Portfolio\tests\units;

use mageekguy\atoum;
use Smak\Portfolio;

require_once __DIR__ . '/../../../bootstrap.php';

class Set extends atoum\test
{
    public function testNewSet()
    {
        $set = $this->_bootstrap();
        $this->assert->object($set)->isInstanceOf('Symfony\Component\Finder\Finder');
        $this->assert->object($set)->isInstanceOf('Countable');
    }
    
    public function testCount()
    {
        $set = $this->_bootstrap();
        $this->assert->integer($set->count())->isEqualTo(4);
    }
    
    public function testGetPhotos()
    {
        $set = $this->_bootstrap();
        $this->assert->object($set->getPhotos())->isInstanceOf('Iterator');
    }
    
    public function testGetSetInfo()
    {
        $set = $this->_bootstrap();
        $this->assert->object($set->getInfo())->isInstanceOf('SplFileInfo');
        $this->assert->string($set->getInfo()->getFileName())->isEqualTo('chile.twig');
    }
    
    public function testGetPhotoInRightOrder()
    {
        $set = $this->_bootstrap();
        $results = array();
        $expected = array(
            'sample-4.png',
            'sample-3.jpg',
            'sample-2.jpG',
            'sample-1.jpeg'
        );
        
        foreach ($set->getPhotos() as $file) {
            $results[] = $file->getFilename();
        }
        
        $this->assert->array($expected)->isEqualTo($results);
    }
    
    protected function _bootstrap()
    {
        return new \Smak\Portfolio\Set(new \SplFileInfo($this->_getFs()));
    }
    
    protected function _getFs()
    {
        return __DIR__ . '/../../../fs/Travels/Chile';
    }
}