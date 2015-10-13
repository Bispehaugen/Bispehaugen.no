<?php

include_once "sider/intern/slagverkhjelp/funksjoner.php";

if (!er_logget_inn()) {
	header('Location: index.php');
	die();
}

function formater_gruppe($gruppeid, $medlemmer) {
	$html .= "<section class='gruppe' data-gruppe-id='".$gruppeid."' data-drop-effect='all'>";
	$html .=  "<h2>Gruppe " . $gruppeid . "</h2>";
	$html .=  "<ul class='hjelpere'>";
	foreach($medlemmer as $hjelper) {
		$html .=  "<li class='hjelper' draggable='true' data-medlemsid='".$hjelper['medlemsid']."'>" . $hjelper['fnavn'] . " " . $hjelper['enavn'] . "</li>";
	}
	$html .=  "</ul>";
	$html .=  "</section>";
	return $html;
}

$grupper = hent_slagverkhjelp();

$gruppeIder = array_keys($grupper);

echo "
<script type='text/javascript'>
	var endredeBrukereErIGruppe = {};
	var eksisterendeGrupper = [".join(',', $gruppeIder)."];
</script>
";

echo "<article class='slagverkhjelp'>";

echo "<h1>Slagverkbæregrupper</h1>";
echo "<p>Se <a href='?side=forside'>hovedsiden</a> for å se neste gang du skal bære og hvilken gruppe du hører til</p>";
echo "<p>Klikk og dra navn for å endre slagverkgruppe. Alle uten gruppe finner du i den nederste boksen.</p>";
echo "<button class='button button-border lagre' disabled=disabled>Status: <i class='status-ikon fa'></i><span class='status'>Gjør dine endringer</span></button>";

echo "<div class='clearfix'></div>";

echo "<section class='slagverkgrupper'>";

for($i = 1; $i < 10; $i++) {
	if(!in_array($i, $gruppeIder)) {
		echo formater_gruppe($i, Array());
	}
}

// MÅ SORTERES INN I LISTA... FYLL INN BARE MANGLENDE MELLOM TOPP OG BUNN

foreach($grupper as $gruppeid => $gruppe) {
	echo formater_gruppe($gruppeid, $gruppe);
}

echo "<section class='gruppe add-gruppe button'>
	<i class='ikon fa fa-plus'></i>
	<p>Legg til ny gruppe</p>
</section>";

echo "</section>";
echo "</article>";

?>
<script type="text/javascript">

function addEventlistenerToGroup(gruppe) {
	gruppe.addEventListener('dragover', handleDragOver, false);
	gruppe.addEventListener('drop', handleDrop, false);
}

var statusIkon = function(ikon, text) {
	var lagreknapp = $(".lagre");

	lagreknapp.find(".status").text(text);

	lagreknapp.find(".status-ikon")
		.removeClass("fa-refresh fa-check fa-error")
		.addClass(ikon);
}

var lagreknappStatus = function(isDisabled) {
	var lagreknapp = $(".lagre");
	if (isDisabled) {
		lagreknapp.prop("disabled", "disabled");
	} else {
		lagreknapp.removeAttr("disabled");
	}
}

var leggTilNyGruppe = function() {
	console.log(eksisterendeGrupper);
	var gruppeId = 1;
	while(eksisterendeGrupper.indexOf(gruppeId) != -1) {
		console.log("fant gruppeId: "+gruppeId);
		gruppeId++;

		if(gruppeId > 30) {
			break;
		}
	}

	var nyGruppeHtml = "<section class='gruppe' data-gruppe-id='" + gruppeId + "' data-drop-effect='all'>";
	nyGruppeHtml += "<h2>Gruppe " + gruppeId + "</h2>";
	nyGruppeHtml += "<ul class='hjelpere'>";
	nyGruppeHtml += "</ul>";
	nyGruppeHtml += "</section>";

	$(".add-gruppe").before(nyGruppeHtml);
	eksisterendeGrupper.push(gruppeId);

	addEventlistenerToGroup($(".gruppe[data-gruppe-id='" + gruppeId+ "']")[0]);
}

var lagreSlagverkoppsett = function () {
	statusIkon("fa-refresh", "Lagrer");
	lagreknappStatus(false);
	var data = {'endredeBrukereErIGruppe': endredeBrukereErIGruppe};

	$.post("sider/intern/slagverkhjelp/lagre.php", data)
		.success(function(message){
			console.log("lagret", message);
			statusIkon("fa-check", "Lagret");
			lagreknappStatus(true);

			_.delay(statusIkon, 2000, "", "Gjør dine endringer");
		})
		.fail(function(error) {
			console.log("Feeeeil", error);
			statusIkon("fa-error", "Feilet - Klikk for å lagre på nytt");
		});
};

// Bare lagre slagverkoppsett hvis det er ett sekund siden forrige flytt
var lazyLagreSlagverkoppsett = _.debounce(lagreSlagverkoppsett, 1000);

var drattMedlemEl = null;

function handleDragStart(e) {
	drattMedlemEl = this;

	e.dataTransfer.effectAllowed = 'move';
	e.dataTransfer.setData('text/html', this.innerHTML);
}

function handleDragOver(e) {
	if (e.stopPropagation) {
		e.preventDefault(); // Stops some browsers from redirecting.
	}
	if (e.stopPropagation) {
		e.stopPropagation(); // Stops some browsers from redirecting.
	}

	if (drattMedlemEl != this && drattMedlemEl != null) {
		var gruppeEl = $(this);
		gruppeEl.find(".hjelpere").append(drattMedlemEl);

		var brukerId = parseInt(drattMedlemEl.dataset.medlemsid, 10);
		var gruppeId = parseInt(gruppeEl.data("gruppe-id"), 10);
		
		if (!_.isNaN(brukerId) && !_.isNaN(gruppeId)) {
			endredeBrukereErIGruppe[brukerId] = gruppeId;
		}
	}
  	return false;
}

function handleDrop(e) {
	lazyLagreSlagverkoppsett();
    return true;
}

window.addEventListener('load',function(){

	var grupper = document.querySelectorAll('.slagverkgrupper .gruppe');
	var hjelpere = document.querySelectorAll('.slagverkgrupper .gruppe .hjelper');

	[].forEach.call(hjelpere, function(hjelper) {
		hjelper.addEventListener('dragstart', handleDragStart, false);
	});

	[].forEach.call(grupper, function(gruppe) {
		addEventlistenerToGroup(gruppe);
	});


	$(".lagre").click(lagreSlagverkoppsett);
	$(".add-gruppe").click(leggTilNyGruppe);

});
</script>