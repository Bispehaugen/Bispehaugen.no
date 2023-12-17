<?php 
    include_once("sider/forum/funksjoner.php");

    setlocale(LC_TIME, "Norwegian");

    //funksjonalitet
    
    //sjekker om man er logget inn
    if(!er_logget_inn()){
        header('Location: index.php');
        die();
    };

    //Her legges det inn en oversikt over alle forumene
    list_forum();
    
    echo "<section class='forum'>";
    echo "<h2>Siste poster</h2>";

    siste_forumposter_liste(5, "forum-innlegg-liste siste-poster");

    echo "</section>";
?>  
