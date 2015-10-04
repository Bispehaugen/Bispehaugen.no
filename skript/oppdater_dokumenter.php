<?php
error_reporting(E_ERROR | E_WARNING | E_PARSE | E_NOTICE);

setlocale(LC_TIME, "Norwegian", "nb_NO", "nb_NO.utf8");

$root = "../";

include_once $root."db_config.php";
include_once $root.'funksjoner.php';

$tilkobling = koble_til_database($database_host, $database_user, $database_string, $database_database);

if ($tilkobling === false) {
	die("Ingen tilkobling");
}

if(!er_logget_inn() || !tilgang_full()) {
	die("Må være admin!");
}

$antall_mapper = 0;
$antall_filer = 0;
$dir = str_replace("skript", "dokumenter", getcwd())."/";

function legg_inn_directory_i_database($dir, $idpath, $path, $foreldreid) {
	$navnUtenNorskeTegn = fornorske($dir);

	$sql = "INSERT INTO mapper (mappenavn, idpath, tittel, mappetype, foreldreid) VALUES ('".$navnUtenNorskeTegn."', '".addslashes($idpath)."', '".$dir."', '1', '".$foreldreid."')";
	mysql_query($sql) or die(mysql_error() ." <-- There was an error when proccessing query");

	$id = mysql_insert_id();

	$navnUtenNorskeTegn = $id."-".$navnUtenNorskeTegn;

	$sql_update = "UPDATE mapper SET mappenavn = '".$navnUtenNorskeTegn."' WHERE id = ".$id;

	mysql_query($sql_update) or die(mysql_error() ." <-- There was an error when proccessing query");

	echo "</section>";
	echo "<section class='mappe'>";
	echo "<h2>$navnUtenNorskeTegn</h2>";

	$GLOBALS['antall_mapper'] = $GLOBALS['antall_mapper'] + 1;

	return $id;
}

function legg_inn_fil_i_database($file, $path, $foreldreid) {
	$navnUtenNorskeTegn = fornorske($file);
	$tittel = fjern_filtype($file);
	$filtype = gjett_filtype($file);

	$sql = "INSERT INTO filer (filnavn, tittel, filtype, medlemsid, mappeid) VALUES ('".$file."', '".$tittel."', '".$filtype."', 211,'".$foreldreid."')";
	mysql_query($sql) or die(mysql_error() ." <-- There was an error when proccessing query");

	$id = mysql_insert_id();

	$navnUtenNorskeTegn = $id."-".$navnUtenNorskeTegn;

	$sql_update = "UPDATE filer SET filnavn = '".$navnUtenNorskeTegn."' WHERE id = ".$id;

	mysql_query($sql_update) or die(mysql_error() ." <-- There was an error when proccessing query");

	echo "<p>$navnUtenNorskeTegn</p>";

	$GLOBALS['antall_filer'] = $GLOBALS['antall_filer'] + 1;

	return $id;
}

function flytt_dir_hvis_gammelt_navn($id, $dir, $path) {
	if (strpos($dir, $id."-") == false) {
		// bare rename hvis den ikke inneholder id
		rename($path.$dir, $GLOBALS['dir'].$id."-".fornorske($dir));
	}
}
function flytt_fil_hvis_gammelt_navn($id, $file, $path) {
	if (strpos($file, $id."-") == false) {
		// bare rename hvis den ikke inneholder id
		rename($path.$file, $path.$id."-".fornorske($file));
	}
}

function gjett_filtype($file) {
	return strtolower(array_pop(preg_split("/\./", $file)));
}

function fjern_filtype($file) {
	$filtype = gjett_filtype($file);

	return substr($file, 0, -1*(strlen($filtype)+1));
}

function finn_alt_i_dir($dir) {
	$all_in_dir = scandir($dir);
	$dirs = Array();
	$files = Array();

	foreach($all_in_dir as $file) {
		$er_dir = is_dir($dir.$file);
		$er_denne_eller_over_dir = ($file == "." || $file == ".." || $file == ".DS_Store" || $file == ".htaccess");

		if($er_dir && !$er_denne_eller_over_dir) {
			array_push($dirs, $file);
		} else if (!$er_dir && !$er_denne_eller_over_dir) {
			array_push($files, $file);
		}
	}

	return Array($dirs, $files);
}

function parse_dir($parentdir, $idpath, $path, $foreldreid) {
	$parentdir = str_replace('//', '/', $parentdir);
	list($dirs, $files) = finn_alt_i_dir($parentdir);

	foreach($files as $file) {
		$id = legg_inn_fil_i_database($file, $path, $foreldreid);
		flytt_fil_hvis_gammelt_navn($id, $file, $path);
	}

	foreach($dirs as $dir) {
		$currDir = $path.$dir;
		$id = legg_inn_directory_i_database($dir, $idpath, $currDir, $foreldreid);
		parse_dir($parentdir.'/'.$dir.'/', $idpath.$id.'/', $currDir.'/', $id);
		flytt_dir_hvis_gammelt_navn($id, $dir, $path);
	}
}

function slett_mapper($type) {
	$sql = "TRUNCATE mapper"; //DELETE FROM mapper WHERE mappetype = ".$type;
	mysql_query($sql) or die(mysql_error() ." <-- There was an error when proccessing query");
}
function slett_filer() {
	$sql = "TRUNCATE filer";
	mysql_query($sql) or die(mysql_error() ." <-- There was an error when proccessing query");
}

//slett_mapper(1);
//slett_filer();

ob_start();
parse_dir($dir, '/', $dir, 0);
$innhold = ob_get_contents();
ob_end_clean();

// egen funksjon for å kalkulere idpath
echo "<h1>Mapper og filer funndet og lagt til i databasen</h1>";
echo "<p>$dir</p>";
echo "<p>Antall mapper: $antall_mapper, antall filer: $antall_filer</p>";
echo "<section>";
echo $innhold;
echo "</section>";
