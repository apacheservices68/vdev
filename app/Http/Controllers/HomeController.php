<?php

namespace App\Http\Controllers;

use App\Http\Requests;
use Illuminate\Http\Request;
use App\Http\Controllers\BaseController;
use Route;

class HomeController extends BaseController
{
    public $data;
    /**
     * Create a new controller instance.
     *
     * @return void
     */
    public function __construct()
    {
        //$this->middleware('auth');
    }

    /**
     * Show the application dashboard.
     *
     * @return Response
     */
    public function index()
    {
        return redirect("/");
    }    

    /**
     * @param $language
     * @return string
     */
    public function getTitle($language)
    {
        $method = $this->extractRoute();        
        return trans($language . '.' . $method . '.title');
    }

    /**
     * @param $language
     * @return string
     */
    public function getDescription($language)
    {
        $method = $this->extractRoute();

        return trans($language . '.' . $method . '.description');
    }

    /**
     * @return mixed
     */
    protected function extractRoute()
    {
        $route  = Route::current();        
        $data   = explode('@', $route->getActionName());
        
        $method = $data[1];        
        return $method;
    }

    
    protected function output($template = 'home' , $admin_login = false , $with = NULL){
        $ext = $admin_login === false ? "app" : "custom"; 
        $this->data['extend'] = 'layouts.'.$template.'.'.$ext;
        if(!is_null($with) && is_array($with) && count($with) > 0) {            
            return view('layouts.'.$template.'.default' , $this->data)->with($with[0] , $with[1]);    
        }
        return view('layouts.'.$template.'.default' , $this->data);
    }

}
