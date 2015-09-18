<?php

// just a dummy for ext_autoload.php
class t3lib_extMgm {
	public static function extPath() {
		return '';
	}
}

$vendorName     = 'ApacheSolrForTypo3';
$migrationsPath = 'Migrations/Code/';
$mapFileName    = 'ClassAliasMap.php';
$ideFileName    = 'LegacyClassesForIde.php';


function getClassType($className, $classPath) {
	$fileContent = file_get_contents($classPath);

	if (preg_match('/interface ' . $className . ' /', $fileContent)) {
		return 'interface';
	} else if (preg_match('/abstract class ' . $className . ' /', $fileContent)) {
		return 'abstract class';
	} else {
		return 'class';
	}
}

if (!file_exists($migrationsPath)) {
	mkdir($migrationsPath, 0777, true);
}

// write ClassAliasMap header
$mapFile = fopen($migrationsPath . $mapFileName, 'w');
fwrite($mapFile, "<?php
return array(

");

// write LegacyClassesForIde header
$ideFile = fopen($migrationsPath . $ideFileName, 'w');
fwrite($ideFile, "<?php

");


$classes = include('ext_autoload.php');
foreach ($classes as $oldClass => $classPath) {
	$oldClassUpperCased = $newClass = ucwords($oldClass, '_');

	$classNameParts = explode('_', $newClass);
	$classPathParts = explode('/', $classPath);

	// the path has the proper CamelCase name of the class, cut off the .php extension
	$classNameParts[count($classNameParts) - 1] = substr($classPathParts[count($classPathParts) - 1], 0, -4);
	$oldClassUpperCased = $newClass = implode('_', $classNameParts);
	$newClass = str_replace(['_', 'Tx'], ['\\', $vendorName], $newClass);

	$type = getClassType($oldClassUpperCased, $classPath);

	// write ClassAliasMap
	$newClassEscaped = str_replace('\\', '\\\\', $newClass);
	fwrite($mapFile, "\t'$oldClassUpperCased' => '$newClassEscaped',\n");

	// write LegacyClassesForIde
	$legacyClass = <<<LEGACY
/**
 * @deprecated
 */
$type $oldClassUpperCased extends $newClass {}


LEGACY;
	fwrite($ideFile, $legacyClass);
}


// write ClassAliasMap footer
fwrite($mapFile, "
);");


fclose($mapFile);
fclose($ideFile);