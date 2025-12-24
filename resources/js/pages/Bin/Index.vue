<template>
<v-overlay :model-value="isLoading" class="d-flex align-center justify-center" persistent opacity="0.6" style="z-index: 3000">
    <v-card class="pa-5 text-center">
        <v-progress-circular indeterminate color="primary" size="40" />
        <div class="mt-4">{{loadingText ? loadingText : 'Fetching data...'}}</div>
    </v-card>
</v-overlay>

<Head title="Bins" />
<Layout>
    <v-breadcrumbs :items="breadcrumbs">
        <template v-slot:prepend></template>
    </v-breadcrumbs>
    <section>
        <VCard class="mb-6">
            <v-row class="flex-wrap" no-gutters>
                <v-col cols="12" md="2">
                    <v-sheet class="ma-2 pa-2 filter-panel">
                        <!-- <p>Current Zoom : {{ currentZoom }}</p>
                        <p>Current Mode : {{ currentMode }}</p> -->

                        <!-- Add "All" checkbox for map filter -->
                        <VCheckbox
                            label="All"
                            :model-value="isAllMapTypesSelected"
                            @update:model-value="toggleAllMapTypes"
                            :indeterminate="isSomeMapTypesSelected"
                        />
                        <v-divider class="my-2" />

                        <template v-for="bin_type in bin_types" :key="bin_type.id">
                            <VCheckbox :label="bin_type.name" :value="bin_type.id" v-model="filterBinTypeMap" />
                        </template>
                    </v-sheet>
                </v-col>

                <v-col cols="12" md="10">
                    <v-sheet class="ma-2 pa-2 map-container">
                        <LeafLetMap ref="mapRef" @zoom-change="handleZoomChange" @mode-change="handleModeChange" @loading-change="handleMapLoadingChange" :locations="bin_locations" :clickable="false">
                        </LeafLetMap>
                    </v-sheet>
                </v-col>
            </v-row>
        </VCard>

        <VCard class="mb-6" title="Search Filter">
            <v-row class="flex-wrap" no-gutters>
                <!-- Items per page select -->
                <v-col cols="12" md="6">
                    <v-sheet class="ma-2 pa-2">
						<v-combobox
                            v-model="binType"
                            :items="binTypeOptions"
                            item-title="name"
                            return-object
                            clearable
                            chips
                            multiple
                            @update:model-value="handleBinTypeChange"
                        />
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
                  { value: -1, title: 'All' },
                ]" @update:model-value="itemsPerPage = parseInt($event, 10)" />
                    </v-sheet>
                </v-col>

                <v-col cols="12" md="5">
                    <v-sheet class="ma-2 pa-2">
                        <AppTextField v-model="search" placeholder="Search" />
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
                                    <v-list-item href="bins/export">
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
                                            <v-file-input prepend-icon="" label="Select file" accept=".csv, .xlsx, .xls" v-model="importFile" required />

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

                            <v-btn @click="handleDownloadQRMultiple" class="me-2 mb-2" style="background-color: #4da634 !important">
                                <v-icon end icon="tabler-qrcode" class="me-2" />
                                Download QR
                            </v-btn>
                            <v-btn v-if="$filters.can('create-bin')" @click="addBin" color="primary" class="mb-2">
                                <v-icon end icon="tabler-plus" class="me-2" />
                                Add New Bin
                            </v-btn>
                        </div>
                    </v-sheet>
                </v-col>
            </v-row>

            <VDataTableServer v-model:items-per-page="itemsPerPage" v-model:page="page" :items-length="binsData.total" :items-per-page-options="[
				{ value: 5, title: '5' },
				{ value: 10, title: '10' },
				{ value: -1, title: '$vuetify.dataFooter.itemsPerPageAll' },
			]" v-model:sort-by="sortBy" :headers="headers" :items="binsData.data" item-value="id" class="text-no-wrap">

                <template #header.id>
                    <v-checkbox :model-value="allSelected" @change="toggleAllSelected" :indeterminate="someSelected" density="compact" />
                </template>

                <template #item.id="{ item }">
                    <v-checkbox v-if="isItemSelectable(item)" :model-value="selectedIds.includes(item.id)" @change="() => toggleItem(item.id)" density="compact" />
                </template>

                <template #item.code="{ item }">
                    <div class="d-flex align-center gap-x-4">
                        <div class="d-flex flex-column">
                            <h6 class="text-base">
                                <a :href="`#`" @click.prevent="detailBin(item.id)" class="font-weight-medium text-link">
                                    {{ item.code }}
                                </a>
                            </h6>
                        </div>
                    </div>
                </template>

                <template #item.bin_type_id="{ item }">
                    {{ item.type.name }}
                </template>

                <template #item.address="{ item }">
                    <div style="white-space: normal; word-break: break-word">
                        {{ item.address }}
                    </div>
                </template>

                <template #item.status="{ item }">
					<VChip :color="$filters.resolveStatusVariant(item.status)" size="small" label class="text-capitalize">
                        {{ item.status_text }}
                    </VChip>
                </template>

				<template #item.visibility="{ item }">
                    <div class="d-flex align-center">
                        <VSwitch :model-value="!item.visibility" hide-details inset @click.prevent.stop="toggleVisibilityBin(item)" />
                        <span class="pl-7">{{ setVisibilityLabel(item.visibility) }}</span>
                    </div>
                </template>

                <template #item.actions="{ item }">
                    <VBtn icon variant="text" color="medium-emphasis">
                        <VIcon icon="tabler-dots-vertical" />
                        <VMenu activator="parent">
                            <VList>
                                <VListItem @click="detailBin(item.id)">
                                    <template #prepend>
                                        <VIcon icon="tabler-eye" />
                                    </template>

                                    <VListItemTitle>View</VListItemTitle>
                                </VListItem>

                                <VListItem v-if="$filters.can('update-bin')" @click="editBin(item.id)">
                                    <template #prepend>
                                        <VIcon icon="tabler-pencil" />
                                    </template>
                                    <VListItemTitle>Edit</VListItemTitle>
                                </VListItem>

                                <VListItem @click="handleDownload(item)" v-if="item.bin_type_id != 1">
                                    <template #prepend>
                                        <VIcon icon="tabler-download" />
                                    </template>
                                    <VListItemTitle>Download QR</VListItemTitle>
                                </VListItem>

								<VListItem
									v-if="$filters.can('update-bin')"
									@click="toggleStatusBin(item)"
									variant="text"
								>
									<template #prepend>
										<VIcon :icon="item.status ? 'tabler-square-off' : 'tabler-check'" />
									</template>

									<VListItemTitle>
										{{ item.status ? "Deactivate" : "Activate" }}
									</VListItemTitle>
								</VListItem>

                            </VList>
                        </VMenu>
                    </VBtn>
                </template>

                <template #bottom>
                    <TablePagination v-model:page="page" :items-per-page="itemsPerPage" :total-items="binsData.total" />
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
    ref,
    watch,
    onMounted,
    nextTick
} from "vue";
import {
    toast
} from "vue3-toastify";
import LeafLetMap from "../../Components/LeafLetMap.vue";
import Layout from "../../layouts/blank.vue";
import {
    useApi
} from '@/composables/useApi'
import axios from 'axios';

const isDialogVisible = ref(false);
const importFile = ref(null);
const isLoading = ref(false);
const currentZoom = ref(12);
const currentMode = ref("overview");
const mapRef = ref(null);
const isMapLoading = ref(false);

function handleZoomChange(zoom) {
    currentZoom.value = zoom;
}

function handleModeChange(mode) {
    currentMode.value = mode;
}

function handleMapLoadingChange(loading) {
    isMapLoading.value = loading;
}

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

    const formData = new FormData();
    formData.append("file", importFile.value);

    isLoading.value = true;

    try {
        const response = await axios.post("/bins/import", formData, {
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
            router.visit(route("bins.index"));
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

const props = defineProps({
    binsData: Object,
    filters: Object,
    roles: Object,
    qty_bin: Number,
    bin_types: Object,
});

const binTypeOptions = computed(() => [{
        value: "all",
        name: "All",
    },
    ...props.bin_types.map((type) => ({
        value: type.id,
        name: type.name,
    })),
]);

const breadcrumbs = [{
    title: "Bins",
    disabled: false,
    href: "/bins",
}, ];

const headers = [{
        title: "",
        key: "id",
        sortable: false,
    },
    {
        title: "ID",
        key: "code",
    },
    {
        title: "Type",
        key: "bin_type_id",
    },
    {
        title: "Address",
        key: "address",
    },
    {
        title: "Remark",
        key: "remark",
    },
    {
        title: "Status",
        key: "status",
    },
	{
        title: "Hide Bin",
        key: "visibility",
    },
    {
        title: "Actions",
        key: "actions",
        sortable: false,
    },
];

const selectedIds = ref([])

const isItemSelectable = item => item.bin_type_id !== 1

const toggleItem = id => {
    if (selectedIds.value.includes(id)) {
        selectedIds.value = selectedIds.value.filter(i => i !== id)
    } else {
        selectedIds.value.push(id)
    }
}

const allSelected = ref(false)

const someSelected = computed(() => {
    return selectedIds.value.length > 0 && selectedIds.value.length < totalSelectableIds.value.length
})

const totalSelectableIds = ref([])

const toggleAllSelected = async () => {
    allSelected.value = !allSelected.value // toggle the value manually

    if (allSelected.value) {
        await fetchAllSelectableIds()
        selectedIds.value = [...(totalSelectableIds.value || [])]
    } else {
        selectedIds.value = []
    }
}

const fetchAllSelectableIds = async () => {
    try {
		const res = await axios.post("api/v1/bin-management/getAllBinIds", {
            bin_type_id: filterBinTypeMap.value,
        });
        // const res = await useApi('/bin-management/getAllBinIds' + (binType.value ? '/' + binType.value : ''))
        if (res.data) {
            totalSelectableIds.value = res.data
        } else {
            totalSelectableIds.value = [] // fallback
        }
    } catch (err) {
        console.error('Failed to fetch selectable IDs:', err)
        totalSelectableIds.value = []
    }
}

const search = ref(props.filters.search || "");
const page = ref(props.binsData.current_page);
const itemsPerPage = ref(props.binsData.per_page);
const sortBy = ref([]);
const binType = ref(props.binsData.bin_type || []);
const status = ref(props.binsData.status);

// Initialize filterBinTypeMap with all bin type IDs
const filterBinTypeMap = ref(props.bin_types.map(type => type.id));

// Computed properties for "All" checkbox functionality in map filter
const isAllMapTypesSelected = computed(() => {
    return filterBinTypeMap.value.length === props.bin_types.length;
});

const isSomeMapTypesSelected = computed(() => {
    return filterBinTypeMap.value.length > 0 && filterBinTypeMap.value.length < props.bin_types.length;
});

// Function to toggle all map types
const toggleAllMapTypes = (value) => {
    if (value) {
        // Select all
        filterBinTypeMap.value = props.bin_types.map(type => type.id);
        // Also update datatable filter to show all
        binType.value = [binTypeOptions.value[0], ...props.bin_types.map(type => ({ value: type.id, name: type.name }))];
    } else {
        // Deselect all
        filterBinTypeMap.value = [];
        // Also update datatable filter to show none
        binType.value = [];
    }
};

// Function to handle datatable bin type changes and sync with map
const handleBinTypeChange = (newValue) => {
    binType.value = newValue;

    // Check if "All" is selected in datatable
    const hasAllOption = newValue.some(item => item.value === "all");

    if (hasAllOption) {
        // If "All" is selected, toggle behavior
        if (newValue.length === 1 && newValue[0].value === "all") {
            // Only "All" is selected, select all types
            binType.value = [binTypeOptions.value[0], ...props.bin_types.map(type => ({ value: type.id, name: type.name }))];
            filterBinTypeMap.value = props.bin_types.map(type => type.id);
        } else {
            // "All" is selected along with other items, remove "All"
            binType.value = newValue.filter(item => item.value !== "all");
            filterBinTypeMap.value = binType.value.map(item => item.value);
        }
    } else {
        // No "All" option, sync normally
        if (newValue.length === 0) {
            // If nothing selected, clear map filter
            filterBinTypeMap.value = [];
        } else if (newValue.length === props.bin_types.length) {
            // If all individual types are selected, add "All" option
            binType.value = [binTypeOptions.value[0], ...newValue];
            filterBinTypeMap.value = props.bin_types.map(type => type.id);
        } else {
            // Normal selection, sync with map
            filterBinTypeMap.value = newValue.map(item => item.value);
        }
    }
};

const fetchData = async () => {
	loadingText.value = 'Fetching data...'
    isLoading.value = true
    try {
        await router.get(
            route("bins.index"), {
                search: search.value,
                page: page.value,
                paginate: itemsPerPage.value,
                sort_by: sortBy.value[0]?.key,
                sort_order: sortBy.value[0]?.order,
                bin_type: binType.value,
                status: status.value,
            }, {
                preserveState: true,
                preserveScroll: true,
                onFinish: () => {
                    isLoading.value = false
                }
            }
        );
    } catch (error) {
        console.error('Error fetching data:', error)
        isLoading.value = false
    }
};

onMounted(async () => {
    // Initialize binType with all types selected if not already set
    if (!binType.value || binType.value.length === 0) {
        binType.value = [binTypeOptions.value[0], ...props.bin_types.map(type => ({ value: type.id, name: type.name }))];
    }

    // Wait for next tick to ensure map component is mounted
    await nextTick();

    // Add a small delay to ensure map ref is available
    // setTimeout(() => {
        fetchBinLocationData();
    // }, 100);
});

// Watch for changes in map filter and sync with datatable
watch(filterBinTypeMap, (newValue) => {
    // Update datatable filter based on map filter changes
    if (newValue.length === 0) {
        binType.value = [];
    } else if (newValue.length === props.bin_types.length) {
        binType.value = [binTypeOptions.value[0], ...props.bin_types.map(type => ({ value: type.id, name: type.name }))];
    } else {
        const selectedTypes = props.bin_types
            .filter(type => newValue.includes(type.id))
            .map(type => ({ value: type.id, name: type.name }));
        binType.value = selectedTypes;
    }

    // Fetch bin location data for map
    fetchBinLocationData();
}, { deep: true });

watch(search, debounce(fetchData, 300));
watch([page, itemsPerPage, sortBy, binType, status], (newValues, oldValues) => {
    if ((newValues[3] !== oldValues[3]) && allSelected.value) {
        toggleAllSelected()
    }
    fetchData();
});

const widgetData = ref([{
    title: "Session",
    value: props.qty_bin,
    desc: "Total Bins",
    icon: "tabler-bins",
    iconColor: "primary",
}, ]);

const updateOptions = (options) => {
    page.value = options.page;
    itemsPerPage.value = options.itemsPerPage;
};

const toggleStatusBin = async (item) => {
    const isActive = item.status;

    const text = isActive ?
        "This bin will be deactivated." :
        "This bin will be activated";

    const result = await Swal.fire({
        title: "Are you sure? <br> <i class='fa-solid fa-trash-can'></i>",
        text,
        icon: "warning",
        showCancelButton: true,
        confirmButtonColor: "#ea5455",
        cancelButtonColor: "#6CC9CF",
        confirmButtonText: "Yes, Proceed!",
        cancelButtonText: "Cancel",
    });

    if (result.isConfirmed) {
        try {
            const response = await axios.post(`/bins/${item.id}/toggleStatus`);
            if (response.data.success) {
                toast.success(response.data.message, {
                    theme: "colored"
                });
				fetchData();
            } else {
                toast.error(response.data.message, {
                    theme: "colored"
                });
            }
        } catch (error) {
            toast.error("An error occurred while changing bin status.", {
                theme: "colored"
            });
        }
    }
};

const toggleVisibilityBin = async (item) => {
    const isShown = item.visibility;

    const text = isShown ?
        "This bin will be hidden from the map in mobile app." :
        "This bin will be shown in the map";

    const result = await Swal.fire({
        title: "Are you sure? <br> <i class='fa-solid fa-trash-can'></i>",
        text,
        icon: "warning",
        showCancelButton: true,
        confirmButtonColor: "#ea5455",
        cancelButtonColor: "#6CC9CF",
        confirmButtonText: "Yes, Proceed!",
        cancelButtonText: "Cancel",
    });

    if (result.isConfirmed) {
        try {
            const response = await axios.post(`/bins/${item.id}/toggleVisibility`);
            if (response.data.success) {
                toast.success(response.data.message, {
                    theme: "colored"
                });
                item.visibility = !isShown;
            } else {
                toast.error(response.data.message, {
                    theme: "colored"
                });
            }
        } catch (error) {
            toast.error("An error occurred while changing bin visibility.", {
                theme: "colored"
            });
        }
    }
};


const detailBin = async (id) => {
    router.visit(route("bins.show", id));
};

const editBin = async (id) => {
    router.visit(route("bins.edit", id));
};

const addBin = async (id) => {
    router.visit(route("bins.create"));
};

const resolveBinRoleVariant = () => {
    return;
};

const bin_locations = ref([]);

const fetchBinLocationData = async () => {
    // Show loading on map when filtering
    if (mapRef.value && mapRef.value.setLoading) {
        mapRef.value.setLoading(true);
    }

    try {
        const response = await axios.post("/bins/getMapLocation", {
            bin_type_id: filterBinTypeMap.value,
        });

        if (response.data.success) {
            bin_locations.value = response.data.data.locations;
        } else {
            toast.error(response.data.message, {
                theme: "colored",
                type: "error",
            });
        }
    } catch (error) {
        toast.error("Error fetching map data", {
            theme: "colored",
            type: "error",
        });
    } finally {
        // Loading will be handled by the map component itself
        // when it finishes updating markers
    }
};

const handleDownload = (bin) => {
    downloadQR(bin);
};

const loadingText = ref(null)
const handleDownloadQRMultiple = async () => {
    if (selectedIds.value.length === 0) {
        Swal.fire({
            title: "Failed to download QR Code",
            text: "Select bin to download QR Code",
            icon: "warning",
            confirmButtonText: "Back",
        });
        return;
    }

	loadingText.value = 'Downloading...'
    isLoading.value = true;

    try {
        const response = await axios.post('/bins/fetchDetailByIds', {
            ids: selectedIds.value
        });

        const bins = response.data;

        if (bins.length > 5) {
            await generateZipQR(bins);
        } else {
            for (const bin of bins) {
                await downloadQR(bin);
                await new Promise(res => setTimeout(res, 500));
            }
        }
    } catch (error) {
        console.error("Failed to fetch bin details", error);
        Swal.fire({
            title: "Error",
            text: "Could not fetch bin data",
            icon: "error"
        });
    } finally {
        isLoading.value = false;
    }
};

const setVisibilityLabel = (visibility) => {
    return !visibility ? 'On' : 'Off'
}
</script>

<style scoped>
.filter-panel {
    position: relative;
    z-index: 1000;
    background: white;
}

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
