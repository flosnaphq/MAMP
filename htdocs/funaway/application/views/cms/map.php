

<link href='https://api.mapbox.com/mapbox.js/v2.4.0/mapbox.css' rel='stylesheet' />
<div id="map" style="width:100%; height:768px; height:100vh;">
			
</div>
<script>



function showMap(lat,lang,dragable){
	L.mapbox.accessToken ='<?php echo FatApp::getConfig('mapbox_access_token')?>';
	 map = L.mapbox.map('map', 'mapbox.satellite')
		.setView([lat,lang],12);

	  layers = {
		  Satellite: L.mapbox.tileLayer('mapbox.satellite'),
		  Streets: L.mapbox.tileLayer('mapbox.streets'),
		  Outdoors: L.mapbox.tileLayer('mapbox.outdoors'),
		  
	  };

	  layers.Satellite.addTo(map);
	  L.control.layers(layers).addTo(map).setPosition('topleft');
	  marker = L.marker(new L.LatLng(lat,lang), {
		icon: L.mapbox.marker.icon({
			'marker-color': 'ff8888'
		}),
		draggable: false
	});
//	marker.bindPopup('This marker is draggable! Move it around.');
	marker.addTo(map); 
}


showMap(<?php echo $lat?>,<?php echo $long?>,0);

</script>