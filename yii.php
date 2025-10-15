<?php
/**
 * yii.php
 *
 * PHP Version 8.2+
 *
 * @author David Ghyse <david.ghysefree.fr>
 * @version XXX
 * @package app\config
 */

use yii\console\Application;

// fcgi doesn't have STDIN and STDOUT defined by default
defined('STDIN') or define('STDIN', fopen('php://stdin', 'r'));
defined('STDOUT') or define('STDOUT', fopen('php://stdout', 'w'));

// init autoloaders
require __DIR__.'/vendor/autoload.php';

require __DIR__.'/common/config/bootstrap.php';

require __DIR__.'/vendor/yiisoft/yii2/Yii.php';

$config = require __DIR__.'/console/config/main.php';
$exitCode = (new Application($config))->run();
exit($exitCode);
