#!/usr/bin/env php
<?php
use Wisp\Orm\Dev\Toolbox;
(@include_once __DIR__ . '/../vendor/autoload.php')
or (@include_once __DIR__ . '/../autoload.php')
or (@include_once __DIR__ . '/../../../autoload.php')
or die('cannot find autoload.php');

Toolbox::main();