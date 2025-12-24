<template>
<Head title="Events" />
<Layout>

	<v-overlay :model-value="isLoading" class="d-flex align-center justify-center" persistent opacity="0.6" style="z-index: 3000">
	    <v-card class="pa-5 text-center">
	        <v-progress-circular indeterminate color="primary" size="40" />
	        <div class="mt-4">Importing data...</div>
	    </v-card>
	</v-overlay>

    <v-breadcrumbs :items="breadcrumbs">
        <template v-slot:prepend></template>
    </v-breadcrumbs>
    <section>
        <VCard class="mb-6" title="Search Filter">

			<v-row class="flex-wrap" no-gutters>
				<v-col cols="12" md="6">
					<v-sheet class="ma-2 pa-2">
						<AppSelect :model-value="eventType" :items="eventTypeOptions" @update:model-value="eventType = $event" placeholder="Select event type" />
					</v-sheet>
				</v-col>

				<v-col cols="12" md="6">
                    <v-sheet class="ma-2 pa-2">
                        <AppSelect :model-value="status" :items="[
							{ value: 'all', title: 'All' },
							{ value: 1, title: 'Active' },
							{ value: 0, title: 'Inactive' },
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

				<v-col cols="12" md="10">
					<v-row class="flex-wrap" no-gutters>
						<v-col cols="12" md="7">
							<v-sheet class="ma-2 pa-2">
								<AppTextField v-model="search" placeholder="Search Event" class="w-100"/>
							</v-sheet>
						</v-col>
						<v-col cols="12" md="5">
							<v-sheet class="ma-2 pa-2">
								<div class="d-flex align-center flex-wrap justify-between">
									<v-menu>
		                                <template v-slot:activator="{ props }">
		                                    <v-btn class="me-2 mb-2" color="info" v-bind="props">
		                                        <v-icon end icon="tabler-download" class="me-2" />
		                                        Export/Import
		                                    </v-btn>
		                                </template>

		                                <v-list>
		                                    <v-list-item href="events/export">
		                                        <v-list-item-title>Export</v-list-item-title>
		                                    </v-list-item>
		                                    <v-list-item @click="isDialogVisible = true">
		                                        <v-list-item-title>Import</v-list-item-title>
		                                    </v-list-item>
		                                </v-list>
		                            </v-menu>

		                            <v-dialog v-model="isDialogVisible" width="500">
		                                <DialogCloseBtn @click="isDialogVisible = false" />

		                                <v-card title="Import Data">
		                                    <v-card-text>
		                                        <v-form @submit.prevent="submitImport">
													<AppSelect label="Event Type" class="my-2" v-model="importEventType" :items="[
											          { value: 1, title: 'E-Drive' },
											          { value: 2, title: 'Cash For Trash' },
											          /* { value: 3, title: 'Private Event' },
											          { value: 4, title: 'ALBA Event' }, */
											        ]" />

		                                            <v-file-input label="Select file" prepend-icon="" accept=".csv, .xlsx, .xls" v-model="importFile" required />

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

									<!-- <v-btn :href="'/events/export/'" color="info" class="me-2 mb-2">
										<v-icon end icon="tabler-download" class="me-2" />
										Export
									</v-btn> -->
									<v-btn v-if="$filters.can('create-event')" @click="addEvent" color="primary" class="mb-2">
										<v-icon end icon="tabler-plus" class="me-2" />
										Add New Event
									</v-btn>
								</div>
							</v-sheet>
						</v-col>
		            </v-row>
                </v-col>

            </v-row>


            <VDataTableServer v-model:items-per-page="itemsPerPage" v-model:page="page" :items-length="eventsData.total" :items-per-page-options="[
                { value: 5, title: '5' },
                { value: 10, title: '10' },
                { value: -1, title: '$vuetify.dataFooter.itemsPerPageAll' }
	            ]" v-model:sort-by="sortBy" :headers="headers" :items="eventsData.data" item-value="id" class="text-no-wrap">

				<template #item.code="{ item }">
					<div class="d-flex align-center gap-x-4">
						<div class="d-flex flex-column">
							<h6 class="text-base">
								<a :href="`#`" @click.prevent="detailEvent(item.id)" class="font-weight-medium text-link">
									{{ item.code }}
								</a>
							</h6>
						</div>
					</div>
				</template>

				<template #item.type="{ item }">
					{{ item.type.name }}
				</template>

				<template #item.address="{ item }">
					{{ item.address ?? 'Entire Singapore' }}
				</template>

				<template #item.datetime="{ item }">
					{{ $filters.formatDate(item.date_start) + ' - ' + $filters.formatDate(item.date_end) }}<br>
					{{ item.time_start_formatted }} - {{item.time_end_formatted}}
				</template>

                <template #item.status="{ item }">
                    <VChip :color="$filters.resolveStatusVariant(item.status)" size="small" label class="text-capitalize">
                        {{ item.status_text }}
                    </VChip>
                </template>

                <template #item.actions="{ item }">
                    <VBtn icon variant="text" color="medium-emphasis">
                        <VIcon icon="tabler-dots-vertical" />
                        <VMenu activator="parent">
                            <VList>
                                <VListItem @click="detailEvent(item.id)">
                                    <template #prepend>
                                        <VIcon icon="tabler-eye" />
                                    </template>

                                    <VListItemTitle>View</VListItemTitle>
                                </VListItem>

                                <VListItem v-if="$filters.can('update-event')" @click="editEvent(item.id)">
                                    <template #prepend>
                                        <VIcon icon="tabler-pencil" />
                                    </template>
                                    <VListItemTitle>Edit</VListItemTitle>
                                </VListItem>

                                <VListItem v-if="$filters.can('update-event')" @click="toggleStatusEvent(item.id, item.status)" variant="text">
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
                    <TablePagination v-model:page="page" :items-per-page="itemsPerPage" :total-items="eventsData.total" />
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

const events = ref([]);

const props = defineProps({
    eventsData: Object,
    filters: Object,
    qty_event: Number,
	event_types: Object
})

const breadcrumbs = [{
        title: 'Events',
        disabled: false,
        href: '/events',
    },
    {
        title: 'Event List',
        disabled: true,
    }
]

const headers = [
    {
        title: "ID",
        key: "code"
    },
	{
		title: "Type",
		key: "type"
	},
	{
		title: "Code",
		key: "secret_code"
	},
    {
        title: "Address",
        key: "address"
    },
    {
        title: "Date/Time",
        key: "datetime"
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

const eventTypeOptions = computed(() => [{
        value: 'all',
        title: 'All'
    },
    ...props.event_types.map(event => ({
        value: event.id,
        title: event.name
    }))
]);

const search = ref(props.filters.search || "");
const page = ref(props.eventsData.current_page);
const itemsPerPage = ref(props.eventsData.per_page);
const sortBy = ref([])
const status = ref(props.eventsData.status);
const eventType = ref(props.eventsData.event_type);

const fetchData = async () => {
    router.get(
        route("events.index"), {
            search: search.value,
            page: page.value,
            paginate: itemsPerPage.value,
            sort_by: sortBy.value[0]?.key,
            sort_order: sortBy.value[0]?.order,
			event_type: eventType.value,
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
watch([page, itemsPerPage, sortBy, eventType, status], fetchData);

const widgetData = ref([{
    title: 'Session',
    value: props.qty_user,
    desc: 'Total Events',
    icon: 'tabler-events',
    iconColor: 'primary',
}]);

const updateOptions = (options) => {
    page.value = options.page;
    itemsPerPage.value = options.itemsPerPage;
};

const toggleStatusEvent = async (id, isActive) => {
	let text = 'This event will be activated'
	if (isActive) {
		text = 'This event will be inactivated.'
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
            const response = await axios.post(`/events/${id}/toggleStatus`);
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

const detailEvent = async (id) => {
    router.visit(route('events.show', id));
};

const editEvent = async (id) => {
    router.visit(route('events.edit', id));
};

const addEvent = async (id) => {
    router.visit(route('events.create'));
};

const resolveEventRoleVariant = () => {
    return;
}

const isDialogVisible = ref(false);
const importFile = ref(null);
const importEventType = ref(null);
const isLoading = ref(false);
const submitImport = async () => {
    if (!importFile.value) {
        return Swal.fire({
            title: "Error <br> <i class='fa-solid fa-x'></i>",
            text: "Please select a file to import",
            icon: "error",
            confirmButtonColor: "#6CC9CF",
            confirmButtonText: "OK!",
            didOpen: () => {
                document.querySelector('.swal2-container').style.zIndex = 99999;
            }
        });
    }

	if (!importEventType.value) {
        return Swal.fire({
            title: "Error <br> <i class='fa-solid fa-x'></i>",
            text: "Please select event type",
            icon: "error",
            confirmButtonColor: "#6CC9CF",
            confirmButtonText: "OK!",
            didOpen: () => {
                document.querySelector('.swal2-container').style.zIndex = 99999;
            }
        });
    }

    const formData = new FormData();
    formData.append("file", importFile.value);
	formData.append("event_type_id", importEventType.value);

    isLoading.value = true;

    try {
        const response = await axios.post("/events/import", formData, {
            headers: {
                "Content-Type": "multipart/form-data",
            },
        });

        isDialogVisible.value = false;
        importFile.value = null;
        isLoading.value = false;

        await Swal.fire({
            title: "Success <br> <i class='fa-solid fa-check'></i>",
            text: "File imported successfully",
            icon: "success",
            confirmButtonColor: "#6CC9CF",
            confirmButtonText: "OK!",
        }).then(() => {
            location.reload();
        });
    } catch (error) {
        return Swal.fire({
            title: "Error <br> <i class='fa-solid fa-x'></i>",
            text: 'Import failed : ' + error.response.data.error + '. Please fix your import file and try again.',
            icon: "error",
            confirmButtonColor: "#6CC9CF",
            confirmButtonText: "OK!",
            didOpen: () => {
                document.querySelector('.swal2-container').style.zIndex = 99999;
            }
        });
    } finally {
        isLoading.value = false;
    }
};

</script>
