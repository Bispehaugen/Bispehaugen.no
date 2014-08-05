<section id="dropTarget">
	
	<h1>Filer som lastes opp:</h1>
	<ul class="filliste"></ul>
	<input type="file" id="browseButton" value"Browse" /><br />
	<button onclick="javascript:flow.upload()">Last opp</button>
</section>

<script src="vendor/Flow/flow.js"></script>
<script src="js/jquery-1.10.2.min.js"></script>

<script>

var flow = new Flow({
  target:'upload.php', 
  query:{upload_token:'my_token'}
});
flow.assignBrowse(document.getElementById('browseButton'));
flow.assignDrop(document.getElementById('dropTarget'));

var filliste = $(".filliste");

flow.on('fileAdded', function(file, event){
	console.log("fileAdded");
	console.log(file, event);
	
	filliste.append("<li>"+file.name+"</li>");
});
flow.on('fileSuccess', function(file,message){
	console.log("fileSuccess");
	console.log(file,message);
	filliste.html("");
});
flow.on('fileError', function(file, message){
	console.log("fileError");
	console.log(file, message);
});
</script>

<?php