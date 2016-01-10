<?php

namespace App\Http\Controllers\Guest;

use Illuminate\Http\Request;

use App\Http\Requests;
use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\Session;
use App\Http\Controllers\HomeController;

class UserController extends HomeController
{
    //
    public function __construct(){
    	$this->middleware('auth');
    }

    public function index(){
    	$this->data['body'] = "layouts.home.pages.index";
        $this->data['content'] = "layouts.home.pages.user.index";
    	return $this->output();
    }
}
