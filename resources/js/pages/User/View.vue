<script setup>
import {
    Head
} from "@inertiajs/vue3";
import Layout from "../../layouts/blank.vue";
import UserDetailPanel from '../User/UserDetailPanel.vue';
import UserDetailTab from '../User/UserDetailTab.vue';
import UserActivityTab from '../User/UserActivityTab.vue';
import UserEventTab from '../User/UserEventTab.vue';
import UserVoucherTab from '../User/UserVoucherTab.vue';

const userTab = ref(0)

const props = defineProps({
    user: Object,
	activities: Object,
	filters: Object
})
const tabs = [
	{
        icon: 'tabler-user',
        title: 'Account Details',
    },
	{
		icon: 'tabler-history',
		title: 'Activity',
	},
	{
		icon: 'tabler-calendar-check',
		title: 'Joined Event',
	},
	{
		icon: 'tabler-ticket',
		title: 'Redemeed Vouchers',
	},
]

const breadcrumbs = [
	{
		title: 'Users',
		disabled: false,
		href: '/users',
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
            <UserDetailPanel :user-data="props.user" />
        </VCol>

        <VCol cols="12" md="7" sm="12" lg="8">
            <VTabs v-model="userTab" class="v-tabs-pill">
                <VTab v-for="tab in tabs" :key="tab.icon">
                    <VIcon :size="18" :icon="tab.icon" class="me-1" />
                    <span>{{ tab.title }}</span>
                </VTab>
            </VTabs>

            <VWindow v-model="userTab" class="mt-6 disable-tab-transition" :touch="false">
                <VWindowItem>
                    <UserDetailTab :user-data="props.user"/>
                </VWindowItem>
				<VWindowItem>
                    <UserActivityTab :user-data="props.user" />
                </VWindowItem>
				<VWindowItem>
                    <UserEventTab :events="props.user.joined_events" />
                </VWindowItem>
				<VWindowItem>
                    <UserVoucherTab :user-data="props.user" />
                </VWindowItem>
            </VWindow>
        </VCol>
    </VRow>
</Layout>
</template>
