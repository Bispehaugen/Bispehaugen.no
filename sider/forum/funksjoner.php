<?php
define("antall_tema_per_side", 25);

function forum_innlegg_liste($innleggliste, $class="forum-innlegg-liste", $temaid = 0) {
    global $dbh;
    $medlemsid = $_SESSION["medlemsid"];

    $har_temaid =  ($temaid > 0);

    $ulesteinnlegg = Array();
    $listeinnlegg = Array();
    if ($temaid != 0) {
        //henter listeid til alle innlegg i valgte forum og tema som det er en liste knyttet til
        $sql="SELECT forum_liste.listeid, forum_liste.tittel, forum_liste.expires FROM forum_liste, forum_innlegg_ny
            WHERE forum_liste.listeid=forum_innlegg_ny.innleggid AND forum_innlegg_ny.temaid=?";
        $listeinnlegg=hent_og_putt_inn_i_array($sql, array($temaid));
        
        //henter ut alle aktuelle liste-oppføringer
        $sql="SELECT medlemsid, fnavn, enavn, forum_listeinnlegg_ny.listeid, forum_listeinnlegg_ny.tid, forum_listeinnlegg_ny.innleggid, 
        forum_listeinnlegg_ny.kommentar, forum_listeinnlegg_ny.flagg, forum_liste.expires 
        FROM forum_liste, forum_innlegg_ny, forum_listeinnlegg_ny, forum_tema, medlemmer 
        WHERE forum_liste.listeid=forum_innlegg_ny.innleggid AND forum_liste.listeid=forum_listeinnlegg_ny.listeid AND
         forum_tema.temaid=? AND forum_innlegg_ny.temaid=? AND brukerid=medlemmer.medlemsid ORDER BY tid ;";
        $listeoppforinger=hent_og_putt_inn_i_array($sql, array($temaid, $temaid), "innleggid"); 


        //Henter ut siste uleste innlegg i tråd
        $sql="SELECT uleste_innlegg FROM forum_leste WHERE temaid=? AND medlemsid=?";
        $stmt = $dbh->prepare($sql);
        $stmt->execute(array($temaid, $medlemsid));
        $ulesteinnlegg = $stmt->fetchAll(PDO::FETCH_COLUMN);
    }
    
    $hentMedlemsid = function($innleggliste) {
        return $innleggliste['skrevetavid'];
    };
    $brukerIder = array_map($hentMedlemsid, $innleggliste);

    $brukerdata = hent_brukerdata($brukerIder);

    echo "
        <section class='".$class."'>
            <ul>
    ";

    foreach($innleggliste as $id => $innlegg) {
        $b = $brukerdata[$innlegg['skrevetavid']];
        $tid = strtotime($innlegg['skrevet']);

        $erLestClass = in_array($id, $ulesteinnlegg) ? "ulest" : "lest";

        echo "<li class='innlegg ".$erLestClass."'>";
        echo "<header>";
        
        if (!empty($b['foto'])) {
            echo "<span class='foto'><img src='".thumb($b['foto'], 40)."' /></span>";
        }
        echo "<section class='info'>";
        echo "<h5 class='navn'>".$b['fnavn']." ".$b['enavn']."</h5>";
        echo "<abbr class='tid timeago' title='".date("c", $tid)."''>kl. ".date("H:i", $tid)." den ".date("d. F Y", $tid)."</abbr>";
/*
        //legger til liker-ikon med antall likes (vises ikke for lister)
        if(!$innlegg['liste']){
            echo"<i class='fa fa-thumbs-up' title='Antall som liker dette'>XX";
            //du kan bare like andres innlegg
            if($forum_innlegg['skrevetavid']!=$_SESSION['medlemsid']){
                echo"<br><a href='?side=forum/innlegg&id=".$temaid."&likerinnlegg=".$forum_innlegg['innleggid']."'>Lik dette</i></a>";
            }
        }
*/
        echo "</section>";

        if (!$har_temaid) {
            echo "<span class='plassering'>";
            echo "<span class='forum-tittel'><a href='?side=forum/tema&id=".$innlegg['forumid']."'>".$innlegg['tematittel']."</a></span>";
            echo " <i class='fa fa-caret-right'></i> ";
            echo "<span class='tema-tittel'><a href='?side=forum/innlegg&id=".$innlegg['temaid']."'>".$innlegg['innleggtittel']."</a></span>";
            echo "</span>";
        }

        //viser endre/slett-knapper på egne innlegg og for admin (så de har mulighet til å overstyre)
        if(($innlegg['skrevetavid']==$medlemsid || $_SESSION['rettigheter']>1) && $har_temaid){
            echo "<section class='tools'>";
                //echo "<i class='fa fa-edit tool' title='Klikk for å endre'></i>";
                echo "<a class='tool' href='javascript:void(0)' 
                        onclick='confirm_url(\"?side=forum/innlegg&id=".$temaid."&sletteinnlegg=".$id."\", 
                                             \"Er du sikker på at du vil slette kommentaren?\")'>";
                    echo "<i class='fa fa-times' title='Klikk for å slette'></i>";
                echo "</a>";
            echo "</section>";
        }


        echo "</header>";
        echo "<article>";
            echo "<p class='tekst'>".nl2br($innlegg['tekst'])."</p>";
        
        //if som skriver ut liste hvis det hører en liste til innlegget
        if(array_key_exists($id, $listeinnlegg)){
            $oppfort_paa_liste = False;
            echo "<table class='paameldingsliste'>";
            foreach($listeoppforinger as $listeoppforing){
                if($listeoppforing['listeid']==$id){
                    $strek_igjennom_klasse = ($listeoppforing['flagg']==1) ? "skrek-igjennom" : "";
                    echo "<tr><td class='".$strek_igjennom_klasse."'>";
                    echo $listeoppforing['fnavn']." ".$listeoppforing['enavn'];
                    echo "</td><td>".$listeoppforing['kommentar']."</td></tr>";
                    
                    #For å vite om bruker står på lista og dermed ikke kan skrive seg på nytt
                    if($listeoppforing['medlemsid']==$medlemsid){$oppfort_paa_liste=True;}  
                };  
            };
            //Legger til tekstfelt for å melde seg på hvis ikke lista har expired
            if($oppfort_paa_liste){
                echo "<tr><td colspan='2'><b>Du er allerede skrevet på lista</b></td></tr> ";                      
            }elseif(isset($listeinnlegg[$id]['expires']) && strtotime(date('Y-m-d'))/(60*60*24) <= strtotime(substr($listeinnlegg[$id]['expires'],0,10))/(60*60*24)){
            echo "<form class='forum' method='post' action='?side=forum/innlegg&id=".$temaid."'>
                <tr><td>Kommentar (frivillig):<br><input type='text' name='kommentar' class='kommentar' autofocus><br><label><input type='checkbox' name='flagg' value='1'> Stryk navnet</label></td>
                <td><input type='hidden' name='medlemsid' value='".$_SESSION['medlemsid']."'>
                <input type='hidden' name='listeinnlegg' value='".$listeinnlegg[$innlegg['innleggid']]['listeid']."'>
                <input type='submit' name='nyttListeInnlegg' value='Skriv meg på lista'></td></tr>";
            }else{
                echo "<tr><td colspan='2'><b>Det er ikke lenger mulig å melde seg på denne lista</b></td></tr> "; 
            };
            echo "</form></table>";
        };
            

        echo "</article>";
        echo "</li>";
    }

    echo "
            </ul>
        </section>
    ";
}

function forum_list_tema($forumid, $skip) {
    if (empty($skip)) $skip = 0;

    //henter ut alle temaene i valgte forum og henter ut siste innlegg
    $sql="SELECT forum_tema.temaid, forum_tema.forumid, tittel, sisteinnleggid, skrevetavid, tekst, innleggid, skrevet
    FROM forum_tema LEFT JOIN forum_innlegg_ny ON innleggid=sisteinnleggid WHERE forum_tema.forumid=? ORDER BY sisteinnleggid DESC LIMIT ? , ?";

    $forumtemaer = hent_og_putt_inn_i_array($sql, array($forumid, $skip, antall_tema_per_side));

    $hentMedlemsid = function($innlegg) {
        return $innlegg['skrevetavid'];
    };
    $brukerIder = array_map($hentMedlemsid, $forumtemaer);

    $brukerdata = hent_brukerdata($brukerIder);

    //Henter ut alle temaer med uleste innlegg
    $medlemsid= $_SESSION["medlemsid"];
    $sql="SELECT forum_leste.temaid FROM forum_leste WHERE medlemsid=?";
    $uleste_innlegg = hent_og_putt_inn_i_array($sql, array($medlemsid));

    echo "<section class='forum temaliste'>";

    //skriver ut alle temaene i forumet sortet på sist oppdaterte med siste innlegg og av hvem
    foreach($forumtemaer as $temaid => $forumtema){
        $b = hent_bruker($brukerdata, $forumtema['skrevetavid']);
        $tid = strtotime($forumtema['skrevet']);

        echo "<article class='tema";
        if(array_key_exists($temaid, $uleste_innlegg) && $uleste_innlegg[$temaid]){
            echo " uleste-poster";
        }
        echo "'>";

        echo"<h1 class='overskrift'>";

        if(array_key_exists($temaid, $uleste_innlegg) && $uleste_innlegg[$temaid]){
            echo "<i class='fa fa-envelope'></i>";
        } else {
            echo "<i class='fa fa-envelope-o'></i>";
        }

        echo "<a href='?side=forum/innlegg&id=$temaid'>".$forumtema['tittel']."</a></h1>
            <div class='siste-post'>";
            if (!empty($b['foto'])) {
                $foto = $b['foto'];
            } else {
                $foto = "icon_logo_hvit.png";
            }
            echo "<span class='foto'><img src='".thumb($foto, 40)."' /></span>";
            echo "<section class='info'>";
            echo "<h5 class='navn'>".$b['fnavn']." ".$b['enavn']."</h5>";
            if ($tid != 0) {
                echo "<abbr class='tid timeago' title='".date("c", $tid)."''>kl. ".date("H:i", $tid)." den ".date("d. F Y", $tid)."</abbr>";
            }
            echo "</div>";
        echo "</article>";
    }
    echo "</section>";

}

function forum_paginering($id, $skip, $type) {
    global $dbh;

    switch($type) {
        case "tema":
            $sql = "SELECT COUNT( temaid ) AS antall FROM forum_tema WHERE forumid=:id";
        break;
        case "innlegg":
            $sql = "SELECT COUNT( innlegg_id ) AS antall FROM forum_innlegg_ny WHERE temaid=:id";
            die("IKKE IMPLEMENTERT, sjekk om dette er riktig...");
        break;
        default:
            die("Pagineringstype ".$type." finnes ikke");
    }

    $stmt = $dbh->prepare($sql);
    $stmt->execute(array(":id" => $id));
    $antall = $stmt->rowCount();

    $max_antall_sider = floor($antall / antall_tema_per_side);
    $midtside = floor($max_antall_sider/2);
    //die("FIX ME, vis hvilken som er valgt");

    $sideNr = 1;
    echo "<ul class='forum pagination'>";

    if ($skip > 0) {
        echo "<li><a href='?side=forum/tema&id=".$id."&skip=".($skip-antall_tema_per_side)."'><i class='icon-chevron-left'></i>Forrige</a></li>";
    }

    if ($antall > 12 * antall_tema_per_side) {

        for($lokalSkip = 0; $lokalSkip < 4*antall_tema_per_side; $lokalSkip += antall_tema_per_side) {
            $erAktiv = pagineringErAktiv($lokalSkip, $skip, antall_tema_per_side);

            pagineringslenke($id, $lokalSkip, $sideNr, $erAktiv);

            $sideNr += 1;
        }
            echo "<li class='dotdotdot'>...</li>";

            $midtSideMinusEn = ($midtside-2);
            $midtSide = ($midtside-1);
            $midtSidePlussEn = ($midtside);
            $midtSidePlussTo = ($midtside+1);

            echo "<li><a href='?side=forum/tema&id=".$id."&skip=".$midtSideMinusEn*antall_tema_per_side."'>".$midtSideMinusEn."</a></li>";
            echo "<li><a href='?side=forum/tema&id=".$id."&skip=".$midtSide*antall_tema_per_side."'>".$midtside."</a></li>";
            echo "<li><a href='?side=forum/tema&id=".$id."&skip=".$midtSidePlussEn*antall_tema_per_side."'>".$midtSidePlussEn."</a></li>";
            echo "<li><a href='?side=forum/tema&id=".$id."&skip=".$midtSidePlussTo*antall_tema_per_side."'>".$midtSidePlussTo."</a></li>";

            echo "<li class='dotdotdot'>...</li>";

            $fjerdeSisteSide = ($max_antall_sider-4);
            $tredjeSisteSide = ($max_antall_sider-3);
            $nestSisteSide = ($max_antall_sider-2);
            $sisteSide = $max_antall_sider-1;

            echo "<li><a href='?side=forum/tema&id=".$id."&skip=".$fjerdeSisteSide*antall_tema_per_side."'>".$fjerdeSisteSide."</a></li>";
            echo "<li><a href='?side=forum/tema&id=".$id."&skip=".$tredjeSisteSide*antall_tema_per_side."'>".$tredjeSisteSide."</a></li>";
            echo "<li><a href='?side=forum/tema&id=".$id."&skip=".$nestSisteSide*antall_tema_per_side."'>".$nestSisteSide."</a></li>";
            echo "<li><a href='?side=forum/tema&id=".$id."&skip=".$sisteSide*antall_tema_per_side."'>".$sisteSide."</a></li>";
        
    } else {
        for($lokalSkip = 0; $lokalSkip <= $antall; $lokalSkip+=antall_tema_per_side) {
            $erAktiv = pagineringErAktiv($lokalSkip, $skip, antall_tema_per_side);

            pagineringslenke($id, $lokalSkip, $sideNr, $erAktiv);

            $sideNr++;
        }
    }

    if ($skip < ($max_antall_sider-1)*antall_tema_per_side) {
        echo "<li><a href='?side=forum/tema&id=".$id."&skip=".($skip+antall_tema_per_side)."'>Neste <i class='icon-chevron-right'></i></a></li>";
    }
    echo "</ul>";
}

function pagineringslenke($id, $skip, $nummer, $erAktiv = false) {
    echo "<li";
    if ($erAktiv) {
        echo " class='aktiv'";
    }
    echo "><a href='?side=forum/tema&id=".$id."&skip=".$skip."'>".$nummer."</a></li>";
}

function pagineringErAktiv($lokalSkip, $skip, $antall_tema_per_side) {
    return $lokalSkip <= $skip && ($lokalSkip + $antall_tema_per_side) > $skip;
}

//lister opp alle forumene med link til hvert forum 
function list_forum($aktivId = ""){
    $sql = "SELECT * FROM  `forum` ORDER BY pos ASC";
    $forum_liste = hent_og_putt_inn_i_array($sql);
?>
    <section class="forum-liste">
        <ul>
            <?php
            foreach($forum_liste as $forumid => $forum) {
                if ($_SESSION['rettigheter'] >= $forum['rettigheter']) {
                    echo "<li";
                    if($aktivId == $forumid){
                        echo " class='aktiv'";
                    }
                    echo "><a href='?side=forum/tema&id=".$forumid."' title='".$forum['beskrivelse']."'>".$forum['tittel']."</a></li>";
                }
            }
            ?>
        </ul>
    </section>

<?php
}

function har_tilgang_til_forum($forumid = "", $temaid = "") {
    $sql = "";
    $params = array();
    if (!empty($forumid)) {
        $sql = "SELECT forumid, rettigheter FROM forum WHERE forumid = ?";
        $params[] = $forumid;
    }

    if (!empty($temaid)) {
        $sql = "SELECT ft.forumid, f.rettigheter FROM forum_tema AS ft LEFT JOIN forum AS f ON ft.forumid = f.forumid WHERE ft.temaid = ?";
        $params[] = $temaid;
    }

    $forum = hent_og_putt_inn_i_array($sql, $params);

    if (session("rettigheter") < $forum['rettigheter']) {
        die("Du har ikke tilgang til dette forumet");
    }
}

function sett_sisteinnleggid($temaid){
    global $dbh;
    //oppdaterer sisteinnleggid i forum_tema-tabellen
    $sql="SELECT innleggid FROM forum_innlegg_ny WHERE temaid=? ORDER BY innleggid DESC LIMIT 1";
    $stmt = $dbh->prepare($sql);
    $stmt->execute(array($temaid));
    $sisteinnleggid = $stmt->fetchColumn();
    
    $sql="UPDATE forum_tema SET sisteinnleggid=? WHERE temaid=?";
    $stmt = $dbh->prepare($sql);
    $stmt->execute(array($sisteinnleggid, $temaid));
        
    //oppdaterer sisteinnleggig i forum-tabellen
    $sql="SELECT sisteinnleggid, forumid FROM forum_tema ORDER BY sisteinnleggid DESC LIMIT 1";
    $sisteinnlegg = hent_og_putt_inn_i_array($sql);
    
    $sql="UPDATE forum SET sisteinnleggid=? WHERE forumid=?";
    $stmt = $dbh->prepare($sql);
    $stmt->execute(array($sisteinnlegg[0]['sisteinnleggid'], $sisteinnlegg[0]['forumid']));
}

function siste_forumposter_liste($antall = 5, $class="forum-innlegg-liste", $temaid = 0) {
    $sql = "SELECT fi . * , ft.tittel as innleggtittel, f.tittel as tematittel
            FROM forum_innlegg_ny AS fi
            LEFT JOIN forum_tema AS ft ON fi.temaid = ft.temaid
            LEFT JOIN forum AS f ON fi.forumid = f.forumid
            WHERE f.rettigheter <= ?
            ORDER BY skrevet DESC 
            LIMIT ?";
    $innleggliste = hent_og_putt_inn_i_array($sql, array(session("rettigheter"), $antall));
    return forum_innlegg_liste($innleggliste, $class, $temaid);
}

