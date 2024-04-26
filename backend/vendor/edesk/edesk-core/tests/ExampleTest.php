<?php

namespace Tests;

require_once "./vendor/autoload.php";


use Edesk\dbQuery\MyQuery;
use PHPUnit\Framework\TestCase;

class ExampleTest extends TestCase
{
    /**
     * @test
     */
    public function test_sum()
    {
        $MyQuery = new MyQuery();
        $res = $MyQuery->sum(2,3);
        return $res;
    }
}

