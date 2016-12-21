<?php
setlocale(LC_TIME, "Norwegian", "nb_NO", "nb_NO.utf8");
ini_set('memory_limit', '256M');

include_once 'image.php';

if(isset($_GET['filid'])) {
    include_once 'funksjoner.php';
    include_once 'sider/dokumenter/funksjoner.php';

    $filid = get('filid');
    $file = hent_fil_med_mappeinfo($filid);
    $src = hent_filpath($file);
} else if (isset($_GET['src'])) {
    $src = $_GET['src'];
}

thumbnail($src, $_GET['size'], $_GET['crop'], $_GET['trim'], $_GET['zoom'], $_GET['align'], $_GET['sharpen'], $_GET['gray'], $_GET['ignore']);
