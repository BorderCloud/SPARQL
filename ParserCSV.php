 <?php
/**
 * @git git@github.com:BorderCloud/SPARQL.git
 * @author Karima Rafes <karima.rafes@bordercloud.com>
 * @license http://creativecommons.org/licenses/by-sa/4.0/
*/

class ParserCSV {

	function csv_to_array($csv, $delimiter = '\t', $enclosure = '\'', $escape = '\\', $terminator = "\n") { 
	
		$r = array();
		//$string = utf8_encode($csv);
		//echo mb_detect_encoding($names);
		//echo $csv;
		$rows = explode($terminator,trim($csv)); 
		$names = array_shift($rows); 
		$names = str_getcsv($names,$delimiter,$enclosure,$escape) ; 
		$nc = count($names); 
		foreach ($rows as $row) { 
			if (trim($row)) { 
				$values =str_getcsv($row,$delimiter,$enclosure,$escape) ;  
				
				if (!$values) $values = array_fill(0,$nc,null); 
				
				$tabTemp = array(); 				
				//array_combine($names,$values);
				foreach($names as $key=>$nameCol){ 
					if(isset($values[$key])){
					
					    $value = $values[$key]; 
					    if (ToolsConvert::isTrueFloat($value)) {
						$value = floatval($value);
					    } elseif (is_int($value)) {
						$value = intval($value);
					    }
					    $tabTemp[$nameCol] = $value; 
					}else{						
						$tabTemp[$nameCol] = NULL; 
					}
				}  
				$r[] = $tabTemp;
				
			} 
		} 
		return $r; 
   } 
   
   	function mySortAllColumn($row1, $row2){
		$result = 0;
		$countTab = 0;
		if( count($row1) > count($row2)){
			$countTab = count($row1);
		}else{
			$countTab = count($row2);
		}
		
		for($i=0; $i < $countTab; $i++){
			if((!isset($row1[$i])) || (!isset($row2[$i]))){
				if(isset($row1[$i]) && isset($row2[$i])){//impossible in theory
					$result =  0;
					break;
				}elseif(!isset($row1[$i])){
					$result =  -1;
					break;
				}elseif(!isset($row2[$i])){
					$result =  1;
					break;
				}
			}
			else if($row1[$i] < $row2[$i]){
				$result =  1;
				break;
			}
			else if($row1[$i] < $row2[$i]){
				$result =  -1;
				break;
			}
		}
		return $result;
	}
	
	function sortTable($array){
		$result = $array;
		usort($result, 'ParserCSV::mySortAllColumn');
		return $result;
	}
	
	 
} 
