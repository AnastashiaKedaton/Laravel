@extends('layouts.template')
 

 @section('styles')

 <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/leaflet.draw/1.0.4/leaflet.draw.css">


    <style>
        html, body {
            height: 100%;
            width: 100%;
        }

        #map {
            height: calc(100vh - 56px);
            width: 100%;
            margin: 0;
        }

    </style>
    @endsection
    

    @section('content')
    <div id ="map"></div>


    <!-- Modal Edit Point-->
<div class="modal fade" id="PointModal" tabindex="-1" aria-labelledby="PointModalLabel" aria-hidden="true">
    <div class="modal-dialog">
      <div class="modal-content">
        <div class="modal-header">
          <h1 class="modal-title fs-5" id="PointModalLabel"><i class="fa-solid fa-location-dot"></i> Edit Point</h1>
          <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
        </div>
        <div class="modal-body">
            <form action="{{ route('update-point', $id) }}" method="POST" enctype="multipart/form-data">
            @csrf
            @method('PATCH')
            <div class="mb-3">
                <label for="name" class="form-label">Name</label>
                <input type="text" class="form-control" id="name" name="name" placeholder="Feature Name">
              </div>
              <div class="mb-3">
                <label for="description" class="form-label">Description</label>
                <textarea class="form-control" id="description" name="description" rows="3"></textarea>
              </div>
              <div class="mb-3">
                <label for="geom" class="form-label">Geometry</label>
                <textarea class="form-control" id="geom_point" name="geom" rows="3" readonly></textarea>
              </div>
              <div class="mb-3">
                <label for="image" class="form-label">Image</label>
                <input type="file" class="form-control" id="image_point"
                name="image"onchange="document.getElementById ('preview-image-point').src = window.URL.createObjectURL(this.files[0])">
                <script>
                    document.getElementById().src = window.URL
                </script>

                <input type="hidden" class="form-control" id="image_old" name="image_old">

            </div>
            <div class="tabmb-3">
                <img src="" alt="Preview" id="preview-image-point"
                class="img-thumbnail" width="400">
            </div>

            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Close</button>
                <button type="submit" class="btn btn-primary">Save</button>
            </form>
        </div>
      </div>
    </div>
  </div>

    @endsection


    @section('script')
    <script src="https://cdnjs.cloudflare.com/ajax/libs/leaflet.draw/1.0.4/leaflet.draw.js"></script>
    <script src="https://unpkg.com/@terraformer/wkt"></script>

    <script>
        //Map
        var map = L.map('map').setView([-7.771458115827877, 110.3775613226732], 13);

        // Basemap
        L.tileLayer('https://tile.openstreetmap.org/{z}/{x}/{y}.png', {
            maxZoom: 19,
            attribution: '&copy; <a href="https://www.openstreetmap.org/copyright">OpenStreetMap</a> contributors'
        }).addTo(map);



    /* Digitize Function */
var drawnItems = new L.FeatureGroup();
map.addLayer(drawnItems);

var drawControl = new L.Control.Draw({
draw: {
    position: 'topleft',
    polyline: false,
    polygon: false,
    rectangle: false,
    circle: false,
    marker: false,
    circlemarker: false
},
edit: {
    featureGroup: drawnItems,
    edit: true,
    remove: false
}
});

map.addControl(drawControl);

map.on('draw:edited', function(e) {
	var layer = e.layers;

    layer.eachLayer(function(layer) {
        var geojson = layer.toGeoJSON();

        var wkt = Terraformer.geojsonToWKT(geojson.geometry);

        $('#name').val(layer.feature.properties.name);
        $('#description').val(layer.feature.properties.description);
        $('#geom_point').val(wkt);
        $('#image_old').val(layer.feature.properties.image);
        $('#preview-image-point').attr('src', '{{ asset('storage/images/') }}/' + layer.feature.properties.image);
        $('#PointModal').modal('show');
    });cd
});

			/* GeoJSON Point */
            //menampilkan fitur-fitur GeoJSON di peta Leaflet.
			var point = L.geoJson(null, {
				onEachFeature: function (feature, layer) {

                    // add
                    drawnItems.addLayer(layer);

					var popupContent = "<h3> " + feature.properties.name + "</h3>" +
          "" + feature.properties.description + "<br>" +
                    "<hr><img src='{{ asset('storage/images/') }}/" + feature.properties.image +
                    "'class='' alt='' width='200'> ";

					layer.on({
						click: function (e) {
							point.bindPopup(popupContent);
						},
						mouseover: function (e) {
							point.bindTooltip(feature.properties.name);
						},
					});
				},
			});
            // menampilkan point pada peta
			$.getJSON("{{ route('api.point', $id) }}", function (data) {
				point.addData(data);
				map.addLayer(point);
                map.fitBounds(point.getBounds());
			});

    </script>
    @endsection