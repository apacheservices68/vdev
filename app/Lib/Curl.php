<?php namespace App\Lib;
use Illuminate\Session ;
class Curl{
    private $agent = 'Mozilla/5.0 (Windows NT 6.1; WOW64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/38.0.2125.111 Safari/537.36';
    public function get($url, $referer, $timeout, $header){ // header->0
        if(!isset($timeout))
            $timeout=30;
        $curl = curl_init();
        if(strstr($referer,"://")){
            curl_setopt ($curl, CURLOPT_REFERER, $referer);
        }
        curl_setopt ($curl, CURLOPT_URL, $url);
        curl_setopt ($curl, CURLOPT_TIMEOUT, $timeout);
        curl_setopt ($curl, CURLOPT_USERAGENT, $this->agent);
        curl_setopt ($curl, CURLOPT_HEADER, (int)$header);
        curl_setopt ($curl, CURLOPT_RETURNTRANSFER, 1);
        curl_setopt ($curl, CURLOPT_SSL_VERIFYPEER, 0);
        $html = curl_exec ($curl);
        curl_close ($curl);
        return $html;
    }
    
    public function post($url,$pvars,$referer,$timeout){
        if(!isset($timeout))
            $timeout=30;
        $curl = curl_init();
        $post = http_build_query($pvars);
        if(isset($referer)){
            curl_setopt ($curl, CURLOPT_REFERER, $referer);
        }
        curl_setopt ($curl, CURLOPT_URL, $url);
        curl_setopt ($curl, CURLOPT_TIMEOUT, $timeout);
        curl_setopt ($curl, CURLOPT_USERAGENT, $this->agent);
        curl_setopt ($curl, CURLOPT_HEADER, 0);
        curl_setopt ($curl, CURLOPT_RETURNTRANSFER, 1);
        curl_setopt ($curl, CURLOPT_SSL_VERIFYPEER, 0);
        curl_setopt ($curl, CURLOPT_POST, 1);
        curl_setopt ($curl, CURLOPT_POSTFIELDS, $post);
        curl_setopt ($curl, CURLOPT_HTTPHEADER,
            array("Content-type: application/x-www-form-urlencoded"));
        $html = curl_exec ($curl);
        curl_close ($curl);
        return $html;
    }

    public function curl_get_file_size( $url ) {
      // Assume failure.
      $result = -1;

      $curl = curl_init( $url );

      // Issue a HEAD request and follow any redirects.
      curl_setopt( $curl, CURLOPT_NOBODY, true );
      curl_setopt( $curl, CURLOPT_HEADER, true );
      curl_setopt( $curl, CURLOPT_RETURNTRANSFER, true );
      curl_setopt( $curl, CURLOPT_FOLLOWLOCATION, true );
      curl_setopt( $curl, CURLOPT_USERAGENT, 'Mozilla/5.0 (Windows NT 6.3; rv:36.0) Gecko/20100101 Firefox/36.0' );

      $data = curl_exec( $curl );
      curl_close( $curl );

      if( $data ) {
        $content_length = "unknown";
        $status = "unknown";

        if( preg_match( "/^HTTP\/1\.[01] (\d\d\d)/", $data, $matches ) ) {
          $status = (int)$matches[1];
        }

        if( preg_match( "/Content-Length: (\d+)/", $data, $matches ) ) {
          $content_length = (int)$matches[1];
        }

        // http://en.wikipedia.org/wiki/List_of_HTTP_status_codes
        if( $status == 200 || ($status > 300 && $status <= 308) ) {
          $result = $content_length;
        }
      }

      return $result/1024;
    }
    /*
    public function downloads($url) {
        set_time_limit(0);
        $ch = curl_init();
        curl_setopt($ch, CURLOPT_URL, $url);
        curl_setopt($ch, CURLOPT_FOLLOWLOCATION, true);
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
        curl_setopt($ch, CURLOPT_SSL_VERIFYHOST, 0);
        curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, 0);
        $r = curl_exec($ch);
        curl_close($ch);
        header('Expires: 0'); // no cache
        header('Cache-Control: must-revalidate, post-check=0, pre-check=0');
        header('Last-Modified: ' . gmdate('D, d M Y H:i:s', time()) . ' GMT');
        header('Cache-Control: private', false);
        header('Content-Type: application/force-download');
        header('Content-Disposition: attachment; filename="' . basename($url) . '"');
        header('Content-Transfer-Encoding: binary');
        header('Content-Length: ' . strlen($r)); // provide file size
        header('Connection: close');
        return $r;
    }
    */

    private function external_exists($url)
    {
        if(!$this->detect_utf8($url)){
            $_path = explode("/",$url);                
            $temp = rawurlencode(urldecode($_path[count($_path) - 1]));    
            $_path[count($_path) - 1] = $temp;
            $url = implode("/",$_path);
        }
        $ch = curl_init($url);
        curl_setopt($ch, CURLOPT_NOBODY, true);
        curl_exec($ch);
        $retCode = curl_getinfo($ch, CURLINFO_HTTP_CODE);
        curl_close($ch);
        return $retCode;
    }

    private function detect_utf8($input_line){
        return !(bool)preg_match("/[âẫẩậấầằắẵăặẳôồốộỗổơờớợỡởêệềếễểẽẻéèẹưừứựữửụũủúùĩỉìíịỷỹỵýỳÂẪẨẬẤẦẰẮẶẴẲĂÔỐỒỖỔỘỞỠỚỜỢƠÊỆẾỀỂỄÉÈẸẼẺƯỪỨỰỬỮỤÚÙŨỦÍÌỊỈĨÝỲỴỸỶ]+/", $input_line, $output_array);
    } 

    public function handle_list_image(){

    }

    public function multi_download($urls = array(), $paths = array() , $type = 'default'){
        // Path to save files in        
        $multi_handle   = curl_multi_init();  
        $file_pointers  = $curl_handles = array();
        $flag           = true;            
        // Add curl multi handles, one per file we don't already have 
        foreach ($urls as $key => $url) {  
            //$file = $save_path.'/'.basename($url);  
            if($this->external_exists( $url ) != 200){
                return false;
            }
            $file = $paths[$key];
            if($type == 'default'){
                $_path = explode("/",$url);                
                $temp = rawurlencode(urldecode($_path[count($_path) - 1]));    
                $_path[count($_path) - 1] = $temp;
                $url = implode("/",$_path);               
            }
            // if( !is_file( $file ) ) {
                $curl_handles[$key]     =   curl_init($url);  
                $file_pointers[$key]    =   fopen ($file, "w");  
                curl_setopt ($curl_handles[$key] , CURLOPT_FILE,           $file_pointers[$key]);  
                curl_setopt ($curl_handles[$key] , CURLOPT_HEADER ,        0);
                curl_setopt ($curl_handles[$key] , CURLOPT_CONNECTTIMEOUT, 60);  
                curl_multi_add_handle ($multi_handle,$curl_handles[$key]);  
            // }
        }
        // Download the files  
        do {  
          curl_multi_exec($multi_handle,$running);  
        }  
        while($running > 0);
        // Free up objects
        foreach ($urls as $key => $url) {  
          curl_multi_remove_handle($multi_handle , $curl_handles[$key]);  
          curl_close($curl_handles[$key]);  
          fclose ($file_pointers[$key]);
        }  
        curl_multi_close($multi_handle);  
        foreach($paths as $k => $v){
            if(!file_exists($v)){
                $flag = false;
                break;
            }
        }
        return $flag;
    }

    public function download($url, $path)
	{
	  # open file to write
	  $fp = fopen ($path, 'w+');
	  # start curl
	  $ch = curl_init();            
	  curl_setopt( $ch, CURLOPT_URL, $url );
	  # set return transfer to false
	  curl_setopt($ch, CURLOPT_FOLLOWLOCATION, true);
	  curl_setopt( $ch, CURLOPT_RETURNTRANSFER, 1 );
	  curl_setopt( $ch, CURLOPT_BINARYTRANSFER, true );
	  curl_setopt( $ch, CURLOPT_SSL_VERIFYPEER, 0 );  
	  curl_setopt($ch, CURLOPT_HEADER, 0);    
	  # increase timeout to download big file
	  curl_setopt( $ch, CURLOPT_CONNECTTIMEOUT, 10 );
	  # write data to local file
	  curl_setopt( $ch, CURLOPT_FILE, $fp );
	  # execute curl
	  curl_exec( $ch );
      
	  # close curl
	  curl_close( $ch );
	  # close local file
	  fclose( $fp );

	  if (filesize($path) > 0) return true;
	}
}

?>