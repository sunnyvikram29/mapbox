<!DOCTYPE html>
<html>
<head>
  <meta charset="utf-8" />
  <title>Rajasthan Marker and Sites</title>
  <meta name="viewport" content="width=device-width, initial-scale=1" />
  <script src="https://api.mapbox.com/mapbox-gl-js/v2.15.0/mapbox-gl.js"></script>
  <link href="https://api.mapbox.com/mapbox-gl-js/v2.15.0/mapbox-gl.css" rel="stylesheet" />
  <script src="https://cdn.jsdelivr.net/npm/@turf/turf@6.5.0/turf.min.js"></script>
  <style>
    body { margin: 0; padding: 0; }
    #map { position: absolute; top: 0; bottom: 0; width: 100%; }
  </style>
</head>
<body>
<div id="map"></div>

<script>
mapboxgl.accessToken = 'pk.eyJ1IjoiY3lyb3Zlcml0cmVlIiwiYSI6ImNrc3o4ZWhsNTJyNXMydnA3NWhzbTlmeHAifQ.UFGmxyhFZRSci4SGxpIaqQ'; // Replace this

const map = new mapboxgl.Map({
  container: 'map',
  style: 'mapbox://styles/mapbox/streets-v12',
  center: [78.9629, 22.5937],
  zoom: 4,
  maxBounds: [[68.0, 6.0], [98.0, 37.0]]
});

// Simplified Rajasthan Polygon
const rajasthanPolygon = {
  "type": "Feature",
  "geometry": {
    "type": "Polygon",
    "coordinates": [[
      [69.8, 30.1], [75.9, 30.1], [76.5, 27.7], [75.0, 25.0],
      [73.3, 24.5], [71.0, 24.8], [70.3, 27.7], [69.8, 30.1]
    ]]
  }
};

// Main Rajasthan cities (sites)
const rajasthanSites = [
  { name: "Jaipur", coordinates: [75.7873, 26.9124] },
  { name: "Jodhpur", coordinates: [73.0243, 26.2389] },
  { name: "Bikaner", coordinates: [73.3119, 28.0229] },
  { name: "Kota", coordinates: [75.8648, 25.2138] },
  { name: "Jaisalmer", coordinates: [70.9083, 26.9157] }
];

let rajasthanMarker = null;
let siteMarkers = [];

// On map load
map.on('load', () => {
  map.on('click', (e) => {
    const clickedPoint = turf.point([e.lngLat.lng, e.lngLat.lat]);
    const inside = turf.booleanPointInPolygon(clickedPoint, rajasthanPolygon);

    if (inside) {
      // Remove existing Rajasthan marker
      if (rajasthanMarker) rajasthanMarker.remove();
      siteMarkers.forEach(marker => marker.remove());
      siteMarkers = [];

      // Add central Rajasthan marker (you can adjust the coordinates)
      const centerOfRajasthan = [73.9, 27.0];
      rajasthanMarker = new mapboxgl.Marker({ color: 'red' })
        .setLngLat(centerOfRajasthan)
        .setPopup(new mapboxgl.Popup().setText("Click to show Rajasthan sites"))
        .addTo(map);

      rajasthanMarker.getElement().addEventListener('click', () => {
        // Add city/site markers
        rajasthanSites.forEach(site => {
          const marker = new mapboxgl.Marker()
            .setLngLat(site.coordinates)
            .setPopup(new mapboxgl.Popup().setText(site.name))
            .addTo(map);
          siteMarkers.push(marker);
        });
      });

      // Fit map to Rajasthan polygon
      const coords = rajasthanPolygon.geometry.coordinates[0];
      const bounds = coords.reduce((b, coord) => b.extend(coord), new mapboxgl.LngLatBounds(coords[0], coords[0]));
      map.fitBounds(bounds, { padding: 40 });
    }
  });
});
</script>
</body>
</html>