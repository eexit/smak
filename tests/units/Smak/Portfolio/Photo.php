<?php

namespace Smak\Portfolio\tests\units;

use Smak\Portfolio;
use Symfony\Component\Finder\Adapter;
use tests\units\Smak\Portfolio\Fs;

require_once __DIR__ . '/../../../../vendor/autoload.php';

class Photo extends Fs\FsAdapter
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
        $this->buildFs();
    }

    public function buildFs()
    {
        $fs = new Fs\FsBuilder($this->fsTreeProvider());
        $fs->setDiffTime(true)->build();

        return $fs;
    }

    public function buildSet(Adapter\AdapterInterface $adapter)
    {
        $fs = $this->buildFs();
        $tree = $fs->getTree();

        $photo_test = array_shift($tree['Canon_450D']['Sandrine']);
        $set_root = new \SplFileInfo($fs->getRoot()
            . DIRECTORY_SEPARATOR
            . 'Canon_450D'
            . DIRECTORY_SEPARATOR
            . 'Sandrine');
        file_put_contents($set_root->getRealPath() . DIRECTORY_SEPARATOR. $photo_test,
            base64_decode(self::IMG_DATA), LOCK_EX);

        $set = new \Smak\Portfolio\Set($set_root);
        $set->removeAdapters()->addAdapter($adapter);

        return $set;
    }
    
    public function beforeTestMethod($method)
    {
        foreach ($this->getValidAdapters() as $adapter) {
            $photo = $this->getPhoto($adapter);
            $this->string($photo->getFilename())
                 ->isEqualTo('sample-1.jpg');
        }
    }
    
    /**
     * @dataProvider getAdaptersTestData
     */
    public function testClassType(Adapter\AdapterInterface $adapter)
    {
        $set = $this->buildSet($adapter);

        foreach ($set->getAll() as $photo) {
            $this->object($photo)->isInstanceOf('\Smak\Portfolio\Photo');
            $this->object($photo)->isInstanceOf('\SplFileInfo');
        }
    }
    
    /**
     * @dataProvider getAdaptersTestData
     */
    public function testHasRightSize(Adapter\AdapterInterface $adapter)
    {
        $photo = $this->getPhoto($adapter);

        $this->integer($photo->getSize())
             ->isEqualTo(self::IMG_SIZE);
    }
    
    /**
     * @dataProvider getAdaptersTestData
     */
    public function testGetHumanReadingSize(Adapter\AdapterInterface $adapter)
    {
        $photo = $this->getPhoto($adapter);

        $this->string($photo->getHRSize())
             ->isEqualTo(sprintf('%d %s', self::IMG_SIZE, 'b'));
    }
    
    /**
     * @dataProvider getAdaptersTestData
     */
    public function testGetComputedSize(Adapter\AdapterInterface $adapter)
    {
        $photo = $this->getPhoto($adapter);

        $this->integer($photo->getWidth())
             ->isEqualTo(self::IMG_DIM);
        
        $this->integer($photo->getHeight())
             ->isEqualTo(self::IMG_DIM);
        
        $this->string($photo->getHtmlAttr())
             ->isEqualTo(sprintf('width="%d" height="%d"', self::IMG_DIM, self::IMG_DIM));
    }
    
    /**
     * @dataProvider getAdaptersTestData
     */
    public function testGetPhotoType(Adapter\AdapterInterface $adapter)
    {
        $photo = $this->getPhoto($adapter);

        $this->integer($photo->getPhotoType())
             ->isEqualTo(self::IMG_TYPE);
    }
    
    public function tearDown()
    {
        $this->buildFs()->clear();
    }

    public function getPhoto(Adapter\AdapterInterface $adapter)
    {
        return $this->buildSet($adapter)->getById(0);
    }
    
    protected function fsTreeProvider()
    {
        return array('Canon_450D'   => array(
            'Sandrine'  => array(
                'sample-1.jpg',
                'sample-2.jpg',
                'sandrine.twig'
        )));
    }
}
