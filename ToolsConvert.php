<?php
/**
 * @git git@github.com:BorderCloud/SPARQL.git
 * @author Karima Rafes <karima.rafes@bordercloud.com>
 * @license http://creativecommons.org/licenses/by-sa/4.0/
*/
class ToolsConvert {

      public static function isTrueFloat($val) 
      { 
	  $pattern = '/^[+-]?(\d*\.\d+([eE]?[+-]?\d+)?|\d+[eE][+-]?\d+)$/'; 
	  //echo 'Val:'. $val."\n";
	  //var_dump(is_float($val) || preg_match($pattern, trim($val)));
	  return (is_float($val) || preg_match($pattern, trim($val))); 
      } 
}