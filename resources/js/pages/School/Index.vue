<template>
<Head title="Schools" />
<Layout>
    <v-breadcrumbs :items="breadcrumbs">
        <template v-slot:prepend></template>
    </v-breadcrumbs>
    <section>
        <VCard class="mb-6" title="Search Filter">

			<v-row class="flex-wrap" no-gutters>
				<v-col cols="12">
					<v-sheet class="ma-2 pa-2">
						<AppSelect :model-value="filterStatus" :items="[
							{ value: 'all', title: 'Select Status' },
							{ value: 1, title: 'Active' },
							{ value: 0, title: 'Inactive' },
						]" @update:model-value="filterStatus = $event" placeholder="Select Status" />
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

				<v-col cols="12" md="10">
					<v-row class="flex-wrap" no-gutters>
						<v-col cols="12" md="7">
							<v-sheet class="ma-2 pa-2">
								<AppTextField v-model="search" placeholder="Search School" class="w-100"/>
							</v-sheet>
						</v-col>
						<v-col cols="12" md="5">
							<v-sheet class="ma-2 pa-2">
								<div class="d-flex align-center flex-wrap justify-end">
									<v-btn :href="'/schools/export/'" color="info" class="me-2 mb-2">
										<v-icon end icon="tabler-download" class="me-2" />
										Export
									</v-btn>
									<v-btn v-if="$filters.can('create-school')" @click="addSchool" color="primary" class="mb-2">
										<v-icon end icon="tabler-plus" class="me-2" />
										Add New School
									</v-btn>
								</div>
							</v-sheet>
						</v-col>
		            </v-row>
                </v-col>

            </v-row>


            <VDataTableServer v-model:items-per-page="itemsPerPage" v-model:page="page" :items-length="schoolsData.total" :items-per-page-options="[
                { value: 5, title: '5' },
                { value: 10, title: '10' },
                { value: -1, title: '$vuetify.dataFooter.itemsPerPageAll' }
	            ]" v-model:sort-by="sortBy" :headers="headers" :items="schoolsData.data" item-value="id" class="text-no-wrap">

                <template #item.name="{ item }">
                    <div class="d-flex align-center gap-x-4">
                        <VAvatar size="34" :variant="!item.avatar ? 'tonal' : undefined" :color="!item.avatar ? resolveSchoolRoleVariant(item.roles)?.color : undefined">
                            <VImg v-if="item.avatar" :src="item.avatar" />
                            <span v-else>{{ avatarText(item.fullName) }}</span>
                        </VAvatar>
                        <div class="d-flex flex-column">
                            <h6 class="text-base">
                                <a :href="`#`" @click.prevent="detailSchool(item.id)" class="font-weight-medium text-link">
                                    {{ item.name }}
                                </a>
                            </h6>
                            <div class="text-sm">
                                {{ item.email }}
                            </div>
                        </div>
                    </div>
                </template>

                <template #item.status="{ item }">
                    <VChip :color="$filters.resolveStatusVariant(item.status_text)" size="small" label class="text-capitalize">
                        {{ item.status_text }}
                    </VChip>
                </template>

				<template #item.can_order_pickup="{ item }">
					<div class="d-flex align-center mt-2 mb-4">
						<VCheckbox v-model="item.can_order_pickup" disabled />
					</div>
                </template>

                <template #item.actions="{ item }">
                    <VBtn icon variant="text" color="medium-emphasis">
                        <VIcon icon="tabler-dots-vertical" />
                        <VMenu activator="parent">
                            <VList>
                                <VListItem @click="detailSchool(item.id)">
                                    <template #prepend>
                                        <VIcon icon="tabler-eye" />
                                    </template>

                                    <VListItemTitle>View</VListItemTitle>
                                </VListItem>

                                <VListItem v-if="$filters.can('update-school')" @click="editlSchool(item.id)">
                                    <template #prepend>
                                        <VIcon icon="tabler-pencil" />
                                    </template>
                                    <VListItemTitle>Edit</VListItemTitle>
                                </VListItem>

                                <VListItem v-if="$filters.can('update-school')" @click="toggleStatusSchool(item.id, item.status)" variant="text">
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
                    <TablePagination v-model:page="page" :items-per-page="itemsPerPage" :total-items="schoolsData.total" />
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
    toast
} from "vue3-toastify";
import Layout from "../../layouts/blank.vue";
import {
    computed,
    ref
} from 'vue';

const schools = ref([]);

const props = defineProps({
    schoolsData: Object,
    filters: Object,
    roles: Object,
    qty_user: Number,
    departments: Array
})

const breadcrumbs = [{
        title: 'Schools',
        disabled: false,
        href: '/schools',
    }
]

const headers = [{
        title: "Name",
        key: "name"
    },
    {
        title: "Unique ID",
        key: "username"
    },
	{
		title: "Contact",
		key: "phone"
	},
    {
        title: "Status",
        key: "status"
    },
	{
        title: "Can Order Pickup",
        key: "can_order_pickup"
    },
    {
        title: "Actions",
        key: "actions",
        sortable: false
    },
];

const search = ref(props.filters.search || "");
const page = ref(props.schoolsData.current_page);
const itemsPerPage = ref(props.schoolsData.per_page);
const sortBy = ref([])
const filterStatus = ref(props.schoolsData.status);

const fetchData = async () => {
    router.get(
        route("schools.index"), {
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

const widgetData = ref([{
    title: 'Session',
    value: props.qty_user,
    desc: 'Total Schools',
    icon: 'tabler-schools',
    iconColor: 'primary',
}]);

const updateOptions = (options) => {
    page.value = options.page;
    itemsPerPage.value = options.itemsPerPage;
};

const toggleStatusSchool = async (id, isActive) => {
	let text = 'This account will be activated'
	if (isActive) {
		text = 'This account will be inactivated. This account will not be able to login after this.'
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
            const response = await axios.post(`/schools/${id}/toggleStatus`);
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
            toast.error("An error occurred while deleting the user.", {
                theme: "colored",
                type: "error",
            });
        }
    }
};

const detailSchool = async (id) => {
    router.visit(route('schools.show', id));
};

const editlSchool = async (id) => {
    router.visit(route('schools.edit', id));
};

const addSchool = async (id) => {
    router.visit(route('schools.create'));
};

const resolveSchoolRoleVariant = () => {
    return;
}
</script>
