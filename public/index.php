<?php

namespace Index;

/**
 * Define Error Handlers
 */
define('ErrorHandler', 'Core\Errors\Handler::errorHandler');
define('ErrorException', 'Core\Errors\Handler::exceptionHandler');

/**
 * Define timezone
 */
define('Timezone', 'Europe/Rome');
date_default_timezone_set(Timezone);

/**
 * Start with bootstrap autoloader
 */
require __DIR__.'/../bootstrap/autoload.php';

use Bootstrap\Kernel;

/**
 * Set mode stage | prod
 */

Kernel::mode('stage');
Kernel::invokeHandlers(ErrorHandler, ErrorException);
Kernel::env();
Kernel::run();
	