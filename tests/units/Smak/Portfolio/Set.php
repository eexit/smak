<?php

namespace Smak\Portfolio\tests\units;

use mageekguy\atoum;
use Smak\Portfolio;
use Smak\Portfolio\SortHelper;
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
    
    public function testClassDeclaration()
    {
        $this->assert->object($this->instance)
             ->isInstanceOf('\Symfony\Component\Finder\Finder');

        $this->assert->object($this->instance)
             ->isInstanceOf('\Smak\Portfolio\Portfolio');
        
        $this->assert->class('\Smak\Portfolio\Set')
             ->hasInterface('\Countable');
    }
    public function testCount()
    {
        $this->assert->integer($this->instance->count())
             ->isEqualTo(4);
    }
    
    public function testExtensions()
    {
        $set = $this->instance;
        
        $this->assert->array($set->getExtensions())
             ->isEqualTo(array('.jpg', '.jpeg', '.jpf', '.png'));
        
        $this->assert->object($set->setExtensions(
            $new_ext = array('.tiff', '.gif')
        ))->isInstanceOf('\Smak\Portfolio\Set');
        
        $this->assert->array($set->getExtensions())
             ->isEqualTo($new_ext);
        
        $this->assert->exception(function() use ($set) {
            $set->setExtensions(array());
        })->isInstanceOf('\InvalidArgumentException');
    }
    
    public function testTemplateExtension()
    {
        $set = $this->instance;
        
        $this->assert->array($set->getTemplateExtensions())
             ->isEqualTo(array('.html.twig'));
        
        $this->assert->object($set->setTemplateExtensions(
            $new_ext = array('.html', '.txt')
        ))->isInstanceOf('\Smak\Portfolio\Set');
        
        $this->assert->array($set->getTemplateExtensions())
             ->isEqualTo($new_ext);
        
        $this->assert->exception(function() use ($set) {
            $set->setTemplateExtensions(array());
        })->isInstanceOf('\InvalidArgumentException');
    }
    
    public function testGetAll()
    {
        $this->assert->object($this->instance->getAll())
             ->isInstanceOf('\ArrayIterator');
    }
    
    public function testGetById()
    {
        $set = $this->instance;
        $tree = $this->fs->getTree();
        $tree = $tree['Travels']['Chile'];
        array_pop($tree);
        sort($tree);
        
        $this->assert->exception(function() use ($set) {
            $set->getById("foo");
        })->isInstanceOf('\InvalidArgumentException');
        
        $this->assert->string($set->getById(2)->getFilename())
             ->isEqualTo($tree[2]);
        
        $this->assert->exception(function() use ($set) {
            $set->getById(123);
        })->isInstanceOf('\OutOfRangeException');
    }
    
    public function testGetByName()
    {
        $set = $this->instance;
        $tree = $this->fs->getTree();
        $tree = $tree['Travels']['Chile'];
        array_pop($tree);
        sort($tree);
        
        $this->assert->exception(function() use ($set) {
            $set->getByName(23.34);
        })->isInstanceOf('\InvalidArgumentException');
        
        $this->assert->string($set->getByName('sample-4')->getFilename())
             ->isEqualTo($tree[3]);
        
        $this->assert->variable($set->getByName('foobar'))
            ->isNull();
    }
    
    public function testGetTemplate()
    {
        $this->assert->object($this->instance->getTemplate())
             ->isInstanceOf('\SplFileInfo');
        
        $this->assert->string($this->instance->getTemplate()->getFilename())
             ->isEqualTo('chile.html.twig');
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

    public function testGetInReversedOrder()
    {
        $tree = $this->fs->getTree();
        $expected = $tree['Travels']['Chile'];
        array_pop($expected);
        sort($expected);
        
        foreach ($this->instance->sort(SortHelper::reverse())->getAll() as $file) {
            $results[] = $file->getFilename();
        }
        
        $this->assert->array(array_reverse($expected))->isEqualTo($results);
    }

    public function testGetSetAsCollection()
    {
        $tree = $this->fs->getTree();
        $setRoot = new \SplFileInfo($this->fs->getRoot() . '/Travels');
        $this->instance = new \Smak\Portfolio\Set($setRoot);
        $expected = array_keys($tree['Travels']);

        $this->assert->object($collection = $this->instance->asCollection())
             ->isInstanceOf('\Smak\Portfolio\Collection');

        $this->assert->object($collection)
             ->isInstanceOf('\Symfony\Component\Finder\Finder');

        foreach ($collection->getAll() as $set) {
            $results[] = $set->name;
        }

        $this->assert->array($expected)->isEqualTo($results);
    }

    public function testSerialization()
    {   
        $helpers = array(
            'foo'   => 'bar',
            'bar'   => 'baz',
            'time'  => time()
        );

        $set_root = $this->instance->getSplInfo()->getRealPath();
        $set_name = $this->instance->name;
        $this->instance->helpers = $helpers;
        $unserialized_instance = unserialize(serialize($this->instance));

        $this->assert->string($unserialized_instance->name)
            ->isEqualTo($set_name);

        $this->assert->string($unserialized_instance->getSplInfo()->getRealPath())
            ->isEqualTo($set_root);

        $this->assert->array($unserialized_instance->helpers)
            ->isEqualTo($helpers);

        $this->assert->integer($unserialized_instance->count())
            ->isEqualTo(4);
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
                'chile.html.twig'
        )));
    }
}