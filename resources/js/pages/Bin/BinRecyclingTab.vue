<script setup>
import { router } from "@inertiajs/vue3";
import {
    toast
} from "vue3-toastify";
import { ref, computed, onMounted, watch } from 'vue'
import {
    debounce
} from "lodash";

const props = defineProps({
    binData: {
        type: Object,
        required: true,
    },
});

const recyclings = ref({
    data: [],
    total: 0,
});
const loading = ref(true);
const search = ref("");
const page = ref(1);
const itemsPerPage = ref(10);
const sortBy = ref([]);

const headers = [{
        title: "Date/Time",
        key: "created_at"
    },
    {
        title: "User",
        key: "user"
    },
	{
        title: 'Reward',
        key: 'reward'
    },
	{
        title: 'Action',
        key: 'actions'
    }
];

const fetchData = async () => {
    loading.value = true;
    try {
        const response = await axios.get(
            route("bins.recyclings", props.binData.id), {
                params: {
                    page: page.value,
                    paginate: itemsPerPage.value,
                    sort_by: sortBy.value[0]?.key,
                    sort_order: sortBy.value[0]?.order,
                    search: search.value,
                },
            }
        );
        recyclings.value = response.data;
    } catch (error) {
        console.error("Error fetching bin activities :", error);
    } finally {
        loading.value = false;
    }
};

watch(search, debounce(fetchData, 300));

const dialog = ref(false)
const selectedImage = ref('')

const showImage = (src) => {
    selectedImage.value = src
    dialog.value = true
}

const detailRecycling = async (id) => {
    router.visit(route('recyclings.show', id));
};

const detailUser = async (id) => {
  router.visit(route("users.show", id));
};

</script>

<template>
<VRow>
    <VCol cols="12">
		<VCard title="Bin Activity :">
            <VCardText>
                <VRow class="mb-4">
                    <VCol cols="12" md="12">
                        <AppTextField v-model="search" placeholder="Search..." />
                    </VCol>
                </VRow>
                <VDataTableServer v-model:items-per-page="itemsPerPage" v-model:page="page" :items-length="recyclings.total" :headers="headers" :items="recyclings.data" :loading="loading" class="text-no-wrap" v-model:sort-by="sortBy"
                    @update:options="fetchData">
					<template #item.created_at="{ item }">
						{{$filters.formatDateTime(item.created_at)}}
					</template>

	                <template #item.user="{ item }">
	                    <div class="d-flex align-center gap-x-4">
	                        <div class="d-flex flex-column">
	                            <a :href="`#`" @click.prevent="detailUser(item.user_id)" class="text-link">
	                                {{ item.user.name }}<br>
									{{ item.user.email }}
	                            </a>
	                        </div>
	                    </div>
	                </template>

	                <template #item.bin="{ item }">
						<a v-if="item.bin" :href="`#`" @click.prevent="detailBin(item.bin.id)" class="font-weight-medium text-link">
							{{item.bin?.code || '-'}}
						</a>
	            	</template>

					<template #item.reward="{ item }">
	                    {{item.reward}} Pts
	                </template>

	                <template #item.actions="{ item }">
	                    <VBtn icon variant="text" color="medium-emphasis">
	                        <VIcon icon="tabler-dots-vertical" />
	                        <VMenu activator="parent">
	                            <VList>
	                                <VListItem @click="detailRecycling(item.id)">
	                                    <template #prepend>
	                                        <VIcon icon="tabler-eye" />
	                                    </template>

	                                    <VListItemTitle>View Detail</VListItemTitle>
	                                </VListItem>

									<VListItem @click="showImage(`/storage/photos/recycling/${item.photo}`)">
	                                    <template #prepend>
	                                        <VIcon icon="tabler-camera" />
	                                    </template>

	                                    <VListItemTitle>View Recycling Pict</VListItemTitle>
	                                </VListItem>
	                            </VList>
	                        </VMenu>
	                    </VBtn>
	                </template>

                    <template #bottom>
                        <TablePagination v-model:page="page" :items-per-page="itemsPerPage" :total-items="recyclings.total" />
                    </template>
                </VDataTableServer>

				<v-dialog v-model="dialog" max-width="600px">
	                <v-card>
	                    <img :src="selectedImage" alt="" style="max-width: 100%; height: auto;" />
	                    <v-card-actions>
	                        <v-spacer></v-spacer>
	                        <v-btn text @click="dialog = false">Close</v-btn>
	                    </v-card-actions>
	                </v-card>
	            </v-dialog>
            </VCardText>
        </VCard>
    </VCol>
</VRow>
</template>

<style scoped>
.map-container {
    position: relative;
    z-index: 1;
    overflow: hidden;
}

.map-container :deep(.leaflet-container) {
    z-index: 1;
}

.map-container :deep(.leaflet-control-container) {
    z-index: 100;
}

.map-container :deep(.leaflet-popup-pane) {
    z-index: 200;
}

.map-container :deep(.leaflet-tooltip-pane) {
    z-index: 150;
}

.map-container :deep(.leaflet-marker-pane) {
    z-index: 50;
}

.map-container :deep(.leaflet-tile-pane) {
    z-index: 1;
}
</style>
