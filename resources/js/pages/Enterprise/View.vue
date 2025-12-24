<script setup>
import {
    Head
} from "@inertiajs/vue3";
import Layout from "../../layouts/blank.vue";
import EnterpriseDetailPanel from '../Enterprise/EnterpriseDetailPanel.vue';
import EnterpriseDetailTab from '../Enterprise/EnterpriseDetailTab.vue';
import BinTab from '../Enterprise/BinTab.vue';

const enterpriseTab = ref(null)

const props = defineProps({
    user: Object
})

const tabs = [
	{
        icon: 'tabler-user',
        title: 'Account Details',
    },
	{
        icon: 'tabler-trash',
        title: 'Bins',
    },
]

const breadcrumbs = [
	{
		title: 'Enterprises',
		disabled: false,
		href: '/enterprises',
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
            <EnterpriseDetailPanel :user-data="props.user" />
        </VCol>

        <VCol cols="12" md="7" sm="12" lg="8">
            <VTabs v-model="enterpriseTab" class="v-tabs-pill">
                <VTab v-for="tab in tabs" :key="tab.icon">
                    <VIcon :size="18" :icon="tab.icon" class="me-1" />
                    <span>{{ tab.title }}</span>
                </VTab>
            </VTabs>

            <VWindow v-model="enterpriseTab" class="mt-6 disable-tab-transition" :touch="false">
                <VWindowItem>
                    <EnterpriseDetailTab :user-data="props.user"/>
                </VWindowItem>

				<VWindowItem>
                    <BinTab :bins-data="props.user.bins"/>
                </VWindowItem>
            </VWindow>
        </VCol>
    </VRow>
</Layout>
</template>
