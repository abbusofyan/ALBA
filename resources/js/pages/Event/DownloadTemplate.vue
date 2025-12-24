<script setup>
import {
    Head
} from "@inertiajs/vue3";
import Layout from "../../layouts/blank.vue";
import BinDetailPanel from '../Bin/BinDetailPanel.vue';
import BinDetailTab from '../Bin/BinDetailTab.vue';
import BinLocationTab from '../Bin/BinLocationTab.vue';

const breadcrumbs = [
	{
		title: 'Event',
		disabled: false,
		href: '/events',
	},
	{
		title: 'Download Template',
		disabled: true,
	}
]
</script>

<template>
<Head title="Detail Bin" />
<Layout>
    <v-breadcrumbs :items="breadcrumbs">
        <template v-slot:prepend></template>
    </v-breadcrumbs>
	<VCard title="Download Template">
		<VCardText>
			Choose the right template for each event type
		</VCardText>
		<VCardText>
			<VBtn color="primary" class="mx-2" href="/events/download-import-template/1">
				E-Drive
			</VBtn>

			<VBtn color="primary" class="mx-2" href="/events/download-import-template/2">
				Cash For Trash
			</VBtn>

			<!-- <VBtn color="primary" class="mx-2" href="/events/download-import-template/3">
				Private Event
			</VBtn>

			<VBtn color="primary" class="mx-2" href="/events/download-import-template/4s">
				ALBA Event
			</VBtn> -->
		</VCardText>

	</VCard>
</Layout>
</template>
