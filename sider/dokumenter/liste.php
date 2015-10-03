<?php

include_once("sider/dokumenter/funksjoner.php");

if(!er_logget_inn()) {
	header('Location: ?side=ikke_funnet');
	die();
}

$foreldreId = intval(has_get('mappe')) ? get('mappe') : 0;
if(empty($foreldreId)) {
	$foreldreId = 0;
}

?>
<script type="text/javascript">
function slett_fil(id, navn) {
	var skalSlette = confirm("Er du sikker du vil slette filen: "+navn);
	if (skalSlette) {
		$.post( "sider/dokumenter/slett.php?fil="+id, function() {
			location.reload();
		})
		.fail(function(data) {
		    alert(data.responseJSON.message);
		});
	}
}
function slett_mappe(id, navn) {
	var skalSlette = confirm("Er du sikker du vil slette mappen: "+navn);
	if (skalSlette) {
		$.post( "sider/dokumenter/slett.php?mappe="+id, function() {
			location.reload();
		})
		.fail(function(data) {
		    alert(data.responseJSON.message);
		});
	}
}
function open_new_folder() {
	$(".add-folder").toggle();
	$(".add-folder .navn").focus();
}
function open_new_files() {
	$(".add-files").toggle();
}
function close_add() {
	$(".add-files-and-folder").hide();
}
</script>
<?php

echo "<section class='dokumenter'>";

echo "<header class='header'>";
$tittel = "Dokumenter";

if ($foreldreId > 0) {
	$foreldremappe = hent_mappe($foreldreId);
	$tittel = $foreldremappe['tittel'];
}

echo "<h2 class='overskrift'><i class='fa fa-folder-open-o'></i> " . $tittel . "</h2>";

formater_tilbakeknapp($foreldremappe, $foreldreId > 0);

formater_ny_knapp($foreldreId, "mappe", "open_new_folder");
formater_ny_knapp($foreldreId, "filer", "open_new_files");

echo "</header>";

if (tilgang_endre()) {
	formater_legg_til_ny_mappe($foreldreId);
	formater_legg_til_nye_filer($foreldreId);
}

echo "<section class='mapper'>";

$mapper = hent_undermapper($foreldreId);

$filer = hent_filer($foreldreId);

$dine_komiteer = hent_komiteer_for_bruker();

$antall_mapper_og_filer = 0;

foreach($mapper as $mappe ) {
	$komiteid = $mappe['komiteid'];
	if (is_null($komiteid) || tilgang_full() || in_array($komiteid, $dine_komiteer)) {
		formater_mappe($mappe);
		$antall_mapper_og_filer++;
	}
}

foreach($filer as $fil) {
	$komiteid = $mappe['komiteid'];
	if (is_null($komiteid) || tilgang_full() || in_array($komiteid, $dine_komiteer)) {
		formater_fil($fil);
		$antall_mapper_og_filer++;
	}
}
echo "</section>";

if ($antall_mapper_og_filer == 0) {
	echo "<h3>Denne mappen er tom.</h3>";
}
echo "</section>";


if (tilgang_endre()) {
?>
<script src="vendor/Flow/flow.js"></script>

<script>

var dropzone = $('.dropzone');
var lastOppKnapp = $(".last-opp");
var filliste = $(".filelist");
var statusElement = $(".status");
var intervalId;

function dropHover() {
	dropzone.addClass("file-hover");
}

function dropDropped() {
	dropzone.removeClass("file-hover");
}

function showStatusbar(flow, statusElement) {

	intervalId = window.setInterval(updateStatusbar, 250, flow, statusElement)
}

function updateStatusbar(flow, statusElement) {
	var progress = Math.round(flow.progress() * 10000)/100;

	statusElement.show();
	statusElement.find('.bar').css('width', progress + '%');

	if (progress == 100) {
		statusElement.hide();
		filliste.html("");
		window.clearInterval(intervalId);
		location.reload();
	}
}

function disableUploadButton() {
	lastOppKnapp.prop("disabled", "disabled");
}

var flow = new Flow({
  target:'sider/dokumenter/nye-filer.php',
  singleFile: false,
  query: {
  	foreldreId: '<?php echo $foreldreId; ?>'
  }
});
Flow.prototype.assignDrop = function (domNodes) {
	domNode = domNodes[0];
	domNode.addEventListener('dragover', this.preventEvent, false);
	domNode.addEventListener('dragenter', dropHover, false);
	domNode.addEventListener('dragleave', dropDropped, false);
	domNode.addEventListener('drop', dropDropped, false);
	domNode.addEventListener('drop', this.onDrop, false);
};


flow.assignBrowse($('.velg-filer'));
flow.assignDrop(dropzone);

flow.on('fileAdded', function(fileinfo, message){
	disableUploadButton();

	var name = fileinfo.name;
	if (name.indexOf(".") == 0) {
		console.log("Ikke legg til filer som starter med .");
		return false;
	}

	filliste.append("<li data-id='" + fileinfo.uniqueIdentifier + "'>" + name + "</li>");
});

flow.on('filesSubmitted', function(array, message){
	lastOppKnapp.removeAttr("disabled");
});

flow.on('fileSuccess', function(file,message){
	var item = filliste.find("[data-id='"+file.uniqueIdentifier+"']");
	item.append(" <i class='fa fa-check suksess'></i>");
});
flow.on('fileError', function(file, message){
	if (intervalId) {
		window.clearInterval(intervalId);
	}
	// Vis feilmelding
	alert("Oppdateringen av filer feilet dessverre. Webkom er varslet og vil se på saken.");
});

lastOppKnapp.click(function() {
	flow.upload();

	showStatusbar(flow, statusElement);
});
</script>

<?php
}