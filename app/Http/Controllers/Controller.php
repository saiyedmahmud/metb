<?php

namespace App\Http\Controllers;

use App\Traits\ErrorTrait;
use App\Traits\SearchTrait;
use App\Traits\UpToThreeDecimalTrait;
use Illuminate\Foundation\Auth\Access\AuthorizesRequests;
use Illuminate\Foundation\Validation\ValidatesRequests;
use Illuminate\Routing\Controller as BaseController;

class Controller extends BaseController
{
    use AuthorizesRequests, ValidatesRequests, ErrorTrait, SearchTrait, UpToThreeDecimalTrait;
}
