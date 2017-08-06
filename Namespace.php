<?php
/**
 * @git git@github.com:BorderCloud/SPARQL.git
 * @author Karima Rafes <karima.rafes@bordercloud.com>
 * @license http://creativecommons.org/licenses/by-sa/4.0/
 */
namespace BorderCloud\SPARQL;

class FourStore_Namespace
{

    protected static $_namespaces = array();

    public static function addW3CNamespace()
    {
        self::add('rdf', 'http://www.w3.org/1999/02/22-rdf-syntax-ns#');
        self::add('rdfs', 'http://www.w3.org/2000/01/rdf-schema#');
        self::add('xsd', 'http://www.w3.org/2001/XMLSchema#');
    }

    public static function add($short, $long)
    {
        self::$_namespaces[$short] = $long;
    }

    public static function get($short)
    {
        return self::$_namespaces[$short];
    }

    public static function to_sparql()
    {
        $sparql = "";
        foreach (self::$_namespaces as $short => $long) {
            $sparql .= "PREFIX $short: <$long>\n";
        }
        return $sparql;
    }

    public static function to_turtle()
    {
        $turtle = "";
        foreach (self::$_namespaces as $short => $long) {
            $turtle .= "@prefix $short: <$long> .\n";
        }
        return $turtle;
    }
}
