<?php
function __autoload($class_name) {
    require_once $class_name . '.php';
}

$smak = new Smak;
$smak->loadNav('json/nav.json');
$smak->render();
?>