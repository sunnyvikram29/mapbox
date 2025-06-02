<!DOCTYPE html>
<html>
<head>
  <meta charset="utf-8">
  <title>India Map Drilldown</title>
  <meta name="viewport" content="width=device-width, initial-scale=1">
  <link href="https://api.mapbox.com/mapbox-gl-js/v2.15.0/mapbox-gl.css" rel="stylesheet" />
  <style>
    body, html { margin: 0; padding: 0; height: 100%; overflow: hidden; }
    #map { width: 100%; height: 100%; }
    #stateDropdown {
      position: absolute;
      top: 10px;
      left: 10px;
      z-index: 1;
      padding: 6px;
      font-size: 14px;
    }
    .blue-marker, .orange-marker {
      width: 14px;
      height: 14px;
      border-radius: 50%;
      cursor: pointer;
    }
    .blue-marker {
      background: blue;
      box-shadow: 0 0 5px 2px #00f;
    }
    .orange-marker {
      background: orange;
      box-shadow: 0 0 5px 2px #ffa500;
    }
  </style>
</head>
<body>

<select id="stateDropdown">
  <option value="">-- Select State --</option>
  <option value="Rajasthan">Rajasthan</option>
  <option value="Gujarat">Gujarat</option>
</select>
<div id="map"></div>

<script src="https://api.mapbox.com/mapbox-gl-js/v2.15.0/mapbox-gl.js"></script>
<script>
mapboxgl.accessToken = 'pk.eyJ1IjoiY3lyb3Zlcml0cmVlIiwiYSI6ImNrc3o4ZWhsNTJyNXMydnA3NWhzbTlmeHAifQ.UFGmxyhFZRSci4SGxpIaqQ'; // Replace with your token

const map = new mapboxgl.Map({
  container: 'map',
  style: 'mapbox://styles/mapbox/streets-v12',
  center: [78.9629, 22.5937],
  zoom: 1.5,
  pitch: 0,
  bearing: 0,
  projection: 'globe'
});

map.on('style.load', () => {
 // map.setFog({});
  map.addSource('terrain', {
    type: "raster-dem",
    url: "mapbox://mapbox.terrain-rgb",
    tileSize: 512,
    maxzoom: 14
  });
  map.setTerrain({ source: "terrain", exaggeration: 1.5 });
   // Optional: Add 3D buildings layer
  map.addLayer({
    id: '3d-buildings',
    source: 'composite',
    'source-layer': 'building',
    filter: ['==', 'extrude', 'true'],
    type: 'fill-extrusion',
    minzoom: 15,
    paint: {
      'fill-extrusion-color': '#aaa',
      'fill-extrusion-height': ['get', 'height'],
      'fill-extrusion-base': ['get', 'min_height'],
      'fill-extrusion-opacity': 0.6
    }
  });
});


const states = {
  'Rajasthan': [74.2179, 27.0238],
  'Gujarat': [71.1924, 22.2587]
};

const cities = [
  {
    name: 'Jaipur',
    state: 'Rajasthan',
    coords: [75.7873, 26.9124],
    sites: [[75.90, 26.95], [75.85, 26.89]]
  },
  {
    name: 'Jodhpur',
    state: 'Rajasthan',
    coords: [73.0243, 26.2389],
    sites: [[73.10, 26.30]]
  },
  {
    name: 'Ahmedabad',
    state: 'Gujarat',
    coords: [72.5714, 23.0225],
    sites: [[72.60, 23.05], [72.55, 22.99]]
  }
];

let cityMarkers = [];
let siteMarkers = [];
let stateMarker = null;

function clearMarkers(arr) {
  arr.forEach(m => m.remove());
  arr.length = 0;
}

function flyToLocation(center, zoom = 5.5, pitch = 40, bearing = 0) {
  map.flyTo({ center, zoom, pitch, bearing, speed: 1.2, curve: 1.5 });
}

function showCities(stateName) {
  clearMarkers(cityMarkers);
  clearMarkers(siteMarkers);

  cities.filter(c => c.state === stateName).forEach(city => {
    const el = document.createElement('div');
    el.className = 'blue-marker';

    const marker = new mapboxgl.Marker(el)
      .setLngLat(city.coords)
      .setPopup(new mapboxgl.Popup().setText(city.name))
      .addTo(map);

    el.addEventListener('click', () => {
      clearMarkers(siteMarkers); // Do NOT zoom here
      city.sites.forEach(siteCoords => {
        const sEl = document.createElement('div');
        sEl.className = 'orange-marker';

        const siteMarker = new mapboxgl.Marker(sEl)
          .setLngLat(siteCoords)
          .setPopup(new mapboxgl.Popup().setText(`${city.name} - Site`))
          .addTo(map);

        sEl.addEventListener('click', () => {
          flyToLocation(siteCoords, 16, 70, -20); // zoom to site only
        });

        siteMarkers.push(siteMarker);
      });
    });

    cityMarkers.push(marker);
  });
}

document.getElementById('stateDropdown').addEventListener('change', function () {
  const state = this.value;
  if (!state || !states[state]) return;

  clearMarkers(cityMarkers);
  clearMarkers(siteMarkers);
  if (stateMarker) stateMarker.remove();

  flyToLocation(states[state], 5.5, 45, -30);

  const red = new mapboxgl.Marker({ color: 'red' })
    .setLngLat(states[state])
    .setPopup(new mapboxgl.Popup().setText(state))
    .addTo(map);

  red.getElement().addEventListener('click', () => showCities(state));
  stateMarker = red;
});
</script>
</body>
</html>