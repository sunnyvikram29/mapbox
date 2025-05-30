<!DOCTYPE html>
<html>
<head>
  <meta charset="utf-8" />
  <title>Interactive Rajasthan to Jaisalmer to Pokhran</title>
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
  center: [78.9629, 22.5937], // India center
  zoom: 4,
  maxBounds: [[68.0, 6.0], [98.0, 37.0]] // Lock to India
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

// Rajasthan main cities
const rajasthanCities = [
  { name: "Jaipur", coordinates: [75.7873, 26.9124] },
  { name: "Jodhpur", coordinates: [73.0243, 26.2389] },
  { name: "Bikaner", coordinates: [73.3119, 28.0229] },
  { name: "Kota", coordinates: [75.8648, 25.2138] },
  { name: "Jaisalmer", coordinates: [70.9083, 26.9157] }
];

// Jaisalmer nearby places
const jaisalmerNearby = [
  { name: "Pokhran", coordinates: [71.916, 27.095] }
];

let cityMarkers = [];
let nearbyMarkers = [];

map.on('load', () => {
  // Listen for map clicks
  map.on('click', (e) => {
    const point = turf.point([e.lngLat.lng, e.lngLat.lat]);
    const inside = turf.booleanPointInPolygon(point, rajasthanPolygon);

    if (inside) {
      // Remove existing city and nearby markers
      cityMarkers.forEach(m => m.remove());
      cityMarkers = [];

      // Add markers for major cities
      rajasthanCities.forEach(city => {
        const marker = new mapboxgl.Marker()
          .setLngLat(city.coordinates)
          .setPopup(new mapboxgl.Popup().setText(city.name))
          .addTo(map);
        
        marker.getElement().addEventListener('click', () => {
          if (city.name === "Jaisalmer") {
            // Remove old nearby markers
            nearbyMarkers.forEach(m => m.remove());
            nearbyMarkers = [];

            // Add Pokhran
            jaisalmerNearby.forEach(place => {
              const nearbyMarker = new mapboxgl.Marker({ color: 'red' })
                .setLngLat(place.coordinates)
                .setPopup(new mapboxgl.Popup().setText(place.name))
                .addTo(map);
              nearbyMarkers.push(nearbyMarker);
            });
          }
        });

        cityMarkers.push(marker);
      });

      // Zoom into Rajasthan
      const coords = rajasthanPolygon.geometry.coordinates[0];
      const bounds = coords.reduce((b, coord) => b.extend(coord), new mapboxgl.LngLatBounds(coords[0], coords[0]));
      map.fitBounds(bounds, { padding: 40 });
    }
  });
});
</script>
</body>
</html>