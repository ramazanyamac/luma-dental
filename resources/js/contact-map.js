import L from 'leaflet';
import 'leaflet/dist/leaflet.css';

const mapElement = document.getElementById('contact-map');

if (mapElement) {
    const latitude = Number(mapElement.dataset.latitude);
    const longitude = Number(mapElement.dataset.longitude);
    const position = [latitude, longitude];

    const map = L.map(mapElement, {
        scrollWheelZoom: false,
    }).setView(position, 15);

    L.tileLayer('https://tile.openstreetmap.org/{z}/{x}/{y}.png', {
        maxZoom: 19,
        attribution:
            '&copy; <a href="https://www.openstreetmap.org/copyright">OpenStreetMap</a> contributors',
    }).addTo(map);

    const popupContent = document.createElement('p');
    popupContent.textContent = mapElement.dataset.label;

    L.circleMarker(position, {
        radius: 10,
        color: '#ffffff',
        weight: 3,
        fillColor: '#134e4a',
        fillOpacity: 1,
    })
        .addTo(map)
        .bindPopup(popupContent)
        .openPopup();
}