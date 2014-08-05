<?php
include_once 'funksjoner.php';

require("Flow/ConfigInterface.php");
require("Flow/Config.php");
require("Flow/File.php");
require("Flow/RequestInterface.php");
require("Flow/Request.php");
require("Flow/Basic.php");

$config = new \Flow\Config(array(
   'tempDir' => './temp'
));
$request = new \Flow\Request();

print_r($_REQUEST);

$type = $_REQUEST['type'];
$id = $_REQUEST['id'];

// Mangler extension

$fileNameArray = preg_split("/\./", $request->getFileName());
$fileExt = "";

if (count($fileNameArray) > 1) {
	$fileExt = "." . $fileNameArray[count($fileNameArray)-1];
}

if (isset($_REQUEST['name'])) {
	$name = preg_replace("/[^A-Za-z0-9\-]/", '', $_REQUEST['name']) . $fileExt;	
} else {
	$name = $request->getFileName();
}


if ($type == "profilbilde") {
	$medlemsid = $id;
	
	$dir = "/bilder/medlemsfoto/";
} else {
	$dir = "/filer/";
}

$filid = $id."-";

$filename = substr($filid . $name, 0, 251);

if ($type == "profilbilde") {
	
	$filepath = "..".$dir.$filename;
	$sql = "UPDATE medlemmer SET foto = '".$filepath."' WHERE medlemsid = '".$medlemsid."' LIMIT 1";
	mysql_query($sql);
	echo "Oppdaterte bilde for " . $medlemsid . " til ". $filepath;
}

// slett filer som starter på medlemsid: rm 211-*

echo "Prøver å lagre bilde til ".__DIR__ . $dir . $filename;
if (\Flow\Basic::save( __DIR__ . $dir . $filename, $config, $request)) {
  echo "Hurray, file was saved in " . $dir . $filename;
}

// In most cases, do nothing, \Flow\Basic handles all errors