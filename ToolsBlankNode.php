<?php
/**
 * @git git@github.com:BorderCloud/SPARQL.git
 * @author Karima Rafes <karima.rafes@bordercloud.com>
 * @license http://creativecommons.org/licenses/by-sa/4.0/
*/
class ToolsBlankNode {

      public static function removeDuplicate($set)
        {
	  $find = false;
	  $result = array();
	  foreach ($set as $key1=>$value1) {
	    $find = false;
	    foreach ($result as $key2=>$value2) {
	      if(count(array_diff_assoc($value1,$value2)) == 0 ){
		$find = true;
		break;
	      }
	    }
	    if(! $find)
	    	$result[] = $value1;

	  }
	  return $result;
        }
        
      public static function AllPermutations($set)
        {
                $solutions=array();
                $solutions[]=$set;
                $n=count($set);
                $p=array_keys($set);
                $i=1;

                while ($i<$n)
                {
                  if ($p[$i]>0){
                        $p[$i]--;
                        $j=0;
                        if ($i%2==1)
                                $j=$p[$i];
                        //swap
                        $tmp=$set[$j];
                        $set[$j]=$set[$i];
                        $set[$i]=$tmp;
                        $i=1;
                        $solutions[]=$set;
                   }
                   elseif ($p[$i]==0){
                        $p[$i]=$i;
                        $i++;
                   }
                }
                return $solutions;
        }
        
        
    public static function array_diff_assoc_unordered( $rows1,  $rows2) {
        $difference=array();

        //B/ Check the result set have the same number of rows.
        if(count($rows1) != count($rows2)) {
            $difference[1]="Nb rows :".count($rows1);
            $difference[2]="Nb rows :".count($rows2);
            return $difference; //return false ;
        }

        //C/ Pick a row from the test results, scan the expected results
        //   to find a row with same variable/value bindings, and remove
        //   from the expected results. If all test rows, match then
        //   (because of B) the result sets have the same rows.
        //   
        //return equivalent(convert(rs1), convert(rs2), new BNodeIso(NodeUtils.sameValue)) ;
        $clone1 = $rows1;
        $clone2 = $rows2;

        // echo "AVANT";
          //  print_r($clone1);
          //  print_r($clone2);
        foreach ($rows1 as $key1=>&$value1) {
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
            $difference[1]=$clone1;
            $difference[2]=$clone2;
            return $difference; //return false ;
        }
        return $difference;
      }
      
      public static function array_diff_assoc_recursive($array1, $array2) {
		$difference=array();
		foreach($array1 as $key => $value) {
			if( is_array($value) ) {
				if( !isset($array2[$key]) || !is_array($array2[$key]) ) {
				  $difference[1][$key] = $value;
				  $difference[2][$key] = "Not set";
				} else {
					$new_diff = self::array_diff_assoc_recursive($value, $array2[$key]);
					if( count($new_diff)>0 ){
					    $difference[1][$key] = $new_diff[1];
					    $difference[2][$key] = $new_diff[2];
					 }
				}
			} else if( !array_key_exists($key,$array2)) {
				$difference[1][$key] = $value;
				$difference[2][$key] = "Key doesnt exist";
			} else if($array2[$key] !== $value) {
				$difference[1][$key] = $value;
				$difference[2][$key] = $array2[$key];
			}
		}
		return $difference;
	}
	/*
   	function array_diff_assoc_recursive_with_blanknode($array1, $array2) {
		$difference=array();
		$node = array();
		foreach($array1 as $key => $value) {
			if( is_array($value) ) {
				if( !isset($array2[$key]) || !is_array($array2[$key]) ) {
					$difference[$key] = $value;
				} else {
					$new_diff = Tools::array_diff_assoc_recursive_with_blanknode($value, $array2[$key]);
					if( !empty($new_diff) )
						$difference[$key] = $new_diff;
				}
			} else if( !array_key_exists($key,$array2) ) {
				$difference[$key] = $value;			
			} else if( $array2[$key] !== $value && floatval($array2[$key]) !== floatval($value)) {	
				if(isset($value[0]) && $value[0] == "_"){//(array_key_exists("type",$array1) && $array1["type"] == "bnode"){
					if(!array_key_exists($value,$node )){
						$node[$value] = $array2[$key];
					}else if($node[$value] !==  $array2[$key] ){								
						$difference[$key] = $value;
					}
				}else{
					$difference[$key] = $value;
				}
			}
		}
		return $difference;
	}
	*/
}