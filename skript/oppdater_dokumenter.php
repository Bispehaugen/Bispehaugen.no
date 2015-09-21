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


function legg_inn_directory_i_database($dir, $path, $foreldreid) {
	$navn = $dir;
	$navnUtenNorskeTegn = preg_replace("/[^A-ZÆØÅa-zæøå0-9\-]/", '_', $navn);	
	$navnUtenNorskeTegn = str_replace("Æ", 'AE', $navnUtenNorskeTegn);	
	$navnUtenNorskeTegn = str_replace("æ", 'ae', $navnUtenNorskeTegn);	
	$navnUtenNorskeTegn = str_replace("Ø", 'O', $navnUtenNorskeTegn);	
	$navnUtenNorskeTegn = str_replace("ø", 'o', $navnUtenNorskeTegn);	
	$navnUtenNorskeTegn = str_replace("Å", 'AA', $navnUtenNorskeTegn);	
	$navnUtenNorskeTegn = str_replace("å", 'aa', $navnUtenNorskeTegn);	

	$sql = "INSERT INTO mapper (mappenavn, idpath, tittel, mappetype, foreldreid) VALUES ('".$dir."', '".addslashes($path)."', '".$navnUtenNorskeTegn."', '1', '".$foreldreid."')";
	mysql_query($sql) or die(mysql_error() ." <-- There was an error when proccessing query");

	return mysql_insert_id();
}

function finn_alle_undermapper_i_dir($dir) {
	$files = scandir($dir);
	$dirs = Array();

	foreach($files as $file) {
		$er_dir = is_dir($dir.$file);
		$er_denne_eller_over_dir = ($file == "." || $file == "..");

		if($er_dir && !$er_denne_eller_over_dir) {
			array_push($dirs, $file);
		}
	}
	return $dirs;
}

function parse_dir($parentdir, $path, $foreldreid) {
	$parentdir = str_replace('//', '/', $parentdir);
	$dirs = finn_alle_undermapper_i_dir($parentdir);

	foreach($dirs as $dir) {
		$id = legg_inn_directory_i_database($dir, $path, $foreldreid);
		parse_dir($parentdir.'/'.$dir.'/', $path.$id.'/', $id);
	}
}

$root_dir = "../dokumenter/";

// egen funksjon for å kalkulere idpath

parse_dir($root_dir, '/', 0);
