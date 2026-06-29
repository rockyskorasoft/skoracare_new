<?php

namespace App\Http\Controllers\Web;

use App\Http\Controllers\Controller;
use App\Traits\WebResponseTrait;

class WebController extends Controller
{
    use WebResponseTrait;

    /**
     * Shared redirect route used by child controllers after CRUD actions.
     */
    protected $indexRouteName;

    /**
     * Base web controller constructor for shared web-layer behavior.
     */
    public function __construct() {}
}
