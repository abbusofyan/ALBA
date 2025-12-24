<template>
<Head title="Banner" />
<Layout>
    <v-breadcrumbs :items="breadcrumbs">
        <template v-slot:prepend></template>
    </v-breadcrumbs>
    <section>
        <VCard class="mb-6">

			<v-row class="flex-wrap" no-gutters>

			</v-row>

			<v-divider></v-divider>

            <!-- <v-row class="flex-wrap" no-gutters>
                <v-col cols="12" md="2">
                    <v-sheet class="ma-2 pa-2">
                        <AppSelect :model-value="itemsPerPage" :items="[
				          { value: 10, title: '10' },
				          { value: 25, title: '25' },
				          { value: 50, title: '50' },
				          { value: 100, title: '100' },
				          { value: -1, title: 'All' }
				        ]" @update:model-value="itemsPerPage = parseInt($banner, 10)" />
                    </v-sheet>
                </v-col>
				<v-col cols="12" md="4">
					<v-sheet class="ma-2 pa-2">
						<AppSelect :model-value="status" :items="[
							{ value: 'all', title: 'All' },
							{ value: 1, title: 'Active' },
							{ value: 0, title: 'Inactive' },
						]" @update:model-value="status = $banner" placeholder="Select Status" />
					</v-sheet>
				</v-col>
				<v-col cols="12" md="6">
					<v-row class="flex-wrap" no-gutters>
						<v-col cols="12" md="12">
							<v-sheet class="ma-2 pa-2">
								<AppTextField v-model="search" placeholder="Search Banner" class="w-100"/>
							</v-sheet>
						</v-col>
		            </v-row>
                </v-col>

            </v-row> -->

			<v-dialog v-model="dialog" max-width="600px">
				<v-card>
					<img :src="selectedImage" alt="" style="max-width: 100%; height: auto;" />
					<v-card-actions>
						<v-spacer></v-spacer>
						<v-btn text @click="dialog = false">Close</v-btn>
					</v-card-actions>
				</v-card>
			</v-dialog>

            <VDataTableServer v-model:items-per-page="itemsPerPage" v-model:page="page" :items-length="bannerData.total" :items-per-page-options="[
                { value: 5, title: '5' },
                { value: 10, title: '10' },
                { value: -1, title: '$vuetify.dataFooter.itemsPerPageAll' }
	            ]" v-model:sort-by="sortBy" :headers="headers" :items="bannerData.data" item-value="id" class="text-no-wrap">

				<template #item.id="{ item }">
					{{item.name}}
				</template>

				<template #item.image="{ item }">
					<v-carousel
						v-if="item.banners.length > 0"
						height="100%"
						hide-delimiters
						show-arrows="hover"
						cycle
						class="rounded-md elevation-1"
					>
						<v-carousel-item
							v-for="(banner, i) in item.banners"
							:key="i"
						>
							<v-img
								:src="banner.image_url" @click="showImage(banner.image_url)"
								cover
								class="rounded"
							/>
						</v-carousel-item>
					</v-carousel>
				</template>

				<template #item.status="{ item }">
                    <VChip v-if="item.banners" :color="$filters.resolveStatusVariant(item.status)" size="small" label class="text-capitalize">
                        {{ item.status_text }}
                    </VChip>
                </template>

				<template #item.url="{ item }">
					<a :href="item.banner?.url" target="_blank">{{item.banner?.url}}</a>
				</template>

                <template #item.actions="{ item }">
                    <VBtn v-if="$filters.can('update-banner')" icon variant="text" color="medium-emphasis">
                        <VIcon icon="tabler-dots-vertical" />
                        <VMenu activator="parent">
                            <VList>
                                <VListItem v-if="$filters.can('update-banner')" @click="editBanner(item.id)">
                                    <template #prepend>
                                        <VIcon icon="tabler-pencil" />
                                    </template>
                                    <VListItemTitle>Edit</VListItemTitle>
                                </VListItem>

                                <VListItem v-if="$filters.can('update-banner')" @click="toggleStatusBanner(item.id, item.status)" variant="text">
                                    <template #prepend>
                                        <VIcon icon="tabler-trash" />
                                    </template>
                                    <VListItemTitle>{{item.status ? 'Deactivate' : 'Activate'}}</VListItemTitle>
                                </VListItem>
                            </VList>
                        </VMenu>
                    </VBtn>
                </template>

                <template #bottom>
                    <TablePagination v-model:page="page" :items-per-page="itemsPerPage" :total-items="bannerData.total" />
                </template>
            </VDataTableServer>
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
    computed,
    ref
} from 'vue';
import {
    toast
} from "vue3-toastify";
import Layout from "../../layouts/blank.vue";

const banner = ref([]);

const props = defineProps({
    bannerData: Object,
    filters: Object,
    qty_banner: Number,
})

const breadcrumbs = [{
        title: 'Banner',
        disabled: false,
        href: '/banners',
    },
    {
        title: 'Banner List',
        disabled: true,
    }
]

const headers = [
    {
        title: "Placement",
        key: "id"
    },
    {
        title: "Image",
        key: "image"
    },
	{
        title: "URL",
        key: "url"
    },
    {
        title: "Status",
        key: "status"
    },
    {
        title: "Actions",
        key: "actions",
        sortable: false
    },
];

const search = ref(props.filters.search || "");
const page = ref(props.bannerData.current_page);
const itemsPerPage = ref(props.bannerData.per_page);
const sortBy = ref([])
const status = ref(props.bannerData.status);

const fetchData = async () => {
    router.get(
        route("banners.index"), {
            search: search.value,
            page: page.value,
            paginate: itemsPerPage.value,
            sort_by: sortBy.value[0]?.key,
            sort_order: sortBy.value[0]?.order,
            status: status.value
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
watch([page, itemsPerPage, sortBy, status], fetchData);

const updateOptions = (options) => {
    page.value = options.page;
    itemsPerPage.value = options.itemsPerPage;
};

const toggleStatusBanner = async (id, isActive) => {
	let text = 'This banner will be activated'
	if (isActive) {
		text = 'This banner will be deactivated.'
	}

    const result = await Swal.fire({
        title: "Are you sure? <br> <i class='fa-solid fa-trash-can'></i>",
        text: text,
        icon: "warning",
        showCancelButton: true,
        confirmButtonColor: "#ea5455",
        cancelButtonColor: "#6CC9CF",
        confirmButtonText: "Yes, Proceed!",
        cancelButtonText: "Cancel",
    });

    if (result.isConfirmed) {
        try {
            const response = await axios.post(`/banners/${id}/toggleStatus`);
            if (response.data.success) {
                toast.success(response.data.message, {
                    theme: "colored",
                    type: "success",
                });
                fetchData();
            } else {
                toast.error(response.data.message, {
                    theme: "colored",
                    type: "error",
                });
            }
        } catch (error) {
            toast.error("An error occurred.", {
                theme: "colored",
                type: "error",
            });
        }
    }
};

const detailBanner = async (id) => {
    router.visit(route('banners.show', id));
};

const editBanner = async (id) => {
    router.visit(route('banners.edit', id));
};

const addBanner = async (id) => {
    router.visit(route('banners.create'));
};

const resolveEventRoleVariant = () => {
    return;
}

const dialog = ref(false)
const selectedImage = ref('')

const showImage = (src) => {
    selectedImage.value = src
    dialog.value = true
}
</script>
