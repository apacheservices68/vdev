<?php 
class Arr_obj
{
    public function arrayToObject($d) {
		if (is_array($d)) {
			/*
			* Return array converted to object
			* Using __FUNCTION__ (Magic constant)
			* for recursive call
			*/
			return (object) array_map(array($this,__FUNCTION__), $d);
		}
		else {
			// Return object
			return $d;
		}
	}

	public function fake_objects($obj = array()){
		$new_array = array();
		if(!is_array($obj) || is_null($obj)){
			return ;
		}
		foreach($obj as $key => $val){			
			if(is_numeric($key)){
				$new_array[$key] = $this->arrayToObject($val);
			}		
		}
		return $new_array;
	}
    
    public function objectToArray($d) {
		if (is_object($d)) {
			// Gets the properties of the given object
			// with get_object_vars function
			$d = get_object_vars($d);
		}
 
		if (is_array($d)) {
			/*
			* Return array converted to object
			* Using __FUNCTION__ (Magic constant)
			* for recursive call
			*/
			return array_map(array($this,__FUNCTION__), $d);
		}
		else {
			// Return array
			return $d;
		}
	}
}