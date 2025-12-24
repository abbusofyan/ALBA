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
		title: 'Bins',
		disabled: false,
		href: '/bins',
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
			Get started by downloading the excel template for your data upload
		</VCardText>
		<VCardText>
			<VBtn color="primary" href="bins/download-import-template">
				Download Template
			</VBtn>
		</VCardText>

	</VCard>
</Layout>
</template>
