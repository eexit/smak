<?php

/*
Sample atoum configuration file.
Do "php path/to/test/file -c path/to/this/file" or "php path/to/atoum/scripts/runner.php -c path/to/this/file -f path/to/test/file" to use it.
*/

use \mageekguy\atoum;

/*
This will add the default CLI report
*/
$stdOutWriter = new atoum\writers\std\out();

/*
Xunit report
*/
$xunitWriter = new atoum\writers\file(__DIR__ . '/../build/report.xml');
$xunitReport = new atoum\reports\asynchronous\xunit();
$xunitReport->addWriter($xunitWriter);
$runner->addReport($xunitReport);

/*
Code coverage rapport
*/
$coverageField = new atoum\report\fields\runner\coverage\html('atoum', __DIR__ . '/../build/code-coverage');
$coverageField->setRootUrl('http://localhost:8080');

$cliReport = new atoum\reports\realtime\cli();
$cliReport->addWriter($stdOutWriter)
          ->addField($coverageField, array(atoum\runner::runStop));

$runner->addReport($cliReport);