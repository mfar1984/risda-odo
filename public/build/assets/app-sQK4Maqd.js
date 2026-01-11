import { a as axios } from "./vendor-axios-Df7LzhLp.js";
import { m as module_default } from "./vendor-alpine-CRxKTV6t.js";
import { m as maptilersdk } from "./vendor-maptiler-DeeAzTvN.js";
import { L } from "./vendor-leaflet-wuwDVjKX.js";
import "./audit-trail-KLP8CwIT.js";
import "./vendor-common-VklLotv9.js";
window.axios = axios;
window.axios.defaults.headers.common["X-Requested-With"] = "XMLHttpRequest";
const MAPTILER_DEFAULT_STYLES = {
  street: "openstreetmap",
  satellite: "satellite",
  hybrid: "hybrid"
};
function extractMaptilerStyleId(styleUrl) {
  if (!styleUrl) {
    return null;
  }
  try {
    const url = new URL(styleUrl);
    const parts = url.pathname.split("/").filter(Boolean);
    const mapsIndex = parts.indexOf("maps");
    if (mapsIndex !== -1 && parts.length > mapsIndex + 1) {
      return parts[mapsIndex + 1];
    }
  } catch (error) {
    console.warn("Gagal mengekstrak gaya MapTiler:", error);
  }
  return null;
}
function buildMaptilerTileUrl(styleId, apiKey) {
  if (!styleId || !apiKey) {
    return null;
  }
  const extension = styleId === MAPTILER_DEFAULT_STYLES.satellite ? "jpg" : "png";
  return `https://api.maptiler.com/maps/${styleId}/256/{z}/{x}/{y}.${extension}?key=${apiKey}`;
}
function locationPicker(mapId, provider = "maptiler", apiKey = null, styleUrl = null, options = {}) {
  const normalizeCoordinate = (value, fallback) => {
    const numeric = parseFloat(value);
    return Number.isFinite(numeric) ? numeric : fallback;
  };
  const defaultLat = normalizeCoordinate(options.defaultLat, null);
  const defaultLng = normalizeCoordinate(options.defaultLng, null);
  const initialLat = normalizeCoordinate(options.lat, defaultLat);
  const initialLng = normalizeCoordinate(options.lng, defaultLng);
  const effectiveProvider = provider === "maptiler" && apiKey ? "maptiler" : "openstreetmap";
  const customStyleId = extractMaptilerStyleId(styleUrl);
  return {
    provider,
    apiKey,
    styleUrl,
    effectiveProvider,
    customStyleId,
    defaultLat,
    defaultLng,
    map: null,
    marker: null,
    layers: {},
    activeLayer: "street",
    layerOptions: [],
    coords: {
      lat: initialLat.toFixed(6),
      lng: initialLng.toFixed(6)
    },
    searchQuery: "",
    isSearching: false,
    searchResults: [],
    searchError: null,
    init() {
      this.layerOptions = this.computeLayerOptions();
      this.initializeMap();
    },
    computeLayerOptions() {
      const sequence = this.effectiveProvider === "maptiler" ? ["street", "satellite", "hybrid"] : ["street", "satellite"];
      return sequence.map((value) => ({
        value,
        label: this.layerLabel(value)
      }));
    },
    layerLabel(layer) {
      switch (layer) {
        case "satellite":
          return "Satelit";
        case "hybrid":
          return "Hibrid";
        default:
          return "Jalan";
      }
    },
    initializeMap() {
      const lat = parseFloat(this.coords.lat) || this.defaultLat;
      const lng = parseFloat(this.coords.lng) || this.defaultLng;
      if (this.map) {
        this.map.off();
        this.map.remove();
        this.map = null;
        this.marker = null;
        this.layers = {};
      }
      this.map = L.map(mapId, { zoomControl: false }).setView([lat, lng], 13);
      L.control.zoom({ position: "topright" }).addTo(this.map);
      this.layers = this.createLayers();
      this.activateLayer(this.activeLayer);
      this.marker = this.createMarker([lat, lng]).addTo(this.map);
      this.marker.on("dragend", (event) => {
        const position = event.target.getLatLng();
        this.updateCoordinates(position.lat, position.lng);
      });
      this.map.on("click", (event) => {
        const { lat: eventLat, lng: eventLng } = event.latlng;
        this.updateCoordinates(eventLat, eventLng, { pan: true, zoom: 16 });
        this.searchResults = [];
        this.searchError = null;
      });
    },
    createLayers() {
      if (this.effectiveProvider === "maptiler") {
        const streetStyle = this.customStyleId || MAPTILER_DEFAULT_STYLES.street;
        const satelliteStyle = MAPTILER_DEFAULT_STYLES.satellite;
        const hybridStyle = MAPTILER_DEFAULT_STYLES.hybrid;
        const attribution = "© MapTiler © OpenStreetMap contributors";
        const streetLayer = buildMaptilerTileUrl(streetStyle, this.apiKey) ? L.tileLayer(buildMaptilerTileUrl(streetStyle, this.apiKey), {
          attribution,
          maxZoom: 19,
          crossOrigin: true
        }) : null;
        const satelliteLayer = buildMaptilerTileUrl(satelliteStyle, this.apiKey) ? L.tileLayer(buildMaptilerTileUrl(satelliteStyle, this.apiKey), {
          attribution,
          maxZoom: 19,
          crossOrigin: true
        }) : null;
        const hybridLayer = buildMaptilerTileUrl(hybridStyle, this.apiKey) ? L.tileLayer(buildMaptilerTileUrl(hybridStyle, this.apiKey), {
          attribution,
          maxZoom: 19,
          crossOrigin: true
        }) : null;
        return {
          street: streetLayer,
          satellite: satelliteLayer,
          hybrid: hybridLayer
        };
      }
      return {
        street: L.tileLayer("https://{s}.tile.openstreetmap.org/{z}/{x}/{y}.png", {
          attribution: "© OpenStreetMap contributors",
          maxZoom: 19
        }),
        satellite: L.tileLayer(
          "https://server.arcgisonline.com/ArcGIS/rest/services/World_Imagery/MapServer/tile/{z}/{y}/{x}",
          {
            attribution: "© Esri — Source: Esri, i-cubed, USDA, USGS, AEX, GeoEye, Getmapping, Aerogrid, IGN, IGP, UPR-EGP, dan komuniti GIS",
            maxZoom: 19
          }
        )
      };
    },
    activateLayer(layerKey) {
      if (!this.map || !this.layers[layerKey]) {
        return;
      }
      Object.values(this.layers).forEach((layer2) => {
        if (layer2 && this.map.hasLayer(layer2)) {
          this.map.removeLayer(layer2);
        }
      });
      const layer = this.layers[layerKey];
      if (layer) {
        layer.addTo(this.map);
        this.activeLayer = layerKey;
      }
    },
    createMarker(latLng) {
      const icon = L.icon({
        iconUrl: "https://unpkg.com/leaflet@1.9.4/dist/images/marker-icon.png",
        shadowUrl: "https://unpkg.com/leaflet@1.9.4/dist/images/marker-shadow.png",
        iconSize: [25, 41],
        iconAnchor: [12, 41],
        popupAnchor: [1, -34],
        tooltipAnchor: [16, -28],
        shadowSize: [41, 41]
      });
      return L.marker(latLng, { draggable: true, icon });
    },
    updateCoordinates(lat, lng, options2 = {}) {
      var _a;
      const normalizedLat = Number.isFinite(lat) ? lat : this.defaultLat;
      const normalizedLng = Number.isFinite(lng) ? lng : this.defaultLng;
      this.coords.lat = normalizedLat.toFixed(6);
      this.coords.lng = normalizedLng.toFixed(6);
      if (this.marker) {
        this.marker.setLatLng([normalizedLat, normalizedLng]);
      }
      if (options2.pan && this.map) {
        const zoom = (_a = options2.zoom) != null ? _a : this.map.getZoom();
        this.map.flyTo([normalizedLat, normalizedLng], zoom);
      }
    },
    goToCurrentLocation() {
      if (!navigator.geolocation) {
        alert("Geolokasi tidak disokong oleh pelayar ini.");
        return;
      }
      navigator.geolocation.getCurrentPosition(
        (position) => {
          const { latitude, longitude } = position.coords;
          this.updateCoordinates(latitude, longitude, { pan: true, zoom: 16 });
        },
        () => {
          alert("Tidak dapat mendapatkan lokasi semasa.");
        }
      );
    },
    resetToDefault() {
      this.updateCoordinates(this.defaultLat, this.defaultLng, { pan: true, zoom: 13 });
      this.searchResults = [];
      this.searchError = null;
    },
    setLayerFromSelect(value) {
      this.activateLayer(value);
    },
    async searchAddress() {
      const query = (this.searchQuery || "").trim();
      if (!query) {
        this.searchResults = [];
        this.searchError = "Sila masukkan alamat untuk carian.";
        return;
      }
      this.isSearching = true;
      this.searchError = null;
      this.searchResults = [];
      try {
        const params = new URLSearchParams({
          format: "json",
          q: query,
          addressdetails: "1",
          countrycodes: "my",
          limit: "8"
        });
        const nominatimUrl = `https://nominatim.openstreetmap.org/search?${params.toString()}`;
        const response = await fetch(nominatimUrl, {
          headers: {
            "Accept-Language": "ms,en"
          }
        });
        if (!response.ok) {
          throw new Error(`Ralat rangkaian: ${response.status}`);
        }
        const data = await response.json();
        const results = Array.isArray(data) ? data.filter((item) => item.display_name && item.lat && item.lon).map((item) => {
          var _a;
          return {
            id: (_a = item.place_id) != null ? _a : `${item.lat}-${item.lon}`,
            label: item.display_name,
            lat: parseFloat(item.lat),
            lng: parseFloat(item.lon)
          };
        }) : [];
        if (!results.length) {
          this.searchError = "Tiada hasil ditemui untuk carian ini.";
          return;
        }
        this.searchResults = results;
        const first = results[0];
        this.updateCoordinates(first.lat, first.lng, { pan: true, zoom: 16 });
      } catch (error) {
        console.error("Geocoding failed:", error);
        this.searchError = "Ralat semasa mencari alamat. Sila cuba lagi.";
      } finally {
        this.isSearching = false;
      }
    },
    selectSearchResult(result) {
      if (!result) {
        return;
      }
      this.searchQuery = result.label;
      this.updateCoordinates(result.lat, result.lng, { pan: true, zoom: 16 });
      this.searchResults = [];
    }
  };
}
window.Alpine = module_default;
window.maptilersdk = maptilersdk;
window.L = L;
window.locationPicker = locationPicker;
document.addEventListener("alpine:init", () => {
  module_default.store("layout", {
    collapsed: false,
    mobileOpen: false
  });
});
module_default.start();
