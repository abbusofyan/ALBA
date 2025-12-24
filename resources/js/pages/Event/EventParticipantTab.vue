<script setup>
import { ref, computed, onMounted, watch } from 'vue'
import {
    debounce
} from "lodash";
import {
    router
} from "@inertiajs/vue3";
const props = defineProps({
	eventData: {
		type: Object,
		required: true
	}
})

const participants = ref({
    data: [],
    total: 0,
});
const loading = ref(true);
const search = ref("");
const page = ref(1);
const itemsPerPage = ref(10);
const sortBy = ref([]);

const headers = [
    { title: 'Name', key: 'first_name' },
	{ title: 'Nickname', key: 'display_name' },
	{ title: 'Email', key: 'email' },
]

const fetchData = async () => {
    loading.value = true;
    try {
        const response = await axios.get(
            route("events.participants", props.eventData.id), {
                params: {
                    page: page.value,
                    paginate: itemsPerPage.value,
                    sort_by: sortBy.value[0]?.key,
                    sort_order: sortBy.value[0]?.order,
                    search: search.value,
                },
            }
        );
        participants.value = response.data;
    } catch (error) {
        console.error("Error fetching event activities :", error);
    } finally {
        loading.value = false;
    }
};

watch(search, debounce(fetchData, 300));

const detailUser = async (id) => {
    router.visit(route('users.show', id));
};

</script>

<template>
<VRow>
    <VCol cols="12">
        <VCard title="Event participant :">
            <VCardText>
				<VRow>
					<VCol md="10">
						<!-- Search Input -->
						<VTextField
						v-model="search"
						label="Search Participant"
						class="mb-4"
						prepend-inner-icon="mdi-magnify"
						/>
					</VCol>
					<VCol md="2">
						<v-btn :href="`/events/download-participant/${eventData.id}`" color="info" class="me-2 mb-2">
							<v-icon end icon="tabler-download" class="me-2" />
							Download
						</v-btn>
					</VCol>
				</VRow>

				<VDataTableServer v-model:items-per-page="itemsPerPage" v-model:page="page" :items-length="participants.total" :headers="headers" :items="participants.data" :loading="loading" class="text-no-wrap" v-model:sort-by="sortBy"
                    @update:options="fetchData">
                    <template #item.first_name="{ item }">
						<a :href="`#`" @click.prevent="detailUser(item.id)" class="font-weight-medium text-link">
							{{ item.name ? item.name : item }}
						</a>
                    </template>

					<template #item.display_name="{ item }">
						{{item.display_name}}
                    </template>

					<template #item.email="{ item }">
						{{item.email}}
                    </template>
				</VDataTableServer>
            </VCardText>
        </VCard>
    </VCol>
</VRow>
</template>
