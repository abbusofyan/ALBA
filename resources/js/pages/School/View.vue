<script setup>
import {
    Head
} from "@inertiajs/vue3";
import Layout from "../../layouts/blank.vue";
import SchoolDetailPanel from '../School/SchoolDetailPanel.vue';
import SchoolDetailTab from '../School/SchoolDetailTab.vue';
import BinTab from '../School/BinTab.vue';

const schoolTab = ref(null)

const props = defineProps({
    user: Object
})

const tabs = [
	{
        icon: 'tabler-user',
        title: 'School Details',
    },
	{
        icon: 'tabler-trash',
        title: 'Bins',
    },
]

const breadcrumbs = [
	{
		title: 'Schools',
		disabled: false,
		href: '/schools',
	},
	{
		title: props.user.name,
		disabled: true,
	}
]
</script>

<template>
<Head title="Detail User" />
<Layout>
    <v-breadcrumbs :items="breadcrumbs">
        <template v-slot:prepend></template>
    </v-breadcrumbs>
	<VRow v-if="props.user">
        <VCol>
            <SchoolDetailPanel :user-data="props.user" />
        </VCol>

        <VCol cols="12" md="7" sm="12" lg="8">
            <VTabs v-model="schoolTab" class="v-tabs-pill">
                <VTab v-for="tab in tabs" :key="tab.icon">
                    <VIcon :size="18" :icon="tab.icon" class="me-1" />
                    <span>{{ tab.title }}</span>
                </VTab>
            </VTabs>

            <VWindow v-model="schoolTab" class="mt-6 disable-tab-transition" :touch="false">
                <VWindowItem>
                    <SchoolDetailTab :user-data="props.user"/>
                </VWindowItem>

				<VWindowItem>
                    <BinTab :bins-data="props.user.bins"/>
                </VWindowItem>
            </VWindow>
        </VCol>
    </VRow>
</Layout>
</template>
