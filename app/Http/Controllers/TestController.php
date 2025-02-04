<?php

namespace App\Http\Controllers;

use App\Http\Controllers\Procurement\PRController;
use Illuminate\Http\Request;

class TestController extends Controller
{
    public function index()
    {
        $result = app(PRController::class)->getDashboardData();

        return $result;
    }
}
