<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;

use Response;

class HomeController extends Controller{

    public function index(Request $request){

        $name = array(
            "name" => "james",
            "old" => "19"
        );

        return Response::json($name)
            ->header('ContetnType', 'application/json');
    }
}