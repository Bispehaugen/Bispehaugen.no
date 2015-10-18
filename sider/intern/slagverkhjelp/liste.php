<?php

include_once "sider/intern/slagverkhjelp/funksjoner.php";

if (!er_logget_inn()) {
	header('Location: index.php');
	die();
}

function formater_gruppe($gruppeid, $medlemmer, $redigeringsmodus) {
	$html .= "<section class='gruppe med-leder' ";
	if($redigeringsmodus) {
		$html .= "data-gruppe-id='".$gruppeid."' data-drop-effect='all'";
	}
	$html .= ">";
	$html .=  "<h2>Gruppe " . $gruppeid . "</h2>";
	$html .=  "<ul class='hjelpere'>";
	foreach($medlemmer as $hjelper) {
		$html .=  "<li class='hjelper'";
		if($redigeringsmodus) {
			$html .= " draggable='true' data-medlemsid='".$hjelper['medlemsid']."'";
		}
		$html .= ">";
		if($redigeringsmodus) {
			$html .= $hjelper['fnavn'] . " " . $hjelper['enavn'];
		} else {
			$html .= brukerlenke($hjelper, Navnlengde::FulltNavn, false);
		}
		$html .= (($hjelper['hengerfeste']==1 && tilgang_endre()) ? " <span class='hengerfeste' title='Har tilgang på bil med hengerfeste'>(H)</span>":"")
		. "</li>";
	}
	$html .=  "</ul>";
	$html .=  "</section>";
	return $html;
}

$grupper = hent_slagverkhjelp();
$redigeringsmodus = tilgang_endre();

if ($redigeringsmodus) {
	function hent_ut_medlemsid_for_medlemmer_i_grupper($carry, $gruppe)
	{
		foreach($gruppe as $medlemid => $ignore) {
	    	$carry[$medlemid] = $medlemid;
		}
	    return $carry;
	}

	$gruppeIder = range(1, max(1, max(array_keys($grupper)))); // Lag gruppeIder for alle grupper mellom 1 og høyeste id
	$plasserteMedlemsIder = array_reduce($grupper, "hent_ut_medlemsid_for_medlemmer_i_grupper", Array());

	$filtrer_bort_plasserte_medlemmer = function($medlem) use ($plasserteMedlemsIder) {
		return !array_key_exists($medlem['medlemsid'], $plasserteMedlemsIder);
    };

	$uplasserteMedlemmer = array_filter(hent_medlemmer(), $filtrer_bort_plasserte_medlemmer);

?>
<script type='text/javascript'>
	var endredeBrukereErIGruppe = {};
	var endredeBrukerSomErLeder = {};
	var eksisterendeGrupper = [<?php echo join(',', $gruppeIder); ?>];
</script>

<?php } // $redigeringsmodus ?>
<link rel="stylesheet" href="css/slagverkhjelp.css" type="text/css" />
<article class='slagverkhjelp<?php echo $redigeringsmodus ? " rediger":""; ?>'>

<h1>Slagverkbæregrupper</h1>
<p>Se <a href='?side=forside'>hovedsiden</a> for å se neste gang du skal bære og hvilken gruppe du hører til</p>
<p>
	<span class='gruppeleder'>GL = Gruppeleder</span>
	<span class='hengerfeste'>H = Har tilgang til bil med hengefeste</span>
</p>
<?php 
if ($redigeringsmodus) {
?>
<p>Klikk og dra navn for å endre slagverkgruppe. Alle uten gruppe finner du i den nederste boksen.</p>
<button class='button button-border lagre' disabled=disabled>Status: <i class='status-ikon fa'></i><span class='status'>Gjør dine endringer</span></button>

<?php 
} // $redigeringsmodus
?>
<div class='clearfix'></div>

<section class='slagverkgrupper'>

<?php

if ($redigeringsmodus) {
	foreach($gruppeIder as $gruppeid) {
		if(in_array($gruppeid, $gruppeIder)) {
			echo formater_gruppe($gruppeid, $grupper[$gruppeid], $redigeringsmodus=true);
		} else {
			echo formater_gruppe($gruppeid, Array(), $redigeringsmodus=true);
		}
	}
} else {
	foreach($grupper as $gruppeid => $gruppe) {
		echo formater_gruppe($gruppeid, $grupper[$gruppeid], $redigeringsmodus=false);
	}
}

if ($redigeringsmodus) {
?>
	<section class='gruppe add-gruppe button'>
		<i class='ikon fa fa-plus'></i>
		<p>Legg til ny gruppe</p>
	</section>

	<section class='gruppe uplasserte' data-gruppe-id='uplasserte' data-drop-effect='all'>
		<h2>Medlemmer uten gruppe</h2>
		<ul class='hjelpere'>
		<?php
		if(empty($uplasserteMedlemmer)) {
			echo "<li>Ingen. Alle er tildelt gruppe :)</li>";
		} else {
			foreach($uplasserteMedlemmer as $hjelper) {
				echo "<li class='hjelper' draggable='true' data-medlemsid='".$hjelper['medlemsid']."'>" 
				. $hjelper['fnavn'] . " " . $hjelper['enavn']
				. "</li>";
			}
		}
		?>
		</ul>
		</section>
	</section>
</article>

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
	var gruppeId = _.max(eksisterendeGrupper) + 1;
	var template = <?php echo "\"".formater_gruppe("{{gruppeId}}", Array(), $redigeringsmodus)."\""; ?>;

	var nyGruppeHtml = template.replace(/{{gruppeId}}/g, gruppeId);

	$(".add-gruppe").before(nyGruppeHtml);
	eksisterendeGrupper.push(gruppeId);

	addEventlistenerToGroup($(".gruppe[data-gruppe-id='" + gruppeId+ "']")[0]);
}

var lagreSlagverkoppsett = function () {
	statusIkon("fa-refresh", "Lagrer");
	lagreknappStatus(false);

	var data = {
		'endredeBrukereErIGruppe': endredeBrukereErIGruppe,
		'endredeBrukerSomErLeder': endredeBrukerSomErLeder
	};

	$.post("sider/intern/slagverkhjelp/lagre.php", data)
		.success(function(message){
			console.log("lagret", message);
			statusIkon("fa-check", "Lagret");
			lagreknappStatus(true);

			// Endringer lagret, tøm buffer
			endredeBrukerSomErLeder = {};
			endredeBrukereErIGruppe = {};

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

function settInnElementIKorrektPosisjonIListen(e, gruppeEl, drattMedlemEl) {
	var medlemsidForDrattElement = $(drattMedlemEl).data("medlemsid");
	var hjelpere = gruppeEl.find(".hjelper").filter(function(index, hjelper) {
		return $(hjelper).data("medlemsid") != medlemsidForDrattElement;
	});
	var top = e.pageY;
	var hjelpereEl =  gruppeEl.find(".hjelpere");

	if (hjelpere && hjelpere.length > 1) {
		for(var i = 1; i<hjelpere.length; i++) {
			var hjelper = $(hjelpere.get(i));
			var hjelper_top = hjelper.position().top + (hjelper.height()/2);
			var forrige_hjelper = $(hjelpere.get(i-1));
			var forrige_hjelper_top = forrige_hjelper.position().top + (forrige_hjelper.height()/2);

			if (forrige_hjelper_top < top && top < hjelper_top) {
				// Dratt element skal mellom i-1 og i
				forrige_hjelper.after(drattMedlemEl);
				return false;
			}

			if(top < forrige_hjelper_top) {
				// Dratt element er høyere opp enn første hjelper, legg øverst i lista som leder
				hjelpereEl.prepend(drattMedlemEl);
				return true;
			}
		}
	}
	if(hjelpere && hjelpere.length == 1) {
		var hjelper = $(hjelpere.get(i));
		var hjelper_top = hjelper.position().top + (hjelper.height()/2);
		if(top < hjelper_top) {
			// Dratt element er høyere opp enn første hjelper, legg øverst i lista som leder
			hjelpereEl.prepend(drattMedlemEl);
			return true;
		}
	}
	hjelpereEl.append(drattMedlemEl);
	return hjelpere.length == 0;  // Bare leder hvis gruppen var tom
}

function handleDragOver(e) {
	if (e.stopPropagation) {
		e.preventDefault(); // Stops some browsers from redirecting.
	}
	if (e.stopPropagation) {
		e.stopPropagation(); // Stops some browsers from redirecting.
	}

	if (drattMedlemEl != null) {

		if (drattMedlemEl != this) {
			// Endre gruppe for medlem
			var gruppeEl = $(this);


			var brukerId = parseInt(drattMedlemEl.dataset.medlemsid, 10);
			var gruppeId = parseInt(gruppeEl.data("gruppe-id"), 10);

			var innsattSomLeder = settInnElementIKorrektPosisjonIListen(e, gruppeEl, drattMedlemEl);

			if (!_.isNaN(brukerId) && !_.isNaN(gruppeId)) {
				endredeBrukereErIGruppe[brukerId] = gruppeId;
				if (innsattSomLeder) {
					endredeBrukerSomErLeder[gruppeId] = brukerId;
				}
			}
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
<?php
} // $redigeringsmodus