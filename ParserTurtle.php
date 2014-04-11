 <?php
/**
 * @git git@github.com:BorderCloud/SPARQL.git
 * @author Karima Rafes <karima.rafes@bordercloud.com>
 * @license http://creativecommons.org/licenses/by-sa/4.0/
*/

class ParserTurtle {

	function turtle_to_array($turtle,$baseGraph){
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
									$tabResult["triples"][] = array("s"=>$object,"p"=>$property,"o"=>$value);
								}
							}
						}
		}
		return $tabResult;
	}
	
	function relativeToExplicitURI($uri,$prefix){
		$result = $uri;
		
		if(preg_match("/^(\"(?:\\\"|[^\"])*\"|'(?:\\'|[^'])*')(?:\^\^([^\s]*))?$/i",$uri, $matches)){//<>
			if(isset($matches[2])){
				$result = $matches[1]."^^".ParserTurtle::relativeToExplicitURI($matches[2],$prefix);
			}else{
				$result = $matches[1];
			}

		}elseif(preg_match("/^<>$/i",$uri, $matches)){//<>
			$result = "<".$prefix["base"].">";
		}elseif(preg_match("/^<#([^:]+)>$/i",$uri, $matches)){//<#truc>
			$result = "<".$prefix["base"].$matches[1].">";
		}elseif(preg_match("/^<#([^:]+)>$/i",$uri, $matches)){//<truc>
			$len = strlen( $prefix["base"]);
			$prefixbase = substr( $prefix["base"], 0, strrpos ($prefix["base"] , "/"));
			$result = "<".$prefixbase.$matches[1].">";
		}elseif(preg_match("/^:([^\s]*)$/i",$uri, $matches)){//:truc
			$result = "<".$prefix["empty"].$matches[1].">";
		}elseif(preg_match("/^([^:_]*):([^><]*)$/i",$uri, $matches)){//x:truc
			$result = "<".$prefix[$matches[1]].$matches[2].">";
		}
		return $result;
	}
	
	function mySortTriples($itm1, $itm2){
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
	
	function sortTriples($arrayTurtle){
		$result = $arrayTurtle;		
		array_multisort($result["prefix"],SORT_ASC,SORT_STRING);
		
		usort($result["triples"], 'ParserTurtle::mySortTriples');	
		
		return $result;		
	}	
}
