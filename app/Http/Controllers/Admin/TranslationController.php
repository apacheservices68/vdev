<?php

namespace App\Http\Controllers\Admin;

use Storage;
use App\Http\Requests;
use Yangqi\Htmldom\Htmldom;
use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\Response;
use App\Http\Controllers\Admin\AdminController;

class TranslationController extends AdminController
{ 
    //
    public function __construct(){
    	$this->module_name = "translate";
    	parent::__construct();        
    }

    public function getIndex(){
        $this->fetchIndexData();
		$this->data['content'] = 'layouts.admin.pages.translation.index';    	
    	return $this->output('admin',false);        
    }

    public function postIndex(Request $input){
        #dd($input->all());
        if($input->ajax()){            
            $this->fetchIndexData($input['files']);            
            $this->data['pol'] = $input['files'];
            $data['data'] = mb_convert_encoding(view("layouts.admin.pages.translation.index",$this->data)->render(),'UTF-8','UTF-8');    
            return response()->json( $data );
        }
        $folders = Storage::allDirectories('resources/lang');
        $this->addFile($input,$folders);
        foreach($folders as $key => $val){
            $tmp = explode("/",$val);
            $folders[$key] = $tmp[count($tmp)-1];
            if(isset($input['keys'][0]) && trim($input['keys'][0]) != ""){
                $length = count($input['keys']);
                foreach($input['keys'] as $ke => $va){
                    $temp[$folders[$key]][$va] = $input['values_'.$folders[$key]][$ke];
                }
            }
        }        
        $content = "<?php \n\treturn [\n";
        $contents = "";        
        foreach($folders as $key => $val){
            $contents .= $content;
            if(!is_array($input[$val]) && count($input[$val]) <= 0){
                $input[$val] = $temp[$val];
            }else{
                $input[$val] += isset($temp) ? $temp[$val] : array();    
            }
            foreach($input[$val] as $ke => $va){
                if(isset($input['checkbox']) && in_array($ke , $input['checkbox'])){
                    continue;
                }
                $contents .= "\t\t'".$ke."'\t=>\t'".$input[$val][$ke]."',\n";
            }
            if($input['files'] === 'validation'){
                $contents .= "'attributes' => [],\n";
            }
            $contents .= "\t];\n?>";
            Storage::put('resources/lang/'.$val."/".$input['files'].".php", $contents);
            $contents = "";
        }
        flash()->success('Saving success .');
        
		return redirect()->back()->with('flFiles',$input['files']);
    }

    protected function addFile($input = NULL , $lang){
        if(isset($input['addFile']) && trim($input['addFile']) != ""){
            $contents = "<?php \n\treturn [\n";
            $contents .= "\t];\n?>";
            foreach($lang as $key => $val){
                $tmp = explode("/",$val);                
                Storage::put('resources/lang/'.$tmp[count($tmp)-1]."/".$input['addFile'].".php", $contents);
            }
        }
        return ;
    }

    protected function fetchIndexData($files  = 'auth'){        
        $functions = array('allFiles' , 'allDirectories');
        $lists = array('files','folders');
        for($i = 0 ; $i < count($lists) ; $i++){
            foreach(Storage::{$functions[$i]}('resources/lang') as $key => $val){                
                $tmp = explode("/",$val);                
                $this->data[$lists[$i]][] = explode(".",$tmp[count($tmp) - 1])[0];
            }
            $this->data[$lists[$i]] = array_unique($this->data[$lists[$i]]);
        }
        if(session()->has('flFiles')){
            $firsts = session()->get('flFiles');      
            $this->data['flFiles'] = $firsts;      
        }else{
            $firsts = $files;    
        }
        if(is_array(trans($firsts)) && count(trans($firsts)) > 0){            
            foreach(trans($firsts) as $key => $val){
                foreach($this->data['folders'] as $ke => $va){
                    if(is_array($val)){
                        foreach($val as $k => $v){        
                            if(is_array($v)){
                                foreach($v as $_k => $_v){
                                    $this->data['firsts'][$key.".".$k.".".$_k][$va] = trans($firsts.".".$key.".".$k.".".$_k,[],'messages',$va);
                                }
                            }else{
                                $this->data['firsts'][$key.".".$k][$va] = trans($firsts.".".$key.".".$k,[],'messages',$va);    
                            }
                        }
                    }else{
                        $this->data['firsts'][$key][$va] = trans($firsts.".".$key,[],'messages',$va);
                    }
                }
            }
        }
        #dd($this->data['firsts']);
        return ;
    }
}
