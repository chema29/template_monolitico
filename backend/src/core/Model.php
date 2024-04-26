<?php
namespace Jp\Backend\core;

use Edesk\dbQuery\MyQuery;
// class model base
class Model extends MyQuery{
    protected $db;
    public function __construct() {
        parent::__construct();
    }
}