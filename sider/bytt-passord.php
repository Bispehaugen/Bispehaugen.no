<?php

$token = "";
$feilmeldinger = Array();
$token_allerede_brukt = false;

if (!has_post("token") && !has_get("token")) {
	die();
}

if (has_post("token")) {

	$unhashedPassword = post("passord");
	if (empty($unhashedPassword)) {
		$feilmeldinger[] = "Passordet kan ikke være tomt";
	} else if (strlen($unhashedPassword) < 8) {
		$feilmeldinger[] = "Passordet må ha mer enn 7 tegn";
	} else if ($unhashedPassword != post("gjenta_passord")) {
		$feilmeldinger[] = "Passordene er ikke like";
	} else {
		
		$token = post("token");
		$passord = password_hash($unhashedPassword, $passord_algo);
		
		$sql_update = "UPDATE medlemmer SET bytt_passord_token = NULL, passord = '".$passord."' WHERE bytt_passord_token = '".$token."' LIMIT 1";
		
		mysql_query($sql_update);
		
		header("Location: index.php?side=forside");
		die();
	}
	$fornavn = post("fornavn");
}

if (has_get("token")) {
	
	$token = get("token");
	
	$sql = sprintf("SELECT medlemsid, fnavn, enavn, email FROM medlemmer WHERE bytt_passord_token = '%s' LIMIT 1", $token);
	$query = mysql_query($sql);
	
	$token_allerede_brukt = true;

	while($b=mysql_fetch_assoc($query)) {
		$fornavn = $b['fnavn'];
		$token_allerede_brukt = false;
	};
}

?>

<h2>Skift passord</h2>
<?php if ($token_allerede_brukt) {
	echo "<p>Denne lenken er brukt før eller ikke gyldig, prøv å bruke glemt passord på nytt eller ta kontakt med <a href='mailto:webkom@bispehaugen.no'>webkom</a>.</p>";
} else {
?>

<p>Hei <?php echo $fornavn; ?></p>

<?php echo feilmeldinger($feilmeldinger); ?>

<form action="?side=bytt-passord" method="POST">
	<input type="hidden" name="token" value="<?php echo $token; ?>" />
	<input type="hidden" name="fornavn" value="<?php echo $fornavn; ?>" />
	<table>
	    <tr>
	      <td class="label"><label for="passord">Nytt passord</label></td>
	      <td><input type="password" name="passord" /></td>
	    </tr>
	    <tr>
	      <td class="label"><label for="gjenta_passord">Gjenta passord</label></td>
	      <td><input type="password" name="gjenta_passord" /></td>
	    </tr>
	    <tr>
	      <td colspan=2><p class="right"><input type="submit" value="Bytt" /></p></td>
	    </tr>
   </table>
</form>

<?php
}
