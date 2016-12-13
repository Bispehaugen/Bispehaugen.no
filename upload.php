<?php
die("Disabled");
setlocale(LC_TIME, "Norwegian", "nb_NO", "nb_NO.utf8");
include_once 'funksjoner.php';

if(!er_logget_inn()) {
	die("Du må være logget inn");
}

$config = new \Flow\Config(array(
   'tempDir' => './temp'
));
$request = new \Flow\Request();

$type = $_REQUEST['type'];
$id = intval($_REQUEST['id']);

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
	
	$filename = $medlemsid.$fileExt;

	$filepath = "..".$dir.$filename;
}

// slett filer som starter på medlemsid: rm 211-*

if (\Flow\Basic::save( "." . $dir . $filename, $config, $request)) {
	$sql = "UPDATE medlemmer SET foto = ? WHERE medlemsid = ? LIMIT 1";
    $stmt = $dbh->prepare($sql);
    $stmt->execute(array(addslashes($filepath), $medlemsid));
} else {
	echo "Fail... :(";
}

// In most cases, do nothing, \Flow\Basic handles all errors
