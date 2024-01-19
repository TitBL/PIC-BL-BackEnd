<?php

namespace App\Http\Controllers;

use Illuminate\Foundation\Auth\Access\AuthorizesRequests;
use Illuminate\Foundation\Validation\ValidatesRequests;
use Illuminate\Routing\Controller as BaseController;

/**
 * Class Controller
 * 
 * @package App\Http\Controllers 
 * @author Rafael Larrea <jrafael1108@gmail.com>  
 */
class Controller extends BaseController
{
    use AuthorizesRequests, ValidatesRequests;
}
