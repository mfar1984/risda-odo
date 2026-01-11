import './bootstrap';

import Alpine from 'alpinejs';
import * as maptilersdk from '@maptiler/sdk';
import '@maptiler/sdk/dist/maptiler-sdk.css';
import L from 'leaflet';
import 'leaflet/dist/leaflet.css';

// Import audit trail tracker
import './audit-trail';

window.Alpine = Alpine;
window.maptilersdk = maptilersdk;
window.L = L;

import locationPicker from './components/locationPicker';
window.locationPicker = locationPicker;

document.addEventListener('alpine:init', () => {
    Alpine.store('layout', {
        collapsed: false,
        mobileOpen: false,
    });
});

Alpine.start();
