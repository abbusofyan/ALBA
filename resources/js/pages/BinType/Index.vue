<template>
<Head title="Bin Type" />
<Layout>
    <v-breadcrumbs :items="breadcrumbs">
        <template v-slot:prepend></template>
    </v-breadcrumbs>
    <section>
        <VCard class="mb-6" title="Search Filter">
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

                <v-col cols="12" md="10">
                    <v-row class="flex-wrap" no-gutters>
                        <v-col cols="12" md="9">
                            <v-sheet class="ma-2 pa-2">
                                <AppTextField v-model="search" placeholder="Search Bin Type" class="w-100" />
                            </v-sheet>
                        </v-col>
                        <v-col cols="12" md="3">
                            <v-sheet class="ma-2 pa-2">
                                <div class="d-flex align-center flex-wrap justify-end">
                                    <v-btn v-if="$filters.can('create-bin-type')" @click="addBinType" color="primary" class="mb-2">
                                        <v-icon end icon="tabler-plus" class="me-2" />
                                        Add New Bin Type
                                    </v-btn>
                                </div>
                            </v-sheet>
                        </v-col>
                    </v-row>
                </v-col>
            </v-row>

            <VDataTableServer v-model:items-per-page="itemsPerPage" v-model:page="page" :items-length="binTypesData.total" :items-per-page-options="[
                { value: 5, title: '5' },
                { value: 10, title: '10' },
                { value: -1, title: '$vuetify.dataFooter.itemsPerPageAll' }
	            ]" v-model:sort-by="sortBy" :headers="headers" :items="binTypesData.data" item-value="id" class="text-no-wrap">

                <template #item.name="{ item }">
                    <div class="d-flex align-center gap-x-4">
                        <div class="d-flex flex-column">
                            <h6 class="text-base">
                                <a :href="`#`" @click.prevent="detailBinType(item.id)" class="font-weight-medium text-link">
                                    {{ item.name }}
                                </a>
                            </h6>
                        </div>
                    </div>
                </template>

                <template #item.image="{ item }">
                    <div class="d-flex align-center gap-x-4">
                        <div class="d-flex flex-column">
                            <img height="85" :src="`/storage/images/bin-types/${item.image}`" alt="" class="cursor-pointer" @click="showImage(`/storage/images/bin-types/${item.image}`)">
                        </div>
                    </div>
                </template>

                <template #item.actions="{ item }">
                    <VBtn icon variant="text" color="medium-emphasis">
                        <VIcon icon="tabler-dots-vertical" />
                        <VMenu activator="parent">
                            <VList>
                                <VListItem @click="detailBinType(item.id)">
                                    <template #prepend>
                                        <VIcon icon="tabler-eye" />
                                    </template>

                                    <VListItemTitle>View</VListItemTitle>
                                </VListItem>

                                <VListItem v-if="$filters.can('update-bin-type')" @click="editBinType(item.id)">
                                    <template #prepend>
                                        <VIcon icon="tabler-pencil" />
                                    </template>
                                    <VListItemTitle>Edit</VListItemTitle>
                                </VListItem>

                            </VList>
                        </VMenu>
                    </VBtn>
                </template>

                <template #bottom>
                    <TablePagination v-model:page="page" :items-per-page="itemsPerPage" :total-items="binTypesData.total" />
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
import Swal from "sweetalert2";
import {
    toast
} from "vue3-toastify";
import Layout from "../../layouts/blank.vue";
import {
    computed,
    ref
} from 'vue';

const dialog = ref(false)
const selectedImage = ref('')

const showImage = (src) => {
    selectedImage.value = src
    dialog.value = true
}

const schools = ref([]);

const props = defineProps({
    binTypesData: Object,
    filters: Object,
    qty_bin_type: Number,
})

const breadcrumbs = [{
        title: 'Bin Types',
        disabled: false,
        href: '/bin-types',
    },
    {
        title: 'Bin Type List',
        disabled: true,
    }
]

const headers = [{
        title: "Name",
        key: "name"
    },
    {
        title: "Image",
        key: "image"
    },
	{
		title: "CO2 Points",
		key: "point"
	},
    {
        title: "QR Code Type",
        key: "qrcode_type"
    },
    {
        title: "Actions",
        key: "actions",
        sortable: false
    },
];

const search = ref(props.filters.search || "");
const page = ref(props.binTypesData.current_page);
const itemsPerPage = ref(props.binTypesData.per_page);
const sortBy = ref([])
const filterStatus = ref(props.binTypesData.status);

const fetchData = async () => {
    router.get(
        route("bin-types.index"), {
            search: search.value,
            page: page.value,
            paginate: itemsPerPage.value,
            sort_by: sortBy.value[0]?.key,
            sort_order: sortBy.value[0]?.order,
            status: filterStatus.value
        }, {
            preserveState: true,
            preserveScroll: true,
            replace: true,
        }
    );
};

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
});

watch(search, debounce(fetchData, 300));
watch([page, itemsPerPage, sortBy, filterStatus], fetchData);

const updateOptions = (options) => {
    page.value = options.page;
    itemsPerPage.value = options.itemsPerPage;
};

const detailBinType = async (id) => {
    router.visit(route('bin-types.show', id));
};

const editBinType = async (id) => {
    router.visit(route('bin-types.edit', id));
};

const addBinType = async (id) => {
    router.visit(route('bin-types.create'));
};
</script>
