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
        disableUploadButton();
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
    if (domNodes && domNodes.length > 0) {
        domNode = domNodes[0];
        domNode.addEventListener('dragover', this.preventEvent, false);
        domNode.addEventListener('dragenter', dropHover, false);
        domNode.addEventListener('dragleave', dropDropped, false);
        domNode.addEventListener('drop', dropDropped, false);
        domNode.addEventListener('drop', this.onDrop, false);
    }
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