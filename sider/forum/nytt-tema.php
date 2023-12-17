<?php 

global $dbh;

$feilmeldinger = Array();

if(!has_get("forumid") && !has_post("forumid")) {
    die("En feil oppstod. Si hva du gjorde til webkom så kan vi løse det :)");
}

if (!er_logget_inn()) {
    header('Location: index.php');
    die("Logg inn");
}

if(has_post("tittel")) {
    
    $innlogget_bruker = hent_brukerdata();
    
    $forumid = post("forumid");
    $tittel = post("tittel");
    $innlegg = post("innlegg");
    $listeAlternativer = post("liste-alternativer");
    $harListe = post("har-liste");
    $stegningsdato = post("stengningsdato");
    $listeTittel = post("liste-tittel");
    $harListe = ($harListe == "True");
    
    if (empty($tittel)) {
        $feilmeldinger[] = "Tittel kan ikke være tom :)";
    } else if (empty($innlegg)) {
        $feilmeldinger[] = "Innlegg kan ikke være tom :)";
    } else if ($harListe && !empty($stegningsdato) && strtotime($stegningsdato) == 0) {
        $feilmeldinger[] = "Ugyldig dato";
    } else if ($harListe && empty($listeTittel)) {
        $feilmeldinger[] = "Påmeldingens tittel er ikke fylt ut";
    }
    
    if(empty($feilmeldinger)) {
        $innlegg = nl2br(post("innlegg"));
        
        $skrevetav = $innlogget_bruker["fnavn"] . " " . $innlogget_bruker["enavn"];
        $skrevetavid = $innlogget_bruker["medlemsid"];
        
        $tema_sql = "INSERT INTO forum_tema (forumid, tittel, startetav, startetavid, startet, tidsisteinnlegg) VALUES (?, ?, ?, ?, NOW(), NOW())";
        $stmt = $dbh->prepare($tema_sql);
        $stmt->execute(array($forumid, $tittel, $skrevetav, $skrevetavid));
        $temaid = $dbh->lastInsertId();
        
        // INSERT INNLEGG
        $innlegg_sql = "INSERT INTO forum_innlegg_ny (temaid, tekst, skrevet, skrevetav, skrevetavid, forumid) VALUES (?, ?, NOW(), ?, ?, ?)";
        $stmt = $dbh->prepare($innlegg_sql);
        $stmt->execute(array($temaid, $innlegg, $skrevetav, $skrevetavid, $forumid));
        $innleggid = $dbh->lastInsertId();

        $update_tema_sql = "UPDATE forum_tema SET sisteinnleggid = ?,  antallinnlegg = 1, sisteinnleggskrevetavid = ?, sisteinnleggskrevetav = ?, tidsisteinnlegg = NOW() WHERE temaid = ?";
        $stmt = $dbh->prepare($update_tema_sql);
        $stmt->execute(array($innleggid, $skrevetavid, $skrevetav, $temaid));

        if($harListe) {
            $liste_sql = "INSERT INTO forum_liste (listeid, tittel, alternativer, expires) VALUES (?, ?, ?, ?)";
            $stmt = $dbh->prepare($liste_sql);
            $stmt->execute(array($innleggid, $listeTittel, $listeAlternativer, $stegningsdato));
        }
        echo "<script type='text/javascript'>
                window.location = '?side=forum/innlegg&id=".$temaid."';
              </script>";
    }
}

?>

<h2>Nytt tema</h2>

<?php echo feilmeldinger($feilmeldinger); ?>

<section class="forum nytt-tema">
<form action="?side=forum/nytt-tema&forumid=<?php echo get("forumid"); ?>" method="POST">
    
    <table>
        <tr>
            <td><label for="tittel">Tittel:</label></td>
            <td><input type="text" name="tittel" value="<?php echo post("tittel"); ?>" /></td>
        </tr>
        <tr>
            <td><label for="innlegg">Innlegg:</label></td>
            <td><textarea name="innlegg"><?php echo post("innlegg"); ?></textarea></td>
        </tr>
        <tr>
            <td><label for="">Påmeldingsliste / avstemningsliste:</label></td>
            <td>
                <p><label><input type="checkbox" class="har-liste" name="har-liste" value="True" <?php if(has_post("har-liste")) echo "checked"; ?> /> Ja takk</label></p>
                
                <section class="liste-alternativer">
                    <p><label for="liste-tittel">Tittel for påmeldingsliste: <input type="text" name="liste-tittel" value="<?php echo post("liste-tittel");?>" /></label></p>
                    <p><label title="Siste dato det er mulig å stemme / melde seg på">Stengningsdato: <input type="date" name="stengningsdato" value="<?php echo post("stengningsdato");?>" /></label></p>
                    <h3>Alternativer:</h3>
                    <ul>
                        <li class="standard-pamelding">
                            <p>Ingen alternativer å velge mellom. Standard påmelding med mulighet for kommentar</p>
                        </li>
                    </ul>
                    <span class="flere-alternativer"><i class="fa fa-plus"></i> Legg til nytt alternativ</span>
                    <input type="hidden" class="liste-alternativ" name="liste-alternativer" />
                </section>
            </td>
        </tr>
    </table>
    <input type="hidden" name="forumid" value="<?php echo get("forumid"); ?>" />
    <p class="right"><input type="submit" value="Opprett" /></p>
</form>

<script type="text/javascript">
    $(function(){
        var antallAlternativer = 1;

        $(".flere-alternativer").click(function() {
            $(".standard-pamelding").hide();
            
            var html = "<li data-alternativ=\""+antallAlternativer+"\">";
            html += '<input type="text" class="nytt-liste-alternativ" name="liste-alternativ-'+antallAlternativer+'" />';
            html += '<span class="fjern-alternativ" title="Fjern alternativ" data-id="'+antallAlternativer+'"><i class="fa fa-times"></i></span>';
            html += "</li>";
            
            $(".liste-alternativer ul").append(html);

            antallAlternativer++;
        });
        
        $(document).on('click', ".har-liste", function(){
            if ($(".har-liste").prop("checked")){
                $(".liste-alternativer").slideDown();
            } else {
                $(".liste-alternativer").slideUp();
            }
        });
                    
        $(document).on('click', ".fjern-alternativ", function(){
            var id = $(this).data("id");
            $(".liste-alternativer li[data-alternativ='"+id+"']").remove();
            
            if($(".liste-alternativer ul li").length < 2) {
                $(".standard-pamelding").show();
                antallAlternativer = 1;
            }
        });
        
        $(document).on('blur', ".nytt-liste-alternativ", function(){
            var alternativer = [];
            $(".nytt-liste-alternativ").each(function(index, element) {
                var alternativ = $(element).val();
                
                alternativ = alternativ.trim();
                alternativ = alternativ.replace(new RegExp("\\|", 'g'), "/");
                
                if (alternativ.length > 0) {
                    alternativer.push(alternativ);
                }
            });
            
            $(".liste-alternativ").val(alternativer.join("|"));
        });
    })
</script>

</section>
