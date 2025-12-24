<template>
<Head title="Events" />
<Layout>
    <v-breadcrumbs class="pt-0" :items="[
		{
			title: 'Event',
			disabled: false,
			href: '/events',
		},
		{
			title: 'Event List',
			disabled: true,
		},
		{
			title: event ? 'Edit Event' : 'Create Event',
			disabled: true,
		}
	]">
        <template v-slot:prepend></template>
    </v-breadcrumbs>
    <VCard class="mb-5">
		<VForm @submit.prevent="event && event.id ? form.post(route('events.update', event.id)) : form.post(route('events.store'))">
            <VCardItem>
                <VCardTitle>Event Detail</VCardTitle>
                <small>Enter Event Detail</small>
            </VCardItem>

            <VCardText>
                <VRow>
                    <VCol cols="6">
                        <div class="flex items-center flex-none order-0 grow-0 mb-5">
                            <label class="v-label mb-1 text-body-2 text-wrap" for="v-67" style="line-height: 15px;">Event Type <span class="ml-1" style="color: red">*</span></label>
                            <AppSelect v-model="form.event_type_id" :items="eventTypeOptions" placeholder="Select event type" @update:modelValue="onEventTypeChange" />
                            <div v-if="form.errors.event_type_id" class="invalid-feedback text-error">{{ form.errors.event_type_id }}</div>
                        </div>

						<div class="flex items-center flex-none order-0 grow-0 mb-5" v-if="form.event_type_id != 3 && form.event_type_id != 4">
                            <label class="v-label mb-1 text-body-2 text-wrap" for="v-67" style="line-height: 15px;">District <span class="ml-1" style="color: red">*</span></label>
                            <v-autocomplete v-model="form.district_id" :items="districtOptions" />
                            <div v-if="form.errors.district_id" class="invalid-feedback text-error">{{ form.errors.district_id }}</div>
                        </div>

						<div class="flex items-center flex-none order-0 grow-0 w-full" v-if="form.event_type_id != 3 && form.event_type_id != 4">
							<label class="v-label mb-1 text-body-2 text-wrap" for="address" style="line-height: 15px;">
								Address <span class="ml-1" style="color: red">*</span>
							</label>

							<v-autocomplete id="address" v-model="form.address" :items="addressSuggestions" item-title="ADDRESS" item-value="ADDRESS" placeholder="Search address" hide-no-data hide-details :loading="loading"
								@update:search="onAddressSearch" @blur="validateAddressSelection" @update:modelValue="onAddressSelected" clearable class="w-full" />

							<div v-if="form.errors.address" class="invalid-feedback text-error">
								{{ form.errors.address }}
							</div>
						</div>

						<div v-if="form.event_type_id == 3 || form.event_type_id == 4">
							<div v-if="form.event_type_id == 3" class="flex items-center flex-none order-0 grow-0 mb-5">
								<label class="v-label mb-1 text-body-2 text-wrap" for="v-67" style="line-height: 15px;">Related School / Enterprise <span class="ml-1" style="color: red">*</span></label>
								<v-autocomplete v-model="form.user_id" :items="userOptions" />
								<div v-if="form.errors.user_id" class="invalid-feedback text-error">{{ form.errors.user_id }}</div>
							</div>

							<div class="flex items-center flex-none order-0 grow-0 mb-5">
								<label class="v-label mb-1 text-body-2 text-wrap" for="v-67" style="line-height: 15px;">Event Name <span class="ml-1" style="color: red">*</span></label>
								<AppTextField v-model="form.name" />
								<div v-if="form.errors.name" class="invalid-feedback text-error">{{ form.errors.name }}</div>
							</div>

							<div class="flex items-center flex-none order-0 grow-0 mb-5">
								<label class="v-label mb-1 text-body-2 text-wrap" for="v-67" style="line-height: 15px;">Event Description</label>
								<AppTextarea v-model="form.description" />
								<div v-if="form.errors.description" class="invalid-feedback text-error">{{ form.errors.description }}</div>
							</div>

							<div class="flex items-center flex-none order-0 grow-0 mb-5">
								<label class="v-label mb-1 text-body-2 text-wrap" for="v-67" style="line-height: 15px;">Event Image <span class="ml-1" style="color: red">*</span></label>
								<!-- <VFileInput @change="handleImagePreview" accept="image/*" /> -->
								<v-file-input
									@change="handleImagePreview"
									accept="image/*"
									prepend-icon=""
								/>
								<div v-if="form.errors.image" class="invalid-feedback text-error">{{ form.errors.image }}</div>

								<div v-if="imagePreview" class="mt-3 position-relative d-inline-block">
									<img :src="imagePreview" alt="Image Preview" class="rounded border" style="max-width: 100%; max-height: 200px;" />
									<VBtn class="position-absolute" type="button" color="error" style="top: 5px; right: 5px;" @click="deleteImage" size="x-small">
										X
									</VBtn>
								</div>
							</div>
						</div>

                    </VCol>
                    <VCol cols="6">
						<v-sheet class="ma-2 pa-2 map-container">
							<LeafLetMap v-model:clickedLocation="selectedLocation" :coordinate="event ? [event.lat, event.long] : null" :locations="event ? [event] : null" ref="leafletMap" :markable="true" />
							<div v-if="form.errors.lat" class="invalid-feedback text-error">{{ form.errors.lat }}</div>
	                    </v-sheet>


						<div v-if="form.event_type_id == 3 || form.event_type_id == 4">
							<div class="flex items-center flex-none order-0 grow-0 w-full mb-5 mt-5">
								<label class="v-label mb-1 text-body-2 text-wrap" for="address" style="line-height: 15px;">
									Address
								</label>

								<v-autocomplete id="address" v-model="form.address" :items="addressSuggestions" item-title="ADDRESS" item-value="ADDRESS" placeholder="Search address" hide-no-data hide-details :loading="loading"
								@update:search="onAddressSearch" @blur="validateAddressSelection" @update:modelValue="onAddressSelected" clearable class="w-full" />

								<div v-if="form.errors.address" class="invalid-feedback text-error">
									{{ form.errors.address }}
								</div>
							</div>

							<div v-if="form.event_type_id == 3" class="flex items-center flex-none order-0 grow-0 mb-5">
								<label class="v-label mb-1 text-body-2 text-wrap" for="v-67" style="line-height: 15px;">Event Code <span class="ml-1" style="color: red">*</span></label>
								<AppTextField v-model="form.secret_code" @input="form.secret_code = form.secret_code.replace(/\s/g, '')" />
								<div v-if="form.errors.secret_code" class="invalid-feedback text-error">{{ form.errors.secret_code }}</div>
							</div>
						</div>


                    </VCol>
                </VRow>
            </VCardText>

            <v-divider></v-divider>

            <VCardItem>
                <VCardTitle>Date & Time</VCardTitle>
            </VCardItem>

            <VCardText>
                <VRow>
                    <VCol cols="6">
                        <div class="flex items-center flex-none order-0 grow-0 mb-5">
                            <label class="v-label mb-1 text-body-2 text-wrap" for="v-67" style="line-height: 15px;">Date <span class="ml-1" style="color: red">*</span></label>
                            <AppDateTimePicker v-model="form.date" placeholder="Select date" :config="{ mode: 'range' }" />
                            <div v-if="form.errors.date" class="invalid-feedback text-error">{{ form.errors.date }}</div>
                        </div>
                    </VCol>
                    <VCol cols="3">
                        <div class="flex items-center flex-none order-0 grow-0 mb-5">
                            <label class="v-label mb-1 text-body-2 text-wrap" for="v-67" style="line-height: 15px;">Time <span class="ml-1" style="color: red">*</span></label>
                            <AppDateTimePicker v-model="form.time_start" placeholder="Select time" :config="{ enableTime: true, noCalendar: true, dateFormat: 'H:i' }" />
                            <div v-if="form.errors.time_start" class="invalid-feedback text-error">{{ form.errors.time_start }}</div>
                        </div>
                    </VCol>
                    <VCol cols="3">
                        <div class="flex items-center flex-none order-0 grow-0 mb-5">
                            <AppDateTimePicker class="mt-5" v-model="form.time_end" placeholder="Select time" :config="{ enableTime: true, noCalendar: true, dateFormat: 'H:i' }" />
                            <div v-if="form.errors.time_end" class="invalid-feedback text-error">{{ form.errors.time_end }}</div>
                        </div>
                    </VCol>
                </VRow>
            </VCardText>

            <template v-if="form.event_type_id == 3 || form.event_type_id == 4">
                <v-divider></v-divider>

                <VCardItem>
                    <VCardTitle>Bin Selection</VCardTitle>
                </VCardItem>

                <VCardText>
					<!-- Select All Checkbox -->
					<div class="mb-4">
						<VCheckbox
							v-model="form.select_all_bins"
							label="Select All Bins"
							@update:modelValue="onSelectAllToggle"
							color="primary"
						/>
					</div>

					<!-- Autocomplete Section with Infinite Scroll (shown when form.select_all_bins is false) -->
					<div v-if="!form.select_all_bins" class="flex items-center flex-none order-0 grow-0 mb-3">
						<label class="v-label mb-1 text-body-2 text-wrap" for="v-67" style="line-height: 15px;">Bin selection <span class="ml-1" style="color: red">*</span></label>
						<v-autocomplete
							chips
							multiple
							v-model="form.bins"
							:items="binOptions"
							:search="binSearch"
							:loading="binLoading"
							:no-filter="true"
							item-title="name"
							item-value="id"
							@update:search="handleSearchUpdate"
							return-object
							:menu-props="{
								maxHeight: '300px'
							}"
							ref="autocompleteRef"
						>
							<!-- Custom list item template -->
							<template #item="{ props, item }">
								<v-list-item v-bind="props" :title="item.title">
									<template #append>
										<v-chip size="small" color="primary">
											{{ item.raw.point }} pts
										</v-chip>
									</template>
								</v-list-item>
							</template>

							<!-- Loading indicator and Load More button at the bottom -->
							<template #append-item>
								<div v-if="isLoadingMore" class="text-center pa-2">
									<v-progress-circular indeterminate size="24"></v-progress-circular>
									<span class="ml-2">Loading more...</span>
								</div>
								<div
									v-else-if="hasMoreData && binOptions.length > 0"
									class="text-center pa-2"
									@click="loadMoreData"
									style="cursor: pointer;"
								>
									<v-btn variant="text" size="small" color="primary">
										Load More ({{ binOptions.length }} loaded)
									</v-btn>
								</div>
								<div v-else-if="binOptions.length > 0" class="text-center pa-2 text-caption text-medium-emphasis">
									All bins loaded ({{ binOptions.length }} total)
								</div>
								<!-- Invisible trigger element for intersection observer -->
								<div ref="loadTrigger" style="height: 1px;"></div>
							</template>
						</v-autocomplete>
						<div v-if="form.errors.bins" class="invalid-feedback text-error">{{ form.errors.bins }}</div>
					</div>

					<VDataTable
						:headers="[
							{ title: !form.select_all_bins ? 'Bin Name' : 'Bin Type Name', key: 'name', sortable: true },
							{ title: 'CO2 Point Value', key: 'point', sortable: false, width: '150px' },
							{ title: 'Actions', key: 'actions', sortable: false, width: '80px' }
						]"
						:items="form.bins"
						:search="form.select_all_bins ? dataTableSearch : ''"
						:items-per-page="itemsPerPage"
						:items-per-page-options="itemsPerPageOptions"
						item-value="id"
						class="elevation-0"
					>
						<!-- Bin Name Column -->
						<template v-slot:item.name="{ item }">
							<div class="d-flex align-center gap-x-4">
								<v-avatar size="34" color="#EDF2F8">
									<v-icon icon="tabler-recycle" color="primary" />
								</v-avatar>
								<div class="d-flex flex-column">{{ item.name }}</div>
							</div>
						</template>

						<!-- CO2 Point Value Column (Always Editable) -->
						<template v-slot:item.point="{ item }">
							<AppTextField
								v-model="item.point"
								type="number"
								density="compact"
								hide-details
								style="max-width: 120px;"
								:error="!!getPointError(item.id)"
								:error-messages="getPointError(item.id)"
							/>
						</template>

						<!-- Actions Column -->
						<template v-slot:item.actions="{ item, index }">
							<v-icon
								icon="tabler-trash"
								color="red"
								@click="removeBin(index)"
								style="cursor: pointer;"
								title="Remove bin"
							/>
						</template>

						<!-- No data message -->
						<template v-slot:no-data>
							<div class="text-center pa-4">
								No bins selected. {{ form.select_all_bins ? 'Loading all bins...' : 'Use the autocomplete above to select bins.' }}
							</div>
						</template>
					</VDataTable>
                </VCardText>
            </template>

            <template v-else>
                <v-divider></v-divider>

                <VCardItem>
                    <VCardTitle>Accepted Recyclables</VCardTitle>
                </VCardItem>

                <VCardText>
                    <div class="flex items-center flex-none order-0 grow-0 mb-3">
                        <label class="v-label mb-1 text-body-2 text-wrap" for="v-67" style="line-height: 15px;">Accepted Recyclables <span class="ml-1" style="color: red">*</span></label>
                        <v-combobox v-model="form.waste_types" :items="wasteTypeOptions" item-title="name" return-object clearable chips multiple @update:modelValue="handleWasteSelection" />
                        <div v-if="form.errors.waste_types" class="invalid-feedback text-error">{{ form.errors.waste_types }}</div>
                    </div>
                    <VTable class="text-no-wrap">
                        <thead>
                            <tr>
                                <th>RECYCLABLES</th>
                                <th v-if="form.event_type_id == 2">RECYCLABLES PRICES (PER KG)</th>
                                <th>
                                    <VIcon icon="tabler-trash" />
                                </th>
                            </tr>
                        </thead>
                        <tbody>
                            <tr v-for="(waste, i) in form.waste_types" :key="i">
                                <td>
                                    <div class="d-flex align-center gap-x-4">
                                        <v-avatar size="34" color="#EDF2F8">
                                            <v-icon icon="tabler-recycle" color="primary" />
                                        </v-avatar>
                                        <div class="d-flex flex-column">{{ waste.name }}</div>
                                    </div>
                                </td>
								<td v-if="form.event_type_id == 2">
									<AppTextField v-model="waste.price" :error="!!form.errors[`waste_types.${i}.price`]" :error-messages="form.errors[`waste_types.${i}.price`]"/>
								</td>
                                <td>
                                    <v-icon icon="tabler-trash" color="red" @click="removeWaste(i)" />
                                </td>
                            </tr>
                        </tbody>
                    </VTable>
                </VCardText>
            </template>


            <VCardText>
                <VSwitch v-model="form.status" :label="setStatusLabel(form.status)" />

                <VBtn class="mt-5" type="submit" :disabled="form.processing" color="primary">
                    <v-icon end icon="tabler-check" class="me-2" />
                    {{ event ? "Update" : "Create" }}
                </VBtn>
            </VCardText>
        </VForm>
    </VCard>

    <VCard v-if="event && $filters.can('delete-event')">
        <VCardItem>
            <VCardTitle class="my-2">Delete Event</VCardTitle>
            <VAlert type="warning" variant="tonal" density="default">
                <p>Are you sure to delete this event?</p>
                <span>Once you delete this event, there is no going back. Please be certain.</span>
            </VAlert>
            <VCheckbox v-model="confirmDelete" label="I confirm to delete this event." />
            <VBtn class="mt-5" type="submit" :disabled="!confirmDelete" color="error" @click="deleteEvent(event.id)">
                Delete Event
            </VBtn>
        </VCardItem>
    </VCard>
</Layout>
</template>

<script setup>
import {
    Head,
    router,
    useForm,
    usePage
} from "@inertiajs/vue3";
import {
    debounce
} from 'lodash';
import Swal from "sweetalert2";
import {
    computed,
    onMounted,
    onUnmounted,
    nextTick,
    ref,
    watch
} from "vue";
import {
    toast
} from "vue3-toastify";
import LeafLetMap from "../../Components/LeafLetMap.vue";
import Layout from "../../layouts/blank.vue";

const props = defineProps({
    event: Object,
    event_types: Object,
    waste_types: Object,
	districts: Object,
	users: Object
});

const form = useForm({
	name: null,
	secret_code:null,
    event_type_id: null,
    district_id: null,
    address: null,
	postal_code: null,
    lat: null,
    long: null,
    date: null,
    time_start: null,
    time_end: null,
    image: null,
    description: null,
    user_id: null,
    bins: [],
    waste_types: [],
    status: true,
	select_all_bins: false
});

const confirmDelete = ref(false)

const dataTableSearch = ref('')

// Pagination logic for the main table (always form.bins)
const itemsPerPage = computed(() => {
    const itemCount = form.bins.length

    if (form.select_all_bins) {
        // In select all mode, always use pagination with default 10 items per page
        return 10
    } else {
        // In normal mode, use pagination if more than 10 items, otherwise show all
        return itemCount > 10 ? 10 : itemCount || 5
    }
})

const itemsPerPageOptions = computed(() => {
    if (form.select_all_bins) {
        // In select all mode, provide full pagination options
        return [5, 10, 25, 50, 100]
    } else {
        // In normal mode, provide options based on item count
        const itemCount = form.bins.length
        if (itemCount <= 5) {
            return [5]
        } else if (itemCount <= 10) {
            return [5, 10]
        } else if (itemCount <= 25) {
            return [5, 10, 25]
        } else {
            return [5, 10, 25, 50]
        }
    }
})

// Remove bin from form.bins by index
const removeBin = (index) => {
    form.bins.splice(index, 1)
}

watch(
    () => usePage().props.flash,
    (flash) => {
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
    }
);

onMounted(() => {
    if (props.event) {
		form.name = props.event.name;
		form.secret_code = props.event.secret_code;
	    form.event_type_id = props.event.event_type_id;
	    form.district_id = props.event.district_id;
	    form.address = props.event.address ?? 'Entire Singapore';
		form.postal_code = props.event.postal_code;
	    form.lat = props.event.lat;
	    form.long = props.event.long;
	    form.time_start = props.event.time_start;
	    form.time_end = props.event.time_end;
	    form.image = props.event.image;
	    form.description = props.event.description;
	    form.user_id = props.event.user_id;
		form.status = props.event.status;
		form.date = props.event.date_start + ' to ' + props.event.date_end
		form.select_all_bins = props.event.use_all_bins

		if (props.event.image) {
			imagePreview.value = `/storage/images/event/${props.event.image}`;
		}

		form.waste_types = props.event.event_waste_type.map(event_waste_type => {
			return { name: event_waste_type.waste_type.name, price: event_waste_type.price }
		})

		if (props.event.event_bins[0].bin) {
			form.bins = props.event.event_bins.map(event_bin => {
				return {
					id: event_bin.bin.id,
					name: event_bin.bin.type.name + ' | ' + event_bin.bin.code + ' | ' + event_bin.bin.address ,
					point: event_bin.point
				}
			})
		}

		if (props.event.event_bins[0].bin_type) {
			form.bins = props.event.event_bins.map(event_bin => {
				return {
					id: event_bin.bin_type.id,
					name: event_bin.bin_type.name,
					point: event_bin.point
				}
			})
		}

		if (props.event.event_type_id == 3 || props.event.event_type_id == 4) {
			fetchBinOptions('', true) // Load initial data
		}
    }

    // Set up intersection observer for auto-loading
    setupIntersectionObserver()
});

onUnmounted(() => {
    if (observer) {
        observer.disconnect()
    }
})

const toggleSwitch = ref(true)
const toggleFalseSwitch = ref(false)

const setStatusLabel = (status) => {
    return status ? 'Active' : 'Inactive'
}

const deleteEvent = async (id) => {
    const result = await Swal.fire({
        title: "Are you sure? <br> <i class='fa-solid fa-trash-can'></i>",
        text: "This action cannot be undone! The data will be permanently deleted!",
        icon: "warning",
        showCancelButton: true,
        confirmButtonColor: "#ea5455",
        cancelButtonColor: "#6CC9CF",
        confirmButtonText: "Yes, Proceed!",
        cancelButtonText: "Cancel",
    });

    if (result.isConfirmed) {
        try {
            const response = await axios.delete(`/events/${id}`);
            if (response.data.success) {
                toast.success(response.data.message, {
                    theme: "colored",
                    type: "success",
                });
                router.visit(route('events.index'));
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

const eventTypeOptions = computed(() => [
	{ value: '', title: 'Select event type' },
    ...props.event_types.map(event => ({
        value: event.id,
        title: event.name,
    }))
]);

const districtOptions = computed(() => [
	{ value: '', title: 'Select district' },
    ...props.districts.map(district => ({
        value: district.id,
        title: district.name,
    }))
]);

const userOptions = computed(() => [
	{ value: '', title: 'Select entity' },
    ...props.users.map(user => ({
        value: user.id,
        title: user.name,
    }))
]);

const selectedLocation = ref(null)

watch(selectedLocation, (newVal, oldVal) => {
    if (newVal) {
        form.lat = newVal.lat;
        form.long = newVal.lng;
    }
})

const imagePreview = ref(null);

function handleImagePreview(event) {
    const file = event.target?.files?.[0];
    if (file) {
        imagePreview.value = URL.createObjectURL(file);
        form.image = file;
    } else {
        imagePreview.value = null;
        form.image = null;
    }
}

function deleteImage() {
    imagePreview.value = null;
    form.image = null;

    if (props.binType?.image) {
        form.remove_existing_image = true;
    }
}

const wasteTypeOptions = computed(() => {
    return props.waste_types.map(type => ({
        name: type.name,
        price: type.price ?? 0
    }))
})

function handleWasteSelection(selected) {
	form.waste_types = selected.map(item => {
		if (typeof item === 'string') {
			const newItem = { name: item, price: 0 }
			wasteTypeOptions.value.push(newItem)
			return newItem
		}
		return item
	})
}

function removeWaste(index) {
  form.waste_types.splice(index, 1)
}

// ===== INFINITE SCROLL BIN AUTOCOMPLETE =====
const binOptions = ref([])
const binSearch = ref('')
const binLoading = ref(false)
const hasMoreData = ref(true)
const currentPage = ref(1)
const isLoadingMore = ref(false)
const loadTrigger = ref(null)
const autocompleteRef = ref(null)
let observer = null

// Load initial data on component mount for bin autocomplete
const loadInitialBinData = () => {
    if (form.event_type_id == 3 || form.event_type_id == 4) {
        fetchBinOptions('', true) // Load initial data without search query
    }
}

// Debounced search function
const debouncedSearch = debounce((query) => {
    fetchBinOptions(query, true) // Reset to first page on new search
}, 300)

const fetchBinOptions = async (query = '', resetData = false) => {
    // If it's a new search, reset pagination
    if (resetData) {
        currentPage.value = 1
        binOptions.value = []
        hasMoreData.value = true
    }

    // Don't fetch if we're already loading or no more data
    if (binLoading.value || isLoadingMore.value || !hasMoreData.value) {
        return
    }

    binSearch.value = query

    // Set appropriate loading state
    if (resetData) {
        binLoading.value = true
    } else {
        isLoadingMore.value = true
    }

    try {
        const response = await fetch(
            `/auto-complete/bin?q=${encodeURIComponent(query)}&page=${currentPage.value}`
        )
        const data = await response.json()

        const formattedData = data.data.map(bin => ({
            id: bin.id,
            name: bin.type.name + ' | ' + bin.code + ' | ' + bin.address,
            point: bin.point || 0
        }))

        // Append new data to existing options or replace if it's a new search
        if (resetData) {
            binOptions.value = formattedData
        } else {
            binOptions.value = [...binOptions.value, ...formattedData]
        }

        // Update pagination state
        hasMoreData.value = data.current_page < data.last_page
        currentPage.value = data.current_page + 1

    } catch (error) {
        console.error('Error fetching entities:', error)
        toast.error('Failed to fetch bins', {
            theme: "colored",
            type: "error",
        })
    } finally {
        binLoading.value = false
        isLoadingMore.value = false
    }
}

// Load more data when scrolling or clicking Load More
const loadMoreData = () => {
    if (hasMoreData.value && !isLoadingMore.value) {
        fetchBinOptions(binSearch.value, false)
    }
}

// Handle search input changes
const handleSearchUpdate = (query) => {
    debouncedSearch(query)
}

// Set up intersection observer for auto-loading
const setupIntersectionObserver = async () => {
    await nextTick()

    if (loadTrigger.value) {
        observer = new IntersectionObserver((entries) => {
            entries.forEach(entry => {
                if (entry.isIntersecting && hasMoreData.value && !isLoadingMore.value) {
                    loadMoreData()
                }
            })
        }, {
            threshold: 0.1
        })

        observer.observe(loadTrigger.value)
    }
}

// Auto-populate form.bins when select all is toggled
const onSelectAllToggle = async (checked) => {
    if (checked) {
        // Fetch all bins and add them to form.bins
        try {
            const response = await fetch('/auto-complete/bin-type')
            const data = await response.json()

            // Replace form.bins with all available bins
            form.bins = data.map(binType => ({
                id: binType.id,
                name: binType.name,
                point: binType.point || 0
            }))
        } catch (error) {
            console.error('Error fetching all bins:', error)
            toast.error('Failed to fetch all bins', {
                theme: "colored",
                type: "error",
            })
            // Revert checkbox if failed
            form.select_all_bins = false
        }
    } else {
        // Clear form.bins when unchecked
        form.bins = []
        dataTableSearch.value = ''
    }
}

const getPointError = (binId) => {
    const binIndex = form.bins.findIndex(bin => bin.id === binId)
    if (binIndex !== -1) {
        return form.errors[`bins.${binIndex}.point`]
    }
    return null
}

const onEventTypeChange = (eventTypeId) => {
	if (eventTypeId == 3 || eventTypeId == 4) {
		// Reset bin autocomplete state and load initial data
        binOptions.value = []
        binSearch.value = ''
        currentPage.value = 1
        hasMoreData.value = true
        fetchBinOptions('', true)
	} else {
        // Reset bin selection when switching away from alba/private events
        form.select_all_bins = false
        form.bins = []
        binOptions.value = []
    }
}

const addressSuggestions = ref([])
const loading = ref(false)

const fetchAddressSuggestions = debounce(async (query) => {
    if (!query || query.length < 3) {
        addressSuggestions.value = []
        loading.value = false
        return
    }

    loading.value = true

    try {
        const response = await axios.get('https://www.onemap.gov.sg/api/common/elastic/search', {
            params: {
                searchVal: query,
                returnGeom: 'Y',
                getAddrDetails: 'Y',
                pageNum: 1,
            }
        })
        addressSuggestions.value = response.data.results || []
    } catch (error) {
        console.error('Failed to fetch address suggestions:', error)
        addressSuggestions.value = []
    } finally {
        loading.value = false
    }
}, 400)

const onAddressSearch = (searchText) => {
    fetchAddressSuggestions(searchText)
}

const onAddressSelected = (addressValue) => {
    const selectedItem = addressSuggestions.value.find(
        item => item.ADDRESS === addressValue
    )
    selectedLocation.value = {
        lat: parseFloat(selectedItem.LATITUDE),
        lng: parseFloat(selectedItem.LONGITUDE),
    }
	form.postal_code = selectedItem.POSTAL
}

const validateAddressSelection = () => {
    const matched = addressSuggestions.value.find(
        item => item.ADDRESS === form.address
    )

    if (!matched) {
        form.address = null
		form.postal_code = null
    }
}
</script>

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
