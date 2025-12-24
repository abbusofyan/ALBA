<template>
    <div class="relative">
        <div v-if="isLoading" class="absolute inset-0 flex items-center justify-center bg-white bg-opacity-75 z-10 pointer-events-none">
            <div class="text-center">
                <div class="loading-spinner"></div>
                <p class="text-lg font-semibold mt-2">Loading Map...</p>
            </div>
        </div>
        <div id="map" class="h-[500px] w-full" :class="{ 'opacity-50': isLoading }"></div>
    </div>
</template>

<script setup>
import {
    onMounted,
    watch,
    ref,
    shallowRef,
    nextTick
} from 'vue';
import L from 'leaflet';
import 'leaflet/dist/leaflet.css';
import 'leaflet.markercluster/dist/MarkerCluster.css';
import 'leaflet.markercluster/dist/MarkerCluster.Default.css';
import 'leaflet.markercluster';
import 'leaflet-gesture-handling/dist/leaflet-gesture-handling.css';
import GestureHandling from 'leaflet-gesture-handling';

import markerIcon from 'leaflet/dist/images/marker-icon.png';
import markerShadow from 'leaflet/dist/images/marker-shadow.png';
import markerIcon2x from 'leaflet/dist/images/marker-icon-2x.png';

delete L.Icon.Default.prototype._getIconUrl;

L.Icon.Default.mergeOptions({
    iconRetinaUrl: markerIcon2x,
    iconUrl: markerIcon,
    shadowUrl: markerShadow,
});

const props = defineProps({
    locations: Array,
    markable: Boolean,
    radius: Number,
    coordinate: Array,
    clickedLocation: Object
});

const emit = defineEmits(['update:clickedLocation', 'zoom-change', 'mode-change', 'initializeMap', 'loading-change']);

const map = shallowRef({});
const markerClusterGroup = shallowRef({});
const locations = ref([]);
const mapCoordinate = ref([1.3521, 103.8198]);
const zoom = ref(12);
const DETAIL_ZOOM_THRESHOLD = 15;
const currentMode = ref('overview');
let radiusCircle = null;
const isLoading = ref(true);

// Expose loading state to parent
const setLoading = (loading) => {
    isLoading.value = loading;
    emit('loading-change', loading);
};

watch(() => props.clickedLocation, (newLocation) => {
    if (props.markable && newLocation && newLocation.lat && newLocation.lng) {
        locations.value = [{
            lat: newLocation.lat,
            long: newLocation.lng,
            map_radius: props.radius
        }];
        map.value.setView([newLocation.lat, newLocation.lng], 16);
    }
});

L.Map.addInitHook('addHandler', 'gestureHandling', GestureHandling);

const initializeMap = async () => {
    setLoading(true);

    try {
        map.value = L.map('map', {
            gestureHandling: true,
            scrollWheelZoom: false  // GestureHandling will manage scroll zoom behavior
        }).setView(mapCoordinate.value, zoom.value);

        L.tileLayer('https://{s}.basemaps.cartocdn.com/rastertiles/voyager/{z}/{x}/{y}{r}.png', {
            attribution: '&copy; <a href="https://www.openstreetmap.org/copyright">OpenStreetMap</a> contributors &copy; <a href="https://carto.com/attributions">CARTO</a>',
        }).addTo(map.value);

        // Initialize MarkerClusterGroup with better options
        markerClusterGroup.value = L.markerClusterGroup({
            chunkedLoading: true,
            chunkProgress: function(processed, total, elapsed) {
                // Optional: Add progress tracking if needed
            },
            maxClusterRadius: 80,
            spiderfyOnMaxZoom: true,
            showCoverageOnHover: false,
            zoomToBoundsOnClick: true
        });
        map.value.addLayer(markerClusterGroup.value);

        map.value.on('zoomend', handleZoomChange);
        map.value.on('click', onMapClick);

        // Wait for next tick to ensure map is fully rendered
        await nextTick();

        // Add a small delay to ensure tiles are loaded
        setTimeout(() => {
            setLoading(false);
        }, 500);

    } catch (error) {
        console.error('Error initializing map:', error);
        setLoading(false);
    }
};

function handleZoomChange() {
    const currentZoom = map.value.getZoom();
    emit('zoom-change', currentZoom);

    if (currentZoom >= DETAIL_ZOOM_THRESHOLD) {
        switchToDetailMode();
    } else {
        switchToOverviewMode();
    }
}

function switchToDetailMode() {
    if (currentMode.value === 'detail') return;
    currentMode.value = 'detail';
    emit('mode-change', 'detail');
    // fetchDetailBins(map.value.getBounds());
}

function switchToOverviewMode() {
    if (currentMode.value === 'overview') return;
    currentMode.value = 'overview';
    emit('mode-change', 'overview');
    // updateOverviewMarkers();
}

const updateMarkers = async (skipLoading = false) => {
    if (!map.value || !markerClusterGroup.value) return;

    if (!skipLoading) {
        setLoading(true);
    }

    try {
        // Clear existing markers
        markerClusterGroup.value.clearLayers();

        // Clear existing radius circle
        if (radiusCircle) {
            map.value.removeLayer(radiusCircle);
            radiusCircle = null;
        }

        // Add markers
        locations.value.forEach(location => {
            const marker = L.marker([parseFloat(location.lat), parseFloat(location.long)]);

            // Click event for marker
            marker.on('click', () => {
                window.location.href = `/bins/${location.id}`;
            });

            markerClusterGroup.value.addLayer(marker);

            // Add radius circle if markable
            if (location.map_radius && props.markable) {
                radiusCircle = L.circle([location.lat, location.long], {
                    radius: location.map_radius,
                    color: 'blue',
                    fillColor: '#blue',
                    fillOpacity: 0.2
                }).addTo(map.value);
            }
        });

        // Wait for markers to be rendered
        await nextTick();

        // Add a small delay to ensure markers are fully rendered
        setTimeout(() => {
            setLoading(false);
        }, 300);

    } catch (error) {
        console.error('Error updating markers:', error);
        setLoading(false);
    }
};

const onMapClick = (e) => {
    if (props.markable) {
        const {
            lat,
            lng
        } = e.latlng;
        locations.value = [{
            lat: lat,
            long: lng,
            map_radius: props.radius
        }];
        emit('update:clickedLocation', {
            lat,
            lng
        });
    }
};

onMounted(async () => {
    locations.value = props.locations ?? [];
    if (props.coordinate) {
        mapCoordinate.value = props.coordinate;
        zoom.value = 13;
    }
    await initializeMap();
    await updateMarkers();
});

watch(() => props.locations, async () => {
    if (!props.markable) {
        // Don't set loading here as it's already handled by parent component
        locations.value = props.locations;
    }
});

watch(() => locations.value, updateMarkers);

watch(() => props.radius, async (newRadius) => {
    if (locations.value) {
        locations.value = locations.value.map(location => ({
            ...location,
            map_radius: newRadius
        }));
    }
    await updateMarkers();
});

defineExpose({
    map,
    initializeMap,
    setLoading,
    isLoading
});
</script>

<style scoped>
#map {
    height: 500px;
    width: 100%;
    position: relative;
    z-index: 1;
}

.relative {
    position: relative;
}

.absolute {
    position: absolute;
}

.inset-0 {
    top: 0;
    right: 0;
    bottom: 0;
    left: 0;
}

.flex {
    display: flex;
}

.items-center {
    align-items: center;
}

.justify-center {
    justify-content: center;
}

.bg-white {
    background-color: white;
}

.bg-opacity-75 {
    background-color: rgba(255, 255, 255, 0.75);
}

.z-10 {
    z-index: 10;
}

.text-lg {
    font-size: 1.125rem;
}

.font-semibold {
    font-weight: 600;
}

.opacity-50 {
    opacity: 0.5;
}

.text-center {
    text-align: center;
}

.mt-2 {
    margin-top: 0.5rem;
}

.loading-spinner {
    width: 40px;
    height: 40px;
    border: 4px solid #f3f3f3;
    border-top: 4px solid #3498db;
    border-radius: 50%;
    animation: spin 1s linear infinite;
    margin: 0 auto;
}

.pointer-events-none {
    pointer-events: none;
}

@keyframes spin {
    0% { transform: rotate(0deg); }
    100% { transform: rotate(360deg); }
}

/* Prevent map from overlapping other elements */
:deep(.leaflet-container) {
    position: relative !important;
    z-index: 1 !important;
}

:deep(.leaflet-control-container) {
    z-index: 100 !important;
}

:deep(.leaflet-popup-pane) {
    z-index: 200 !important;
}

:deep(.leaflet-tooltip-pane) {
    z-index: 150 !important;
}

:deep(.leaflet-marker-pane) {
    z-index: 50 !important;
}

:deep(.leaflet-tile-pane) {
    z-index: 1 !important;
}

/* Ensure cluster markers are clickable */
:deep(.marker-cluster) {
    z-index: 100 !important;
    pointer-events: auto !important;
}

:deep(.marker-cluster-small) {
    z-index: 100 !important;
    pointer-events: auto !important;
}

:deep(.marker-cluster-medium) {
    z-index: 100 !important;
    pointer-events: auto !important;
}

:deep(.marker-cluster-large) {
    z-index: 100 !important;
    pointer-events: auto !important;
}

/* Ensure dragging doesn't create overflow issues */
:deep(.leaflet-drag-target) {
    z-index: 1 !important;
}

:deep(.leaflet-interactive) {
    z-index: 1 !important;
    pointer-events: auto !important;
}
</style>
