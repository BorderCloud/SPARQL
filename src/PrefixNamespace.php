<?php
declare(strict_types=1);
/**
 * @git git@github.com:BorderCloud/SPARQL.git
 * @author Karima Rafes <karima.rafes@bordercloud.com>
 * @license http://creativecommons.org/licenses/by-sa/4.0/
 */
namespace BorderCloud\SPARQL;

/**
 * Class PrefixNamespace
 * @package BorderCloud\SPARQL
 */
class PrefixNamespace
{
    /**
     * List of namespace
     *
     * @var array
     */
    protected static $_namespaces = array();

    /**
     * Init the list of namespace
     */
    public static function addW3CNamespace()
    {
        self::addNamespace('rdf', 'http://www.w3.org/1999/02/22-rdf-syntax-ns#');
        self::addNamespace('rdfs', 'http://www.w3.org/2000/01/rdf-schema#');
        self::addNamespace('xsd', 'http://www.w3.org/2001/XMLSchema#');
    }

    /**
     * Add a namespace
     *
     * @param $prefix
     * @param $url
     */
    public static function addNamespace($prefix, $url)
    {
        self::$_namespaces[$prefix] = $url;
    }

    /**
     * Get a namespace
     *
     * @param $prefix
     * @return mixed
     */
    public static function getNamespace($prefix)
    {
        return self::$_namespaces[$prefix];
    }

    /**
     * Get namespace prefix for Sparql and Turtle 1.1
     *
     * @return string
     */
    public static function toSparql()
    {
        $sparql = "";
        foreach (self::$_namespaces as $shortNamespace => $longNamespace) {
            $sparql .= "PREFIX $shortNamespace: <$longNamespace>\n";
        }
        return $sparql;
    }

    /**
     * Get namespace prefix for Turtle 1.0
     *
     * @return string
     */
    public static function toTurtle()
    {
        $turtle = "";
        foreach (self::$_namespaces as $shortNamespace => $longNamespace) {
            $turtle .= "@prefix $shortNamespace: <$longNamespace> .\n";
        }
        return $turtle;
    }
}
