<?php

namespace Wristler\Rest;

use Wristler\Rest\Routes\Information;
use Wristler\Rest\Routes\Watches;

class Rest
{

    protected $watches;

    protected $information;

    public function __construct()
    {
        $this->watches = new Watches();
        $this->information = new Information();
    }

}