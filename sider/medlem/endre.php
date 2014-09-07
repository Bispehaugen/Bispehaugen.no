<?php 
//TODO Bilde, sjekk på at alle obligatoriske felter er fyllt ut, hvis man endrer seg selv autogenerer mail til webkom/sekretær

	global $opprett_ny_medlem;
	
	//funksjonalitet

	if (has_get('id')) {
		$id = get('id');
	} elseif(has_post('id')){
		$id = post('id');
	} else {
		if(isset($_SESSION['medlemsid'])) {
			$id = $_SESSION['medlemsid'];
		} else {
			die("Du må logge inn");
		}
	}
	
	$bruker = hent_brukerdata($id);
	
	//sjekker om man er admin eller prøver å endre seg selv
	if($_SESSION['medlemsid']==$id){
		$endre_seg_selv=1;//brukes for å sende mail til sekretær ved endring
	}
	elseif($_SESSION['rettigheter']<3){
		header('Location: ?side=medlem/liste');
		die();
	};
	
	//henter ut alle instrumenter
	$sql="SELECT instrument, posisjon, instrumentid FROM instrument ORDER BY posisjon";
	$instrumenter=hent_og_putt_inn_i_array($sql, $id_verdi='posisjon');
	
	$feilmeldinger = Array();
	//hvis et medlem er lagt inn og noen har trykket på lagre hentes verdiene ut
	if(has_post('id') || has_post('fnavn')){
		$medlemsid=post('id');
		$fnavn=post('fnavn');
		$enavn=post('enavn');
		$brukernavn=post('brukernavn');
		$instnr=post('instnr');		
		$grleder=post('grleder');
		$status=post('status');
		$fdato=date("Y-m-d", strtotime(post('fdato')));
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
		$foto = post('foto');
		$begrenset=post('begrenset');
		if(has_post('rettigheter')){
			$rettigheter=post('rettigheter');
		}else if(isset($bruker['rettigheter'])){
			$rettigheter=$bruker['rettigheter'];
		}else{
			$rettigheter='0';
		};

		if(empty($fnavn) || empty($enavn)) {
			$feilmeldinger[] = "Navn må være fylt ut";
		} else if (empty($fdato) || strtotime($fdato) == 0) {
			$feilmeldinger[] = "Fødselsdato må være fylt ut";
		} else if (empty($adresse) || empty($poststed)) {
			$feilmeldinger[] = "Adresse og poststed må være fylt ut";
		} else if (empty($tlfmobil) || empty($email)) {
			$feilmeldinger[] = "Mobil og epost må være fylt ut";
		} else if (empty($fdato) && strtotime($fdato) > 0) {
			$feilmeldinger[] = "Fødselsdato må være fylt ut";
		}
		
		if (empty($feilmeldinger)) {
			
			if (empty($foto)) {
				$foto = $bruker['foto'];
			}
		
			if($grleder==1){
				$sql="SELECT medlemsid, instrument FROM medlemmer WHERE grleder=1 AND instnr='".$instnr."';";
				$mysql_result = mysql_query($sql);
				
				while ($medl = mysql_fetch_assoc($mysql_result)) {
					$sql2 = "UPDATE medlemmer SET grleder=0 WHERE medlemsid=".$medl['medlemsid']." AND instrument=".$medl['instrument'].";";
					mysql_query($sql2);
				}
			}
	
			//harEndretAdresse
			if (empty($bruker) || $bruker['adresse'] != $adresse || $bruker['fnavn'] != $fnavn || $bruker['enavn'] != $enavn ) {
				$message = "
Adresseendring:
$fnavn $enavn har endret adressen sin til:
$adresse
$postnr $poststed

Den gamle adressen var:
" . $bruker['adresse'] . "
" . $bruker['postnr'] . " " . $bruker['poststed'];
				
				$to = "webkom@bispehaugen.no";
				$replyto = $fnavn." ".$enavn." <".$email.">";
				$subject = "Bispehaugen.no - Adressendring";
				
				epost($to,$replyto,$subject,$message);
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
					instnr = '$instnr',
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
					begrenset = '$begrenset', 
					rettigheter = '$rettigheter'
				WHERE 
					medlemsid = '$medlemsid';
				";
				mysql_query($sql);
				header('Location: ?side=medlem/liste');
			}else{
				$sql="
				INSERT INTO 
				medlemmer (fnavn, enavn, fdato, status, instrument, instnr, grleder, adresse, postnr, poststed, tlfmobil, 
					email, bakgrunn, startetibuk_date, sluttetibuk_date, studieyrke, kommerfra, ommegselv, begrenset, rettigheter)
				values ('$fnavn','$enavn','$fdato','$status','$instrument','$instnr','$grleder','$adresse','$postnr','$poststed','$tlfmobil',
					'$email','$bakgrunn','$startetibuk','$sluttetibuk','$studieyrke','$kommerfra','$ommegselv','$begrenset','$rettigheter')";
				mysql_query($sql);
				
				header('Location: ?side=medlem/liste');
			}
		}
	}
	//henter valgte medlem fra databasen hvis "endre"
	if(has_get('id')||has_post('id')){	
		$medlemmer = hent_brukerdata($id);
	} else if ($opprett_ny_medlem) {
		$medlemmer = Array();
	} else {
		$medlemmer = hent_brukerdata();
	}
	$gyldige_statuser = Array("Aktiv", "Permisjon", "Sluttet", "Ubekreftet");
	$gyldige_rettigheter = Array("0", "1", "2", "3");
	$navn_gyldige_rettigheter = Array("Ikke tilgang", "Internsider", "Admin light", "Full admin");
	
	//printer ut skjema med forhåndsutfylte verdier hvis disse eksisterer
	
?>


<?php		
	echo "
    	<h2>".($opprett_ny_medlem ? "Nytt medlem" : "Rediger medlem")."</h2>";
    	
    	echo feilmeldinger($feilmeldinger);
    	
    	echo "
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
		
					echo "
				<tr><td>Fødselsdato:</td><td><input type='date' name='fdato' value='".kanskje($medlemmer, 'fdato')."'></td></tr>
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
				
				if (!$opprett_ny_medlem) {
				echo "
				<tr class='dropzone'>
					<td>Bilde:</td>
					<td>
						<div id='bytt-bilde'>
							<img src='".kanskje($medlemmer, 'foto')."' />
							<i class='ikon fa fa-edit'></i>
						</div>
						<p><label><input type='checkbox' name='begrenset' value='1' ".$begrensetChecked." /> Vises kun for innloggede</label></p>
					</td>
				</tr>
				";
				if(session('rettigheter')>2){
					echo"<tr><td>Rettigheter*:</td><td>
					<select name='rettigheter'>";
					foreach($gyldige_rettigheter as $rettighet){
						$selected = (kanskje($medlemmer, 'rettigheter')=="$rettighet") ? " selected=selected" : "";						
						echo"<option value='".$rettighet."'".$selected.">".$navn_gyldige_rettigheter[$rettighet]."</option>";
					}
					echo "</select><br>*admin light kan endre nyheter, endre aktiviteter og legge til dokumenter. Det kreves full admin for å se styre-forum/styre-dokumenter, endre medlemmer og tilganger.";
					};
				}
				
				echo "</td></tr>
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
	
if (!$opprett_ny_medlem) {
?>
<script src="vendor/Flow/flow.js"></script>

<script>

var flow = new Flow({
  target:'upload.php',
  singleFile: true,
  query: {
  	type:'profilbilde',
  	id: '<?php echo $id; ?>',
  	name: '<?php echo $bruker['fnavn']." ".$bruker['enavn']; ?>'
	}
});
flow.assignBrowse(document.getElementById('bytt-bilde'));
flow.assignDrop($('.dropzone'));

var preview = $("#bytt-bilde img");

flow.on('fileAdded', function(file, event){
	console.log("fileAdded");
	console.log(file, event);
	
	var reader = new FileReader();
	reader.onload = function (e) {
		preview.attr('src', e.target.result);
	}
	reader.readAsDataURL(file.file);
	
});

flow.on('filesSubmitted', function(array, event){
	flow.upload();
});

flow.on('fileSuccess', function(file,message){
	console.log("fileSuccess");
	console.log(file,message);
	
	flow.removeFile(file);
	console.log(flow.files);

	// update local image
});
flow.on('fileError', function(file, message){
	console.log("fileError");
	console.log(file, message);
	
	var oldPic = '<?php echo kanskje($medlemmer, 'foto'); ?>';
	preview.attr('src', oldPic);
	
	// Vis feilmelding
});
</script>
<?php }