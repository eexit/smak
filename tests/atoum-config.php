<?php

/*
Sample atoum configuration file.
Do "php path/to/test/file -c path/to/this/file" or "php path/to/atoum/scripts/runner.php -c path/to/this/file -f path/to/test/file" to use it.
*/

use \mageekguy\atoum;

/*
Write all on stdout.
*/
$stdOutWriter = new atoum\writers\std\out();

/*
Xunit report
*/
$xunit = new atoum\reports\asynchronous\xunit();
$runner->addReport($xunit);
$writer = new atoum\writers\file(__DIR__ . '/../build/atoum/report.xml');
$xunit->addWriter($writer);

/*
Code coverage rapport
*/
$coverageField = new atoum\report\fields\runner\coverage\html('atoum', __DIR__ . '/../build/code-coverage');
$coverageField->setRootUrl('http://localhost:8080');

$cliReport = new atoum\reports\realtime\cli();
$cliReport->addWriter($stdOutWriter)
          ->addField($coverageField, array(atoum\runner::runStop));

$runner->addReport($cliReport);