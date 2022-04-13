<?php

namespace Bootstrap;

try {
	if (!@include_once(__DIR__ . '/../vendor/autoload.php')) {
		throw new Exception('Composer is not initialized');
	}
} catch (Exception $e) {
	exit($e->getMessage());
}

require_once __DIR__ . '/../core/Support/helpers.php';

use Core\{
	Routing\Router,
	Utilities\Util,
};

use \Dotenv\Dotenv as Env;

final class Kernel
{

	protected $mode = 'stage';

	public static function env()
	{
		(Env::createImmutable(__DIR__ . '/../'))->load();
	}
	public static function invokeHandlers($error, $exception)
	{
		set_error_handler($error);
		set_exception_handler($exception);
	}

	public static function mode($mode = false)
	{
		if (!$mode && in_array($mode, ['stage', 'prod'])) {
			self::$mode = $mode;
		}
	}

	public static function run()
	{

		$router = new Router();
		require_once __DIR__ . '/../app/Routes.php';
		$router->run();
	}
}
