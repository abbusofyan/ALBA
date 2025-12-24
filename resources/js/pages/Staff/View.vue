<script setup>
import {
    Head
} from "@inertiajs/vue3";
import Layout from "../../layouts/blank.vue";
import StaffDetailPanel from '../Staff/StaffDetailPanel.vue';
import StaffDetailTab from '../Staff/StaffDetailTab.vue';

const staffTab = ref(null)

const props = defineProps({
    user: Object
})

const tabs = [
	{
        icon: 'tabler-user',
        title: 'Account Details',
    },
]

const breadcrumbs = [
	{
		title: 'Staffs',
		disabled: false,
		href: '/staffs',
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
            <StaffDetailPanel :user-data="props.user" />
        </VCol>

        <VCol cols="12" md="7" sm="12" lg="8">
            <VTabs v-model="staffTab" class="v-tabs-pill">
                <VTab v-for="tab in tabs" :key="tab.icon">
                    <VIcon :size="18" :icon="tab.icon" class="me-1" />
                    <span>{{ tab.title }}</span>
                </VTab>
            </VTabs>

            <VWindow v-model="staffTab" class="mt-6 disable-tab-transition" :touch="false">
                <VWindowItem>
                    <StaffDetailTab :user-data="props.user"/>
                </VWindowItem>
            </VWindow>
        </VCol>
    </VRow>
</Layout>
</template>
