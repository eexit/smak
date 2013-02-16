<?php

namespace Smak\Portfolio\tests\units;

use mageekguy\atoum;
use Smak\Portfolio;
use Smak\Portfolio\SortHelper;
use tests\Fs;

require_once __DIR__ . '/../../../../vendor/autoload.php';

class Set extends atoum\test
{   
    public function setUp()
    {
        $fs = new Fs($this->_fsTreeProvider());
        $fs->setDiffTime(true)->clear()->build();
        $this->boolean($fs->isBuilt())->isTrue();
    }
    
    public function beforeTestMethod($method)
    {
        $this->fs       = new Fs($this->_fsTreeProvider());
        $setRoot        = new \SplFileInfo($this->fs->getRoot() . '/Travels/Chile');
        $this->instance = new \Smak\Portfolio\Set($setRoot);

        $this->string($this->instance->name)
             ->isEqualTo('Chile');
    }
    
    public function testClassDeclaration()
    {
        $this->object($this->instance)
             ->isInstanceOf('\Symfony\Component\Finder\Finder');

        $this->object($this->instance)
             ->isInstanceOf('\Smak\Portfolio\Portfolio');
        
        $this->class('\Smak\Portfolio\Set')
             ->hasInterface('\Countable');
    }
    
    public function testCount()
    {
        $this->integer($this->instance->count())->isEqualTo(4);
    }
    
    public function testExtensions()
    {
        $set = $this->instance;
        
        $this->array($set->getExtensions())
             ->isEqualTo(array('.jpg', '.jpeg', '.jpf', '.png'));
        
        $this->object($set->setExtensions(
            $new_ext = array('.tiff', '.gif')
        ))->isInstanceOf('\Smak\Portfolio\Set');
        
        $this->array($set->getExtensions())
             ->isEqualTo($new_ext);

        $unserialized_set = unserialize(serialize($set));

        $this->array($unserialized_set->getExtensions())
             ->isEqualTo($new_ext);
        
        $this->exception(function() use ($set) {
            $set->setExtensions(array());
        })->isInstanceOf('\InvalidArgumentException');
    }
    
    public function testTemplateExtension()
    {
        $set = $this->instance;
        
        $this->array($set->getTemplateExtensions())
             ->isEqualTo(array('.html.twig'));
        
        $this->object($set->setTemplateExtensions(
            $new_ext = array('.html', '.txt')
        ))->isInstanceOf('\Smak\Portfolio\Set');
        
        $this->array($set->getTemplateExtensions())
             ->isEqualTo($new_ext);

        $unserialized_set = unserialize(serialize($set));

        $this->array($unserialized_set->getTemplateExtensions())
             ->isEqualTo($new_ext);
        
        $this->exception(function() use ($set) {
            $set->setTemplateExtensions(array());
        })->isInstanceOf('\InvalidArgumentException');
    }
    
    public function testGetAll()
    {
        $this->object($this->instance->getAll())
             ->isInstanceOf('\ArrayIterator');
    }
    
    public function testGetById()
    {
        $set  = $this->instance;
        $tree = $this->fs->getTree();
        $tree = $tree['Travels']['Chile'];
        array_pop($tree);
        sort($tree);
        
        $this->exception(function() use ($set) {
            $set->getById("foo");
        })->isInstanceOf('\InvalidArgumentException');
        
        $this->string($set->getById(2)->getFilename())
             ->isEqualTo($tree[2]);
        
        $this->exception(function() use ($set) {
            $set->getById(123);
        })->isInstanceOf('\OutOfRangeException');
    }
    
    public function testGetByName()
    {
        $set  = $this->instance;
        $tree = $this->fs->getTree();
        $tree = $tree['Travels']['Chile'];
        array_pop($tree);
        sort($tree);
        
        $this->exception(function() use ($set) {
            $set->getByName(23.34);
        })->isInstanceOf('\InvalidArgumentException');
        
        $this->string($set->getByName('sample-4')->getFilename())
             ->isEqualTo($tree[3]);
        
        $this->variable($set->getByName('foobar'))
            ->isNull();
    }
    
    public function testGetTemplate()
    {
        $this->object($this->instance->getTemplate())
             ->isInstanceOf('\SplFileInfo');
        
        $this->string($this->instance->getTemplate()->getFilename())
             ->isEqualTo('Chile.html.twig');
    }
    
    public function testGetSetSplInfo()
    {
        $this->object($this->instance->getSplInfo())
             ->isInstanceOf('\SplFileInfo');
        
        $this->string($this->instance->getSplInfo()->getFilename())
             ->isEqualTo('Chile');
        
        $this->string($this->instance->getSplInfo()->getRealPath())
             ->isEqualTo($this->fs->getRoot() . '/Travels/Chile');
    }
    
    public function testGetFirst()
    {
        $first    = $this->instance->getFirst();
        $tree     = $this->fs->getTree();
        $expected = array_shift($tree['Travels']['Chile']);
        
        $this->object($first)
             ->isInstanceOf('\Smak\Portfolio\Photo');
        
        $this->string($first->getFilename())
             ->isEqualTo($expected);
    }
    
    public function testGetLast()
    {
        $last = $this->instance->getLast();
        $tree = $this->fs->getTree();
        array_pop($tree['Travels']['Chile']); // Removes the twig file from Tree
        $expected = array_pop($tree['Travels']['Chile']);
        
        $this->object($last)
             ->isInstanceOf('\Smak\Portfolio\Photo');
        
        $this->string($last->getFilename())
             ->isEqualTo($expected);
    }

    public function testGetInReversedOrder()
    {
        $tree = $this->fs->getTree();
        $expected = $tree['Travels']['Chile'];
        array_pop($expected);
        sort($expected);
        
        foreach ($this->instance->sort(SortHelper::reverseName())->getAll() as $file) {
            $results[] = $file->getFilename();
        }
        
        $this->array(array_reverse($expected))->isEqualTo($results);
    }

    public function testGetSetAsCollection()
    {
        $tree           = $this->fs->getTree();
        $setRoot        = new \SplFileInfo($this->fs->getRoot() . '/Travels');
        $this->instance = new \Smak\Portfolio\Set($setRoot);
        $expected       = array_keys($tree['Travels']);

        $this->object($collection = $this->instance->asCollection())
             ->isInstanceOf('\Smak\Portfolio\Collection');

        $this->object($collection)
             ->isInstanceOf('\Symfony\Component\Finder\Finder');

        foreach ($collection->getAll() as $set) {
            $results[] = $set->name;
        }

        $this->array($expected)->isEqualTo($results);
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

        foreach ($helpers as $key => $value) {
            $this->instance->$key = $value;
        }

        $unserialized_instance = unserialize(serialize($this->instance));

        $this->string($unserialized_instance->name)
             ->isEqualTo($set_name);

        $this->string($unserialized_instance->getSplInfo()->getRealPath())
             ->isEqualTo($set_root);

        $this->integer($unserialized_instance->count())
             ->isEqualTo(4);

        unset($unserialized_instance->foo);
        array_shift($helpers);

        foreach ($helpers as $key => $value) {
            $this->variable($unserialized_instance->$key)
                 ->isNotNull();

            $this->variable($unserialized_instance->$key)
                 ->isEqualTo($value);
        }
    }
    
    public function tearDown()
    {
        $fs = new Fs($this->_fsTreeProvider());
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
                'Chile.html.twig'
        )));
    }
}
