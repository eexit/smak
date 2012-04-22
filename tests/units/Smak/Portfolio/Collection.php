<?php

namespace Smak\Portfolio\tests\units;

use mageekguy\atoum;
use Smak\Portfolio;
use Smak\Portfolio\SortHelper;
use tests\Fs;

require_once __DIR__ . '/../../../bootstrap.php';

class Application extends atoum\test
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
        $this->instance = new \Smak\Portfolio\Application(__DIR__ . self::FS_REL);
    }
    
    public function testClassDeclaration()
    {
        $this->assert->object($this->instance)
             ->isInstanceOf('\Symfony\Component\Finder\Finder');
        
        $this->assert->class('\Smak\Portfolio\Application')
              ->hasInterface('\Countable');
    }
    
    public function testGetSets()
    {
        $this->assert->object($sets = $this->instance->getSets())
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
    
    public function testGetSet()
    {
        $this->assert->object($this->instance->getSet('Chile'))
             ->isInstanceOf('\Smak\Portfolio\Set');
    }

    public function testGetSetsInNaturalOrder()
    {
        $dir = __DIR__ . self::FS_REL . '/Canon_450D';
        $this->instance = new \Smak\Portfolio\Application($dir);

        $tree = $this->fs->getTree();
        $expected = array_keys($tree['Canon_450D']);
        sort($expected);
        
        foreach ($this->instance->getSets() as $set) {
            $results[] = $set->name;
        }
        
        $this->assert->array($expected)->isEqualTo($results);
    }

    public function testGetSetsInReversedNaturalOrder()
    {
        $dir = __DIR__ . self::FS_REL . '/Canon_450D';
        $this->instance = new \Smak\Portfolio\Application($dir);

        $tree = $this->fs->getTree();
        $expected = array_keys($tree['Canon_450D']);
        sort($expected);
        
        foreach ($this->instance->sort(SortHelper::reverse())->getSets() as $set) {
            $results[] = $set->name;
        }
        
        $this->assert->array(array_reverse($expected))->isEqualTo($results);
    }
    
    public function testGetSetsByMTimeNewestFirst()
    {
        $dir = __DIR__ . self::FS_REL . '/Canon_450D';
        $this->instance = new \Smak\Portfolio\Application($dir);

        $tree = $this->fs->getTree();
        $expected = array_keys($tree['Canon_450D']);
        sort($expected);
        
        foreach ($this->instance->sort(SortHelper::byNewest())->getSets() as $set) {
            $results[] = $set->name;
        }
        
        $this->assert->array($expected)->isEqualTo($results);   
    }

    public function testGetSetsByMTimeOlderFirst()
    {
        $dir = __DIR__ . self::FS_REL . '/Canon_450D';
        $this->instance = new \Smak\Portfolio\Application($dir);

        $tree = $this->fs->getTree();
        $expected = array_keys($tree['Canon_450D']);
        sort($expected);
        
        foreach ($this->instance->sort(SortHelper::byOldest())->getSets() as $set) {
            $results[] = $set->name;
        }
        
        $this->assert->array(array_reverse($expected))->isEqualTo($results);   
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
                'sandrine.twig'
            ),
            '2010-10-01'  => array(
                'sample-1.jpg',
                'sample-2.jpg',
                'sample-3.jpg',
                'weddings.twig'
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