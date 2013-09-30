<?php

namespace Smak\Portfolio\tests\units;

use Smak\Portfolio;
use Smak\Portfolio\SortHelper;
use Symfony\Component\Finder\Adapter;
use tests\units\Smak\Portfolio\Fs;

require_once __DIR__ . '/../../../../vendor/autoload.php';

class Set extends Fs\FsAdapter
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

    public function buildSet(Adapter\AdapterInterface $adapter, \SplFileInfo $set_root = null)
    {
        $fs = $this->buildFs();
        $tree = $fs->getTree();

        if (null == $set_root) {
            $set_root = new \SplFileInfo($fs->getRoot()
                . DIRECTORY_SEPARATOR
                . 'Travels'
                . DIRECTORY_SEPARATOR
                . 'Chile');
        }

        $set = new \Smak\Portfolio\Set($set_root);
        $set->removeAdapters()->addAdapter($adapter);

        return $set;
    }

    public function beforeTestMethod($method)
    {
        foreach ($this->getValidAdapters() as $adapter) {
            $set = $this->buildSet($adapter);
            $this->string($set->name)
                 ->isEqualTo('Chile');
        }
    }
    
    /**
     * @dataProvider getAdaptersTestData
     */
    public function testClassDeclaration(Adapter\AdapterInterface $adapter)
    {
        $set = $this->buildSet($adapter);

        $this->object($set)
             ->isInstanceOf('\Symfony\Component\Finder\Finder');

        $this->object($set)
             ->isInstanceOf('\Smak\Portfolio\Portfolio');
        
        $this->class('\Smak\Portfolio\Set')
             ->hasInterface('\Countable');
    }
    
    /**
     * @dataProvider getAdaptersTestData
     */
    public function testCount(Adapter\AdapterInterface $adapter)
    {
        $set = $this->buildSet($adapter);
        $this->integer($set->count())->isEqualTo(4);
    }
    
    /**
     * @dataProvider getAdaptersTestData
     */
    public function testExtensions(Adapter\AdapterInterface $adapter)
    {
        $set = $this->buildSet($adapter);
        
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
    
    /**
     * @dataProvider getAdaptersTestData
     */
    public function testTemplateExtension(Adapter\AdapterInterface $adapter)
    {
        $set = $this->buildSet($adapter);
        
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
    
    /**
     * @dataProvider getAdaptersTestData
     */
    public function testGetAll(Adapter\AdapterInterface $adapter)
    {
        $set = $this->buildSet($adapter);
        $this->object($set->getAll())
             ->isInstanceOf('\ArrayIterator');
    }
    
    /**
     * @dataProvider getAdaptersTestData
     */
    public function testGetById(Adapter\AdapterInterface $adapter)
    {
        $set = $this->buildSet($adapter);
        $tree = $this->buildFs()->getTree();
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
    
    /**
     * @dataProvider getAdaptersTestData
     */
    public function testGetByName(Adapter\AdapterInterface $adapter)
    {
        $set = $this->buildSet($adapter);
        $tree = $this->buildFs()->getTree();
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
    
    /**
     * @dataProvider getAdaptersTestData
     */
    public function testGetTemplate(Adapter\AdapterInterface $adapter)
    {
        $set = $this->buildSet($adapter);

        $this->object($set->getTemplate())
             ->isInstanceOf('\SplFileInfo');
        
        $this->string($set->getTemplate()->getFilename())
             ->isEqualTo('Chile.html.twig');
    }
    
    /**
     * @dataProvider getAdaptersTestData
     */
    public function testGetSetSplInfo(Adapter\AdapterInterface $adapter)
    {
        $set = $this->buildSet($adapter);
        $set_root = $this->buildFs()->getRoot()
                . DIRECTORY_SEPARATOR
                . 'Travels'
                . DIRECTORY_SEPARATOR
                . 'Chile';

        $this->object($set->getSplInfo())
             ->isInstanceOf('\SplFileInfo');
        
        $this->string($set->getSplInfo()->getFilename())
             ->isEqualTo('Chile');
        
        $this->string($set->getSplInfo()->getRealPath())
             ->isEqualTo($set_root);
    }
    
    /**
     * @dataProvider getAdaptersTestData
     */
    public function testGetFirst(Adapter\AdapterInterface $adapter)
    {
        $set      = $this->buildSet($adapter);
        $first    = $set->getFirst();
        $tree     = $this->buildFs()->getTree();
        $expected = array_shift($tree['Travels']['Chile']);
        
        $this->object($first)
             ->isInstanceOf('\Smak\Portfolio\Photo');
        
        $this->string($first->getFilename())
             ->isEqualTo($expected);
    }
    
    /**
     * @dataProvider getAdaptersTestData
     */
    public function testGetLast(Adapter\AdapterInterface $adapter)
    {
        $set  = $this->buildSet($adapter);
        $last = $set->getLast();
        $tree = $this->buildFs()->getTree();
        array_pop($tree['Travels']['Chile']); // Removes the twig file from Tree
        $expected = array_pop($tree['Travels']['Chile']);
        
        $this->object($last)
             ->isInstanceOf('\Smak\Portfolio\Photo');
        
        $this->string($last->getFilename())
             ->isEqualTo($expected);
    }

    /**
     * @dataProvider getAdaptersTestData
     */
    public function testGetInReversedOrder(Adapter\AdapterInterface $adapter)
    {
        $set      = $this->buildSet($adapter);
        $tree     = $this->buildFs()->getTree();
        $expected = $tree['Travels']['Chile'];
        array_pop($expected);
        sort($expected);
        
        foreach ($set->sort(SortHelper::reverseName())->getAll() as $file) {
            $results[] = $file->getFilename();
        }
        
        $this->array(array_reverse($expected))->isEqualTo($results);
    }

    /**
     * @dataProvider getAdaptersTestData
     */
    public function testGetSetAsCollection(Adapter\AdapterInterface $adapter)
    {
        $fs       = $this->buildFs();
        $tree     = $fs->getTree();
        $set_root = new \SplFileInfo($fs->getRoot() . DIRECTORY_SEPARATOR . 'Travels');
        $set      = $this->buildSet($adapter, $set_root);
        $expected = array_keys($tree['Travels']);

        $this->object($collection = $set->asCollection())
             ->isInstanceOf('\Smak\Portfolio\Collection');

        $this->object($collection)
             ->isInstanceOf('\Symfony\Component\Finder\Finder');

        foreach ($collection->getAll() as $set) {
            $results[] = $set->name;
        }

        $this->array($expected)->isEqualTo($results);
    }

    /**
     * @dataProvider getAdaptersTestData
     */
    public function testSerialization(Adapter\AdapterInterface $adapter)
    {   
        $set = $this->buildSet($adapter);
        $helpers = array(
            'foo'   => 'bar',
            'bar'   => 'baz',
            'time'  => time()
        );

        $set_root = $set->getSplInfo()->getRealPath();
        $set_name = $set->name;

        foreach ($helpers as $key => $value) {
            $set->$key = $value;
        }

        $unserialized_instance = unserialize(serialize($set));

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
        $this->buildFs()->clear();
    }
    
    protected function fsTreeProvider()
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
