<?php

setlocale(LC_TIME, "Norwegian", "nb_NO", "nb_NO.utf8");

$root = "./";

include_once $root.'funksjoner.php';
include_once $root.'/sider/dokumenter/funksjoner.php';

if(!er_logget_inn() || !has_get("fil")) {
	header('Location: index.php?side=ikke_funnet');
	die();
}

$filid = intval(get("fil"));

if (!is_int($filid)) {
	header('Location: index.php?side=ikke_funnet');
	die("Fil id må sendes inn via GET parameteret fil");
}

$file = hent_fil_med_mappeinfo($filid);
$filepath = hent_filpath($file);

$filtype = $file['filtype'];
$filtittel = $file['tittel'] . "." . $filtype;

$file_time = filemtime($filepath);
$file_date = gmdate('D, d M Y H:i:s T', $file_time);
$file_hash = md5("v1.0.0-" . $filepath . $file_time);

if (file_exists($filepath)) {
    if (fil_er_bilde($filtype)) {
		header('Content-Type: image/' . $filtype);
		header('Content-Length: ' . filesize($filepath));
		header('Content-Disposition: inline; filename="' . $filtittel . '"');
		header('Last-Modified: ' . $file_date);
		header('ETag: ' . $file_hash);
		header('Accept-Ranges: none');
	    header('Cache-Control: max-age=604800, must-revalidate');
	    header('Expires: ' . gmdate('D, d M Y H:i:s T', strtotime('+7 days')));
	} else {
		header('Content-Description: File Transfer');
    	header('Content-Type: application/octet-stream');
	    header('Content-Disposition: attachment; filename="'.$filtittel.'"');
		header('Last-Modified: ' . $file_date);
	    header('Expires: 0');
	    header('Cache-Control: must-revalidate');
	    header('Pragma: public');
	    header('Content-Length: ' . filesize($filepath));
	}
    
    readfile($filepath);
    exit;
} else {
	die("Fant ikke filen ".$filtittel);
}
