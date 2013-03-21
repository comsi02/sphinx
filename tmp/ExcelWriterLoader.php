<?php
if (!defined('BASEPATH')) exit('No direct script access allowed');

class ExcelWriterLoader {
	function load($class, $options=null) {

       if(!file_exists($file_path = BASEPATH.'application/phpexcel/Classes/'.$class.'.php')) {  
              
            $path = explode('_',$class);  
            foreach($path as $p) {  
                $ps .= $p.'/';  
                if(file_exists($file_path = BASEPATH.'application/phpexcel/Classes/'.$ps.$class.'.php')) break;  
            }  
  
        } 
 

		require_once($class.'.php');
		$classname = $class;

		if(is_null($options)) {
			return new $classname();
		} else {
			return new $classname($options);
		}
	}
}
?>
