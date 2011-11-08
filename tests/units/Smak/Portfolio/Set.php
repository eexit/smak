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
        $this->assert->boolean($fs->isBuilt())
             ->isTrue();
    }
    
    public function beforeTestMethod($method)
    {
        $this->fs = new Fs(__DIR__ . self::FS_REL, $this->_fsTreeProvider());        
        $setRoot = new \SplFileInfo($this->fs->getRoot() . '/Travels/Chile');
        $this->instance = new \Smak\Portfolio\Set($setRoot);
    }
    
    public function testNewSet()
    {
        $this->assert->object($this->instance)
             ->isInstanceOf('\Symfony\Component\Finder\Finder');
        
        $this->assert->object($this->instance)
             ->isInstanceOf('\Countable');
    }
    public function testCount()
    {
        $this->assert->integer($this->instance->count())
             ->isEqualTo(4);
    }
    
    public function testPhotoExtensions()
    {
        $set = $this->instance;
        
        $this->assert->array($set->getPhotoExtensions())
             ->isEqualTo(array('.jpg', '.jpeg', '.png'));
        
        $this->assert->object($set->setPhotoExtensions(
            $new_ext = array('.tiff', '.gif')
        ))->isInstanceOf('\Smak\Portfolio\Set');
        
        $this->assert->array($set->getPhotoExtensions())
             ->isEqualTo($new_ext);
        
        $this->assert->exception(function() use ($set) {
            $set->setPhotoExtensions(array());
        })->isInstanceOf('\InvalidArgumentException');
    }
    
    public function testInfoExtension()
    {
        $set = $this->instance;
        
        $this->assert->array($set->getInfoExtensions())
             ->isEqualTo(array('.twig'));
        
        $this->assert->object($set->setInfoExtensions(
            $new_ext = array('.html', '.txt')
        ))->isInstanceOf('\Smak\Portfolio\Set');
        
        $this->assert->array($set->getInfoExtensions())
             ->isEqualTo($new_ext);
        
        $this->assert->exception(function() use ($set) {
            $set->setInfoExtensions(array());
        })->isInstanceOf('\InvalidArgumentException');
    }
    
    public function testGetPhotos()
    {
        $this->assert->object($this->instance->getPhotos())
             ->isInstanceOf('\ArrayIterator');
    }
    
    public function testGetPhotoById()
    {
        $set = $this->instance;
        $tree = $this->fs->getTree();
        $tree = $tree['Travels']['Chile'];
        array_pop($tree);
        sort($tree);
        
        $this->assert->exception(function() use ($set) {
            $set->getPhotoById("foo");
        })->isInstanceOf('\InvalidArgumentException');
        
        $this->assert->string($set->getPhotoById(2)->getFileName())
             ->isEqualTo($tree[2]);
        
        $this->assert->exception(function() use ($set) {
            $set->getPhotoById(123);
        })->isInstanceOf('\OutOfRangeException');
    }
    
    public function testGetPhotoByName()
    {
        $set = $this->instance;
        $tree = $this->fs->getTree();
        $tree = $tree['Travels']['Chile'];
        array_pop($tree);
        sort($tree);
        
        $this->assert->exception(function() use ($set) {
            $set->getPhotoByName(23.34);
        })->isInstanceOf('\InvalidArgumentException');
        
        $this->assert->string($set->getPhotoByName('sample-4')->getFileName())
             ->isEqualTo($tree[3]);
        
        $this->assert->exception(function() use ($set) {
            $set->getPhotoByName('foobar');
        })->isInstanceOf('\UnexpectedValueException');
    }
    
    public function testGetInfo()
    {
        $this->assert->object($this->instance->getInfo())
             ->isInstanceOf('\SplFileInfo');
        
        $this->assert->string($this->instance->getInfo()->getFileName())
             ->isEqualTo('chile.twig');
    }
    
    public function testGetSetSplInfo()
    {
        $this->assert->object($this->instance->getSplInfo())
             ->isInstanceOf('\SplFileInfo');
        
        $this->assert->string($this->instance->getSplInfo()->getFilename())
             ->isEqualTo('Chile');
        
        $this->assert->string($this->instance->getSplInfo()->getRealPath())
             ->isEqualTo(realpath(__DIR__ . self::FS_REL) . '/Travels/Chile');
    }
    
    public function testGetFirst()
    {
        $first = $this->instance->getFirst();
        $tree = $this->fs->getTree();
        $expected = array_shift($tree['Travels']['Chile']);
        
        $this->assert->object($first)
             ->isInstanceOf('\Smak\Portfolio\Photo');
        
        $this->assert->string($first->getFilename())
             ->isEqualTo($expected);
    }
    
    public function testGetLast()
    {
        $last = $this->instance->getLast();
        $tree = $this->fs->getTree();
        array_pop($tree['Travels']['Chile']); // Removes the twig file from Tree
        $expected = array_pop($tree['Travels']['Chile']);
        
        $this->assert->object($last)
             ->isInstanceOf('\Smak\Portfolio\Photo');
        
        $this->assert->string($last->getFilename())
             ->isEqualTo($expected);
    }
    
    public function testGetPhotoInNaturalOrder()
    {
        $tree = $this->fs->getTree();
        $expected = $tree['Travels']['Chile'];
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
        $tree = $this->fs->getTree();
        $expected = $tree['Travels']['Chile'];
        array_pop($expected);
        $results = array();
        
        foreach ($this->instance->sortByNewest()->getPhotos() as $file) {
            $results[] = $file->getFilename();
        }
        
        $this->assert->array(array_reverse($expected))
             ->isEqualTo($results);
    }
    
    public function testGetPhotoByMTimeOldestFirst()
    {
        $tree = $this->fs->getTree();
        $expected = $tree['Travels']['Chile'];
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