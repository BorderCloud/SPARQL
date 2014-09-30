 <?php
/**
 * @git git@github.com:BorderCloud/SPARQL.git
 * @author Karima Rafes <karima.rafes@bordercloud.com>
 * @license http://creativecommons.org/licenses/by-sa/4.0/
*/

class ParserTurtle {

	static function turtle_to_array($turtle,$baseGraph,$idMD5 = false){
		$tabResult = array();
		$tabResult["prefix"] = array();
		$tabResult["prefix"]["base"] = $baseGraph;
		$tabResult["triples"] = array();
		
		preg_match_all("/((?:(?:\"|').*(?:\"|')(?:\^\^[^\s]*)?|a|<[^\s]*>|[^\s]*:[^\s]+|;|,|@prefix\s*[^\s]*\s*:\s*<[^\s<>]+>|\s*\r?\n?)+\s\.)/i",$turtle, $matches, PREG_SET_ORDER);
		foreach ($matches as $val) {
		//// http://answers.semanticweb.com/questions/2025/what-is-the-meaning-of-base-and-prefix-in-turtle-documents
		   
			if(preg_match("/^\s*@base\s*<([^\s<>]+)>\s*\.\s*$/i", $val[0], $valMatches)){
				$tabResult["prefix"]["base"] = $valMatches[1];
			}elseif(preg_match("/^\s*@prefix\s*([^\s]+)?\s*:\s*<([^\s<>]+)>\s*\.\s*$/i", $val[0], $valMatches)){
				$namePrefix = $valMatches[1] == "" ? "empty" : $valMatches[1];
				$tabResult["prefix"][$namePrefix] = $valMatches[2];
			}elseif(preg_match("/^\s*(<[^\s]*>|[^\s]*:[^\s]+)\s*(.*)\s*\.$/is", $val[0], $valMatches)){
                                $object = ParserTurtle::relativeToExplicitURI($valMatches[1],$tabResult["prefix"]) ;

                                preg_match_all("/\s*(a|<[^\s]*>|[^\s]*:[^\s]+)\s*((?:(?:<[^\s]*>|[^\s]*:[^\s]+|(?:\"(?:\\\\\"|[^\"])*\"|'(?:\\\\'|[^'])*')(?:\^\^[^\s]*)?|\s*),?)+\s*);?/is",$valMatches[2], $propertyMatches, PREG_SET_ORDER);
                                foreach ($propertyMatches as $propertyVal) {
                                        $property ="";
                                        if($propertyVal[1] == "a"){
                                                $property = "<http://www.w3.org/1999/02/22-rdf-syntax-ns#type>"  ;
                                        }else{
                                                $property = ParserTurtle::relativeToExplicitURI($propertyVal[1],$tabResult["prefix"]) ;
                                        }
                                        preg_match_all("/(<[^\s]*>|[^\s]*:[^\s]+|(?:\"(?:\\\\\"|[^\"])*\"|'(?:\\\\'|[^'])*')(?:\^\^[^\s]*)?)\s*,?/is",$propertyVal[2], $valueMatches, PREG_SET_ORDER);

                                        foreach ($valueMatches as $valueVal) {		
                                                $value = ParserTurtle::relativeToExplicitURI($valueVal[1],$tabResult["prefix"]) ;
                                                //echo "s=>".$object." p=>".$property." o=>".$value."\n";
                                                if($idMD5)
                                                    $tabResult["triples"][md5($object.$property.$value)] = array("s"=>$object,"p"=>$property,"o"=>$value);
                                                else
                                                    $tabResult["triples"][] = array("s"=>$object,"p"=>$property,"o"=>$value);
                                        }
                                }
                        }
		}
		return $tabResult;
	}
	
	static function relativeToExplicitURI($uri,$prefix){
		$result = $uri;
		if(preg_match("/^(\"(?:\\\"|[^\"])*\"|'(?:\\'|[^'])*')(?:\^\^([^\s]*))?$/i",$uri, $matches)){//<>
			if(isset($matches[2])){
			    $type = ParserTurtle::relativeToExplicitURI($matches[2],$prefix);
			    $value = $matches[1];
				if($type == "http://www.w3.org/2001/XMLSchema#double" || 
				   $type == "http://www.w3.org/2001/XMLSchema#decimal" ){
					$value = floatval($value);
				}elseif($type == "http://www.w3.org/2001/XMLSchema#integer"){
					$value = intval($value);
				//}elseif($type == "http://www.w3.org/2001/XMLSchema#boolean"){
				//	$value = $value === "true" ? true : false;
				}			
				$result = "\"".$value."\"^^".$type;
			}else{
				$value = $matches[1];
				if (ToolsConvert::isTrueFloat($value)) {
				    $value = floatval($value);
				} elseif (is_int($value)) {
				    $value = intval($value);
				}
				$result = $value; 
			}

		}elseif(preg_match("/^<>$/i",$uri, $matches)){//<>
			$result = "<".$prefix["base"].">";
		}elseif(preg_match("/^<#([^:]+)>$/i",$uri, $matches)){//<#truc>
			$result = "<".$prefix["base"].$matches[1].">";
		}elseif(preg_match("/^<([^<>]+)>$/i",$uri, $matches)){//<truc>
			$len = strlen( $prefix["base"]);
			$prefixbase = substr( $prefix["base"], 0, strrpos ($prefix["base"] , "/"));
			$result = "<".$prefixbase.$matches[1].">";
		}elseif(preg_match("/^:([^\s]*)$/i",$uri, $matches)){//:truc
			$result = "<".$prefix["empty"].$matches[1].">";
		}elseif(preg_match("/^([^:_]*):([^><]*)$/i",$uri, $matches)){//x:truc
			$result = "<".$prefix[$matches[1]].$matches[2].">";
		}else{	
		    $value = $uri;
		    if (ToolsConvert::isTrueFloat($value)) {
			$value = floatval($value);
		    } elseif (is_int($value)) {
			$value = intval($value);
		    }
		    $result = $value; 
		}
		return $result;
	}
	
	static function mySortTriples($itm1, $itm2){
		if($itm1["s"] > $itm2["s"]){
			return 1;
		}
		else if($itm1["s"] < $itm2["s"]){
			return -1;
		}
		else{
			if($itm1["p"] > $itm2["p"]){
				return 1;
			}
			else if($itm1["p"] < $itm2["p"]){
				return -1;
			}
			else{
				if($itm1["o"] > $itm2["o"]){
					return 1;
				}
				else if($itm1["o"] < $itm2["o"]){
					return -1;
				}
				else{
					return 0;
				}
			}
		}
	}
	
    static function sortTriples($arrayTurtle){
            $result = $arrayTurtle;		
            array_multisort($result["prefix"],SORT_ASC,SORT_STRING);

            usort($result["triples"], 'ParserTurtle::mySortTriples');	

            return $result;		
    }	
        
    static function getKey($arrayTurtle, $s,$p){
        $result = null;
        if(!EMPTY($arrayTurtle)){
            foreach ($arrayTurtle["triples"] as $key=>$triple) {
               if("<".$s.">" == $triple["s"] && "<".$p.">" == $triple["p"] ){
                      $result = $key;
                      break;
                }
            }
        }
        return $key;
    }
    static function getTriple($arrayTurtle, $s,$p){
        $result = null;
        if(!EMPTY($arrayTurtle)){
            foreach ($arrayTurtle["triples"] as $key=>$triple) {
               if("<".$s.">" == $triple["s"] && "<".$p.">" == $triple["p"] ){
                      $result = $triple;
                      preg_match( '@^(?:<tel\:([^<>]+)>|<mailto\:([^<>]+)>|<([^<>]+)>|([^\"<>]+)|\"(.*)\"[^\"]*)$@i',$triple["o"], $matches);
//\<mailto\:([^\<\>])+\>|\<([^\<\>])+\>|                      
//print_r($triple["o"]);
                      //print_r($matches);
                      foreach ($matches as $key=>$match) {
                        if($key != 0 && ! EMPTY($match)){
                          $result["value"] = $match;
                          break;
                        }
                      }
                      break;
                }
            }
        }
        return $result;
    }
    
   public static function array_diff_assoc_unordered( $rs1,  $rs2) {
      $difference=array();
      $rs1Triples = $rs1["triples"];
      $rs2Triples = $rs2["triples"];

      //B/ Check the result set have the same number of rows.
      if(count($rs1Triples) != count($rs2Triples)) {
	  $difference[1]="Nb rows :".count($rs1Triples);
	  $difference[2]="Nb rows :".count($rs2Triples);
	  return $difference; //return false ;
      }

      //C/ Pick a row from the test results, scan the expected results
      //   to find a row with same variable/value bindings, and remove
      //   from the expected results. If all test rows, match then
      //   (because of B) the result sets have the same rows.
      //   
      //return equivalent(convert(rs1), convert(rs2), new BNodeIso(NodeUtils.sameValue)) ;
      $clone1 = $rs1Triples;
      $clone2 = $rs2Triples;
//       echo "AVANT";
// 	  print_r($clone1);
// 	  print_r($clone2);
      foreach ($rs1Triples as $key1=>&$value1) {
	  $tmpclone2 = $clone2;
	    foreach ($tmpclone2 as $key2=>&$value2) {
		
//       echo "-------------";
// 	    print_r($value1);
// 	    print_r($value2);
	      if(count(array_diff_assoc($value1,$value2)) == 0 && 
		  count(array_diff_assoc($value2,$value1)) == 0 ){
		    unset($clone1[$key1]);
		    unset($clone2[$key2]);
		    break;
	      }
	    }
//       echo "-------------APRES";
// 	    print_r($clone1);
// 	    print_r($clone2);
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
