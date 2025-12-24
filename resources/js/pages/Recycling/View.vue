<script setup>
import {
    Head
} from "@inertiajs/vue3";
import Layout from "../../layouts/blank.vue";
import RecyclingDetailPanel from '../Recycling/RecyclingDetailPanel.vue';
import RecyclingDetailTab from '../Recycling/RecyclingDetailTab.vue';

const recyclingTab = ref(null)

const props = defineProps({
    recycling: Object
})

const tabs = [
	{
        icon: 'tabler-history',
        title: 'Recycling Activity',
    }
]

const breadcrumbs = [
	{
		title: 'Recyclings',
		disabled: false,
		href: '/recyclings',
	},
	{
		title: 'Recycling Details',
		disabled: true,
	}
]
</script>

<template>
<Head title="Detail Recycling" />
<Layout>
    <v-breadcrumbs :items="breadcrumbs">
        <template v-slot:prepend></template>
    </v-breadcrumbs>
	<VRow v-if="props.recycling">
        <VCol>
            <RecyclingDetailPanel :recycling-data="props.recycling" />
        </VCol>

        <VCol cols="12" md="7" sm="12" lg="8">
            <VTabs v-model="recyclingTab" class="v-tabs-pill">
                <VTab v-for="tab in tabs" :key="tab.icon">
                    <VIcon :size="18" :icon="tab.icon" class="me-1" />
                    <span>{{ tab.title }}</span>
                </VTab>
            </VTabs>

            <VWindow v-model="recyclingTab" class="mt-6 disable-tab-transition" :touch="false">
                <VWindowItem>
                    <RecyclingDetailTab :recycling-data="props.recycling"/>
                </VWindowItem>
            </VWindow>
        </VCol>
    </VRow>
</Layout>
</template>
