<?php 
	include_once("sider/forum/funksjoner.php");

	//funksjonalitet
	
	//TODO bredde på tabellen, bare hente opp de 10 siste og ha en knapp med 'vis alle' som printer smtlige innlegg
	//og blar til bunnen, format og ev bilde i 'skrevet av' kolonnen.
	
	//TODO: b�r slette post/get etter at de er hentet ut.
	
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
	if(has_post('temaid')){
		$tekst=mysql_escape_string(post('tekst'));
		$sql="INSERT INTO forum_innlegg_ny (temaid, tekst, skrevet, skrevetavid, sistredigert) 
			VALUES ('".post('temaid')."','".$tekst."','".date('Y-m-d H:i:s')."','".post('medlemsid')."','".date('Y-m-d h:i:s')."')";
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
			$sql.="('".$medlem['medlemsid']."','".$id."','".post('temaid')."'),";
		}
		$sql=substr($sql,0,-1);
		mysql_query($sql);
		
		//oppdaterer sistelesteid i b�de forum- og forum_tema-tabellen
		sett_sisteinnleggid($temaid);
	};
	
	//hvis noen har skrevet seg p� en liste
	if(has_post('listeinnlegg')){
		//fjerner alt skummelt fra kommentarfeltet og setter inn feltet
		$kommentar=post('kommentar');
		if(post('flagg')==1){$flagg=1;}else{$flagg=0;};
		//sql - databasen er s�nn at pdd. kan du ikke melde deg p� n�r du allerede er p�meldt
		$sql="INSERT INTO forum_listeinnlegg_ny (listeid, flagg, brukerid, kommentar, tid) 
			VALUES ('".post('listeinnlegg')."','".$flagg."','".$_SESSION["medlemsid"]."','".$kommentar."','".date('Y-m-d h:i:s')."')";
		mysql_query($sql);	
	};
	
	/*
	//henter listeid til alle innlegg i valgte forum og tema som det er en liste knyttet til
	$sql="SELECT forum_liste.listeid, forum_liste.tittel FROM forum_liste, forum_innlegg_ny
		WHERE forum_liste.listeid=forum_innlegg_ny.innleggid;";
	$listeinnlegg=hent_og_putt_inn_i_array($sql, "listeid");	
	
	//henter ut alle aktuelle liste-oppf�ringer
	$sql="SELECT medlemsid, fnavn, enavn, forum_listeinnlegg_ny.listeid, forum_listeinnlegg_ny.tid, forum_listeinnlegg_ny.innleggid, 
	forum_listeinnlegg_ny.kommentar, forum_listeinnlegg_ny.flagg, forum_liste.expires 
	FROM forum_liste, forum_innlegg_ny, forum_listeinnlegg_ny, forum_tema, medlemmer 
	WHERE forum_liste.listeid=forum_innlegg_ny.innleggid AND forum_liste.listeid=forum_listeinnlegg_ny.listeid AND
	 forum_tema.temaid=".$temaid." AND forum_innlegg_ny.temaid=".$temaid." AND brukerid=medlemmer.medlemsid ORDER BY tid ;";
	$listeoppforinger=hent_og_putt_inn_i_array($sql, "innleggid");	
	*/
		
	$medlemsid= $_SESSION["medlemsid"];
	
	//Henter ut tema-tittel
	$sql="SELECT tittel, temaid FROM forum_tema WHERE temaid=".$temaid.";";
	$mysql_result=mysql_query($sql);
	$tema = mysql_fetch_array($mysql_result);
	
	//Setter alle innlegg i aktuelle tr�d som lest i databasen (s� neste gang blir de merket som lest)
	$sql = "DELETE FROM `forum_leste` WHERE temaid=".$temaid." AND medlemsid=".$medlemsid.";";
	mysql_query($sql);
	
    #Det som printes p� sida
    
    //Her legges det inn en oversikt over alle forumene
    list_forum();


	$sql="SELECT fi.* , ft.tittel as innleggtittel, ft.tittel as tematittel FROM forum_tema AS ft, forum_innlegg_ny AS fi
	WHERE ft.temaid=".$temaid." AND fi.temaid=".$temaid." ORDER BY skrevet;";

	//skriver ut temaet for denne tråden
    echo "<section class='forum'>
    	<h1>".$tema['tittel']."</h1>";

	forum_innlegg_liste($sql, "forum-innlegg-liste", $temaid);

	echo "
	<form class='forum' method='post' action='?side=forum/innlegg&id=".$temaid."'>
			<tr><td>Svar p� innlegg:</td><td><textarea name='tekst' autofocus></textarea></td>
			<td><input type='hidden' name='medlemsid' value='".$_SESSION['medlemsid']."'>
			<input type='hidden' name='temaid' value='".$temaid."'>
			<input type='submit' name='nyttInnlegg' value='Lagre'></td></tr>
		</form> 
	</section>";
?>