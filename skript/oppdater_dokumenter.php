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
	$navnUtenNorskeTegn = fornorske($dir);

	$sql = "INSERT INTO mapper (mappenavn, idpath, tittel, mappetype, foreldreid) VALUES ('".$navnUtenNorskeTegn."', '".addslashes($path)."', '".$dir."', '1', '".$foreldreid."')";
	mysql_query($sql) or die(mysql_error() ." <-- There was an error when proccessing query");

	return mysql_insert_id();
}

function legg_inn_fil_i_database($file, $foreldreid) {
	$navnUtenNorskeTegn = fornorske($file);
	$filtype = gjett_filtype($file);

	$sql = "INSERT INTO filer (filnavn, tittel, filtype, medlemsid, mappeid) VALUES ('".$file."', '".$navnUtenNorskeTegn."', '".$filtype."', 211,'".$foreldreid."')";
	mysql_query($sql) or die(mysql_error() ." <-- There was an error when proccessing query");

	return mysql_insert_id();
}

function flytt_dir_hvis_gammelt_navn($id, $dir) {
	rename($dir, $id."-".fornorske($dir));
}

function gjett_filtype($file) {
	return array_pop(preg_split("/\./", $file));
}

function fornorske($navn) {
	$navnUtenNorskeTegn = preg_replace("/[^A-ZÆØÅa-zæøå0-9\-]/", '_', $navn);	
	$navnUtenNorskeTegn = str_replace("Æ", 'AE', $navnUtenNorskeTegn);	
	$navnUtenNorskeTegn = str_replace("æ", 'ae', $navnUtenNorskeTegn);	
	$navnUtenNorskeTegn = str_replace("Ø", 'O', $navnUtenNorskeTegn);	
	$navnUtenNorskeTegn = str_replace("ø", 'o', $navnUtenNorskeTegn);	
	$navnUtenNorskeTegn = str_replace("Å", 'AA', $navnUtenNorskeTegn);	
	$navnUtenNorskeTegn = str_replace("å", 'aa', $navnUtenNorskeTegn);	
	return $navnUtenNorskeTegn;
}

function finn_alt_i_dir($dir) {
	$all_in_dir = scandir($dir);
	$dirs = Array();
	$files = Array();

	foreach($all_in_dir as $file) {
		$er_dir = is_dir($dir.$file);
		$er_denne_eller_over_dir = ($file == "." || $file == "..");

		if($er_dir && !$er_denne_eller_over_dir) {
			array_push($dirs, $file);
		} else if (!$er_dir && !$er_denne_eller_over_dir) {
			array_push($files, $file);
		}
	}

	return Array($dirs, $files);
}

function parse_dir($parentdir, $path, $foreldreid) {
	$parentdir = str_replace('//', '/', $parentdir);
	list($dirs, $files) = finn_alt_i_dir($parentdir);

	foreach($files as $file) {
		legg_inn_fil_i_database($file, $foreldreid);
	}

	foreach($dirs as $dir) {
		$id = legg_inn_directory_i_database($dir, $path, $foreldreid);
		parse_dir($parentdir.'/'.$dir.'/', $path.$id.'/', $id);
		flytt_dir_hvis_gammelt_navn($id, $dir);
	}
}

function slett_mapper($type) {
	$sql = "DELETE FROM mapper WHERE mappetype = ".$type;
	mysql_query($sql) or die(mysql_error() ." <-- There was an error when proccessing query");

}

$root_dir = "../dokumenter/";

// egen funksjon for å kalkulere idpath

slett_mapper(1);

parse_dir($root_dir, '/', 0);
