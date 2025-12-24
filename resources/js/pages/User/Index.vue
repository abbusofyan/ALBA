<template>
<Head title="Users" />
<Layout>

    <v-dialog v-model="loadingExport" persistent width="300">
        <v-card class="pa-4 d-flex align-center">
            <v-progress-circular indeterminate color="primary" />
            <span class="ms-3">Generating export data...</span>
        </v-card>
    </v-dialog>

    <v-breadcrumbs :items="breadcrumbs">
        <template v-slot:prepend></template>
    </v-breadcrumbs>
    <section>
        <VCard class="mb-6" title="Search Filter">
            <v-row class="flex-wrap" no-gutters>
                <v-col cols="12">
                    <v-sheet class="ma-2 pa-2">
                        <AppSelect :model-value="status" :items="[
							{ value: 'all', title: 'All' },
							{ value: 1, title: 'Active' },
							{ value: 0, title: 'Inactive' },
							{ value: 2, title: 'Banned' },
						]" @update:model-value="status = $event" placeholder="Select Status" />
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
                        <AppTextField v-model="search" placeholder="Search User" />
                    </v-sheet>
                </v-col>

                <v-col cols="12" md="2">
                    <v-sheet class="ma-2 pa-2">
                        <v-btn @click="exportUsers" color="info" class="me-2 mb-2">
                            <v-icon end icon="tabler-download" class="me-2" />
                            Export
                        </v-btn>
                    </v-sheet>
                </v-col>
            </v-row>

            <VDataTableServer v-model:items-per-page="itemsPerPage" v-model:page="page" :items-length="usersData.total" :items-per-page-options="[
                { value: 5, title: '5' },
                { value: 10, title: '10' },
                { value: -1, title: '$vuetify.dataFooter.itemsPerPageAll' }
	            ]" v-model:sort-by="sortBy" :headers="headers" :items="usersData.data" item-value="id" class="text-no-wrap">

                <template #item.name="{ item }">
                    <div class="d-flex align-center gap-x-4">
                        <VAvatar size="34" :variant="!item.avatar ? 'tonal' : undefined" :color="!item.avatar ? resolveUserRoleVariant(item.roles)?.color : undefined">
                            <VImg v-if="item.avatar" :src="item.avatar" />
                            <span v-else>{{ avatarText(item.fullName) }}</span>
                        </VAvatar>
                        <div class="d-flex flex-column">
                            <h6 class="text-base">
                                <a :href="`#`" @click.prevent="detailUser(item.id)" class="font-weight-medium text-link">
                                    {{ item.name }}
                                </a>
                            </h6>
                            <div class="text-sm">
                                {{ item.email }}
                            </div>
                        </div>
                    </div>
                </template>

                <template #item.display_name="{ item }">
                    {{item.display_name}}
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
                                <VListItem @click="detailUser(item.id)">
                                    <template #prepend>
                                        <VIcon icon="tabler-eye" />
                                    </template>

                                    <VListItemTitle>View</VListItemTitle>
                                </VListItem>

                                <VListItem v-if="$filters.can('update-user')" @click="editUser(item.id)">
                                    <template #prepend>
                                        <VIcon icon="tabler-pencil" />
                                    </template>
                                    <VListItemTitle>Edit</VListItemTitle>
                                </VListItem>

                                <VListItem v-if="$filters.can('update-user')" @click="item.status_text == 'Active' ? openBanDialog(item.id) : activateUser(item.id, item.status_text)" variant="text">
                                    <template #prepend>
                                        <VIcon :icon="item.status_text == 'Active' ? 'tabler-ban' : 'tabler-user-check'" />
                                    </template>
									<VListItemTitle>
									  {{
									    item.status_text === 'Active'
									      ? 'Ban'
									      : item.status_text === 'Inactive'
									        ? 'Activate'
									        : 'Unban'
									  }}
									</VListItemTitle>
                                </VListItem>

                            </VList>
                        </VMenu>
                    </VBtn>
                </template>

                <template #bottom>
                    <TablePagination v-model:page="page" :items-per-page="itemsPerPage" :total-items="usersData.total" />
                </template>
            </VDataTableServer>

            <v-dialog v-model="isDialogVisible" width="500">
                <DialogCloseBtn @click="isDialogVisible = false" />

                <v-card title="Ban Detail">
                    <v-card-text>
                        <v-form @submit.prevent="submitBanDetail">
							<div class="flex items-center flex-none order-0 grow-0 mb-2">
								<label class="v-label mb-1 text-body-2 text-wrap" for="v-67" style="line-height: 15px;">Reason</label>
								<AppTextarea v-model="banForm.reason" />
							</div>

							<div class="flex items-center flex-none order-0 grow-0 mb-2">
								<label class="v-label mb-1 text-body-2 text-wrap" for="v-67" style="line-height: 15px;">Evidence</label>
								<v-file-input v-model="banForm.evidence" accept="image/*,.pdf" prepend-icon="" outlined />
							</div>

							<AppSelect v-model="banForm.duration" :items="[
								{ title: '1 Day', value: 1 },
								{ title: '7 Days', value: 7 },
								{ title: '30 Days', value: 30 },
								{ title: 'Permanent', value: '' },
							]" label="Duration" />

                            <VCardText class="d-flex justify-end gap-3 flex-wrap">
                                <VBtn color="secondary" variant="tonal" @click="isDialogVisible = false">
                                    Cancel
                                </VBtn>
                                <VBtn type="submit"> Submit </VBtn>
                            </VCardText>
                        </v-form>
                    </v-card-text>
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

const users = ref([]);

const props = defineProps({
    usersData: Object,
    filters: Object,
    roles: Object,
    qty_user: Number,
    departments: Array
})

const breadcrumbs = [{
    title: 'Users',
    disabled: false,
    href: '/users',
}]

const headers = [{
        title: "Name",
        key: "name"
    },
    {
        title: "Nickname",
        key: "display_name"
    },
    {
        title: "CO2 Point",
        key: "point"
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
        title: "Actions",
        key: "actions",
        sortable: false
    },
];

const search = ref(props.filters.search || "");
const page = ref(props.usersData.current_page);
const itemsPerPage = ref(props.usersData.per_page);
const sortBy = ref([])
const status = ref(props.usersData.status);

const fetchData = async () => {
    router.get(
        route("users.index"), {
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

const widgetData = ref([{
    title: 'Session',
    value: props.qty_user,
    desc: 'Total Users',
    icon: 'tabler-users',
    iconColor: 'primary',
}]);

const updateOptions = (options) => {
    page.value = options.page;
    itemsPerPage.value = options.itemsPerPage;
};

const activateUser = async (id, status_text) => {
    const result = await Swal.fire({
        title: "Are you sure? <br> <i class='fa-solid fa-trash-can'></i>",
        text: 'This account will be activated',
        icon: "warning",
        showCancelButton: true,
        confirmButtonColor: "#ea5455",
        cancelButtonColor: "#6CC9CF",
        confirmButtonText: "Yes, Proceed!",
        cancelButtonText: "Cancel",
    });

    if (result.isConfirmed) {
        try {
            const response = await axios.post(`/users/${id}/activate`);
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

const detailUser = async (id) => {
    router.visit(route('users.show', id));
};

const editUser = async (id) => {
    router.visit(route('users.edit', id));
};

const addUser = async (id) => {
    router.visit(route('users.create'));
};

const resolveUserRoleVariant = () => {
    return;
}

const loadingExport = ref(false)

const exportUsers = async () => {
    try {
        loadingExport.value = true
        const response = await axios.get("/users/export", {
            responseType: "blob", // <-- important for files
        })

        // Create blob link to download
        const url = window.URL.createObjectURL(new Blob([response.data]))
        const link = document.createElement("a")
        link.href = url
        link.setAttribute("download", "Export users data.xlsx") // customize filename
        document.body.appendChild(link)
        link.click()
        link.remove()
    } catch (error) {
        console.error("Export failed:", error)
        // optionally show a toast or alert
    } finally {
        loadingExport.value = false
    }
}

const isDialogVisible = ref(false)
const selectedUserId = ref(null)

const banForm = ref({
    reason: "",
    evidence: null,
    duration: null,
})

const openBanDialog = (id) => {
    selectedUserId.value = id
    banForm.value = {
        reason: "",
        evidence: null,
        duration: null
    }
    isDialogVisible.value = true
}

const submitBanDetail = async () => {
    if (!selectedUserId.value) return

    try {
        const formData = new FormData()
        formData.append("reason", banForm.value.reason)
        formData.append("duration", banForm.value.duration)
        if (banForm.value.evidence) {
            formData.append("evidence", banForm.value.evidence)
        }

        const response = await axios.post(
            `/users/${selectedUserId.value}/ban`,
            formData, {
                headers: {
                    "Content-Type": "multipart/form-data"
                }
            }
        )

        if (response.data.success) {
            toast.success(response.data.message || "User banned successfully", {
                theme: "colored",
                type: "success",
            })
            isDialogVisible.value = false
            fetchData()
        } else {
            toast.error(response.data.message || "Ban failed", {
                theme: "colored",
                type: "error",
            })
        }
    } catch (error) {
        console.error(error)
        toast.error("An error occurred while banning the user.", {
            theme: "colored",
            type: "error",
        })
    }
}
</script>
