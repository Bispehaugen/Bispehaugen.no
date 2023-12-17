<?php

setlocale(LC_TIME, "nb_NO.utf8");
include_once "funksjoner.php";

if(has_get('side')){
    $side = get('side');
} else {
    die("Feil bruk");
}

echo inkluder_side_fra_undermappe($side, "sider");
