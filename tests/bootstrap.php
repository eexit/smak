<?php
use \Symfony\Component\ClassLoader\UniversalClassLoader;
use \tests\Fs;

require_once __DIR__ . '/../vendor/Symfony/src/Symfony/Component/ClassLoader/UniversalClassLoader.php';
require_once __DIR__ . '/lib/atoum.phar';
require_once __DIR__ . '/fs/Fs.php';

$loader = new UniversalClassLoader();
$loader->registerNamespaces(array(
    'Symfony'   => __DIR__ . '/../vendor/Symfony/src',
    'Smak'      => __DIR__ . '/../lib'
));

$loader->register();