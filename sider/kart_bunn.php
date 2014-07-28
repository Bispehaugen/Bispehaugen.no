<div id="map_canvas" class="map"></div>
	
<script>
function initialize() {
    var mapCanvas = document.getElementById('map_canvas');
    var latLong = new google.maps.LatLng(63.431466, 10.414018)
    var mapOptions = {
      center: latLong,
      zoom: 15,
      mapTypeId: google.maps.MapTypeId.ROADMAP
    };
    
    var map = new google.maps.Map(mapCanvas, mapOptions);
    
    var marker = new google.maps.Marker({
        position: latLong,
        map: map,
        title: "Bispehaugen Skole"
     });
     
     marker.setMap(map);
}

google.maps.event.addDomListener(window, 'load', initialize);
</script>