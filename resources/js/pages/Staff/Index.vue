<template>
<Head title="Staffs" />
<Layout>
    <v-breadcrumbs :items="breadcrumbs">
        <template v-slot:prepend></template>
    </v-breadcrumbs>
    <section>
        <VCard class="mb-6" title="Search Filter">

            <v-row class="flex-wrap" no-gutters>
                <!-- Items per page select -->
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
                        <AppTextField v-model="search" placeholder="Search Staff" />
                    </v-sheet>
                </v-col>

                <v-col cols="12" md="2">
                    <v-sheet class="ma-2 pa-2">
                        <div class="d-flex align-center flex-wrap justify-between">
                            <v-btn v-if="$filters.can('create-staff')" @click="addStaff" color="primary" class="mb-2">
                                <v-icon end icon="tabler-plus" class="me-2" />
                                Add New Staff
                            </v-btn>
                        </div>
                    </v-sheet>
                </v-col>
            </v-row>


            <VDataTableServer v-model:items-per-page="itemsPerPage" v-model:page="page" :items-length="staffsData.total" :items-per-page-options="[
                { value: 5, title: '5' },
                { value: 10, title: '10' },
                { value: -1, title: '$vuetify.dataFooter.itemsPerPageAll' }
	            ]" v-model:sort-by="sortBy" :headers="headers" :items="staffsData.data" item-value="id" class="text-no-wrap">

                <template #item.name="{ item }">
                    <div class="d-flex align-center gap-x-4">
                        <VAvatar size="34" :variant="!item.avatar ? 'tonal' : undefined" :color="!item.avatar ? resolveStaffRoleVariant(item.roles)?.color : undefined">
                            <VImg v-if="item.avatar" :src="item.avatar" />
                            <span v-else>{{ avatarText(item.fullName) }}</span>
                        </VAvatar>
                        <div class="d-flex flex-column">
                            <h6 class="text-base">
                                <a :href="`#`" @click.prevent="detailStaff(item.id)" class="font-weight-medium text-link">
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

                <template #item.actions="{ item }">
                    <VBtn icon variant="text" color="medium-emphasis">
                        <VIcon icon="tabler-dots-vertical" />
                        <VMenu activator="parent">
                            <VList>
                                <VListItem @click="detailStaff(item.id)">
                                    <template #prepend>
                                        <VIcon icon="tabler-eye" />
                                    </template>

                                    <VListItemTitle>View</VListItemTitle>
                                </VListItem>

                                <VListItem v-if="$filters.can('update-staff')" @click="editStaff(item.id)">
                                    <template #prepend>
                                        <VIcon icon="tabler-pencil" />
                                    </template>
                                    <VListItemTitle>Edit</VListItemTitle>
                                </VListItem>

                                <VListItem v-if="$filters.can('update-staff')" @click="toggleStatusStaff(item.id, item.status)" variant="text">
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
                    <TablePagination v-model:page="page" :items-per-page="itemsPerPage" :total-items="staffsData.total" />
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

const staffs = ref([]);

const props = defineProps({
    staffsData: Object,
    filters: Object,
    roles: Object,
    qty_user: Number,
    departments: Array
})

const breadcrumbs = [{
        title: 'Staffs',
        disabled: false,
        href: '/staffs',
    }
]

const headers = [{
        title: "Name",
        key: "name"
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
const page = ref(props.staffsData.current_page);
const itemsPerPage = ref(props.staffsData.per_page);
const sortBy = ref([])

const fetchData = async () => {
    router.get(
        route("staffs.index"), {
            search: search.value,
            page: page.value,
            paginate: itemsPerPage.value,
            sort_by: sortBy.value[0]?.key,
            sort_order: sortBy.value[0]?.order,
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
watch([page, itemsPerPage, sortBy], fetchData);

const widgetData = ref([{
    title: 'Session',
    value: props.qty_user,
    desc: 'Total Staffs',
    icon: 'tabler-staffs',
    iconColor: 'primary',
}]);

const updateOptions = (options) => {
    page.value = options.page;
    itemsPerPage.value = options.itemsPerPage;
};

const toggleStatusStaff = async (id, isActive) => {
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
            const response = await axios.post(`/staffs/${id}/toggleStatus`);
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

const detailStaff = async (id) => {
    router.visit(route('staffs.show', id));
};

const editStaff = async (id) => {
    router.visit(route('staffs.edit', id));
};

const addStaff = async (id) => {
    router.visit(route('staffs.create'));
};

const resolveStaffRoleVariant = () => {
    return;
}
</script>
