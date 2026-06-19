<?php

namespace App\Exceptions;

use Exception;

class ProjectLimitExceededException extends Exception
{
    public function __construct()
    {
        parent::__construct('Project limit reached for your current plan. Upgrade to Pro to create more projects.');
    }
}
