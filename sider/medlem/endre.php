<?php 
//TODO Bilde, sjekk på at alle obligatoriske felter er fyllt ut, hvis man endrer seg selv autogenerer mail til webkom/sekretær

	global $opprett_ny_nyhet;

	//funksjonalitet

	if (has_get('id')) {
		$id = get('id');
	} else {
		if(isset($_SESSION['medlemsid'])) {
			$id = $_SESSION['medlemsid'];
		} else {
			die("Du må logge inn");
		}
	}
	
	
	//sjekker om man er admin eller prøver å endre seg selv
	if($_SESSION['medlemsid']==$id){
		$endre_seg_selv=1;//brukes for å sende mail til sekretær ved endring
	}
	elseif($_SESSION['rettigheter']<2){
		header('Location: ?side=medlem/liste');
		die();
	};
	
	//henter ut alle instrumenter
		$sql="SELECT instrument, posisjon, instrumentid FROM instrument ORDER BY posisjon";
		$instrumenter=hent_og_putt_inn_i_array($sql, $id_verdi='posisjon');

	//hvis et medlem er lagt inn og noen har trykket på lagre hentes verdiene ut
	if(has_post('id') || has_post('fnavn')){
		$medlemsid=post('id');
		$fnavn=post('fnavn');
		$enavn=post('enavn');
		$instrument=post('instrument');		
		$instnr=post('instnr');		
		$grleder=post('grleder');
		$status=post('status');
		$fdato=post('fdato');
		$adresse=post('adresse');
		$postnr=post('postnr');
		$poststed=post('poststed');
		$tlfmobil=post('tlfmobil');
		$email=post('email');
		$bakgrunn=post('bakgrunn');
		$startetibuk=post('startetibuk_date');
		$sluttetibuk=post('sluttetibuk_date');
		$studieyrke=post('studieyrke');
		$kommerfra=post('kommerfra');
		$ommegselv=post('ommegselv');
		$foto=post('foto');
		$begrenset=post('begrenset');
		
		if($grleder==1){
			$sql="SELECT medlemsid, instrument FROM medlemmer WHERE grleder=1 AND instnr='".$instnr."';";
			$mysql_result = mysql_query($sql);
			
			while ($medl = mysql_fetch_assoc($mysql_result)) {
				$sql2 = "UPDATE medlemmer SET grleder=0 WHERE medlemsid=".$medl['medlemsid']." AND instrument=".$medl['instrument'].";";
				mysql_query($sql2);
			}
		}
		
		//sjekker om man vil legge til eller endre et medlem
		if ($medlemsid){
			$sql="
			UPDATE 
				medlemmer 
			SET 
				fnavn = '$fnavn',
				enavn = '$enavn',
				fdato = '$fdato',
				status = '$status',
				instnr = '$instrument',
				grleder = '$grleder',
				adresse = '$adresse',
				postnr = '$postnr',
				poststed = '$poststed',
				tlfmobil = '$tlfmobil',
				email = '$email',
				bakgrunn = '$bakgrunn',
				startetibuk_date = '$startetibuk',
				sluttetibuk_date = '$sluttetibuk',
				studieyrke = '$studieyrke',
				kommerfra = '$kommerfra',
				ommegselv = '$ommegselv',
				foto = '$foto',
				begrenset = '$begrenset' 
			WHERE 
				medlemsid = '$medlemsid';
			";
			
			mysql_query($sql);
			//header('Location: ?side=medlem/liste');
		}else{		
			$sql="
			INSERT INTO 
			medlemmer (fnavn, enavn, fdato, status, instrument, instnr, grleder, adresse, postnr, poststed, tlfmobil, 
				email, bakgrunn, startetibuk_date, sluttetibuk_date, studieyrke, kommerfra, ommegselv, foto, begrenset)
			values ('$fnavn','$enavn','$fdato','$status','$instrument','$instnr','$grleder','$adresse','$postnr','$poststed','$tlfmobil',
				'$email','$bakgrunn','$startetibuk','$sluttetibuk','$studieyrke','$kommerfra','$ommegselv','$foto','$begrenset')";
			mysql_query($sql);
			
			header('Location: ?side=medlem/liste');
		}
		};
	//henter valgte medlem fra databasen hvis "endre"
	if(has_get('id')){	
		$medlemmer = hent_brukerdata($id);
	} else if ($opprett_ny_nyhet) {
		$medlemmer = Array();
	} else {
		$medlemmer = hent_brukerdata();
	}
	$gyldige_statuser = Array("Aktiv", "Permisjon", "Sluttet");
	
	//printer ut skjema med forhåndsutfylte verdier hvis disse eksisterer
		
	echo "
    <script>
    $(function() {
        $('.datepicker').datepicker({ dateFormat: 'yy-mm-dd' }).val();;
    });
    </script>
    	<h2>".($opprett_ny_nyhet ? "Nytt medlem" : "Rediger medlem")."</h2>
		<form method='post' action='?side=medlem/endre'>
			<table>
				";
				$gruppelederCheck = (kanskje($medlemmer, "grleder") == 1) ? "checked" : "";
				echo "<tr><td>Gruppeleder:</td><td><input type='checkbox' name='grleder' value='1' ".$gruppelederCheck." /></td></tr>";
				echo "
				<tr><td>Fornavn:</td><td><input type='text' name='fnavn' value='".kanskje($medlemmer, 'fnavn')."'></td></tr>
				<tr><td>Etternavn:</td><td><input type='text' name='enavn' value='".kanskje($medlemmer, 'enavn')."'></td></tr>
				<tr><td>status:</td><td>
					<select name='status'>
					";
					
					foreach($gyldige_statuser as $status) {
						$selected = (kanskje($medlemmer, 'status')=="$status") ? " selected=selected" : "";
						
						echo "<option value='".$status."'".$selected.">".$status."</option>";
					}
					echo "
					</select></td></tr>
				<tr><td>Instrument:</td><td>
					<select name='instnr'>";
					foreach($instrumenter as $instrument){
						$selected = ($instrument['instrument']==kanskje($medlemmer, 'instrument')) ? " selected=selected" : "";
						
						echo"<option value='".$instrument['instrumentid']."'".$selected.">".$instrument['instrument']."</option>";
					}
					echo "</select>";
					
					echo "<input type='hidden' name='instrument' value='".$instrument['instrument']."'></td></tr>
				<tr><td>Fødselsdato:</td><td><input type='text' class='datepicker' name='fdato' value='".kanskje($medlemmer, 'fdato')."'></td></tr>
				<tr><td>Adresse:</td><td><input type='text' name='adresse' value='".kanskje($medlemmer, 'adresse')."'></td></tr>
				<tr><td>Postnr:</td><td><input type='text' name='postnr' value='".kanskje($medlemmer, 'postnr')."'></td></tr>
				<tr><td>Poststed:</td><td><input type='text' name='poststed' value='".kanskje($medlemmer, 'poststed')."'></td></tr>
				<tr><td>Mobilnummer:</td><td><input type='text' name='tlfmobil' value='".kanskje($medlemmer, 'tlfmobil')."'></td></tr>
				<tr><td>E-post:</td><td><input type='text' name='email' value='".kanskje($medlemmer, 'email')."'></td></tr>
				<tr><td>Musikalsk bakgrunn:</td><td><textarea name='bakgrunn'>".kanskje($medlemmer, 'bakgrunn')."</textarea></td></tr>
				<tr><td>Startet i BUK:</td><td><input type='text' class='datepicker' name='startetibuk_date' value='".kanskje($medlemmer, 'startetibuk_date')."'></td></tr>
				<tr><td>Sluttet i BUK:</td><td><input type='text' class='datepicker' name='sluttetibuk_date' value='".kanskje($medlemmer, 'sluttetibuk_date')."'></td></tr>
				<tr><td>Studie/yrke:</td><td><input type='text' name='studieyrke' value='".kanskje($medlemmer, 'studieyrke')."'></td></tr>
				<tr><td>Kommer fra:</td><td><input type='text' name='kommerfra' value='".kanskje($medlemmer, 'kommerfra')."'></td></tr>
				<tr><td>Litt om meg selv:</td><td><textarea name='ommegselv'>".kanskje($medlemmer, 'ommegselv')."</textarea></td></tr>
				";
				$begrensetChecked = (kanskje($medlemmer, 'begrenset') == 1) ? "checked" : "";
				echo "
				<tr>
					<td>Bilde:</td>
					<td>
						<input type='text' name='foto' value='".kanskje($medlemmer, 'foto')."' />
						<p><label><input type='checkbox' name='begrenset' value='1' ".$begrensetChecked." /> Vises kun for innloggede</label></p>
					</td>
				</tr>
				<tr>
				<td></td>
				<td class='right'>
					<a href='?side=medlem/liste'>Avbryt</a>
					<input type='submit' name='endreMedlem' value='Lagre'>
				</td>
				</tr>
				</table>
			<input type='hidden' name='id' value='".kanskje($medlemmer, 'medlemsid')."'>
			
		</form> 
	";
?>
	