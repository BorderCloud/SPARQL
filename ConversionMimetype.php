<?php
/**
 * @git git@github.com:BorderCloud/SPARQL.git
 * @author Karima Rafes <karima.rafes@bordercloud.com>
 * @license http://creativecommons.org/licenses/by-sa/4.0/
*/

class ConversionMimetype {

    private static $_mimetypeToShortname;
    private static $_filenameExtensionToMimetype;
    
    private static function init()
    {
	$_mimetypeToShortname = array();
	$_filenameExtensionToMimetype = array();
    	self::add('rdf','rdf', 'application/rdf+xml');
    	self::add('nt','text', 'text/plain');
    	self::add('csv','csv', 'text/csv; charset=utf-8');
    	self::add('','csv', 'text/csv');
    	self::add('tsv','tsv', 'text/tab-separated-values; charset=utf-8');
    	self::add('','tsv', 'text/tab-separated-values');
    	self::add('ttl','ttl', 'text/turtle');
    	self::add('srx','xml', 'application/sparql-results+xml');
    	self::add('srj','json', 'application/sparql-results+json');
    }

    private static function add($extension, $shortname, $mimetype)
    {
        self::$_mimetypeToShortname[$mimetype] = $shortname;
        
        if(!EMPTY($extension))
	  self::$_filenameExtensionToMimetype[$extension] = $mimetype;
    }

    public static function getMimetypeOfFilenameExtensions($extension)
    {
	if (self::$_filenameExtensionToMimetype == null)
	  self::init();
	  
	if (array_key_exists($extension, self::$_filenameExtensionToMimetype)) {
	  return self::$_filenameExtensionToMimetype[$extension];
	}else{
	  return NULL;
	}
    }

    public static function getShortnameOfMimetype($mimetype)
    {
	if (self::$_mimetypeToShortname == null)
	  self::init();
	  
	if (array_key_exists($mimetype, self::$_mimetypeToShortname)) {
	  return self::$_mimetypeToShortname[$mimetype];
	}else{
	  return NULL;
	}
    }
}