<?php

namespace App\Http\Controllers\Admin;

use Illuminate\Http\Request;
use Storage;
use Exception;
use App\Http\Requests;
use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\Response;
use App\Http\Controllers\Admin\AdminController;
use Intervention\Image\Facades\Image; 

class ImageController extends AdminController
{
    //
    protected $des = "uploads/images";

    protected $config ;

    public function __construct()
    {
    	$this->module_name = "image";
    	parent::__construct();
    	$this->config = config('customimage');
    }

    public function getIndex(){
    	$this->fetchImage();
        #dd(Storage::disk('s3'));
        #$path = 'uploads/images/3b7ac514d63dcf489b60ec8a2cb05e25.jpg';
        #dd(Storage::disk('s3')->lastModified($path));
		$this->data['content'] = 'layouts.admin.pages.image.index';    	
    	return $this->output('admin',false);        
    }

    public function postRemove(Request $request){
    	if( ! $request->ajax()){
    		die();
    	}
    	$status = 200;
    	$message = NULL;
    	$name = explode("/",$request['id']);
    	try{
    		Storage::delete('public/uploads/images/'.end($name));
    		Storage::delete('public/uploads/images/thumbs/'.end($name));
    	}catch(Exception $e){
    		$status = 500;
    		$message = $e->getMessage();
    	}
    	return Response::json(array('status'=>$status,'message'=>$message));
    }

    public function postIndex(Request $request){
    	if( ! $request->ajax()){
    		die();
    	}
    	$files = $request->file('file');
    	$message = $return = NULL; $status = 500;    	    	
	    try{
	    	$this->validate($request, [
		        'file.*' => 'mimes:'.$this->config['accept'],
		    ]);
	    	if(count((array)$files) > 0){
	    		$status = 200;
	    		foreach($files as $key => $file){
	    			$tmp = $this->replace_encode_filename($file->getClientOriginalName());
			    	$filename = $this->des."/".$tmp;
			        $upload_success = $file->move($this->des , $filename);
			        $return[$key] = $this->des."/thumbs/".$tmp;
			        Storage::copy('public/'.$filename , 'public/'.$return[$key]);
			        $this->handleImage( [ 'default' => $filename , 'thumb' => $return[$key] ] );
		    	}
		    	foreach($return as &$val){
		    		$val = url($val);
		    	}
	    	}
	    }catch(Exception $e){
	    	$message = $e->getMessage();
	    }
    	return Response::json(['data'=>$return ,'status'=>$status,'message'=>$message]);
    }

    protected function fetchImage(){
    	$files = Storage::allFiles('public/'.$this->des.'/thumbs');
        if(count($files) > 0)
        {
            foreach($files as $key => $val){
                $val = preg_replace("/public\//","",$val);
                $tmp = preg_replace("/thumbs\//","",$val);
                $this->data['files'][url($val)] = url($tmp);
            }
        }
        return ;
    }

    protected function handleImage(array $files){
    	# Thumbs
    	$sizes = ['default' => $this->config['max']['width'] ,'thumb' => $this->config['thumb']];
    	$test = (int)round(Storage::size('public/'.$files['default'])/1024);        
    	foreach($sizes as $key => $val){
    		if($key == 'default' && $test < $val){
    			continue;
    		}
    		$image = Image::make($files[$key]);
			$image->resize($val,null,function($constraint){
				$constraint->aspectRatio();
			});
			$image->save($files[$key]);			
    	}
    	return $files['thumb'];
    }

    public function replace_encode_filename($url){
		$_path = explode("/",$url);
        $temp = rawurlencode(urldecode($_path[count($_path) - 1]));    
        $_path[count($_path) - 1] = md5($temp .time()).".".pathinfo($url, PATHINFO_EXTENSION);
        $url = implode("/",$_path);
        return $url;
	}
}
