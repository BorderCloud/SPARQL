<?php
declare(strict_types=1);
/**
 * @git git@github.com:BorderCloud/SPARQL.git
 * @author Karima Rafes <karima.rafes@bordercloud.com>
 * @license http://creativecommons.org/licenses/by-sa/4.0/
 */
namespace BorderCloud\SPARQL;

/**
 * Class Mimetype
 * TODO
 *
 * @package BorderCloud\SPARQL
 */
class Mimetype
{

    /**
     * TODO
     *
     * @var
     */
    private static $_arrayShortName;

    /**
     * TODO
     *
     * @var
     */
    private static $_arrayFilenameExtension;

    /**
     * TODO
     */
    private static function init()
    {
        self::$_arrayFilenameExtension = array();
        self::$_arrayShortName = array();
        self::add('rdf', 'rdf', 'application/rdf+xml');
        self::add('nt', 'text', 'text/plain');
        self::add('csv', 'csv', 'text/csv; charset=utf-8');
        self::add('', 'csv', 'text/csv');
        self::add('tsv', 'tsv', 'text/tab-separated-values; charset=utf-8');
        self::add('', 'tsv', 'text/tab-separated-values');
        self::add('ttl', 'ttl', 'text/turtle');
        self::add('srx', 'xml', 'application/sparql-results+xml');
        self::add('srj', 'json', 'application/sparql-results+json');
    }

    /**
     * TODO
     *
     * @param $extension
     * @param $shortName
     * @param $mimetype
     */
    private static function add($extension, $shortName, $mimetype)
    {
        self::$_arrayShortName[$mimetype] = $shortName;

        if (! EMPTY($extension)) {
            self::$_arrayFilenameExtension[$extension] = $mimetype;
        }
    }

    /**
     * TODO
     *
     * @param $extension
     * @return null|string
     */
    public static function getMimetypeOfFilenameExtensions($extension)
    {
        if (null === self::$_arrayFilenameExtension)
            self::init();

        if (array_key_exists($extension, self::$_arrayFilenameExtension)) {
            return self::$_arrayFilenameExtension[$extension];
        } else {
            return null;
        }
    }

    /**
     * TODO
     *
     * @param $mimetype
     * @return null|string
     */
    public static function getShortNameOfMimetype($mimetype)
    {
        if (null === self::$_arrayShortName)
            self::init();

        if (array_key_exists($mimetype, self::$_arrayShortName)) {
            return self::$_arrayShortName[$mimetype];
        } else {
            return null;
        }
    }
}
