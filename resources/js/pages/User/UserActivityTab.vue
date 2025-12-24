<script setup>
import {
    debounce
} from "lodash";
import {
    onMounted,
    ref,
    watch
} from "vue";
import { router } from "@inertiajs/vue3";

const props = defineProps({
    userData: {
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
        key: "created_at",
    },
    {
        title: "Bin",
        key: "bin",
		width: '15%'
    },
	{
        title: 'Address',
        key: 'address',
		width: '50%'
    },
	{
        title: 'Reward',
        key: 'reward',
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
            route("users.recyclings", props.userData.id), {
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
        console.error("Error fetching event activities :", error);
    } finally {
        loading.value = false;
    }
};

onMounted(fetchData);

watch(search, debounce(fetchData, 300));
// watch([page, itemsPerPage, sortBy], fetchData, {
//     deep: true
// });

const detailRecycling = async (id) => {
    router.visit(route('recyclings.show', id));
};

const detailBin = async (id) => {
    router.visit(route('bins.show', id));
};


const dialog = ref(false)
const selectedImage = ref('')

const showImage = (src) => {
    selectedImage.value = src
    dialog.value = true
}

</script>


<style scoped>
/* Force table layout fixed for equal column widths */
::v-deep(.v-table) {
table-layout: fixed !important;
width: 100% !important;
}

/* Wrap content in cells (body only) */
::v-deep(.v-data-table__td) {
white-space: normal !important;
word-break: break-word !important;
}

/* Prevent wrapping on headers */
::v-deep(.v-data-table__th) {
white-space: nowrap !important;
overflow: hidden;
text-overflow: ellipsis;
}
</style>

<template>
<VRow>
    <VCol cols="12">
        <VCard title="User Activities :">
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

	                <template #item.bin="{ item }">
						<a :href="`#`" @click.prevent="detailBin(item.bin.id)" class="font-weight-medium text-link">
							{{item.bin?.code || '-'}} <br>
						</a>
						{{item.bin_type.name}}
	            	</template>

					<template #item.address="{ item }">
	                    {{item.bin?.address || '-'}}
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
