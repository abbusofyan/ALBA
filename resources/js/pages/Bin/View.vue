<script setup>
import {
    Head
} from "@inertiajs/vue3";
import Layout from "../../layouts/blank.vue";
import BinDetailPanel from '../Bin/BinDetailPanel.vue';
import BinDetailTab from '../Bin/BinDetailTab.vue';
import BinLocationTab from '../Bin/BinLocationTab.vue';
import BinRecyclingTab from '../Bin/BinRecyclingTab.vue';

const binTab = ref(null)

const props = defineProps({
    bin: Object
})

const tabs = [
	{
        icon: 'tabler-cube',
        title: 'Bin Details',
    },
	{
		icon: 'tabler-pin',
		title: 'Bin Location',
	},
	{
		icon: 'tabler-recycle',
		title: 'Recyclings',
	},
]

const breadcrumbs = [
	{
		title: 'Bins',
		disabled: false,
		href: '/bins',
	},
	{
		title: 'Bin Details',
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
	<VRow v-if="props.bin">
        <VCol>
            <BinDetailPanel :bin-data="props.bin" />
        </VCol>

        <VCol cols="12" md="7" sm="12" lg="8">
            <VTabs v-model="binTab" class="v-tabs-pill">
                <VTab v-for="tab in tabs" :key="tab.icon">
                    <VIcon :size="18" :icon="tab.icon" class="me-1" />
                    <span>{{ tab.title }}</span>
                </VTab>
            </VTabs>

            <VWindow v-model="binTab" class="mt-6 disable-tab-transition" :touch="false">
                <VWindowItem>
                    <BinDetailTab :bin-data="props.bin"/>
                </VWindowItem>

				<VWindowItem>
                    <BinLocationTab :bin-data="props.bin"/>
                </VWindowItem>

				<VWindowItem>
                    <BinRecyclingTab :bin-data="props.bin"/>
                </VWindowItem>
            </VWindow>
        </VCol>
    </VRow>
</Layout>
</template>
