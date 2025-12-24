<template>
<Head title="Create Menu" />
<Layout>
    <!-- Breadcrumbs -->
    <v-breadcrumbs class="pt-0" :items="[
        {
          title: 'Reward',
          disabled: false,
          href: '/rewards',
        },
        {
          title: 'Reward List',
          disabled: true,
        },
        {
          title: reward ? 'Edit Reward' : 'Create Reward',
          disabled: true,
        },
      ]">
        <template v-slot:prepend></template>
    </v-breadcrumbs>

    <VCard class="mb-5">
        <VForm @submit.prevent="submit">
            <VCardText>
                <AppStepper v-model:current-step="currentStep" :items="numberedSteps" class="stepper-icon-step-bg" />
            </VCardText>

            <VDivider />

            <VCardText>
                <VWindow v-model="currentStep" class="disable-tab-transition">
                    <VWindowItem>
                        <VRow>
                            <VCol cols="12">
                                <h6 class="text-h6 font-weight-medium">Reward Detail</h6>
                                <p class="mb-0">Enter reward detail</p>
                            </VCol>

                            <VCol cols="6">
                                <div class="form-group mb-4">
                                    <label class="v-label mb-1 text-body-2">
                                        Reward Name
                                        <span class="text-error ml-1">*</span>
                                    </label>
                                    <AppTextField v-model="form.name" placeholder="Name" />
                                    <div v-if="form.errors.name" class="text-error text-sm mt-1">
                                        {{ form.errors.name }}
                                    </div>
                                </div>

                                <div class="form-group mb-4">
                                    <label class="v-label mb-1 text-body-2">
                                        CO2 Point Required
                                        <span class="text-error ml-1">*</span>
                                    </label>
                                    <AppTextField type="number" v-model="form.price" placeholder="Point required" />
                                    <div v-if="form.errors.price" class="text-error text-sm mt-1">
                                        {{ form.errors.price }}
                                    </div>
                                </div>

                                <div class="form-group mb-4">
                                    <label class="v-label mb-1 text-body-2">
                                        Reward Label
                                        <span class="text-error ml-1">*</span>
                                    </label>
                                    <AppTextField v-model="form.label" placeholder="Label" />
                                    <div v-if="form.errors.label" class="text-error text-sm mt-1">
                                        {{ form.errors.label }}
                                    </div>
                                </div>

                                <div class="form-group mb-4">
                                    <label class="v-label mb-1 text-body-2">
                                        Reward Description
                                        <span class="text-error ml-1">*</span>
                                    </label>
                                    <AppTextarea v-model="form.description" :error-messages="form.errors.description" autofocus placeholder="Description" />
                                </div>

                                <div class="form-group mb-4">
                                    <label class="v-label mb-1 text-body-2">
                                        Reward Terms and Condition
                                        <span class="text-error ml-1">*</span>
                                    </label>
                                    <AppTextarea v-model="form.tnc" :error-messages="form.errors.tnc" placeholder="Terms and Conditions" />
                                </div>

                                <div class="form-group">
                                    <VSwitch v-model="form.status" :label="setStatusLabel(form.status)" class="mt-2" />
                                    <div v-if="form.errors.status" class="text-error text-sm mt-1">
                                        {{ form.errors.status }}
                                    </div>
                                </div>
                            </VCol>

                            <VCol cols="6">
                                <div class="form-group">
                                    <label class="v-label mb-1 text-body-2">
                                        Reward Image
                                        <span class="text-error ml-1">*</span>
                                    </label>
                                    <VFileInput @change="handleImagePreview" accept="image/*" class="mb-3" />
                                    <div v-if="form.errors.image" class="text-error text-sm mb-3">
                                        {{ form.errors.image }}
                                    </div>

                                    <div v-if="imagePreview" class="mt-3 position-relative d-inline-block">
                                        <img :src="imagePreview" alt="Image Preview" class="rounded border" style="max-width: 100%; max-height: 200px" />
                                        <VBtn class="position-absolute" type="button" color="error" style="top: 5px; right: 5px" @click="deleteImage" size="x-small">
                                            X
                                        </VBtn>
                                    </div>
                                </div>

                                <div class="flex items-center flex-none order-0 grow-0 mb-5">
                                    <label class="v-label mb-1 text-body-2 text-wrap" for="v-67" style="line-height: 15px;">Event Tag</label>
                                    <AppSelect v-model="form.event_id" :items="eventOptions" placeholder="Select event" />
                                </div>

                                <div class="flex items-center flex-none order-0 grow-0 mb-5">
                                    <label class="v-label mb-1 text-body-2 text-wrap" for="v-67" style="line-height: 15px;">Date </label>
                                    <AppDateTimePicker v-model="form.date" placeholder="Select date" :config="{ mode: 'range' }" />
                                    <div v-if="form.errors.date" class="invalid-feedback text-error">{{ form.errors.date }}</div>
                                </div>

                                <VRow>
                                    <VCol>
                                        <div class="flex items-center flex-none order-0 grow-0 mb-5">
                                            <label class="v-label mb-1 text-body-2 text-wrap" for="v-67" style="line-height: 15px;">Time </label>
                                            <AppDateTimePicker v-model="form.start_time" placeholder="Start time" :config="{ enableTime: true, noCalendar: true, dateFormat: 'H:i' }" />
                                            <div v-if="form.errors.start_time" class="invalid-feedback text-error">{{ form.errors.start_time }}</div>
                                        </div>
                                    </VCol>
                                    <VCol>
                                        <div class="flex items-center flex-none order-0 grow-0 mb-5">
                                            <AppDateTimePicker class="mt-5" v-model="form.end_time" placeholder="End time" :config="{ enableTime: true, noCalendar: true, dateFormat: 'H:i' }" />
                                            <div v-if="form.errors.end_time" class="invalid-feedback text-error">{{ form.errors.end_time }}</div>
                                        </div>
                                    </VCol>
                                </VRow>
                            </VCol>
                        </VRow>
                    </VWindowItem>

                    <VWindowItem>
                        <VRow>
                            <VCol cols="12">
                                <h6 class="text-h6 font-weight-medium">
                                    Voucher Code Assignments
                                </h6>
                                <p class="mb-0">Add voucher codes to assign to users</p>
                            </VCol>
                        </VRow>

                        <VCol cols="6">
                            <div class="form-group">
                                <label class="v-label mb-1 text-body-2">
                                    Import Voucher
                                </label>
                                <VFileInput @change="handleImportVoucher" accept=".xls,.xlsx,.csv" class="mb-3" />
                                <div v-if="form.errors.import" class="text-error text-sm mb-3">
                                    {{ form.errors.import }}
                                </div>
                            </div>
                        </VCol>

                        <v-virtual-scroll ref="voucherScroll" :items="form.vouchers" height="400" item-height="60">
                            <template #default="{ item: voucher, index: i }">
                                <v-row class="py-2 px-4 border-b align-center">
                                    <v-col cols="9">
                                        <AppTextField
                                            v-model="voucher.code"
                                            :error="!!form.errors[`vouchers.${i}.code`]"
                                            :error-messages="form.errors[`vouchers.${i}.code`]"
                                            :readonly="!!voucher.id"
                                            @input="markVoucherAsModified(voucher)"
                                        />
                                    </v-col>
                                    <v-col cols="2" class="text-right">
                                        <VChip :color="$filters.resolveStatusVariant(voucher.id ? voucher.status_text : 'New')" size="small" label class="text-capitalize">
                                            {{ voucher.id ? voucher.status_text : "New" }}
                                        </VChip>
                                    </v-col>
                                    <v-col cols="1" class="text-right">
                                        <v-icon v-if="voucher.status === 1" icon="tabler-trash" color="error" @click="deleteVoucher(i)" class="cursor-pointer" />
                                    </v-col>
                                </v-row>
                            </template>
                        </v-virtual-scroll>

                        <div v-if="form.errors.vouchers" class="text-error text-sm mb-3">
                            {{ form.errors.vouchers }}
                        </div>

                        <VCol cols="12" class="mt-4 d-flex justify-end pr-0">
                            <VBtn @click="addVoucher" color="info" prepend-icon="tabler-plus">
                                Add Voucher
                            </VBtn>
                        </VCol>
                    </VWindowItem>
                </VWindow>

                <div class="d-flex flex-wrap gap-4 justify-sm-space-between justify-center mt-8">
                    <VBtn color="secondary" variant="tonal" :disabled="currentStep === 0" @click="currentStep--" prepend-icon="tabler-arrow-left">
                        Previous
                    </VBtn>

                    <VBtn v-if="numberedSteps.length - 1 === currentStep" color="primary" type="submit" :disabled="form.processing">
                        {{ reward ? "Update" : "Create" }}
                    </VBtn>

                    <VBtn v-else @click="currentStep++" append-icon="tabler-arrow-right">
                        Next
                    </VBtn>
                </div>
            </VCardText>
        </VForm>
    </VCard>

    <VCard v-if="reward && $filters.can('delete-reward')" class="mt-6">
        <VCardItem>
            <VCardTitle class="text-h6 mb-4">Delete Reward</VCardTitle>

            <VAlert type="warning" variant="tonal" class="mb-4">
                <p class="mb-2">Are you sure you want to delete this reward?</p>
                <span>Once deleted, this action cannot be undone. Please be
                    certain.</span>
            </VAlert>

            <VCheckbox v-model="confirmDelete" label="I confirm I want to delete this reward" class="mb-4" />

            <VBtn type="submit" :disabled="!confirmDelete" color="error" @click="deleteReward(reward.id)">
                Delete Reward
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
import Swal from "sweetalert2";
import {
    nextTick,
    onMounted,
    ref,
    watch,
    computed
} from "vue";
import {
    toast
} from "vue3-toastify";
import Layout from "../../layouts/blank.vue";
import debounce from 'lodash/debounce'

const props = defineProps({
    reward: Object,
    events: Object
});

const currentStep = ref(0);
const confirmDelete = ref(false);

// Voucher tracking for optimization
const originalVouchers = ref([]);
const deletedVoucherIds = ref([]);
const modifiedVoucherIds = ref(new Set());

const numberedSteps = [{
        title: "Reward Detail",
        subtitle: "Type of rewards",
    },
    {
        title: "Voucher Assignment",
        subtitle: "List of vouchers",
    },
];

const form = useForm({
    name: null,
    price: null,
    label: null,
    description: null,
    tnc: null,
    image: null,
    status: true,
    vouchers: [],
    import: null,
    date: null,
    start_time: null,
    end_time: null,
    event_id: null
});

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

function handleImportVoucher(event) {
    const file = event.target?.files?.[0];
    if (file) {
        form.import = file;
    } else {
        form.import = null;
    }
}

function deleteImage() {
    imagePreview.value = null;
    form.image = null;
}

// Voucher tracking functions
function markVoucherAsModified(voucher) {
    if (voucher.id && props.reward) {
        modifiedVoucherIds.value.add(voucher.id);
    }
}

function deleteVoucher(index) {
    const voucher = form.vouchers[index];

    // If it's an existing voucher (has ID), track it as deleted
    if (voucher.id) {
        deletedVoucherIds.value.push(voucher.id);
        // Remove from modified list if it was there
        modifiedVoucherIds.value.delete(voucher.id);
    }

    // Remove from the vouchers array
    form.vouchers.splice(index, 1);
}

function getModifiedVouchers() {
    return form.vouchers.filter(voucher => {
        if (!voucher.id) return false; // Skip new vouchers
        return modifiedVoucherIds.value.has(voucher.id);
    });
}

function getNewVouchers() {
    return form.vouchers.filter(voucher => !voucher.id);
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
    if (props.reward) {
        form.name = props.reward.name;
        form.price = props.reward.price;
        form.label = props.reward.label;
        form.description = props.reward.description;
        form.tnc = props.reward.tnc;
        form.status = props.reward.status == 1 ? true : false;
        form.vouchers = props.reward.vouchers;
        form.event_id = props.reward.event_id;
        form.start_time = props.reward.start_time;
        form.end_time = props.reward.end_time;

        // Store original vouchers for tracking changes
        originalVouchers.value = JSON.parse(JSON.stringify(props.reward.vouchers));

        if (props.reward.image) {
            imagePreview.value = `/storage/images/reward/${props.reward.image}`;
        }
        if (props.reward.start_date) {
            form.date = props.reward.start_date + ' to ' + props.reward.end_date
        }
    }
});

const submit = () => {
    const options = {
        onError: (errors) => {
            const step1Errors = [
                "name",
                "price",
                "label",
                "description",
                "tnc",
                "image",
                "status",
            ];
            if (Object.keys(errors).some((key) => step1Errors.includes(key))) {
                currentStep.value = 0;
            }
        },
    };

    if (props.reward && props.reward.id) {
        // Edit mode - send only changes to optimize resource usage
        const modifiedVouchers = getModifiedVouchers();
        const newVouchers = getNewVouchers();

        form
            .transform((data) => ({
                ...data,
                // Remove the full vouchers array to save bandwidth
                vouchers: undefined,
                // Send only the changes
                deleted_voucher_ids: deletedVoucherIds.value,
                updated_vouchers: modifiedVouchers,
                new_vouchers: newVouchers
            }))
            .post(route("rewards.update", props.reward.id), options);
    } else {
        // Create mode - send all vouchers as before
        form.post(route("rewards.store"), options);
    }
};

const voucherScroll = ref(null); // ref to v-virtual-scroll

const addVoucher = async () => {
    // Add a new voucher at the top
    form.vouchers.unshift({
        code: null,
        status: 1
    });

    await nextTick();

    // Scroll to the first index
    if (voucherScroll.value?.scrollToIndex) {
        voucherScroll.value.scrollToIndex(0, {
            behavior: 'smooth',
            align: 'start'
        });
    }
};

const removeVoucher = (key) => {
    deleteVoucher(key);
};

const setStatusLabel = (status) => {
    return status ? "Active" : "Inactive";
};

const deleteReward = async (id) => {
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
        router.delete(route("rewards.destroy", id), {
            preserveScroll: true,
            onError: () => {
                toast.error("An error occurred while deleting the reward.", {
                    theme: "colored",
                    type: "error",
                });
            },
        });
    }
};

const eventOptions = computed(() => [
    ...props.events.map(event => ({
        value: event.id,
        title: event.type.name + ' | ' + event.code + ' | ' + event.name,
    }))
]);
</script>
