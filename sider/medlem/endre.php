<?php 
//TODO Bilde, sjekk på at alle obligatoriske felter er fyllt ut, hvis man endrer seg selv autogenerer mail til webkom/sekretær
	
	//funksjonalitet
	$id = get('id');
	
	//sjekker om man er admin eller prøver å endre seg selv
	if($_SESSION['medlemsid']==$id){
		$endre_seg_selv=1;//brukes for å sende mail til sekretær ved endring
	}
	elseif($_SESSION['rettigheter']<2){
		header('Location: ?side=medlem/liste');
	};
	
	//henter ut alle instrumenter
		$sql="SELECT instrument, posisjon, instrumentid FROM instrument ORDER BY posisjon";
		$instrumenter=hent_og_putt_inn_i_array($sql, $id_verdi='posisjon');

	//hvis et medlem er lagt inn og noen har trykket på lagre hentes verdiene ut
	if(isset($_POST['id']) || isset($_POST['fnavn'])){
		$medlemsid=post('id');
		$fnavn=post('fnavn');
		$enavn=post('enavn');
		$instrument=post('instrument');		
		$instnr=post('instnr');		
		$grleder=post('grleder');
			if($grleder==1){
				$sql="SELECT medlemsid FROM medlemmer WHERE grleder=1 AND instnr=".$instrument.";";
				$mysql_result=mysql_query($sql);
				$medl=mysql_fetch_array($mysql_result);
				$sql2="UPDATE medlemmer SET grleder=0 WHERE medlemsid=".$medl['medlemsid']." AND instrument=".$instrument.";";
			}

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
			
			echo "loop1".$sql;
			mysql_query($sql);
			//header('Location: ?side=medlem/liste');
		}else{
			die("WRONG");			
			$sql="
			INSERT INTO 
			medlemmer (fnavn, enavn, fdato, status, instrument, instnr, grleder, adresse, postnr, poststed, tlfmobil, 
				email, bakgrunn, startetibuk_date, sluttetibuk_date, studieyrke, kommerfra, ommegselv, foto, begrenset)
			values ('$fnavn','$enavn','$fdato','$status','$instrument','$instnr','$grleder','$adresse','$postnr','$poststed','$tlfmobil',
				'$email','$bakgrunn','$startetibuk','$sluttetibuk','$studieyrke','$kommerfra','$ommegselv','$foto','$begrenset')";
			mysql_query($sql);
			//header('Location: ?side=medlem/liste');
		}
		};
	//henter valgte medlem fra databasen hvis "endre"
	if(has('id')){	
		$medlemmer = hent_brukerdata($id);
	} else {
		$medlemmer = hent_brukerdata();
	}
	$gyldige_statuser = Array("Aktiv", "Permisjon", "Sluttet");
	
	//printer ut skjema med forh�ndsutfylte verdier hvis disse eksisterer
		
	echo "
    <script>
    $(function() {
        $('.datepicker').datepicker({ dateFormat: 'yy-mm-dd' }).val();;
    });
    </script>
		<form method='post' action='?side=medlem/endre'>
			<table>
				<th>Rediger medlem</th><th></th>
				<tr><td>Gruppeleder:</td><td><input type='checkbox' name='grleder' value='true' ";
				if(isset($medlemmer['grleder']) && $medlemmer['grleder']) echo "checked";	
				echo "'></td></tr>
				<tr><td>Fornavn:</td><td><input type='text' name='fnavn' value='".$medlemmer['fnavn']."'></td></tr>
				<tr><td>Etternavn:</td><td><input type='text' name='enavn' value='".$medlemmer['enavn']."'></td></tr>
				<tr><td>status:</td><td>
					<select name='status'>
					";
					
					foreach($gyldige_statuser as $status) {
						echo "<option value='$status' ";
						if($medlemmer['status']=="$status") {
							echo "selected=selected";
						} 
						echo ">".$status."</option>";
					}
					echo "
					</select></td></tr>
				<tr><td>Instrument:</td><td>
					<select name='instnr'>";
					foreach($instrumenter as $instrument){
							echo"
  							<option value='".$instrument['instrumentid']." ";
  							if($instrument['instrument']==$medlemmer['instrument']){
  								echo "selected";
  							};
  							echo ">".$instrument['instrument']."</option>";
					};

					echo "<input type='hidden' name='instrument' value='".$instrument['instrument']."'></select></td></tr>
				<tr><td>Fødselsdato:</td><td><input type='text' class='datepicker' name='fdato' value='".$medlemmer['fdato']."'></td></tr>
				<tr><td>Adresse:</td><td><input type='text' name='adresse' value='".$medlemmer['adresse']."'></td></tr>
				<tr><td>Postnr:</td><td><input type='text' name='postnr' value='".$medlemmer['postnr']."'></td></tr>
				<tr><td>Poststed:</td><td><input type='text' name='poststed' value='".$medlemmer['poststed']."'></td></tr>
				<tr><td>Mobilnummer:</td><td><input type='text' name='tlfmobil' value='".$medlemmer['tlfmobil']."'></td></tr>
				<tr><td>E-post:</td><td><input type='text' name='email' value='".$medlemmer['email']."'></td></tr>
				<tr><td>Musikalsk bakgrunn:</td><td><input type='text' name='bakgrunn' value='".$medlemmer['bakgrunn']."'></td></tr>
				<tr><td>Startet i BUK:</td><td><input type='text' class='datepicker' name='startetibuk_date' value='".$medlemmer['startetibuk_date']."'></td></tr>
				<tr><td>Sluttet i BUK:</td><td><input type='text' class='datepicker' name='sluttetibuk_date' value='".$medlemmer['sluttetibuk_date']."'></td></tr>
				<tr><td>Studie/yrke:</td><td><input type='text' name='studieyrke' value='".$medlemmer['studieyrke']."'></td></tr>
				<tr><td>Kommer fra:</td><td><input type='text' name='kommerfra' value='".$medlemmer['kommerfra']."'></td></tr>
				<tr><td>Litt om meg selv:</td><td><textarea name='ommegselv'>".$medlemmer['ommegselv']."</textarea></td></tr>
				<tr><td>Bilde:</td><td><input type='text' name='foto' value='".$medlemmer['foto']."'></td></tr>
				<tr><td>Vises kun for innloggede:</td><td><input type='checkbox' name='begrenset' value='true' ";
				if (isset($medlemmer['begrenset']) && $medlemmer['begrenset']) echo "checked";
				echo "'></td></tr>
				</table>
			<input type='hidden' name='id' value='".$medlemmer['medlemsid']."'>
			<a href='?side=medlem/liste'>Avbryt</a>
			<input type='submit' name='endreMedlem' value='Lagre'>
		</form> 
	";
?>
	