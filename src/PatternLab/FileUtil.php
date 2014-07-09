<?php

/*!
 * File Util Class
 *
 * Copyright (c) 2014 Dave Olsen, http://dmolsen.com
 * Licensed under the MIT license
 *
 * Generic file related functions that are used throughout Pattern Lab
 *
 */

namespace PatternLab;

use \PatternLab\Config;

class FileUtil {

	/**
	* Copies a file from the given source path to the given public path.
	* THIS IS NOT FOR PATTERNS 
	* @param  {String}       the source file
	* @param  {String}       the public file
	*/
	protected static function moveFile($s,$p) {
		if (file_exists(Config::$options["sourceDir"]."/".$s)) {
			copy(Config::$options["sourceDir"]."/".$s,Config::$options["publicDir"]."/".$p);
		}
	}

	/**
	* Moves static files that aren't directly related to Pattern Lab
	* @param  {String}       file name to be moved
	* @param  {String}       copy for the message to be printed out
	* @param  {String}       part of the file name to be found for replacement
	* @param  {String}       the replacement
	*/
	public static function moveStaticFile($fileName,$copy = "", $find = "", $replace = "") {
		self::moveFile($fileName,str_replace($find, $replace, $fileName));
		Util::updateChangeTime();
		if ($copy != "") {
			print $fileName." ".$copy."...\n";
		}
	}

	/**
	* Check to see if a given filename is in a directory that should be ignored
	* @param  {String}       file name to be checked
	*
	* @return {Boolean}      whether the directory should be ignored
	*/
	public static function ignoreDir($fileName) {
		$id = Config::$options["id"];
		foreach ($id as $dir) {
			$pos = strpos(DIRECTORY_SEPARATOR.$fileName,DIRECTORY_SEPARATOR.$dir.DIRECTORY_SEPARATOR);
			if ($pos !== false) {
				return true;
			}
		}
		return false;
	}
	
	/**
	* Taken from Composer: https://github.com/composer/composer/blob/master/src/Composer/Util/Filesystem.php
	*
	* Normalize a path. This replaces backslashes with slashes, removes ending
	* slash and collapses redundant separators and up-level references.
	*
	* @param  string $path Path to the file or directory
	* @return string
	*/
	public static function normalizePath($path) {
		$parts = array();
		$path = strtr($path, '\\', '/');
		$prefix = '';
		$absolute = false;
		
		if (preg_match('{^([0-9a-z]+:(?://(?:[a-z]:)?)?)}i', $path, $match)) {
			$prefix = $match[1];
			$path = substr($path, strlen($prefix));
		}
		
		if (substr($path, 0, 1) === '/') {
			$absolute = true;
			$path = substr($path, 1);
		}
		
		$up = false;
		foreach (explode('/', $path) as $chunk) {
			if ('..' === $chunk && ($absolute || $up)) {
				array_pop($parts);
				$up = !(empty($parts) || '..' === end($parts));
			} elseif ('.' !== $chunk && '' !== $chunk) {
				$parts[] = $chunk;
				$up = '..' !== $chunk;
			}
		}
		
		return $prefix.($absolute ? '/' : '').implode('/', $parts);
		
	}
	
}
