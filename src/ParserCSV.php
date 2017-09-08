<?php
declare(strict_types=1);
/**
 * @git git@github.com:BorderCloud/SPARQL.git
 * @author Karima Rafes <karima.rafes@bordercloud.com>
 * @license http://creativecommons.org/licenses/by-sa/4.0/
 */
namespace BorderCloud\SPARQL;

/**
 * Class ParserCSV
 * TODO
 *
 * @package BorderCloud\SPARQL
 */
class ParserCSV
{
    /**
     * TODO
     *
     * @param $csv
     * @param string $delimiter
     * @param string $enclosure
     * @param string $escape
     * @param string $terminator
     * @return array
     */
    public static function csvToArray($csv, $delimiter = ',', $enclosure = '\'', $escape = '\\', $terminator = "\n")
    {
        $r = array();
        // $string = utf8_encode($csv);
        // echo mb_detect_encoding($names);
        // echo $csv;
        $rows = explode($terminator, trim($csv));
        $names = array_shift($rows);
        $names = str_getcsv($names, $delimiter, $enclosure, $escape);
        $nc = count($names);
        foreach ($rows as $row) {
            if (trim($row)) {
                $values = str_getcsv($row, $delimiter, $enclosure, $escape);

                if (! $values) {
                    $values = array_fill(0, $nc, null);
                }

                $tabTemp = array();
                // array_combine($names,$values);
                foreach ($names as $key => $nameCol) {
                    if (isset($values[$key])) {
                        $value = $values[$key];
                        if (ToolsConvert::isTrueFloat($value)) {
                            $value = floatval($value);
                        } elseif (is_int($value)) {
                            $value = intval($value);
                        }
                        $tabTemp[$nameCol] = $value;
                    } else {
                        $tabTemp[$nameCol] = NULL;
                    }
                }
                $r[] = $tabTemp;
            }
        }
        return $r;
    }

    /**
     * TODO
     *
     * @param $row1
     * @param $row2
     * @return int
     */
    public static function mySortAllColumn($row1, $row2)
    {
        $result = 0;
        $countTab = 0;
        if (count($row1) > count($row2)) {
            $countTab = count($row1);
        } else {
            $countTab = count($row2);
        }

        for ($i = 0; $i < $countTab; $i ++) {
            if ((! isset($row1[$i])) || (! isset($row2[$i]))) {
                if (isset($row1[$i]) && isset($row2[$i])) { // impossible in theory
                    $result = 0;
                    break;
                } elseif (! isset($row1[$i])) {
                    $result = - 1;
                    break;
                } elseif (! isset($row2[$i])) {
                    $result = 1;
                    break;
                }
            } else if ($row1[$i] < $row2[$i]) {
                $result = 1;
                break;
            } else if ($row1[$i] < $row2[$i]) {
                $result = - 1;
                break;
            }
        }
        return $result;
    }

    /**
     * TODO
     *
     * @param $array
     * @return mixed
     */
    function sortTable($array)
    {
        $result = $array;
        usort($result, 'ParserCSV::mySortAllColumn');
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
        // A/ Check the variables lists in the header are the same.
        if (count($rs1) == 0 && count($rs2) == 0) {
            return $difference; // return true ;
        } elseif (count($rs1[0]) != count($rs2[0])) {
            $difference[1] = "Nb columns :" . count($rs1);
            $difference[2] = "Nb columns :" . count($rs2);
            return $difference; // return false ;
        }

        // Check if there are blanknodes//////////////////////
        // ref : http://blog.datagraph.org/2010/03/rdf-isomorphism

        // 1.Compare graph sizes and all statements without blank nodes. If they do not match, fail.
        // 1.1 remove blank nodes
        $clone1WithoutBlanknodes = NULL;
        $clone2WithoutBlanknodes = NULL;
        if ($distinct) {
            $clone1WithoutBlanknodes = $rs1;
            $clone2WithoutBlanknodes = $rs2;
        } else {
            $clone1WithoutBlanknodes = ToolsBlankNode::removeDuplicate($rs1);
            $clone2WithoutBlanknodes = ToolsBlankNode::removeDuplicate($rs2);
        }

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
                if (is_string($value) && preg_match($patternBlankNode, $value)) {
                    $bnodesInRs2[] = $value;
                    $value = "BLANKNODE"; // remove
                }
            }
        }

        // print_r($clone1WithoutBlanknodes);
        // print_r($clone2WithoutBlanknodes);
        // exit();
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

        // 2.Repeat, for each graph:
        $arrayPermutationsBnode = ToolsBlankNode::allPermutations($bnodesInRs2);
        // echo "PERMUTATION\n";
        // print_r($arrayPermutationsBnode );
        // exit();
        foreach ($arrayPermutationsBnode as $permute) {
            // print_r($permute);
            $clone2 = $rs2;
            foreach ($clone2 as $key => &$row) {
                $arrayVariableTypeBnode = array_keys($row, "bnode");
                foreach ($arrayVariableTypeBnode as $variableTypeBnode) {
                    $variableArray = explode(" ", $variableTypeBnode);
                    $variable = $variableArray[0];

                    $row[$variable] = $bnodesInRs1[array_search($row[$variable], $permute)];
                }
            }

            if ($ordered) {
                $difference = ToolsBlankNode::arrayDiffAssocRecursive($clone1WithoutBlanknodes,$clone2WithoutBlanknodes);
            } else {
                $difference = ToolsBlankNode::arrayDiffAssocUnordered($clone1WithoutBlanknodes, $clone2WithoutBlanknodes);
            }
            if (count($difference) == 0) {
                return $difference; // true
            }
        }

        return $difference;
    }
}
