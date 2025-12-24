<template>
	<v-overlay :model-value="loading" class="d-flex align-center justify-center" persistent opacity="0.6" style="z-index: 3000">
	    <v-card class="pa-5 text-center">
	        <v-progress-circular indeterminate color="primary" size="40" />
	        <div class="mt-4">Fetching address...</div>
	    </v-card>
	</v-overlay>
<Head title="Create Menu" />
<Layout>
    <v-breadcrumbs class="pt-0" :items="[
		{
			title: 'Bins',
			disabled: false,
			href: '/bins',
		},
		{
			title: bin ? 'Edit Bin' : 'Create Bin',
			disabled: true,
		}
	]">
        <template v-slot:prepend></template>
    </v-breadcrumbs>
    <VCard class="mb-5">
        <VForm @submit.prevent=" bin && bin.id ? form.put(route('bins.update', bin.id)) : form.post(route('bins.store'))">
            <VCardText>
                <AppStepper v-model:current-step="currentStep" :items="numberedSteps" class="stepper-icon-step-bg" />
            </VCardText>

            <VDivider />

            <VCardText>
                <VWindow v-model="currentStep" class="disable-tab-transition">
                    <VWindowItem>
                        <VRow>
                            <VCol cols="12">
                                <h6 class="text-h6 font-weight-medium">Bin Detail</h6>
                                <p class="mb-0">Enter Bin Detail</p>
                            </VCol>

                            <VCol cols="12">
                                <div class="flex items-center flex-none order-0 grow-0">
                                    <label class="v-label mb-1 text-body-2 text-wrap" for="v-67" style="line-height: 15px;">Bin Type <span class="ml-1" style="color: red">*</span></label>
                                    <AppSelect v-model="form.bin_type_id" :items="binTypeOptions" @update:modelValue="onBinTypeSelected" placeholder="Select bin type" />
                                    <div v-if="form.errors.bin_type_id" class="invalid-feedback text-error">{{ form.errors.bin_type_id }}</div>
                                </div>
                            </VCol>

                            <VCol cols="12">
                                <div class="flex items-center flex-none order-0 grow-0">
                                    <label class="v-label mb-1 text-body-2 text-wrap" for="v-67" style="line-height: 15px;">Accepted Recyclables</label>
                                    <div class="form-group">
                                        <v-chip color="primary me-2 mb-2" v-for="waste_type in bin_type_wastes">
                                            {{waste_type}}
                                        </v-chip>
                                    </div>
                                </div>
                            </VCol>

							<VCol cols="12" v-if="form.bin_type_id == 1">
                                <div class="flex items-center flex-none order-0 grow-0">
                                    <label class="v-label mb-1 text-body-2 text-wrap" for="v-67" style="line-height: 15px;">Bin ID <span class="ml-1" style="color: red">*</span></label>
                                    <AppTextField v-model="form.code" placeholder="Bin ID"/>
                                    <div v-if="form.errors.code" class="invalid-feedback text-error">{{ form.errors.code }}</div>
                                </div>
                            </VCol>

                            <VCol cols="12">
                                <div class="flex items-center flex-none order-0 grow-0">
                                    <label class="v-label mb-1 text-body-2 text-wrap" for="v-67" style="line-height: 15px;">Remark</label>
                                    <AppTextField v-model="form.remark" placeholder="Remark" />
                                    <div v-if="form.errors.remark" class="invalid-feedback text-error">{{ form.errors.remark }}</div>
                                </div>
                            </VCol>

                            <VCol cols="12">
                                <div class="flex items-center flex-none order-0 grow-0">
                                    <VSwitch v-model="form.status" :label="setStatusLabel(form.status)" />
                                    <div v-if="form.errors.status" class="invalid-feedback text-error">{{ form.errors.status }}</div>
                                </div>
                            </VCol>
                        </VRow>

                    </VWindowItem>

                    <VWindowItem>
                        <VRow>
                            <VCol cols="12">
                                <h6 class="text-h6 font-weight-medium">Location Detail</h6>
                                <p class="mb-0">Enter Location Detail</p>
                            </VCol>

							<VCol cols="6">
								<div class="flex items-center flex-none order-0 grow-0">
									<label class="v-label mb-1 text-body-2 text-wrap" for="v-67" style="line-height: 15px;">Postal Code <span class="ml-1" style="color: red">*</span></label>
									<AppTextField v-model="form.postal_code" @input="onPostalCodeInput" />
									<div v-if="form.errors.postal_code" class="invalid-feedback text-error">{{ form.errors.postal_code }}</div>
								</div>
                            </VCol>

                            <VCol cols="6">
                                <div class="flex items-center flex-none order-0 grow-0 w-full">
                                    <label class="v-label mb-1 text-body-2 text-wrap" for="address" style="line-height: 15px;">
                                        Address <span class="ml-1" style="color: red">*</span>
                                    </label>

									<AppTextField v-model="form.address" readonly />

                                    <div v-if="form.errors.address" class="invalid-feedback text-error">
                                        {{ form.errors.address }}
                                    </div>
                                </div>
                            </VCol>


                            <VCol cols="6">
                                <div class="flex items-center flex-none order-0 grow-0">
                                    <label class="v-label mb-1 text-body-2 text-wrap" for="v-67" style="line-height: 15px;">Radius <span class="ml-1" style="color: red">*</span></label>
                                    <AppSelect v-model="form.map_radius" :items="mapRadiusOptions" placeholder="Select Radius" />
                                    <div v-if="form.errors.map_radius" class="invalid-feedback text-error">{{ form.errors.map_radius }}</div>
                                </div>
                            </VCol>

                            <VCol cols="12">
                                <div class="flex items-center flex-none order-0 grow-0 map-container">
                                    <LeafLetMap v-model:clickedLocation="selectedLocation" :coordinate="bin ? [bin.lat, bin.long] : null" :locations="bin ? [bin] : null" :radius="parseInt(form.map_radius)" ref="leafletMap" :markable="true" />
                                    <div v-if="form.errors.lat" class="invalid-feedback text-error">{{ form.errors.lat }}</div>
                                </div>
                            </VCol>
                        </VRow>

                    </VWindowItem>
                </VWindow>

                <div class="d-flex flex-wrap gap-4 justify-sm-space-between justify-center mt-8">
                    <VBtn color="secondary" variant="tonal" :disabled="currentStep === 0" @click="currentStep--">
                        <VIcon icon="tabler-arrow-left" start class="flip-in-rtl" />
                        Previous
                    </VBtn>

                    <VBtn v-if="numberedSteps.length - 1 === currentStep" color="primary" type="submit" :disabled="form.processing">
                        {{ bin ? "Update" : "Create" }}
                    </VBtn>

                    <VBtn v-else @click="currentStep++">
                        Next
                        <VIcon icon="tabler-arrow-right" end class="flip-in-rtl" />
                    </VBtn>
                </div>
            </VCardText>
        </VForm>
    </VCard>

    <VCard v-if="bin && $filters.can('delete-bin')">
        <VCardItem>
            <VCardTitle class="my-2">Delete Bin</VCardTitle>
            <VAlert type="warning" variant="tonal" density="default">
                <p>Are you sure to delete this bin?</p>
                <span>Once you delete this bin, there is no going back. Please be certain.</span>
            </VAlert>
            <VCheckbox v-model="confirmDelete" label="I confirm to delete this bin." />
            <VBtn class="mt-5" type="submit" :disabled="!confirmDelete" color="error" @click="deleteBin(bin.id)">
                Delete Bin
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
    computed,
    nextTick
} from "vue";
import Layout from "../../layouts/blank.vue";
import {
    toast
} from "vue3-toastify";
import {
    Inertia
} from '@inertiajs/inertia';
import Swal from "sweetalert2";
import LeafLetMap from "../../Components/LeafLetMap.vue";
import {
    debounce
} from 'lodash'
import { usePostalCode } from '@/composables/usePostalCode'

const currentStep = ref(0)

const numberedSteps = [{
        title: 'Bin Details',
        subtitle: 'Type of bin/variant',
    },
    {
        title: 'Location',
        subtitle: 'Bin map location',
    }
]

const props = defineProps({
    bin_types: Object,
    bin: Object,
	organizations: Object
});

const binTypeOptions = computed(() => [
    ...props.bin_types.map(type => ({
        value: type.id,
        title: type.name,
        waste_types: type.waste_types
    }))
]);

const organizationOptions = computed(() => [
    ...props.organizations.map(organization => ({
        value: organization.id,
        title: organization.name + ' | ' + organization.roles[0].name,
    }))
]);

const mapRadiusOptions = [{
        value: '10',
        title: '10m'
    },
    {
        value: '50',
        title: '50m'
    },
    {
        value: '100',
        title: '100m'
    },
    {
        value: '200',
        title: '200m'
    },
]

const form = useForm({
    bin_type_id: null,
	code: null,
    address: null,
	postal_code: null,
    map_radius: null,
    lat: null,
    long: null,
    status: true,
    qrcode: null,
    remark: null
});

const { onPostalCodeInput, selectedLocation, loading } = usePostalCode(form)

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

const bin_type_wastes = ref([])

onMounted(() => {
    if (props.bin) {
        form.bin_type_id = props.bin.bin_type_id
		form.code = props.bin.code
        form.e_waste_bin_type_id = props.bin.e_waste_bin_type_id
        form.map_radius = props.bin.map_radius
        form.address = props.bin.address
		form.postal_code = props.bin.postal_code
        form.lat = props.bin.lat
        form.long = props.bin.long
        form.remark = props.bin.remark
        form.status = props.bin.status
        bin_type_wastes.value = props.bin.type.waste_types.map(waste => waste.name);
    }
});

const toggleSwitch = ref(true)
const toggleFalseSwitch = ref(false)

const setStatusLabel = (status) => {
    return status ? 'Active' : 'Inactive'
}

const deleteBin = async (id) => {
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
            const response = await axios.delete(`/bins/${id}`);
            if (response.data.success) {
                toast.success(response.data.message, {
                    theme: "colored",
                    type: "success",
                });
                router.visit(route('bins.index'));
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

const leafletMap = ref(null)

watch(currentStep, async (newStep) => {
    if (newStep === 1) {
        await nextTick()
        leafletMap.value?.map?.invalidateSize()
    }
})

watch(
    () => form.map_radius,
    async (newVal, oldVal) => {
        await nextTick()
        leafletMap.value?.map?.invalidateSize()
    }
);

// const selectedLocation = ref(null)

watch(selectedLocation, (newVal, oldVal) => {
    if (newVal) {
        form.lat = newVal.lat;
        form.long = newVal.lng;
    }
})

const onBinTypeSelected = (selectedBinTypeId) => {
    const selectedType = binTypeOptions.value.find(type => type.value === selectedBinTypeId);
    if (selectedType) {
        bin_type_wastes.value = selectedType.waste_types.map(waste => waste.name); // or use `waste.id` if you want IDs
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
