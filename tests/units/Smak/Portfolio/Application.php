<?php

namespace Smak\Portfolio\tests\units;

use mageekguy\atoum;
use Smak\Portfolio;
use tests\Fs;

require_once __DIR__ . '/../../../bootstrap.php';

class Application extends atoum\test
{
    const FS_REL = '/../../../fs';
    
    public function setUp()
    {
        $fs = new Fs(__DIR__ . self::FS_REL, $this->_fsTreeProvider());
        $fs->build();
        $this->assert->boolean($fs->isBuilt())->isTrue();
    }
    
    public function beforeTestMethod($method)
    {
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
    
    public function tearDown()
    {
        $fs = new Fs(__DIR__ . self::FS_REL, $this->_fsTreeProvider());
        $fs->clear();
    }
    
    private function _fsTreeProvider()
    {
        return array('Canon_450D'   => array(
            'Sandrine'  => array(
                'sample-1.jpg',
                'sample-2.jpg',
                'sandrine.twig'
            ),
            'Weddings'  => array(
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