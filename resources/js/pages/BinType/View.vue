<script setup>
import {
    Head
} from "@inertiajs/vue3";
import Layout from "../../layouts/blank.vue";
import BinTypeDetailPanel from '../BinType/BinTypeDetailPanel.vue';
import BinTypeDetailTab from '../BinType/BinTypeDetailTab.vue';

const binTypeTab = ref(null)

const props = defineProps({
    binType: Object
})

const tabs = [
	{
        icon: 'tabler-box',
        title: 'Accepted Recyclables',
    },
]

const breadcrumbs = [
	{
		title: 'Bin Type',
		disabled: false,
		href: '/bin-types',
	},
	{
		title: props.binType.name,
		disabled: true,
	}
]
</script>

<template>
<Head title="Bin Type Detail" />
<Layout>
    <v-breadcrumbs :items="breadcrumbs">
        <template v-slot:prepend></template>
    </v-breadcrumbs>
	<VRow v-if="props.binType">
        <VCol>
            <BinTypeDetailPanel :bin-type-data="props.binType" />
        </VCol>

        <VCol cols="12" md="7" sm="12" lg="8">
            <VTabs v-model="binTypeTab" class="v-tabs-pill">
                <VTab v-for="tab in tabs" :key="tab.icon">
                    <VIcon :size="18" :icon="tab.icon" class="me-1" />
                    <span>{{ tab.title }}</span>
                </VTab>
            </VTabs>

            <VWindow v-model="binTypeTab" class="mt-6 disable-tab-transition" :touch="false">
                <VWindowItem>
                    <BinTypeDetailTab :bin-type-data="props.binType"/>
                </VWindowItem>
            </VWindow>
        </VCol>
    </VRow>
</Layout>
</template>
