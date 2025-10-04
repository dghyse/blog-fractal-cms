<?php
/**
 * index.php
 *
 * PHP Version 8.2+
 *
 * @version XXX
 * @package www
 */

use yii\web\Application;

// init autoloaders
require dirname(__DIR__).'/vendor/autoload.php';

require dirname(__DIR__).'/common/config/bootstrap.php';

require dirname(__DIR__).'/vendor/yiisoft/yii2/Yii.php';

$config = require dirname(__DIR__).'/webapp/config/main.php';

(new Application($config))->run();
