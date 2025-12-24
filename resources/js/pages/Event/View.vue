<script setup>
import {
    Head
} from "@inertiajs/vue3";
import Layout from "../../layouts/blank.vue";
import EventDetailPanel from '../Event/EventDetailPanel.vue';
import EventDetailTab from '../Event/EventDetailTab.vue';
import EventLocationTab from '../Event/EventLocationTab.vue';
import EventParticipantTab from '../Event/EventParticipantTab.vue';
import EventActivityTab from '../Event/EventActivityTab.vue';
import EventCheckInBinTab from '../Event/EventCheckInBinTab.vue';

const eventTab = ref(null)

const props = defineProps({
    event: Object
})

const tabs = [
    {
        icon: 'tabler-calendar',
        title: 'Event Details',
    },
    {
        icon: 'tabler-map',
        title: 'Event Location',
    },
	{
        icon: 'tabler-history',
        title: 'Activities',
    },
	{
        icon: 'tabler-trash',
        title: 'Check-in Bin',
    },
    ...(props.event.event_type_id === 3 || props.event.event_type_id === 4
        ? [{
            icon: 'tabler-users',
            title: 'Participant',
        }]
        : []
    ),
]

const breadcrumbs = [
	{
		title: 'Events',
		disabled: false,
		href: '/events',
	},
	{
		title: 'Event Details',
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
	<VRow v-if="props.event">
        <VCol>
            <EventDetailPanel :event-data="props.event" />
        </VCol>

        <VCol cols="12" md="7" sm="12" lg="8">
            <VTabs v-model="eventTab" class="v-tabs-pill">
                <VTab v-for="tab in tabs" :key="tab.icon">
                    <VIcon :size="18" :icon="tab.icon" class="me-1" />
                    <span>{{ tab.title }}</span>
                </VTab>
            </VTabs>

            <VWindow v-model="eventTab" class="mt-6 disable-tab-transition" :touch="false">
                <VWindowItem>
                    <EventDetailTab :event-data="props.event"/>
                </VWindowItem>

				<VWindowItem>
                    <EventLocationTab :event-data="props.event"/>
                </VWindowItem>

				<VWindowItem>
					<EventActivityTab :event-data="props.event"/>
                </VWindowItem>

				<VWindowItem>
					<EventCheckInBinTab :event-data="props.event"/>
                </VWindowItem>

				<VWindowItem>
                    <EventParticipantTab :event-data="props.event"/>
                </VWindowItem>
            </VWindow>
        </VCol>
    </VRow>
</Layout>
</template>
