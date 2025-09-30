import './bootstrap';

import Alpine from 'alpinejs';
import * as maptilersdk from '@maptiler/sdk';
import '@maptiler/sdk/dist/maptiler-sdk.css';
import L from 'leaflet';
import 'leaflet/dist/leaflet.css';

window.Alpine = Alpine;
window.maptilersdk = maptilersdk;
window.L = L;

import locationPicker from './components/locationPicker';
window.locationPicker = locationPicker;

Alpine.start();
