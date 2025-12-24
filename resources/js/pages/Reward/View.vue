<script setup>
import {
    Head
} from "@inertiajs/vue3";
import Layout from "../../layouts/blank.vue";
import RewardDetailPanel from '../Reward/RewardDetailPanel.vue';
import RewardDetailTab from '../Reward/RewardDetailTab.vue';
import VouchersTab from '../Reward/VouchersTab.vue';

const rewardTab = ref(null)

const props = defineProps({
    reward: Object
})

const tabs = [
	{
        icon: 'tabler-package',
        title: 'Reward Detail',
    },
	{
		icon: 'tabler-ticket',
		title: 'Vouchers Assignment',
	},
]

const breadcrumbs = [
	{
		title: 'Rewards',
		disabled: false,
		href: '/rewards',
	},
	{
		title: 'Reward Details',
		disabled: true,
	}
]
</script>

<template>
<Head title="Detail Reward" />
<Layout>
    <v-breadcrumbs :items="breadcrumbs">
        <template v-slot:prepend></template>
    </v-breadcrumbs>
	<VRow v-if="props.reward">
        <VCol>
            <RewardDetailPanel :reward-data="props.reward" />
        </VCol>

        <VCol cols="12" md="7" sm="12" lg="8">
            <VTabs v-model="rewardTab" class="v-tabs-pill">
                <VTab v-for="tab in tabs" :key="tab.icon">
                    <VIcon :size="18" :icon="tab.icon" class="me-1" />
                    <span>{{ tab.title }}</span>
                </VTab>
            </VTabs>

            <VWindow v-model="rewardTab" class="mt-6 disable-tab-transition" :touch="false">
                <VWindowItem>
                    <RewardDetailTab :reward-data="props.reward"/>
                </VWindowItem>

				<VWindowItem>
                    <VouchersTab :reward-data="props.reward"/>
                </VWindowItem>
            </VWindow>

			
        </VCol>
    </VRow>
</Layout>
</template>
