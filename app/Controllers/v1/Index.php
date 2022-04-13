<?php

namespace App\Controllers\v1;

use Core\{
    Response\Json,
    Response\Api,
    Utilities\Util,
    Utilities\Password,
    Controller\Main,
};

use App\Classes\Composer;

class Index extends Main
{

    public function array(): array
    {
        return ['You called array class'];
    }

    public function string(): string
    {
        return 'You called string class';
    }
}
