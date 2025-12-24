<template>
<Head title="Create Menu" />
<Layout>
	<v-breadcrumbs
	  class="pt-0"
	  :items="enterprise
	    ? [
	        { title: 'Enterprise', disabled: false, href: '/enterprises' },
	        { title: enterprise.name, disabled: false, href: `/enterprises/${enterprise.id}` },
	        { title: 'Edit', disabled: true }
	      ]
	    : [
	        { title: 'Enterprise', disabled: false, href: '/enterprises' },
	        { title: 'Create Enterprise', disabled: true }
	      ]"
	>
	  <template v-slot:prepend></template>
	</v-breadcrumbs>
    <VCard class="mb-5">
		<VForm @submit.prevent=" enterprise && enterprise.id ? form.put(route('enterprises.update', enterprise.id)) : form.post(route('enterprises.store'))">
			<VCardItem>
	            <VCardTitle>Account Detail</VCardTitle>
	            <small>Enter Account Detail</small>
	        </VCardItem>

	    	<VCardText>
	            <VRow>
	                <VCol cols="6">
	                    <div class="flex items-center flex-none order-0 grow-0">
	                        <label class="v-label mb-1 text-body-2 text-wrap" for="v-67" style="line-height: 15px;">Company Name <span class="ml-1" style="color: red">*</span></label>
	                        <AppTextField v-model="form.name" placeholder="Name" />
							<div v-if="form.errors.name" class="invalid-feedback text-error">{{ form.errors.name }}</div>
	                    </div>
	                </VCol>
					<VCol cols="6">
	                    <div class="flex items-center flex-none order-0 grow-0">
	                        <label class="v-label mb-1 text-body-2 text-wrap" for="v-67" style="line-height: 15px;">Unique ID <span class="ml-1" style="color: red">*</span></label>
	                        <AppTextField v-model="form.username" label="" placeholder="Unique ID" readonly />
							<div v-if="form.errors.username" class="invalid-feedback text-error">{{ form.errors.username }}</div>
	                    </div>
	                </VCol>
	                <VCol cols="6">
						<div class="flex items-center flex-none order-0 grow-0">
	                        <label class="v-label mb-1 text-body-2 text-wrap" for="v-67" style="line-height: 15px;">Email <span class="ml-1" style="color: red">*</span></label>
	                        <AppTextField v-model="form.email" label="" placeholder="Email" />
							<div v-if="form.errors.email" class="invalid-feedback text-error">{{ form.errors.email }}</div>
	                    </div>
	                </VCol>
					<VCol cols="6">
						<div class="flex items-center flex-none order-0 grow-0">
							<label class="v-label mb-1 text-body-2 text-wrap" for="v-67" style="line-height: 15px;">Contact <span class="ml-1" style="color: red">*</span></label>
							<AppTextField v-model="form.phone" placeholder="Contact" />
							<div v-if="form.errors.phone" class="invalid-feedback text-error">{{ form.errors.phone }}</div>
						</div>
					</VCol>
					<VCol cols="6">
	                    <div class="flex items-center flex-none order-0 grow-0">
	                        <label class="v-label mb-1 text-body-2 text-wrap" for="v-67" style="line-height: 15px;">Address <span class="ml-1" style="color: red">*</span></label>
	                        <AppTextField v-model="form.address" placeholder="Address" />
							<div v-if="form.errors.address" class="invalid-feedback text-error">{{ form.errors.address }}</div>
	                    </div>
	                </VCol>
					<VCol cols="6">
	                    <div class="flex items-center flex-none order-0 grow-0">
	                        <label class="v-label mb-1 text-body-2 text-wrap" for="v-67" style="line-height: 15px;">Postal Code <span class="ml-1" style="color: red">*</span></label>
	                        <AppTextField v-model="form.postal_code" type="number" placeholder="123456" />
							<div v-if="form.errors.postal_code" class="invalid-feedback text-error">{{ form.errors.postal_code }}</div>
	                    </div>
	                </VCol>
	                <VCol cols="6">
	                    <div class="flex items-center flex-none order-0 grow-0">
	                        <label class="v-label mb-1 text-body-2 text-wrap" for="v-67" style="line-height: 15px;">Password <span class="ml-1" style="color: red">{{enterprise ? '' : '*'}}</span></label>
	                        <AppTextField v-model="form.password" placeholder="Password" type="password" />
							<div v-if="form.errors.password" class="invalid-feedback text-error">{{ form.errors.password }}</div>
					    </div>
	                </VCol>
					<VCol cols="6">
					   <div class="flex items-center flex-none order-0 grow-0">
						   <label class="v-label mb-1 text-body-2 text-wrap" for="v-67" style="line-height: 15px;">Confirm Password <span class="ml-1" style="color: red">{{enterprise ? '' : '*'}}</span></label>
						   <AppTextField v-model="form.password_confirmation" placeholder="Password" type="password" />
						   <div v-if="form.errors.password_confirmation" class="invalid-feedback text-error">{{ form.errors.password_confirmation }}</div>
					   </div>
				   </VCol>
				   <VCol cols="6">
					   <div class="d-flex align-center mt-2 mb-4">
						   <VCheckbox id="privacy-policy" v-model="form.can_order_pickup" inline />
						   <VLabel for="privacy-policy" style="opacity: 1;">
							   <span class="me-1">Can Order Pickup</span>
						   </VLabel>
					   </div>
				  </VCol>
	            </VRow>

				<VRow>
					<VCol cols="12" v-for="(email, key) in form.secondary_email" :key="key">
						<div class="flex items-center flex-none order-0 grow-0 w-full">
							<label class="v-label mb-1 text-body-2 text-wrap" style="line-height: 15px;">Secondary Email</label>
							<div class="d-flex w-full align-center">
								<v-text-field
									v-model="form.secondary_email[key]"
									class="mr-2 flex-grow-1"
									placeholder="Email"
								></v-text-field>

								<v-btn color="error" @click="deleteSecondaryEmail(key)">
									<v-icon end icon="tabler-x" />
								</v-btn>
							</div>

							<div v-if="form.errors[`secondary_email.${key}`]" class="invalid-feedback text-error">
								{{ form.errors[`secondary_email.${key}`] }}
							</div>
						</div>
					</VCol>


					<VCol cols="12">
						<VBtn @click="addSecondaryEmail" color="info">
							<v-icon end icon="tabler-plus" class="me-2" />
							Add email
						</VBtn>
					</VCol>
	            </VRow>
	        </VCardText>

			<v-divider></v-divider>

			<VCardItem>
				<VCardTitle>Bins</VCardTitle>
			</VCardItem>

			<VCardText>
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
						<template #item="{ props, item }">
							<v-list-item v-bind="props" :title="item.title">
								<template #append>
									<v-chip size="small" color="primary">
										{{ item.raw.point }} pts
									</v-chip>
								</template>
							</v-list-item>
						</template>

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
							<div ref="loadTrigger" style="height: 1px;"></div>
						</template>
					</v-autocomplete>
					<div v-if="form.errors.bins" class="invalid-feedback text-error">{{ form.errors.bins }}</div>
				</div>
			</VCardText>

			<VDataTable
				:headers="[
					{ title: !form.select_all_bins ? 'Bin Name' : 'Bin Type Name', key: 'name', sortable: true },
					{ title: 'Actions', key: 'actions', sortable: false, width: '80px' }
				]"
				:items="form.bins"
				:search="form.select_all_bins ? dataTableSearch : ''"
				:items-per-page="itemsPerPage"
				:items-per-page-options="itemsPerPageOptions"
				item-value="id"
				class="elevation-0"
			>
				<template v-slot:item.name="{ item }">
					<div class="d-flex align-center gap-x-4">
						<v-avatar size="34" color="#EDF2F8">
							<v-icon icon="tabler-recycle" color="primary" />
						</v-avatar>
						<div class="d-flex flex-column">{{ item.name }}</div>
					</div>
				</template>

				<template v-slot:item.actions="{ item, index }">
					<v-icon
						icon="tabler-trash"
						color="red"
						@click="removeBin(index)"
						style="cursor: pointer;"
						title="Remove bin"
					/>
				</template>

				<template v-slot:no-data>
					<div class="text-center pa-4">
						No bins selected. {{ form.select_all_bins ? 'Loading all bins...' : 'Use the autocomplete above to select bins.' }}
					</div>
				</template>
			</VDataTable>


			<VCardText>
				<VSwitch
				    v-model="form.status"
				    :label="setStatusLabel(form.status)"
				    :true-value="1"
				    :false-value="0"
				/>

				<VBtn class="mt-5" type="submit" :disabled="form.processing" color="primary">
					{{ enterprise ? "Update" : "Create" }}
				</VBtn>
	        </VCardText>
		</VForm>
    </VCard>

	<VCard v-if="enterprise && $filters.can('delete-enterprise')">
		<VCardItem>
			<VCardTitle class="my-2">Delete Account</VCardTitle>
			<VAlert type="warning" variant="tonal" density="default">
				<p>Are you sure to delete this account?</p>
				<span>Once you delete this account, there is no going back. Please be certain.</span>
			</VAlert>
			<VCheckbox v-model="confirmDelete" label="I confirm to delete this account."/>
			<VBtn class="mt-5" type="submit" :disabled="!confirmDelete" color="error" @click="deleteEnterprise(enterprise.id)">
				Delete Account
			</VBtn>
		</VCardItem>
    </VCard>
</Layout>
</template>

<script setup>
import {
    Head,
    Link,
    useForm,
    usePage,
	router
} from "@inertiajs/vue3";
import {
    onMounted,
    computed
} from "vue";
import Layout from "../../layouts/blank.vue";
import {
    toast
} from "vue3-toastify";
import { Inertia } from '@inertiajs/inertia';
import Swal from "sweetalert2";
import {
    debounce
} from 'lodash';

const props = defineProps({
    enterprise: Object,
	unique_id: String
});

const form = useForm({
    name: null,
    username: null,
	email: null,
	phone: null,
    password: null,
	password_confirmation: null,
    status: true,
    address: null,
	postal_code: null,
	can_order_pickup: false,
	secondary_email: [],
	bins: [],
});

const confirmDelete = ref(false)

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
	form.username = props.unique_id;
	form.phone = '+65';

    if (props.enterprise) {
        form.name = props.enterprise.name;
		form.username = props.enterprise.username;
		form.email = props.enterprise.email;
		form.phone = props.enterprise.phone;
		form.status = props.enterprise.status
		form.address = props.enterprise.address;
		form.postal_code = props.enterprise.postal_code;
		form.can_order_pickup = props.enterprise.can_order_pickup;
		form.secondary_email = props.enterprise.secondary_email.map((email) => {
			return email.email
		})
    }

	if (props.enterprise.bins) {
		props.enterprise.bins.forEach(bin => {
			form.bins.push({
				id: bin.id,
				name: `${bin.type.name} | ${bin.code} | ${bin.address}`,
				point: bin.point || 0
			});
		});
	}

	fetchBinOptions('', true) // Load initial data

});

const toggleSwitch = ref(true)
const toggleFalseSwitch = ref(false)

const setStatusLabel = (status) => {
    return status ? 'Active' : 'Inactive'
}

const deleteEnterprise = async (id) => {
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
            const response = await axios.delete(`/enterprises/${id}`);
            if (response.data.success) {
                toast.success(response.data.message, {
                    theme: "colored",
                    type: "success",
                });
				router.visit(route('enterprises.index'));
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

watch(form, () => {
	if (!form.password) {
		form.password_confirmation = null
	}
})

const addSecondaryEmail = () => {
	if (form.secondary_email.length == 4) {
		return Swal.fire({
	        title: "Warning! <br> <i class='fa-solid fa-trash-can'></i>",
	        text: "Only allowed to add up to 4 secondary email!",
	        icon: "warning",
	        confirmButtonColor: "#6CC9CF",
	        confirmButtonText: "OK",
	    });
		return alert('Cant add more email')
	}
	form.secondary_email.push('')
}

const deleteSecondaryEmail = (key) => {
	form.secondary_email.splice(key, 1);
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

const removeBin = (index) => {
    form.bins.splice(index, 1)
}

</script>
