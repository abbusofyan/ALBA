<template>
<v-overlay :model-value="isLoading" class="d-flex align-center justify-center" persistent opacity="0.6" style="z-index: 3000">
    <v-card class="pa-5 text-center">
        <v-progress-circular indeterminate color="primary" size="40" />
        <div class="mt-4">Processing...</div>
    </v-card>
</v-overlay>

<Head title="Recycling" />
<Layout>
    <v-breadcrumbs :items="breadcrumbs">
        <template v-slot:prepend></template>
    </v-breadcrumbs>
    <section>

		<VCard class="mb-6" title="Recycling Activity Per Bin (Weekly Report)">
			<RecyclingDashboard ref="dashboardRef" :stats="stats" :chart-data="chart_data" />
        </VCard>

        <VCard class="mb-6" title="Search Filter">
            <v-row class="flex-wrap" no-gutters>
                <v-col cols="12">
                    <v-sheet class="ma-2 pa-2">
                        <AppSelect :model-value="binType" :items="binTypeOptions" @update:model-value="binType = $event" placeholder="Select bin type" />
                    </v-sheet>
                </v-col>
            </v-row>

            <v-divider></v-divider>

            <v-row class="flex-wrap" no-gutters>
                <v-col cols="12" md="2">
                    <v-sheet class="ma-2 pa-2">
                        <AppSelect :model-value="itemsPerPage" :items="[
				          { value: 10, title: '10' },
				          { value: 25, title: '25' },
				          { value: 50, title: '50' },
				          { value: 100, title: '100' },
				          { value: -1, title: 'All' }
				        ]" @update:model-value="itemsPerPage = parseInt($event, 10)" />
                    </v-sheet>
                </v-col>

                <v-col cols="12" md="8">
                    <v-sheet class="ma-2 pa-2">
                        <AppTextField v-model="search" placeholder="Search" />
                    </v-sheet>
                </v-col>

                <v-col cols="12" md="2">
                    <v-sheet class="ma-2 pa-2">
                        <div class="d-flex align-center flex-wrap justify-between">
							<v-btn href="recyclings/exportStream" color="info" class="me-2 mb-2">
								<v-icon end icon="tabler-download" class="me-2" />
								Export
							</v-btn>
                        </div>
                    </v-sheet>
                </v-col>
            </v-row>

            <VDataTableServer v-model:items-per-page="itemsPerPage" v-model:page="page" :items-length="recyclingsData.total" :items-per-page-options="[
                { value: 5, title: '5' },
                { value: 10, title: '10' },
                { value: -1, title: '$vuetify.dataFooter.itemsPerPageAll' }
	            ]" v-model:sort-by="sortBy" :headers="headers" :items="recyclingsData.data" item-value="id" class="text-no-wrap">

				<template #item.created_at="{ item }">
					{{$filters.formatDateTime(item.created_at)}}
				</template>

                <template #item.user_id="{ item }">
                    <div class="d-flex align-center gap-x-4">
                        <div class="d-flex flex-column">
                            <a :href="`#`" @click.prevent="detailUser(item.user_id)" class="text-link">
                                {{ item.user.name }}<br>
								{{ item.user.email }}
                            </a>
                        </div>
                    </div>
                </template>

                <template #item.bin_id="{ item }">
					<a v-if="item.bin" :href="`#`" @click.prevent="detailBin(item.bin.id)" class="font-weight-medium text-link">
						{{item.bin?.code || '-'}}
					</a>
					<!-- <img height="85" :src="`/storage/images/bin-types/${item.bin_type.image}`" alt="" class="cursor-pointer" @click="showImage(`/storage/images/bin-types/${item.bin_type.image}`)"> -->
            	</template>

				<template #item.address="{ item }">
					<div style="white-space: normal; word-break: break-word;">
						{{ item.bin?.address || '-' }}
					</div>
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
                    <TablePagination v-model:page="page" :items-per-page="itemsPerPage" :total-items="recyclingsData.total" />
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
        </VCard>
    </section>
</Layout>
</template>
<script setup>
import {
    Head,
    router,
    usePage
} from "@inertiajs/vue3";
import {
    debounce
} from "lodash";
import {
    computed,
    ref
} from "vue";
import {
    toast
} from "vue3-toastify";
import Layout from "../../layouts/blank.vue";
import RecyclingDashboard from './RecyclingDashboard.vue';

const dashboardRef = ref(null)

const props = defineProps({
    recyclingsData: Object,
    filters: Object,
    roles: Object,
    qty_bin: Number,
    bin_types: Object,
	stats: Array,
	chart_data: Array
})

const binTypeOptions = computed(() => [{
        value: 'all',
        title: 'All'
    },
    ...props.bin_types.map(type => ({
        value: type.id,
        title: type.name
    }))
]);

const breadcrumbs = [{
    title: 'Recyclings',
    disabled: false,
    href: '/recyclings',
}]

const headers = [
    {
        title: "Date/Time",
        key: "created_at"
    },
    {
        title: "User",
        key: "user_id"
    },
    {
        title: "Bin",
        key: "bin_id"
    },
    {
        title: "Address",
        key: "address"
    },
	{
		title: "Reward",
		key: "reward"
	},
    {
        title: "Actions",
        key: "actions",
        sortable: false
    },
];

const search = ref(props.filters.search || "");
const page = ref(props.recyclingsData.current_page);
const itemsPerPage = ref(props.recyclingsData.per_page);
const sortBy = ref([])
const binType = ref(props.recyclingsData.bin_type);
const status = ref(props.recyclingsData.status);

const fetchData = async () => {
    router.get(
        route("recyclings.index"), {
            search: search.value,
            page: page.value,
            paginate: itemsPerPage.value,
            sort_by: sortBy.value[0]?.key,
            sort_order: sortBy.value[0]?.order,
            bin_type: binType.value,
            status: status.value
        }, {
            preserveState: true,
            preserveScroll: true,
            replace: true,
        }
    );
};

const filterBinTypeMap = ref([]);

onMounted(() => {
    const flash = usePage().props.flash

    if (flash.success) {
        toast.success(flash.success, {
            theme: "colored",
            type: "success",
        });
    } else if (flash.error) {
        toast.error(flash.error, {
            theme: "colored",
            type: "error",
        });
    }

    filterBinTypeMap.value = Object.values(props.bin_types).map(type => type.id);
});

watch(search, debounce(fetchData, 300));
watch([page, itemsPerPage, sortBy, binType, status], fetchData);

const updateOptions = (options) => {
    page.value = options.page;
    itemsPerPage.value = options.itemsPerPage;
};

const detailUser = async (id) => {
    router.visit(route('users.show', id));
};

const detailRecycling = async (id) => {
    router.visit(route('recyclings.show', id));
};

const editBin = async (id) => {
    router.visit(route('recyclings.edit', id));
};

const addBin = async (id) => {
    router.visit(route('recyclings.create'));
};

const resolveBinRoleVariant = () => {
    return;
}

const detailBin = async (id) => {
    router.visit(route("bins.show", id));
};

const selectedRecyclings = ref([])

const dialog = ref(false)
const selectedImage = ref('')

const showImage = (src) => {
    selectedImage.value = src
    dialog.value = true
}

</script>
