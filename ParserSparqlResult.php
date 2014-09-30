<?php
/**
 * @git git@github.com:BorderCloud/SPARQL.git
 * @author Karima Rafes <karima.rafes@bordercloud.com>
 * @license http://creativecommons.org/licenses/by-sa/4.0/
*/

class ParserSparqlResult extends Base {
   private $_result;
   private $_rowCurrent;
   private $_cellCurrent;
   private $_value;
   
   function __construct() {
   		parent::__construct();
   		$this->_result = array();
   }
   
   function getParser(){
	   	$objectParser = xml_parser_create();
	   	xml_set_object ($objectParser, $this);
	   	
	   	//Don't alter the case of the data
	   	xml_parser_set_option($objectParser, XML_OPTION_CASE_FOLDING, false);
	   	
	   	xml_set_element_handler($objectParser,"startElement","endElement");
	   	xml_set_character_data_handler($objectParser, "contentHandler");
	   	return $objectParser;
   }
   
   function getResult(){
   		return $this->_result;
   }

   //callback for the start of each element
   function startElement($parser_object, $elementname, $attribute) {
   	if($elementname == "sparql"){   		
   		$this->_result['result'] =  array();
   	}else if($elementname == "head"){
   		$this->_result['result']['variables'] =  array();
   	}else if($elementname == "variable"){
   		$this->_result['result']['variables'][] = $attribute['name'];
   	}else if($elementname == "results"){
   		$this->_rowCurrent = -1;
   		$this->_result['result']['rows'] =  array();
   	}else if($elementname == "result"){
   		$this->_rowCurrent++;
   		$this->_result['result']['rows'][] =  array();   		
   	}else if($elementname == "binding"){
   		$this->_value = "";
   		$this->_cellCurrent = $attribute['name'];
   	}else if($this->_cellCurrent != null){
   		$this->_result['result']['rows'][$this->_rowCurrent][$this->_cellCurrent." type"] = $elementname;
   		
   		if(isset($attribute['xml:lang']))
   			$this->_result['result']['rows'][$this->_rowCurrent][$this->_cellCurrent." lang"] = $attribute['xml:lang'];
   	
   		if(isset($attribute['datatype']))
   			$this->_result['result']['rows'][$this->_rowCurrent][$this->_cellCurrent." datatype"] = $attribute['datatype'];
   	}
   }

   //callback for the end of each element
   function endElement($parser_object, $elementname) {
    	if($elementname == "binding"){
			if(!isset($this->_result['result']['rows'][$this->_rowCurrent][$this->_cellCurrent." type"]))
				$this->_result['result']['rows'][$this->_rowCurrent][$this->_cellCurrent." type"]=NULL;
				
			if($this->_result['result']['rows'][$this->_rowCurrent][$this->_cellCurrent." type"] == "uri"){
				$this->_result['result']['rows'][$this->_rowCurrent][$this->_cellCurrent] = trim($this->_value);
                        }elseif($this->_result['result']['rows'][$this->_rowCurrent][$this->_cellCurrent." type"] == "bnode"){
                                $this->_result['result']['rows'][$this->_rowCurrent][$this->_cellCurrent] = trim($this->_value);
			}elseif($this->_result['result']['rows'][$this->_rowCurrent][$this->_cellCurrent." type"] == "literal"){
				$value = trim($this->_value);
				if(array_key_exists($this->_cellCurrent." datatype",$this->_result['result']['rows'][$this->_rowCurrent])){
					if($this->_result['result']['rows'][$this->_rowCurrent][$this->_cellCurrent." datatype"] == "http://www.w3.org/2001/XMLSchema#double" ||
						$this->_result['result']['rows'][$this->_rowCurrent][$this->_cellCurrent." datatype"] == "http://www.w3.org/2001/XMLSchema#decimal" 
						){
						$this->_result['result']['rows'][$this->_rowCurrent][$this->_cellCurrent] = floatval($value);
					}elseif($this->_result['result']['rows'][$this->_rowCurrent][$this->_cellCurrent." datatype"] == "http://www.w3.org/2001/XMLSchema#integer"
						){
						$this->_result['result']['rows'][$this->_rowCurrent][$this->_cellCurrent] = intval($value);
					}elseif($this->_result['result']['rows'][$this->_rowCurrent][$this->_cellCurrent." datatype"] == "http://www.w3.org/2001/XMLSchema#boolean"
						){
						$this->_result['result']['rows'][$this->_rowCurrent][$this->_cellCurrent] = $value === "true" ? true : false;
					}else{						
						$this->_result['result']['rows'][$this->_rowCurrent][$this->_cellCurrent] = $value;
					}
				}else{
					$this->_result['result']['rows'][$this->_rowCurrent][$this->_cellCurrent] = $value;
				}
			}else{
				$this->_result['result']['rows'][$this->_rowCurrent][$this->_cellCurrent] = $this->_value;
			}
   			$this->_cellCurrent = null;
   			$this->_value = "";
   		}
    }

    //callback for the content within an element
    function contentHandler($parser_object,$data)
    {
		  if($this->_cellCurrent != null){
		    //	echo "DATA". $data." - ".$this->_cellCurrent."\n";
			  $this->_value .= $data;
		  }
    }
   
    function sortResult($array){
	    $result = $array;				
	    if(isset($result['result']['rows']))
		    usort($result['result']['rows'], 'ParserSparqlResult::mySortResult');
	    return $result;		
    }
    
    function mySortResult($row1, $row2){	
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
    
    public static function array_diff_assoc_unordered( $rs1,  $rs2) {
      $difference=array();
      //A/ Check the variables lists in the header are the same.
      if(! isset($rs1['result']['variables']) && ! isset($rs2['result']['variables'])){
	  return $difference; //return true ;
      }elseif (! isset($rs1['result']['variables']) || ! isset($rs2['result']['variables']) ) {
	  $difference[1]=$rs1['result']['variables'];
	  $difference[2]=$rs2['result']['variables'];
	  return $difference; //return false ;
      }
      
      $difference=array_diff($rs1,$rs2);
      if (count($difference) != 0) {
	  return $difference; //return false ;
      }
      
      //B/ Check the result set have the same number of rows.
      if(count($rs1['result']['rows']) != count($rs2['result']['rows'])) {
	  $difference[1]="Nb rows :".count($rs1['result']['rows']);
	  $difference[2]="Nb rows :".count($rs2['result']['rows']);
	  return $difference; //return false ;
      }

      //C/ Pick a row from the test results, scan the expected results
      //   to find a row with same variable/value bindings, and remove
      //   from the expected results. If all test rows, match then
      //   (because of B) the result sets have the same rows.
      //   
      //return equivalent(convert(rs1), convert(rs2), new BNodeIso(NodeUtils.sameValue)) ;
      $clone1 = $rs1['result']['rows'];
      $clone2 = $rs2['result']['rows'];
      
     // echo "AVANT";
	//  print_r($clone1);
	//  print_r($clone2);
      foreach ($rs1['result']['rows'] as $key1=>&$value1) {
	  $tmpclone2 = $clone2;
	    foreach ($tmpclone2 as $key2=>&$value2) {
		
              //echo "-------------";
	      //print_r($value1);
	      //print_r($value2);
	      if(count(array_diff_assoc($value1,$value2)) == 0 && 
		  count(array_diff_assoc($value2,$value1)) == 0 ){
		    unset($clone1[$key1]);
		    unset($clone2[$key2]);
		    break;
	      }
	    }
            //echo "-------------APRES";
	    //print_r($clone1);
	    //print_r($clone2);
      }

      if(count($clone1) != 0 || 
	  count($clone2) != 0 ){
	  $difference[1]=$clone1;
	  $difference[2]=$clone2;
	  return $difference; //return false ;
      }

      return $difference;
  }
}