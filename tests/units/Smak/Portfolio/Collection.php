<?php

namespace Smak\Portfolio\tests\units;

use mageekguy\atoum;
use Smak\Portfolio;
use Smak\Portfolio\SortHelper;
use tests\Fs;

require_once __DIR__ . '/../../../../vendor/autoload.php';

class Collection extends atoum\test
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
        $this->instance = new \Smak\Portfolio\Collection($this->fs->getRoot());
    }
    
    public function testClassDeclaration()
    {
        $this->object($this->instance)
             ->isInstanceOf('\Smak\Portfolio\Portfolio');

        $this->object($this->instance)
             ->isInstanceOf('\Symfony\Component\Finder\Finder');
        
        $this->class('\Smak\Portfolio\Collection')
             ->hasInterface('\Countable');
    }
    
    public function testGetAll()
    {
        $this->object($sets = $this->instance->getAll())
             ->isInstanceOf('\ArrayIterator');

        foreach ($sets as $set) {
            $this->object($set)->isInstanceOf('\Smak\Portfolio\Set');
        }
    }
    
    public function testCount()
    {
        $this->integer($this->instance->count())
             ->isEqualTo(5);
    }

    public function testGetById()
    {
        $collection = $this->instance;
        $tree       = $this->fs->getTree();
        $tree       = array_keys($tree);
        
        $this->exception(function() use ($collection) {
            $collection->getById("foo");
        })->isInstanceOf('\InvalidArgumentException');
        
        $this->string($collection->getById(3)->getSplInfo()->getBasename())
             ->isEqualTo($tree[1]);
        
        $this->exception(function() use ($collection) {
            $collection->getById(123);
        })->isInstanceOf('\OutOfRangeException');
    }
    
    public function testGetByName()
    {
        $collection = $this->instance;

        $this->exception(function() use ($collection) {
            $collection->getByName(23.34);
        })->isInstanceOf('\InvalidArgumentException');

        $this->object($set = $collection->getByName('Travels'))
             ->isInstanceOf('\Smak\Portfolio\Set');

        $this->string($set->name)->isEqualTo('Travels');
        
        $this->variable($collection->getByName('foobar'))
             ->isNull();
    }

    public function testGetFirst()
    {
        $first    = $this->instance->getFirst();
        $tree     = array_keys($this->fs->getTree());
        $expected = array_shift($tree);
        
        $this->object($first)
             ->isInstanceOf('\Smak\Portfolio\Set');
        
        $this->string($first->getSplInfo()->getBasename())
             ->isEqualTo($expected);
    }

    public function testGetLast()
    {
        $tree     = $this->fs->getTree();
        $last     = $this->instance->getLast();
        $expected = array_keys(array_pop($tree));
        
        $this->object($last)
             ->isInstanceOf('\Smak\Portfolio\Set');
        
        $this->string($last->getSplInfo()->getBasename())
             ->isEqualTo($expected[0]);
    }

    public function testGetAllInReversedOrder()
    {
        $this->instance = new \Smak\Portfolio\Collection($this->fs->getRoot() . '/Canon_450D');

        $tree = $this->fs->getTree();
        $expected = array_keys($tree['Canon_450D']);
        sort($expected);
        
        foreach ($this->instance->sort(SortHelper::reverseName())->getAll() as $set) {
            $results[] = $set->name;
        }
        
        $this->array(array_reverse($expected))->isEqualTo($results);
    }
    
    public function testGetCollectionAsSet()
    {
        $this->instance = new \Smak\Portfolio\Collection($this->fs->getRoot() . '/Canon_450D/2012-12-12');
        $tree           = $this->fs->getTree();
        $expected       = $tree['Canon_450D']['2012-12-12'];

        $this->object($set = $this->instance->asSet())
             ->isInstanceOf('\Smak\Portfolio\Set');

        $this->object($set)
             ->isInstanceOf('\Symfony\Component\Finder\Finder');

        $this->integer($set->count())->isEqualTo(count($expected) - 1);
        $this->string($set->getTemplate()->getFilename())
             ->isEqualTo(array_pop($expected));
    }

    public function testSerialization()
    {
        $collection_dir        = $this->instance->asSet()->getSplInfo()->getRealPath();
        $unserialized_instance = unserialize(serialize($this->instance));

        $this->string($unserialized_instance->asSet()->getSplInfo()->getRealPath())
             ->isEqualTo($collection_dir);
        
        $this->integer($unserialized_instance->count())
             ->isEqualTo(5);
    }
    
    public function tearDown()
    {
        $fs = new Fs($this->_fsTreeProvider());
        $fs->clear();
    }
    
    private function _fsTreeProvider()
    {
        return array(
            'Canon_450D'        => array(
                '2012-12-12'    => array(
                    'sample-1.jpg',
                    'sample-2.jpg',
                    '2012-12-12.html.twig'
                ),
                '2010-10-01'    => array(
                    'sample-1.jpg',
                    'sample-2.jpg',
                    'sample-3.jpg',
                    '2010-10-01.html.twig'
                )),
            'Travels'           => array(
                'Chile' => array(
                    'sample-1.jpeg',
                    'sample-3.jpG',
                    'sample-2.jpg',
                    'sample-4.png',
                    'chile.twig'
        )));
    }
}
