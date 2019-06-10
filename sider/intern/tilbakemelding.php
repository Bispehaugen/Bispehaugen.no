<?php
$feilmeldinger = Array();

if (has_post('send')) {
    if (post('tittel') == "") {
	$feilmeldinger[] = "Du må skrive en tittel";
    }
    if (post('innhold') == "") {
	$feilmeldinger[] = "Du må skrive en tilbakemelding";
    }

    if (empty($feilmeldinger)) {
	$tittel = post('tittel');
	$innhold = post('innhold');

	$from = "From: Bispehaugen.no<ikke-svar@bispehaugen.no>";
	$to = 'tilbakemelding@bispehaugen.no';
	$realfrom_tmp = getenv("REMOTE_HOST") ? getenv("REMOTE_HOST") : getenv("REMOTE_ADDR");
	$realfrom = "Real-From: $realfrom_tmp";
	$subject = "Tilbakemelding - $tittel";

	$message = "<pre>$innhold</pre><br><br>Dette er en anonym tilbakemelding. Du kan ikke svare på denne eposten.<br>";

	$header="$from\r\n$realfrom\r\n$content_type";
	if(epost($to, $replyto, $subject, $message)) {
	    header('Location: ?side=forside');
	    die();
	} else {
	    $feil_under_sending_av_mail = true;
	}
    }
}
?>

<h2>Anonym tilbakemelding</h2>
<p>Her kan du gi anonym tilbakemelding til styret.</p>

<?php echo feilmeldinger($feilmeldinger); ?>

<form method='post' action=''>
    <table>
	<tr>
	    <td><span>Tittel:</span></td>
	    <td><input type='text' name='tittel'></td>
	</tr>
	<tr>
	    <td><span>Innhold:</span></td>
	    <td><textarea name='innhold'></textarea></td>
	</tr>
	<tr>
	    <td></td>
	    <td class='right'>
		<input type='submit' name='send' value='Send'>
	    </td>
	</tr>
    </table>
</form>
