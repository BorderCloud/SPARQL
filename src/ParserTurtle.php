<?php
declare(strict_types=1);
/**
 * @git git@github.com:BorderCloud/SPARQL.git
 * @author Karima Rafes <karima.rafes@bordercloud.com>
 * @license http://creativecommons.org/licenses/by-sa/4.0/
 */
namespace BorderCloud\SPARQL;

/**
 * Class ParserTurtle
 * TODO
 *
 * @package BorderCloud\SPARQL
 */
class ParserTurtle
{
    /**
     * TODO
     *
     * @param $turtle
     * @param $baseGraph
     * @param bool $idMD5
     * @return array
     */
    public static function turtleToArray($turtle, $baseGraph, $idMD5 = false)
    {
        $tabResult = array();
        $tabResult["prefix"] = array();
        $tabResult["prefix"]["base"] = $baseGraph;
        $tabResult["triples"] = array();

        preg_match_all("/((?:(?:\"|').*(?:\"|')(?:\^\^[^\s]*)?|a|<[^\s]*>|[^\s]*:[^\s]+|;|,|@prefix\s*[^\s]*\s*:\s*<[^\s<>]+>|\s*\r?\n?)+\s?\.)/i", $turtle, $matches, PREG_SET_ORDER);

        foreach ($matches as $val) {
            // // http://answers.semanticweb.com/questions/2025/what-is-the-meaning-of-base-and-prefix-in-turtle-documents

            if (preg_match("/^\s*@base\s*<([^\s<>]+)>\s*\.\s*$/i", $val[0], $valMatches)) {
                $tabResult["prefix"]["base"] = $valMatches[1];
            } elseif (preg_match("/^\s*@prefix\s*([^\s]+)?\s*:\s*<([^\s<>]+)>\s*\.\s*$/i", $val[0], $valMatches)) {
                $namePrefix = $valMatches[1] == "" ? "empty" : $valMatches[1];
                $tabResult["prefix"][$namePrefix] = $valMatches[2];
            } elseif (preg_match("/^\s*(<[^\s]*>|[^\s]*:[^\s]+)\s*(.*)\s*\.$/is", $val[0], $valMatches)) {
                $object = ParserTurtle::relativeToExplicitURI($valMatches[1], $tabResult["prefix"]);
                preg_match_all("/\s*(a|<[^\s\,\;]*>|[^\s]*:[^\s\;]+)\s*((?:(?:<[^\s\,\;]*>|[^\s]*:[^\s\;]+|(?:\"(?:\\\\\"|[^\"])*\"|'(?:\\\\'|[^'])*')(?:\^\^[^\s\;]*)?|\s*),?)+\s*);?/is", $valMatches[2], $propertyMatches, PREG_SET_ORDER);
                foreach ($propertyMatches as $propertyVal) {

                    $property = "";
                    if ($propertyVal[1] == "a") {
                        $property = "<http://www.w3.org/1999/02/22-rdf-syntax-ns#type>";
                    } else {
                        $property = ParserTurtle::relativeToExplicitURI($propertyVal[1], $tabResult["prefix"]);
                    }
                    preg_match_all("/(<[^\s\,\;]*>|[^\s]*:[^\s\,]+|(?:\"(?:\\\\\"|[^\"])*\"|'(?:\\\\'|[^'])*')(?:\^\^[^\s\,]*)?)\s*,?/is", $propertyVal[2], $valueMatches, PREG_SET_ORDER);
                    foreach ($valueMatches as $valueVal) {
                        $value = ParserTurtle::relativeToExplicitURI($valueVal[1], $tabResult["prefix"]);
                        // echo "s=>".$object." p=>".$property." o=>".$value."\n";
                        if ($idMD5) {
                            $tabResult["triples"][md5($object . $property . $value)] = array(
                                "s" => $object,
                                "p" => $property,
                                "o" => $value
                            );
                        } else {
                            $tabResult["triples"][] = array(
                                "s" => $object,
                                "p" => $property,
                                "o" => $value
                            );
                        }
                    }
                }
            }
        }
        return $tabResult;
    }

    /**
     * TODO
     *
     * @param $uri
     * @param $prefix
     * @return float|int|string
     */
    public static function relativeToExplicitURI($uri, $prefix)
    {
        $result = $uri;
        if (preg_match("/^(\"(?:\\\"|[^\"])*\"|'(?:\\'|[^'])*')(?:\^\^([^\s]*))?$/i", $uri, $matches)) { // <>
            if (isset($matches[2])) {
                $type = ParserTurtle::relativeToExplicitURI($matches[2], $prefix);
                $value = $matches[1];
                if ($type == "http://www.w3.org/2001/XMLSchema#double" || $type == "http://www.w3.org/2001/XMLSchema#decimal") {
                    $value = floatval($value);
                } elseif ($type == "http://www.w3.org/2001/XMLSchema#integer") {
                    $value = intval($value);
                    // }elseif($type == "http://www.w3.org/2001/XMLSchema#boolean"){
                    // $value = $value === "true" ? true : false;
                }
                $result = "\"" . $value . "\"^^" . $type;
            } else {
                $value = $matches[1];
                if (ToolsConvert::isTrueFloat($value)) {
                    $value = floatval($value);
                } elseif (is_int($value)) {
                    $value = intval($value);
                }
                $result = $value;
            }
        } elseif (preg_match("/^<>$/i", $uri, $matches)) { // <>
            $result = "<" . $prefix["base"] . ">";
        } elseif (preg_match("/^<#([^:]+)>$/i", $uri, $matches)) { // <#truc>
            $result = "<" . $prefix["base"] . $matches[1] . ">";
        } elseif (preg_match("/^<([^:]+)>$/i", $uri, $matches)) { // <truc>
            // $len = strlen($prefix["base"]);
            $prefixBase = substr($prefix["base"], 0, strrpos($prefix["base"], "/"));
            $result = "<" . $prefixBase . $matches[1] . ">";
        } elseif (preg_match("/^:([^\s]*)$/i", $uri, $matches)) { // :truc
            $result = "<" . $prefix["empty"] . $matches[1] . ">";
        } elseif (preg_match("/^([^:_]*):([^><]*)$/i", $uri, $matches)) { // x:truc
            $result = "<" . $prefix[$matches[1]] . $matches[2] . ">";
        } else {
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

    /**
     * TODO
     *
     * @param $itm1
     * @param $itm2
     * @return int
     */
    static function mySortTriples($itm1, $itm2)
    {
        if ($itm1["s"] > $itm2["s"]) {
            return 1;
        } else if ($itm1["s"] < $itm2["s"]) {
            return - 1;
        } else {
            if ($itm1["p"] > $itm2["p"]) {
                return 1;
            } else if ($itm1["p"] < $itm2["p"]) {
                return - 1;
            } else {
                if ($itm1["o"] > $itm2["o"]) {
                    return 1;
                } else if ($itm1["o"] < $itm2["o"]) {
                    return - 1;
                } else {
                    return 0;
                }
            }
        }
    }

    /**
     * TODO
     *
     * @param $arrayTurtle
     * @return mixed
     */
    static function sortTriples($arrayTurtle)
    {
        $result = $arrayTurtle;
        array_multisort($result["prefix"], SORT_ASC, SORT_STRING);

        usort($result["triples"], 'ParserTurtle::mySortTriples');

        return $result;
    }

    /**
     * TODO
     *
     * @param $arrayTurtle
     * @param $s
     * @param $p
     * @return int|null|string
     */
    static function getKey($arrayTurtle, $s, $p)
    {
        $result = null;
        if (! empty($arrayTurtle)) {
            foreach ($arrayTurtle["triples"] as $key => $triple) {
                if ("<" . $s . ">" == $triple["s"] && "<" . $p . ">" == $triple["p"]) {
                    $result = $key;
                    break;
                }
            }
        }
        return $result;
    }

    /**
     * TODO
     *
     * @param $arrayTurtle
     * @param $s
     * @param $p
     * @return null
     */
    static function getTriple($arrayTurtle, $s, $p)
    {
        $result = null;
        if (! empty($arrayTurtle)) {
            foreach ($arrayTurtle["triples"] as $key => $triple) {
                if ("<" . $s . ">" == $triple["s"] && "<" . $p . ">" == $triple["p"]) {
                    $result = $triple;
                    preg_match('@^(?:<tel\:([^<>]+)>|<mailto\:([^<>]+)>|<([^<>]+)>|([^\"<>]+)|\"(.*)\"[^\"]*)$@i', $triple["o"], $matches);
                    // \<mailto\:([^\<\>])+\>|\<([^\<\>])+\>|
                    // print_r($triple["o"]);
                    // print_r($matches);
                    foreach ($matches as $keyMatches => $match) {
                        if ($keyMatches != 0 && ! empty($match)) {
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

    /**
     * TODO
     *
     * @param $rs1
     * @param $rs2
     * @param bool $ordered
     * @param bool $distinct
     * @return array
     */
    public static function compare($rs1, $rs2, $ordered = false, $distinct = false)
    {
        $difference = array();
        $rs1Triples = null;
        $rs2Triples = null;

        if ($distinct) {
            $rs1Triples = $rs1["triples"];
            $rs2Triples = $rs2["triples"];
        } else {
            $rs1Triples = ToolsBlankNode::removeDuplicate($rs1["triples"]);
            $rs2Triples = ToolsBlankNode::removeDuplicate($rs2["triples"]);
        }

        // B/ Check the result set have the same number of rows.
        if (count($rs1Triples) != count($rs2Triples)) {
            $difference[1] = "Nb rows :" . count($rs1Triples);
            $difference[2] = "Nb rows :" . count($rs2Triples);
            return $difference; // return false ;
        }

        // Check if there are blanknodes//////////////////////
        // ref : http://blog.datagraph.org/2010/03/rdf-isomorphism

        // 1.Compare graph sizes and all statements without blank nodes. If they do not match, fail.
        // 1.1 remove blank nodes
        $clone1WithoutBlanknodes = $rs1Triples;
        $clone2WithoutBlanknodes = $rs2Triples;
        $bnodesInRs1 = array();
        $bnodesInRs2 = array();
        $patternBlankNode = '/^_:/';

        // echo "BEFORE";
        // print_r($clone1);
        // print_r($clone2);
        foreach ($clone1WithoutBlanknodes as &$row) {
            foreach ($row as $key => &$value) {
                if (is_string($value) &&  preg_match($patternBlankNode, $value)) {
                    $bnodesInRs1[] = $value;
                    $value = "BLANKNODE"; // remove
                }
            }
        }
        foreach ($clone2WithoutBlanknodes as &$row) {
            foreach ($row as $key => &$value) {
                if (is_string($value) &&  preg_match($patternBlankNode, $value)) {
                    $bnodesInRs2[] = $value;
                    $value = "BLANKNODE"; // remove
                }
            }
        }

        // print_r($clone1WithoutBlanknodes);
        // print_r($clone2WithoutBlanknodes);
        // 1.2 compare
        if ($ordered) {
            $difference = ToolsBlankNode::arrayDiffAssocRecursive($clone1WithoutBlanknodes, $clone2WithoutBlanknodes);
        } else {
            $difference = ToolsBlankNode::arrayDiffAssocUnordered($clone1WithoutBlanknodes, $clone2WithoutBlanknodes);
        }

        // Check if there are blank nodes
        if ((count($bnodesInRs1) == 0 && count($bnodesInRs2) == 0) || count($difference) != 0) {
            return $difference;
        }

        // With blank nodes
        $bnodesInRs1 = array_values(array_unique($bnodesInRs1));
        $bnodesInRs2 = array_values(array_unique($bnodesInRs2));
        if (count($bnodesInRs1) != count($bnodesInRs2)) {
            $difference[1] = "Nb bnode :" . count($bnodesInRs1);
            $difference[2] = "Nb bnode :" . count($bnodesInRs2);
            return $difference; // return false ;
        }

        // echo "BLANKNODE\n";
        // print_r($bnodesInRs1);
        // print_r($bnodesInRs2);
        $clone1 = $rs1Triples;
        // print_r($clone1);
        // 2.Repeat, for each graph:
        $arrayPermutationsBnode = ToolsBlankNode::allPermutations($bnodesInRs2);
        // echo "PERMUTATION\n";
        // print_r($arrayPermutationsBnode );
        // exit();
        foreach ($arrayPermutationsBnode as $permute) {
            // print_r($permute);
            $clone2 = $rs2Triples;
            foreach ($clone2 as $key => &$row) {
                $arrayVariableTypeBnode = array_keys($row, "bnode");
                foreach ($arrayVariableTypeBnode as $variableTypeBnode) {
                    $variableArray = explode(" ", $variableTypeBnode);
                    $variable = $variableArray[0];

                    $row[$variable] = $bnodesInRs1[array_search($row[$variable], $permute)];
                }
            }

            // print_r($clone2);
            // $difference = self::sub_array_diff_assoc_unordered( $clone1,$clone2) ;
            if ($ordered) {
                $difference = ToolsBlankNode::arrayDiffAssocRecursive($clone1, $clone2);
            } else {
                $difference = ToolsBlankNode::arrayDiffAssocUnordered($clone1, $clone2);
            }
            if (count($difference) == 0) {
                return $difference; // true
            }
        }

        return $difference;
    }

    /* public static function array_diff_assoc_unordered( $rs1, $rs2) {
     * $difference=array();
     * $rs1Triples = $rs1["triples"];
     * $rs2Triples = $rs2["triples"];
     *
     * //B/ Check the result set have the same number of rows.
     * if(count($rs1Triples) != count($rs2Triples)) {
     * $difference[1]="Nb rows :".count($rs1Triples);
     * $difference[2]="Nb rows :".count($rs2Triples);
     * return $difference; //return false ;
     * }
     *
     * //C/ Pick a row from the test results, scan the expected results
     * // to find a row with same variable/value bindings, and remove
     * // from the expected results. If all test rows, match then
     * // (because of B) the result sets have the same rows.
     * //
     * //return equivalent(convert(rs1), convert(rs2), new BNodeIso(NodeUtils.sameValue)) ;
     * $clone1 = $rs1Triples;
     * $clone2 = $rs2Triples;
     * // echo "AFTER";
     * // print_r($clone1);
     * // print_r($clone2);
     * foreach ($rs1Triples as $key1=>&$value1) {
     * $tmpClone2 = $clone2;
     * foreach ($tmpClone2 as $key2=>&$value2) {
     *
     * // echo "-------------";
     * // print_r($value1);
     * // print_r($value2);
     * if(count(array_diff_assoc($value1,$value2)) == 0 &&
     * count(array_diff_assoc($value2,$value1)) == 0 ){
     * unset($clone1[$key1]);
     * unset($clone2[$key2]);
     * break;
     * }
     * }
     * // echo "-------------AFTER";
     * // print_r($clone1);
     * // print_r($clone2);
     * }
     *
     * if(count($clone1) != 0 ||
     * count($clone2) != 0 ){
     * $difference[1]=$clone1;
     * $difference[2]=$clone2;
     * return $difference; //return false ;
     * }
     *
     * return $difference;
     * }
     */
}
