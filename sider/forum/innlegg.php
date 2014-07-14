<?php 
	include_once("sider/forum/funksjoner.php");

	//funksjonalitet
	
	//TODO bredde på tabellen, bare hente opp de 10 siste og ha en knapp med 'vis alle' som printer smtlige innlegg
	//og blar til bunnen, format og ev bilde i 'skrevet av' kolonnen.
	
	//TODO: bør slette post/get etter at de er hentet ut.
	
	//sjekker om man er logget inn
	if(!er_logget_inn()){
		header('Location: ../index.php');
	};
	
	$temaid=get('id');
	
	//slette innlegg
	if(has_get('sletteinnlegg')){
		$sletteinnleggid=get('sletteinnlegg');
		$sql="DELETE FROM `forum_innlegg_ny` WHERE innleggid=".$sletteinnleggid;
		mysql_query($sql);
		sett_sisteinnleggid($temaid);
		header('Location: ?side=forum/innlegg&id='.$temaid.'');
	};
	
	//liker et innlegg
	if(has_get('likerinnlegg')){
		$likerinnleggid=get('likerinnlegg');
		$sql="INSERT INTO `forum_liker`(`medlemsid`, `innleggid`) VALUES ('".$_SESSION["medlemsid"]."',".$likerinnleggid.")";
		mysql_query($sql);
		sett_sisteinnleggid($temaid);
		header('Location: ?side=forum/innlegg&id='.$temaid.'');
	};
	
	//hvis det er lagt til et nytt innlegg legges dette inn i databasen
	if(has_post('tekst')){
		$sql="INSERT INTO forum_innlegg_ny (temaid, tekst, skrevet, skrevetavid, sistredigert) 
			VALUES ('".$temaid."','".post('tekst')."','".date('Y-m-d H:i:s')."','".$_SESSION['medlemsid']."','".date('Y-m-d h:i:s')."')";
		mysql_query($sql);
		//henter ut id til det nye innlegget
		$id = mysql_insert_id();
		//henter ut liste over alle aktive medlemmer
		$sql="SELECT medlemsid FROM medlemmer WHERE status!='Sluttet';";
		$aktivemedlemmer=hent_og_putt_inn_i_array($sql, "medlemsid");
		//legger inn innlegget som ulest for alle aktive medlemmer
		$sql="INSERT INTO `forum_leste`(`medlemsid`, `uleste_innlegg`, `temaid`) 
			VALUES ";
		foreach ($aktivemedlemmer as $medlemsid => $medlem) {
			$sql.="('".$medlem['medlemsid']."','".$id."','".$temaid."'),";
		}
		$sql=substr($sql,0,-1);
		mysql_query($sql);
		
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
			VALUES ('".post('listeinnlegg')."','".$flagg."','".$_SESSION["medlemsid"]."','".$kommentar."','".date('Y-m-d h:i:s')."')";
		mysql_query($sql);	
	};
		
	$medlemsid= $_SESSION["medlemsid"];
	
	//Henter ut tema-tittel
	$sql="SELECT tittel, temaid FROM forum_tema WHERE temaid=".$temaid.";";
	$mysql_result=mysql_query($sql);
	$tema = mysql_fetch_array($mysql_result);
	
	//Setter alle innlegg i aktuelle tråd som lest i databasen (så neste gang blir de merket som lest)
	$sql = "DELETE FROM `forum_leste` WHERE temaid=".$temaid." AND medlemsid=".$medlemsid.";";
	mysql_query($sql);
	
    #Det som printes på sida
    
    //Her legges det inn en oversikt over alle forumene
    list_forum();


	$sql="SELECT fi.* , ft.tittel as innleggtittel, ft.tittel as tematittel FROM forum_tema AS ft, forum_innlegg_ny AS fi
	WHERE ft.temaid=".$temaid." AND fi.temaid=".$temaid." ORDER BY skrevet;";

	//skriver ut temaet for denne tråden
    echo "<section class='forum'>
    	<h1>".$tema['tittel']."</h1>";

	echo forum_innlegg_liste($sql, "forum-innlegg-liste", $temaid);

	$innlogget_bruker = innlogget_bruker();

	echo "<section class='nytt-innlegg'>";
		if (!empty($innlogget_bruker['foto'])) {
			echo "<img class='foto' src='".$innlogget_bruker['foto']."' />";
		}
		echo "<div class='info'>";
		echo "<h5 class='navn'>".$innlogget_bruker['fnavn']." ".$innlogget_bruker['enavn']."</h5>";
		echo "<abbr>Nå</abbr>";
		echo "</div>";

	echo "
			<form method='post' action='?side=forum/innlegg&id=".$temaid."'>
				<textarea name='tekst' class='tekst' autofocus></textarea>
				<input type='submit' class='lagre' value='Lagre'>
				<div class='clearfix'></div>
			</form>
		</section>
	</section>";
?>