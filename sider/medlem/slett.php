<?php
global $dbh;

if (has_get('id')) {
	$id = get('id');
} elseif(has_post('id')){
	$id = post('id');
} else {
	header('Location: ?side=medlem/liste');
	die('Ugyldig ID');
}

if (!tilgang_endre()) {
	header('Location: ?side=medlem/vis?id=$id');
	die('Du har ikke tilgang til å slette medlemmer');
}

$medlem = hent_brukerdata($id);

if (has_post('id')) {
	$foto = $medlem['foto'];

	if ($foto != null && file_exists($foto)) {
		unlink($foto);
	}

	$sql="
	UPDATE
		medlemmer
	SET
		fnavn = 'Slettet',
		enavn = '',
		status = 'Slettet',
		fdato = null,
		startetibuk = null,
		sluttetibuk = null,
		startetibuk_date = null,
		sluttetibuk_date = null,
		andreinstr = null,
		verv = null,
		adresse = '',
		postnr = '0000',
		poststed = '',
		bosted = null,
		tlfprivat = null,
		tlfmobil = null,
		tlfarbeid = null,
		http = null,
		email = null,
		msn = null,
		beskrivelsesdok = null,
		bakgrunn = null,
		studieyrke = null,
		utdanning = null,
		kommerfra = null,
		ommegselv = null,
		avatar = null,
		foto = null,
		begrenset = 1,
		hengerfeste = 0,
		bil = 0,
		grleder = 0,
		brukernavn = null,
		passord = null,
		bytt_passord_token = null,
		rettigheter = 0
	WHERE
		medlemsid = ?;
	";
	$stmt = $dbh->prepare($sql);
	$stmt->execute(array($id));

	header('Location: ?side=medlem/liste');
	die();
}

$navn = "{$medlem['fnavn']} {$medlem['enavn']}";

?>

<h2>Slett medlem</h2>
<p>
    Er du sikker på at du vil slette <?php echo($navn); ?>? Denne operasjonen kan ikke reverseres.
</p>
<br />

<form method='post'>
    <input type='hidden' name='id' value='<?php echo($id); ?>'>

    <input type='submit' name='slettMedlem' value='Slett'>

    <a href='?side=medlem/vis&id=<?php echo($id); ?>' style='margin: auto 4em auto 4em'>
        Avbryt
    </a>
</form>
