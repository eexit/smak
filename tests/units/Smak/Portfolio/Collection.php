<?php

namespace Smak\Portfolio\tests\units;

use Smak\Portfolio;
use Smak\Portfolio\SortHelper;
use Symfony\Component\Finder\Adapter;
use tests\units\Smak\Portfolio\Fs;

require_once __DIR__ . '/../../../../vendor/autoload.php';

class Collection extends Fs\FsAdapter
{
    public function setUp()
    {
        $this->buildFs();
    }

    public function buildFs()
    {
        $fs = new Fs\FsBuilder($this->fsTreeProvider());
        $fs->setDiffTime(true)->build();

        return $fs;
    }

    public function buildCollection(Adapter\AdapterInterface $adapter, $dir = null)
    {
        if (! $dir) {
            $fs = $this->buildFs();
            $dir = $fs->getRoot();
        }

        $collection = new \Smak\Portfolio\Collection($dir);
        $collection->removeAdapters()->addAdapter($adapter);

        return $collection;
    }

    public function beforeTestMethod($method)
    {
        $fs = $this->buildFs();
        $this->boolean($fs->isBuilt())->isTrue();
    }
    
    /**
     * @dataProvider getAdaptersTestData
     */
    public function testClassDeclaration(Adapter\AdapterInterface $adapter)
    {
        $collection = $this->buildCollection($adapter);

        $this->object($collection)
             ->isInstanceOf('\Smak\Portfolio\Portfolio');

        $this->object($collection)
             ->isInstanceOf('\Symfony\Component\Finder\Finder');
        
        $this->class('\Smak\Portfolio\Collection')
             ->hasInterface('\Countable');
    }
    
    /**
    * @dataProvider getAdaptersTestData
    */
    public function testGetAll(Adapter\AdapterInterface $adapter)
    {
        $collection = $this->buildCollection($adapter);

        $this->object($sets = $collection->getAll())
             ->isInstanceOf('\ArrayIterator');

        foreach ($sets as $set) {
            $this->object($set)->isInstanceOf('\Smak\Portfolio\Set');
        }
    }
    
    /**
    * @dataProvider getAdaptersTestData
    */
    public function testCount(Adapter\AdapterInterface $adapter)
    {
        $collection = $this->buildCollection($adapter);

        $this->integer($collection->count())
             ->isEqualTo(5);
    }
    
    /**
    * @dataProvider getAdaptersTestData
    */
    public function testGetById(Adapter\AdapterInterface $adapter)
    {
        $collection = $this->buildCollection($adapter);
        $tree       = $this->buildFs()->getTree();
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
    
    /**
    * @dataProvider getAdaptersTestData
    */
    public function testGetByName(Adapter\AdapterInterface $adapter)
    {
        $collection = $this->buildCollection($adapter);

        $this->exception(function() use ($collection) {
            $collection->getByName(23.34);
        })->isInstanceOf('\InvalidArgumentException');

        $this->object($set = $collection->getByName('Travels'))
             ->isInstanceOf('\Smak\Portfolio\Set');

        $this->string($set->name)->isEqualTo('Travels');
        
        $this->variable($collection->getByName('foobar'))
             ->isNull();
    }

    /**
    * @dataProvider getAdaptersTestData
    */
    public function testGetFirst(Adapter\AdapterInterface $adapter)
    {
        $first    = $this->buildCollection($adapter)->getFirst();
        $tree     = array_keys($this->buildFs()->getTree());
        $expected = array_shift($tree);
        
        $this->object($first)
             ->isInstanceOf('\Smak\Portfolio\Set');
        
        $this->string($first->getSplInfo()->getBasename())
             ->isEqualTo($expected);
    }

    /**
     * @dataProvider getAdaptersTestData
     */
    public function testGetLast(Adapter\AdapterInterface $adapter)
    {
        $last     = $this->buildCollection($adapter)->getLast();
        $tree     = $this->buildFs()->getTree();
        $expected = array_keys(array_pop($tree));
        
        $this->object($last)
             ->isInstanceOf('\Smak\Portfolio\Set');
        
        $this->string($last->getSplInfo()->getBasename())
             ->isEqualTo($expected[0]);
    }

    /**
     * @dataProvider getAdaptersTestData
     */
    public function testGetAllInReversedOrder(Adapter\AdapterInterface $adapter)
    {
        $fs         = $this->buildFs();
        $collection = $this->buildCollection($adapter, $fs->getRoot() . DIRECTORY_SEPARATOR . 'Canon_450D');
        $tree       = $fs->getTree();
        $expected   = array_keys($tree['Canon_450D']);
        sort($expected);
        
        foreach ($collection->sort(SortHelper::reverseName())->getAll() as $set) {
            $results[] = $set->name;
        }
        
        $this->array(array_reverse($expected))->isEqualTo($results);
    }
    
    /**
     * @dataProvider getAdaptersTestData
     */
    public function testGetCollectionAsSet(Adapter\AdapterInterface $adapter)
    {
        $fs         = $this->buildFs();
        $path       = $fs->getRoot() . DIRECTORY_SEPARATOR . 'Canon_450D' . DIRECTORY_SEPARATOR . '2012-12-12';
        $collection = $this->buildCollection($adapter, $path);
        $tree       = $fs->getTree();
        $expected   = $tree['Canon_450D']['2012-12-12'];

        $this->object($set = $collection->asSet())
             ->isInstanceOf('\Smak\Portfolio\Set');

        $this->object($set)
             ->isInstanceOf('\Symfony\Component\Finder\Finder');

        $this->integer($set->count())->isEqualTo(count($expected) - 1);
        $this->string($set->getTemplate()->getFilename())
             ->isEqualTo(array_pop($expected));
    }

    /**
     * @dataProvider getAdaptersTestData
     */
    public function testSerialization(Adapter\AdapterInterface $adapter)
    {
        $collection              = $this->buildCollection($adapter);
        $collection_dir          = $collection->asSet()->getSplInfo()->getRealPath();
        $unserialized_collection = unserialize(serialize($collection));

        $this->string($unserialized_collection->asSet()->getSplInfo()->getRealPath())
             ->isEqualTo($collection_dir);
        
        $this->integer($unserialized_collection->count())
             ->isEqualTo(5);
    }
    
    public function tearDown()
    {
        $this->buildFs()->clear();
    }
    
    protected function fsTreeProvider()
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
