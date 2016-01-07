<?php

namespace App\Http\Controllers\Guest;

use Illuminate\Http\Request;

use App\Http\Requests;
use App\Http\Controllers\Controller;
use App\Http\Controllers\BaseController;
use Illuminate\Support\Facades\Session;
use App\Http\Controllers\HomeController;

class GuestController extends HomeController
{
    //
    public function __construct()
    {
    	/*$this->middleware('guest');*/
    }

    public function dump(){
        dd(bcrypt('306900'));
    }
    
    public function index()
    {
    	$this->data['body'] = "layouts.home.pages.index";
        #$this->data['content'] = "layouts.tmp.pages.local.index";
        return $this->output();
    }  
}
