<?php 
	include_once("sider/forum/funksjoner.php");
    global $dbh;

	//funksjonalitet
	
	//TODO bredde på tabellen, bare hente opp de 10 siste og ha en knapp med 'vis alle' som printer smtlige innlegg
	//og blar til bunnen, format og ev bilde i 'skrevet av' kolonnen.
	
	//TODO: bør slette post/get etter at de er hentet ut.
	
	//sjekker om man er logget inn
	if(!er_logget_inn()){
		header('Location: ../index.php');
		die();
	};
	
	$temaid=get('id');

	har_tilgang_til_forum("", $temaid);
	
	//slette innlegg
	if(has_get('sletteinnlegg')){
		$sletteinnleggid=get('sletteinnlegg');
		$sql="DELETE FROM `forum_innlegg_ny` WHERE innleggid=?";
        $stmt = $dbh->prepare($sql);
        $stmt->execute(array($sletteinnleggid));
		sett_sisteinnleggid($temaid);
		header('Location: ?side=forum/innlegg&id='.$temaid.'');
	};
	
	//liker et innlegg
	if(has_get('likerinnlegg')){
		$likerinnleggid=get('likerinnlegg');
		$sql="INSERT INTO `forum_liker`(`medlemsid`, `innleggid`) VALUES (?,?)";
        $stmt = $dbh->prepare($sql);
        $stmt->execute(array($_SESSION["medlemsid"], $likerinnleggid));
		sett_sisteinnleggid($temaid);
		header('Location: ?side=forum/innlegg&id='.$temaid.'');
	};
	
	//hvis det er lagt til et nytt innlegg legges dette inn i databasen
	if(has_post('tekst')){
		$sql="INSERT INTO forum_innlegg_ny (temaid, tekst, skrevet, skrevetavid, sistredigert) 
			VALUES (?,?,'".date('Y-m-d H:i:s')."',?,'".date('Y-m-d h:i:s')."')";
        $stmt = $dbh->prepare($sql);
        $stmt->execute(array($temaid, post("tekst"), $_SESSION["medlemsid"]));
		//henter ut id til det nye innlegget
		$id = $dbh->lastInsertId();
		//henter ut liste over alle aktive medlemmer
		$sql="SELECT medlemsid FROM medlemmer WHERE status!='Sluttet';";
		$aktivemedlemmer=hent_og_putt_inn_i_array($sql);
		//legger inn innlegget som ulest for alle aktive medlemmer
		$sql="INSERT INTO `forum_leste`(`medlemsid`, `uleste_innlegg`, `temaid`) 
			VALUES (?, ?, ?)";
        $stmt = $dbh->prepare($sql);
		foreach ($aktivemedlemmer as $medlemsid => $medlem) {
            $stmt->execute(array($medlem["medlemsid"], $id, $temaid));
		}

        // Oppdaterer tidsisteinnlegg
        $sql = "SELECT fnavn, enavn FROM medlemmer where medlemsid=".$_SESSION["medlemsid"];
        $row = $dbh->query($sql)->fetch();
        $sql = "UPDATE forum_tema SET tidsisteinnlegg='".date("Y-m-d H:i:s")."', sisteinnleggskrevetav='{$row[0]} {$row[1]}' WHERE temaid=$temaid";
        $dbh->query($sql);
		
		//oppdaterer sistelesteid i både forum- og forum_tema-tabellen
		sett_sisteinnleggid($temaid);
	};
	
	//hvis noen har skrevet seg på en liste
	if(has_post('listeinnlegg')){
		//fjerner alt skummelt fra kommentarfeltet og setter inn feltet
		$kommentar=post('kommentar');
		if(post('flagg')==1){$flagg=1;}else{$flagg=0;};
		//sql - databasen er sånn at pdd. kan du ikke melde deg på når du allerede er påmeldt
		$sql="INSERT INTO forum_listeinnlegg_ny (listeid, flagg, brukerid, kommentar, tid) 
			VALUES (?,?,?,?,'".date('Y-m-d h:i:s')."')";
        $stmt = $dbh->prepare($sql);
        $stmt->execute(array(post("listeinnlegg"), $flagg, $_SESSION["medlemsid"], $kommentar));
	};
		
	$medlemsid= $_SESSION["medlemsid"];
	
	//Henter ut tema-tittel
	$sql="SELECT tittel, temaid FROM forum_tema WHERE temaid=?";
    $stmt = $dbh->prepare($sql);
    $stmt->execute(array($temaid));
	$tema = $stmt->fetch();
	
	//Setter alle innlegg i aktuelle tråd som lest i databasen (så neste gang blir de merket som lest)
	$sql = "DELETE FROM `forum_leste` WHERE temaid=? AND medlemsid=?";
    $stmt = $dbh->prepare($sql);
    $stmt->execute(array($temaid, $medlemsid));
	
    #Det som printes på sida
    
    //Her legges det inn en oversikt over alle forumene
    list_forum();


	$sql="SELECT fi.* , ft.tittel as innleggtittel, ft.tittel as tematittel FROM forum_tema AS ft, forum_innlegg_ny AS fi
	WHERE ft.temaid=? AND fi.temaid=ft.temaid ORDER BY skrevet;";
    $innleggliste = hent_og_putt_inn_i_array($sql, array($temaid));

	//skriver ut temaet for denne tråden
    echo "<section class='forum'>
    	<h2>".$tema['tittel']."</h2>";

	echo forum_innlegg_liste($innleggliste, "forum-innlegg-liste", $temaid);

	$innlogget_bruker = innlogget_bruker();

	echo "<section class='nytt-innlegg'>";
		if (!empty($innlogget_bruker['foto'])) {
			echo "<span class='foto'><img src='".thumb($innlogget_bruker['foto'], 40)."' /></span>";
		}
		echo "<div class='info'>";
		echo "<h5 class='navn'>".$innlogget_bruker['fnavn']." ".$innlogget_bruker['enavn']."</h5>";
		echo "<abbr></abbr>";
		echo "</div>";

	echo "
			<form method='post' action='?side=forum/innlegg&id=".$temaid."'>
				<textarea name='tekst' class='tekst' placeholder='Din kommentar...' autofocus></textarea>
				<input type='submit' class='lagre' value='Lagre'>
				<div class='clearfix'></div>
			</form>
		</section>
	</section>";
?>
