<?php

/**
 * Includo e istanzio l'autoloader presente in /bootstrap/autoload.php
 */
require __DIR__.'/../bootstrap/autoload.php';

$kernel = new Kernel();

/**
 * Impoto la modalità del sistema stage|prod
 * Eseguo il run (handlers + routing)
 */

$kernel->mode('stage');
$kernel->run();
	