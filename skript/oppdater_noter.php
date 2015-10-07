<?php
error_reporting(E_ERROR | E_WARNING | E_PARSE | E_NOTICE);

setlocale(LC_TIME, "Norwegian", "nb_NO", "nb_NO.utf8");

$root = "../";
$dir = $root . "noter/";
$dir = "/home/webkom/filer/filer/noter/";//str_replace("skript", "dokumenter", getcwd())."/";

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
$antall_noter = 0;

function gjett_filtype($file) {
	return strtolower(array_pop(preg_split("/\./", $file)));
}

function fjern_filtype($file) {
	$filtype = gjett_filtype($file);

	return substr($file, 0, -1*(strlen($filtype)+1));
}

function legg_inn_directory_i_database($mappenavn, $path, $foreldreid) {
	$tittel = ucfirst(str_replace("?", "å", str_replace("-", " ", $mappenavn)));

	$sql = "INSERT INTO mapper (mappenavn, tittel, mappetype, foreldreid) VALUES ('".$mappenavn."', '".$tittel."', '".Mappetype::Noter."', '".$foreldreid."')";
	mysql_query($sql) or die($sql . "<br />" . mysql_error() ." <-- There was an error when proccessing query");

	$id = mysql_insert_id();

	$mappenavnMedId = $id."-".$mappenavn;

	$sql_update = "UPDATE mapper SET mappenavn = '".$mappenavnMedId."' WHERE id = ".$id;

	mysql_query($sql_update) or die($sql_update . "<br />" . mysql_error() ." <-- There was an error when proccessing query");

	$filpath = "/noter/" . $mappenavn . "/";
	$sql_update_notesett = "UPDATE noter_notesett SET mappeid = '".$id."' WHERE filpath = '".addslashes($filpath)."'";

	mysql_query($sql_update_notesett) or die($sql_update_notesett . "<br />" . mysql_error() ." <-- There was an error when proccessing query");

	$sql_update_mappe_med_tittel_fra_notesett = "UPDATE mapper SET tittel = (SELECT tittel FROM noter_notesett WHERE mappeid = '".$id."') WHERE id = '".$id."'";

	mysql_query($sql_update_mappe_med_tittel_fra_notesett) or die($sql_update_mappe_med_tittel_fra_notesett . "<br />" . mysql_error() ." <-- There was an error when proccessing query");

	echo "</section>";
	echo "<section class='mappe'>";
	echo "<h2>$mappenavnMedId</h2>";

	$GLOBALS['antall_mapper'] = $GLOBALS['antall_mapper'] + 1;

	return $id;
}

function legg_inn_note_i_database($file, $path, $foreldreid) {
	$navnUtenNorskeTegn = fornorske($file);
	$tittel = fjern_filtype($file);
	$filtype = gjett_filtype($file);


	$sql = "INSERT INTO filer (filnavn, tittel, filtype, medlemsid, mappeid, mappetype) VALUES ('".$file."', '".$tittel."', '".$filtype."', 211,'".$foreldreid."', '".Mappetype::Noter."')";
	
	mysql_query($sql) or die($sql . "<br />" . mysql_error() ." <-- There was an error when proccessing query");

	$id = mysql_insert_id();

	$navnUtenNorskeTegn = $id."-".$navnUtenNorskeTegn;

	$sql_update = "UPDATE filer SET filnavn = '".$navnUtenNorskeTegn."' WHERE id = ".$id;

	mysql_query($sql_update) or die($sql_update . "<br />" . mysql_error() ." <-- There was an error when proccessing query");

	echo "<p>$navnUtenNorskeTegn</p>";

	$GLOBALS['antall_noter'] = $GLOBALS['antall_noter'] + 1;
	
	return $id;
}

function flytt_dir_hvis_gammelt_navn($id, $dir, $path) {
	if (strpos($dir, $id."-") == false) {
		// bare rename hvis den ikke inneholder id
		if(!rename($path.$dir, $GLOBALS['dir'].$id."-".$dir)) {
			echo "<p>kunne ikke flytte mappe til: ".$GLOBALS['dir'].$id."-".$dir."</p>";
		}
	}
}
function flytt_note_hvis_gammelt_navn($id, $note, $path) {
	if (strpos($note, $id."-") == false) {
		// bare rename hvis den ikke inneholder id
		if(!rename($path.$note, $path.$id."-".fornorske($note))) {
			echo "<p>kunne ikke flytte note til: ".$path.$id."-".fornorske($note)."</p>";
		}
	}
}

function finn_alt_i_dir($dir) {
	$all_in_dir = scandir($dir);
	$dirs = Array();
	$noter = Array();

	foreach($all_in_dir as $file) {
		$er_dir = is_dir($dir.$file);
		$er_denne_eller_over_dir = ($file == "." || $file == ".." || $file == ".DS_Store" || $file == ".htaccess" || $file == "index.php");

		if($er_dir && !$er_denne_eller_over_dir) {
			array_push($dirs, $file);
		} else if (!$er_dir && !$er_denne_eller_over_dir) {
			array_push($noter, $file);
		}
	}

	return Array($dirs, $noter);
}

function parse_dir($parentdir, $path, $foreldreid) {
	$parentdir = str_replace('//', '/', $parentdir);
	list($dirs, $noter) = finn_alt_i_dir($parentdir);

	foreach($noter as $note) {
		$id = legg_inn_note_i_database($note, $path, $foreldreid);
		flytt_note_hvis_gammelt_navn($id, $note, $path);
	}

	foreach($dirs as $dir) {
		$currDir = $path.$dir;
		$id = legg_inn_directory_i_database($dir, $currDir, $foreldreid);
		parse_dir($parentdir.'/'.$dir.'/', $currDir.'/', $id);
		flytt_dir_hvis_gammelt_navn($id, $dir, $path);
	}
}

function slett_mapper() {
	$sql = "DELETE FROM mapper WHERE mappetype = ".Mappetype::Noter;
	mysql_query($sql) or die(mysql_error() ." <-- There was an error when proccessing query");
}
function slett_noter() {
	$sql = "DELETE FROM filer WHERE mappetype = ".Mappetype::Noter;
	mysql_query($sql) or die(mysql_error() ." <-- There was an error when proccessing query");
}
function slett_mapping_mot_notesett() {
	$sql = "UPDATE noter_notesett SET mappeid = NULL";
	mysql_query($sql) or die(mysql_error() ." <-- There was an error when proccessing query");
}

slett_mapper();
slett_noter();
slett_mapping_mot_notesett();

ob_start();
parse_dir($dir, $dir, 0);
$innhold = ob_get_contents();
ob_end_clean();

echo "<h1>Mapper og noter funndet og lagt til i databasen</h1>";
echo "<p>$dir</p>";
echo "<p>Antall mapper: $antall_mapper, antall noter: $antall_noter</p>";
echo "<section>";
echo $innhold;
echo "</section>";
