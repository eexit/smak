<?php

namespace Smak\Portfolio\tests\units;

use mageekguy\atoum;
use Smak\Portfolio;
use tests\Fs;

require_once __DIR__ . '/../../../bootstrap.php';

class Set extends atoum\test
{
    const FS_REL = '/../../../fs';
    
    public function setUp()
    {
        $fs = new Fs(__DIR__ . self::FS_REL, $this->_fsTreeProvider());
        $fs->setDiffTime(true);
        $fs->build();
        $this->assert->boolean($fs->isBuilt())->isTrue();
    }
    
    public function beforeTestMethod($method)
    {
        $this->fs = new Fs(__DIR__ . self::FS_REL, $this->_fsTreeProvider());
        $setRoot = new \SplFileInfo($this->fs->getRoot() . '/Travels/Chile');
        $this->instance = new \Smak\Portfolio\Set($setRoot);
    }
    
    public function testNewSet()
    {
        $this->assert->object($this->instance)->isInstanceOf('Symfony\Component\Finder\Finder');
        $this->assert->object($this->instance)->isInstanceOf('Countable');
    }
    public function testCount()
    {
        $this->assert->integer($this->instance->count())->isEqualTo(4);
    }
    
    public function testGetPhotos()
    {
        $this->assert->object($this->instance->getPhotos())->isInstanceOf('Iterator');
    }
    
    public function testGetSetInfo()
    {
        $this->assert->object($this->instance->getInfo())->isInstanceOf('SplFileInfo');
        $this->assert->string($this->instance->getInfo()->getFileName())->isEqualTo('chile.twig');
    }
    
    public function testGetPhotoInNaturalOrder()
    {
        $expected = $this->fs->getTree();
        $expected = $expected['Travels']['Chile'];
        array_pop($expected);
        sort($expected);
        $results = array();
        
        
        foreach ($this->instance->getPhotos() as $file) {
            $results[] = $file->getFilename();
        }
        
        $this->assert->array($expected)->isEqualTo($results);
    }
    
    public function testGetPhotoByMTimeNewestFirst()
    {
        $expected = $this->fs->getTree();
        $expected = $expected['Travels']['Chile'];
        array_pop($expected);
        $results = array();
        
        foreach ($this->instance->sortByNewest()->getPhotos() as $file) {
            $results[] = $file->getFilename();
        }
        
        $this->assert->array(array_reverse($expected))->isEqualTo($results);
    }
    
    public function testGetPhotoByMTimeOldestFirst()
    {
        $expected = $this->fs->getTree();
        $expected = $expected['Travels']['Chile'];
        array_pop($expected);
        $results = array();
        
        foreach ($this->instance->sortByOldest()->getPhotos() as $file) {
            $results[] = $file->getFilename();
        }
        
        $this->assert->array($expected)->isEqualTo($results);
    }
    
    public function tearDown()
    {
        $fs = new Fs(__DIR__ . self::FS_REL, $this->_fsTreeProvider());
        $fs->clear();
    }
    
    private function _fsTreeProvider()
    {
        return array('Travels'  => array(
            'Chile' => array(
                'sample-1.jpeg',
                'sample-3.jpG',
                'sample-2.jpg',
                'sample-4.png',
                'chile.twig'
        )));
    }
}