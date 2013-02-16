<?php

namespace Smak\Portfolio\tests\units;

use mageekguy\atoum;
use Smak\Portfolio;
use tests\Fs;

require_once __DIR__ . '/../../../../vendor/autoload.php';

class Photo extends atoum\test
{   
    const IMG_DIM = 10;
    
    const IMG_SIZE = 162;
    
    const IMG_TYPE = IMAGETYPE_JPEG;
    
    const IMG_DATA = <<< EOD
/9j/4AAQSkZJRgABAQAAAQABAAD/2wBDAAEBAQEBAQEBAQEBAQEBAQEBAQEBAQEBAQEBAQEBAQEB
AQEBAQEBAQEBAQECAgICAgICAgICAgMDAwMDAwMDAwP/wAALCAAKAAoBAREA/8QAFQABAQAAAAAA
AAAAAAAAAAAAAAr/xAAUEAEAAAAAAAAAAAAAAAAAAAAA/9oACAEBAAA/AL+AH//Z
EOD;
    
    public function setUp()
    {
        $fs = new Fs($tree = $this->_fsTreeProvider());
        $fs->clear()->build();
        $this->boolean($fs->isBuilt())->isTrue();
        
        $photo_test = array_shift($tree['Canon_450D']['Sandrine']);
        file_put_contents($fs->getRoot() . '/Canon_450D/Sandrine/' . $photo_test,
        base64_decode(self::IMG_DATA), LOCK_EX);
    }
    
    public function beforeTestMethod($method)
    {
        $this->fs  = new Fs($this->_fsTreeProvider());
        $setRoot   = new \SplFileInfo($this->fs->getRoot() . '/Canon_450D/Sandrine');
        $this->set = new \Smak\Portfolio\Set($setRoot);
        
        $this->photo = $this->set->getById(0);
        $this->string($this->photo->getFilename())
             ->isEqualTo('sample-1.jpg');
    }
    
    public function testClassType()
    {
        foreach ($this->set->getAll() as $photo) {
            $this->object($photo)->isInstanceOf('\Smak\Portfolio\Photo');
            $this->object($photo)->isInstanceOf('\SplFileInfo');
        }
    }
    
    public function testHasRightSize()
    {
        $this->integer($this->photo->getSize())
             ->isEqualTo(self::IMG_SIZE);
    }
    
    public function testGetHumanReadingSize()
    {
        $this->string($this->photo->getHRSize())
             ->isEqualTo(sprintf('%d %s', self::IMG_SIZE, 'b'));
    }
    
    public function testGetComputedSize()
    {
        $this->integer($this->photo->getWidth())
             ->isEqualTo(self::IMG_DIM);
        
        $this->integer($this->photo->getHeight())
             ->isEqualTo(self::IMG_DIM);
        
        $this->string($this->photo->getHtmlAttr())
             ->isEqualTo(sprintf('width="%d" height="%d"', self::IMG_DIM, self::IMG_DIM));
    }
    
    public function testGetPhotoType()
    {
        $this->integer($this->photo->getPhotoType())
             ->isEqualTo(self::IMG_TYPE);
    }
    
    public function tearDown()
    {
        $fs = new Fs($this->_fsTreeProvider());
        $fs->clear();
    }
    
    private function _fsTreeProvider()
    {
        return array('Canon_450D'   => array(
            'Sandrine'  => array(
                'sample-1.jpg',
                'sample-2.jpg',
                'sandrine.twig'
        )));
    }
}
