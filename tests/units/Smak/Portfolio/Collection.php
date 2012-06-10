<?php

namespace Smak\Portfolio\tests\units;

use mageekguy\atoum;
use Smak\Portfolio;
use Smak\Portfolio\SortHelper;
use tests\Fs;

require_once __DIR__ . '/../../../../vendor/autoload.php';

class Collection extends atoum\test
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
        $this->instance = new \Smak\Portfolio\Collection(__DIR__ . self::FS_REL);
    }
    
    public function testClassDeclaration()
    {
        $this->assert->object($this->instance)
             ->isInstanceOf('\Smak\Portfolio\Portfolio');

        $this->assert->object($this->instance)
             ->isInstanceOf('\Symfony\Component\Finder\Finder');
        
        $this->assert->class('\Smak\Portfolio\Collection')
              ->hasInterface('\Countable');
    }
    
    public function testGetAll()
    {
        $this->assert->object($sets = $this->instance->getAll())
             ->isInstanceOf('\ArrayIterator');

        foreach ($sets as $set) {
            $this->assert->object($set)
                ->isInstanceOf('\Smak\Portfolio\Set');
        }
    }
    
    public function testCount()
    {
        $this->assert->integer($this->instance->count())
             ->isEqualTo(5);
    }

    public function testGetById()
    {
        $collection = $this->instance;
        $tree = $this->fs->getTree();
        $tree = array_keys($tree);
        
        $this->assert->exception(function() use ($collection) {
            $collection->getById("foo");
        })->isInstanceOf('\InvalidArgumentException');
        
        $this->assert->string($collection->getById(3)->getSplInfo()->getBasename())
             ->isEqualTo($tree[1]);
        
        $this->assert->exception(function() use ($collection) {
            $collection->getById(123);
        })->isInstanceOf('\OutOfRangeException');
    }
    
    public function testGetByName()
    {
        $collection = $this->instance;

        $this->assert->exception(function() use ($collection) {
            $collection->getByName(23.34);
        })->isInstanceOf('\InvalidArgumentException');

        $this->assert->object($set = $collection->getByName('Travels'))
             ->isInstanceOf('\Smak\Portfolio\Set');

        $this->assert->string($set->name)->isEqualTo('Travels');
        
        $this->assert->variable($collection->getByName('foobar'))
            ->isNull();
    }

    public function testGetFirst()
    {
        $first = $this->instance->getFirst();
        $tree = array_keys($this->fs->getTree());
        $expected = array_shift($tree);
        
        $this->assert->object($first)
             ->isInstanceOf('\Smak\Portfolio\Set');
        
        $this->assert->string($first->getSplInfo()->getBasename())
             ->isEqualTo($expected);
    }

    public function testGetLast()
    {
        $tree = $this->fs->getTree();
        $last = $this->instance->getLast();
        $expected = array_keys(array_pop($tree));
        
        $this->assert->object($last)
             ->isInstanceOf('\Smak\Portfolio\Set');
        
        $this->assert->string($last->getSplInfo()->getBasename())
             ->isEqualTo($expected[0]);
    }

    public function testGetAllInReversedOrder()
    {
        $dir = __DIR__ . self::FS_REL . '/Canon_450D';
        $this->instance = new \Smak\Portfolio\Collection($dir);

        $tree = $this->fs->getTree();
        $expected = array_keys($tree['Canon_450D']);
        sort($expected);
        
        foreach ($this->instance->sort(SortHelper::reverseName())->getAll() as $set) {
            $results[] = $set->name;
        }
        
        $this->assert->array(array_reverse($expected))->isEqualTo($results);
    }
    
    public function testGetCollectionAsSet()
    {
        $dir = __DIR__ . self::FS_REL . '/Canon_450D/2012-12-12';
        $this->instance = new \Smak\Portfolio\Collection($dir);
        $tree = $this->fs->getTree();
        $expected = $tree['Canon_450D']['2012-12-12'];

        $this->assert->object($set = $this->instance->asSet())
            ->isInstanceOf('\Smak\Portfolio\Set');

        $this->assert->object($set)
            ->isInstanceOf('\Symfony\Component\Finder\Finder');

        $this->assert->integer($set->count())->isEqualTo(count($expected) - 1);
        $this->assert->string($set->getTemplate()->getFilename())
            ->isEqualTo(array_pop($expected));
    }

    public function testSerialization()
    {
        $collection_dir = $this->instance->asSet()->getSplInfo()->getRealPath();
        $unserialized_instance = unserialize(serialize($this->instance));

        $this->assert->string($unserialized_instance->asSet()->getSplInfo()->getRealPath())
             ->isEqualTo($collection_dir);
        
        $this->assert->integer($unserialized_instance->count())
             ->isEqualTo(5);
    }
    
    public function tearDown()
    {
        $fs = new Fs(__DIR__ . self::FS_REL, $this->_fsTreeProvider());
        $fs->clear();
    }
    
    private function _fsTreeProvider()
    {
        return array('Canon_450D'   => array(
            '2012-12-12'  => array(
                'sample-1.jpg',
                'sample-2.jpg',
                '2012-12-12.html.twig'
            ),
            '2010-10-01'  => array(
                'sample-1.jpg',
                'sample-2.jpg',
                'sample-3.jpg',
                '2010-10-01.html.twig'
            )),'Travels'            => array(
            'Chile'     => array(
                'sample-1.jpeg',
                'sample-3.jpG',
                'sample-2.jpg',
                'sample-4.png',
                'chile.twig'
        )));
    }
}