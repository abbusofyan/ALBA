<script setup>
import {
    useForm
} from '@inertiajs/vue3';
import {
    toast
} from "vue3-toastify";
import {
    onMounted,
    computed
} from "vue";

import LeafLetMap from "../../Components/LeafLetMap.vue";

const isNewPasswordVisible = ref(false)
const isConfirmPasswordVisible = ref(false)
const isTwoFactorDialogOpen = ref(false);

const props = defineProps({
    binData: {
        type: Object,
        required: true,
    },
})

onMounted( async() => {
	await nextTick()
	leafletMap.value?.map?.invalidateSize()
});

const leafletMap = ref(null)
</script>

<template>
<VRow>
    <VCol cols="12">
        <VCard>
            <VCardText>
				<v-sheet class="ma-2 pa-2 map-container">
					<LeafLetMap :coordinate="binData ? [binData.lat, binData.long] : null" :locations="binData ? [binData] : null" :radius="parseInt(binData.map_radius)" ref="leafletMap" :markable="true" />
				</v-sheet>
            </VCardText>
			<VCardText>
				<v-btn target="_blank" :href="'https://maps.google.com/?q='+binData.lat+',' + binData.long" color="info" class="me-2 mb-2">
					<v-icon end icon="tabler-location" class="me-2" />
					Open maps
				</v-btn>
            </VCardText>

        </VCard>
    </VCol>
</VRow>
</template>

<style scoped>
.map-container {
    position: relative;
    z-index: 1;
    overflow: hidden;
}

.map-container :deep(.leaflet-container) {
    z-index: 1;
}

.map-container :deep(.leaflet-control-container) {
    z-index: 100;
}

.map-container :deep(.leaflet-popup-pane) {
    z-index: 200;
}

.map-container :deep(.leaflet-tooltip-pane) {
    z-index: 150;
}

.map-container :deep(.leaflet-marker-pane) {
    z-index: 50;
}

.map-container :deep(.leaflet-tile-pane) {
    z-index: 1;
}
</style>
