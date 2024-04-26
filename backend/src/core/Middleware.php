<?php
namespace Jp\Backendmbe\core; 

use Edesk\dbQuery\MyQuery;

class Middleware extends MyQuery
{

    public function __construct()
    {
        parent::__construct();
    }

    // response
    public function response()
    {
        return new Response();
    }
}
