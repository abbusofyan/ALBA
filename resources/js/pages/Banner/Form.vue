<template>
<Head title="Create Banner" />
<Layout>
    <v-breadcrumbs class="pt-0" :items="[
		{
			title: 'Banner',
			disabled: false,
			href: '/banners',
		},
		{
			title: 'Banner List',
			disabled: true,
		},
		{
			title: banner ? 'Edit Banner' : 'Create Banner',
			disabled: true,
		}
	]">
        <template v-slot:prepend></template>
    </v-breadcrumbs>
    <VCard class="mb-5">
        <VForm @submit.prevent="banner && banner.id ? form.post(route('banners.update', banner.id)) : form.post(route('banners.store'))">
            <VCardItem>
                <VCardTitle>Banner Detail</VCardTitle>
                <small>Enter Banner Detail</small>
            </VCardItem>

            <VCardText>

				<label class="v-label mb-1 text-body-2 text-wrap" for="v-67" style="line-height: 15px;">Banner Placement <span class="ml-1" style="color: red">*</span></label>
				<AppSelect
					v-model="form.banner_placement_id"
					:items="placementsOptions"
					placeholder="Select Ads Placement" readonly
				/>
				<p class="my-2">Optimal Resolution : {{form.dimension}}</p>
                <VTable class="border rounded mt-5">
					<thead>
						<tr>
							<th width="50%">Banner</th>
							<th>Preview</th>
						</tr>
					</thead>
                    <tbody>
                        <tr v-for="(banner, i) in form.banners" :key="i">
                            <td class="banner-column">
                                <div class="form-group mx-2 my-2">
                                    <v-file-input
                                        v-model="banner.image"
                                        label="Select Image"
                                        accept="image/*"
                                        prepend-icon=""
                                        @change="handleImagePreview($event, i)"
                                    />
                                </div>
                                <div v-if="form.errors[`banners.${i}.image`]" class="invalid-feedback text-error">
                                    {{ form.errors[`banners.${i}.image`] }}
                                </div>

								<div class="form-group mx-2 my-2">
									<AppTextField v-model="banner.url" placeholder="Input Redirect URL"/>
								</div>
								<div v-if="form.errors[`banners.${i}.url`]" class="invalid-feedback text-error">
									{{ form.errors[`banners.${i}.url`] }}
								</div>
								<VBtn type="button" color="error" class="ms-2 mb-2" @click="deleteBanner(i)" >
				                    <v-icon start icon="tabler-trash" />
									Delete Banner
				                </VBtn>
                            </td>
							<td>
								<div v-if="banner.preview" class="mt-3 position-relative d-inline-block">
                                    <img
                                        :src="banner.preview"
                                        alt="Image Preview"
                                        class="rounded border"
                                        style="max-width: 100%; max-height: 200px;"
                                    />
                                    <VBtn
                                        class="position-absolute"
                                        type="button"
                                        color="error"
                                        style="top: 5px; right: 5px;"
                                        @click="deleteImage(i)"
                                        size="x-small"
                                        icon="tabler-x"
                                    />
                                </div>
							</td>
                        </tr>

                        <tr v-if="form.banners.length === 0">
                            <td colspan="4" class="text-center py-8">
                                <div class="text-h6 text-disabled mb-2">No banners added yet</div>
                            </td>
                        </tr>
                    </tbody>
                </VTable>

				<VCol cols="12">
					<div class="flex items-center flex-none order-0 grow-0">
						<VSwitch v-model="form.status" :label="setStatusLabel(form.status)" />
						<div v-if="form.errors.status" class="invalid-feedback text-error">{{ form.errors.status }}</div>
					</div>
				</VCol>

                <!-- <div class="mt-4">
					<VCol cols="12" class="mt-4 d-flex justify-end pr-0">
						<VBtn @click="addBanner" color="info" prepend-icon="tabler-plus">
							Add Voucher
						</VBtn>
					</VCol>
                </div> -->
            </VCardText>

            <VCardText>
                <VBtn
                    type="submit"
                    :disabled="form.processing"
                    color="primary"
                    :loading="form.processing"
                >
                    <v-icon start icon="tabler-check" />
                    {{ banner ? "Update" : "Create" }}
                </VBtn>
				<VBtn type="button" color="info" class="ms-2" @click="addBanner" >
                    <v-icon start icon="tabler-plus" />
					Add Banner Image
                </VBtn>

                <!-- <div v-if="form.banners.length === 0" class="text-error mt-2">
                    <small>At least one banner is required</small>
                </div> -->
            </VCardText>
        </VForm>
    </VCard>

    <!-- <VCard v-if="banner && $filters.can('delete-reward')">
        <VCardItem>
            <VCardTitle class="my-2">Delete Banner</VCardTitle>
            <VAlert type="warning" variant="tonal" density="default">
                <p>Are you sure to delete this banner?</p>
                <span>Once you delete this ads, there is no going back. Please be certain.</span>
            </VAlert>
            <VCheckbox v-model="confirmDelete" label="I confirm to delete this ads." />
            <VBtn
                class="mt-5"
                type="submit"
                :disabled="!confirmDelete"
                color="error"
                @click="deleteAds(ads.id)"
            >
                Delete Ads
            </VBtn>
        </VCardItem>
    </VCard> -->
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
    ref,
    watch
} from "vue";
import {
    toast
} from "vue3-toastify";
import Layout from "../../layouts/blank.vue";

const props = defineProps({
    banner: Object,
	placements: Object
});

const form = useForm({
    banner_placement_id: null,
	status: 1,
	banners: [],
});

const confirmDelete = ref(false);

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
	form.banner_placement_id = props.banner.id;
	form.status = props.banner.status;
	form.dimension = props.banner.width + ' X ' + props.banner.height;
	if (props.banner.banners.length > 0) {
		form.banners = props.banner.banners.map((banner) => {
			return {
				id: banner.id,
				url: banner.url,
				image: banner.image,
				preview: banner.image_url
			}
		})
	} else {
		// form.banners.push({
		// 	id: null,
		// 	image: null,
		// 	url: null,
		// 	preview: props.banner.banner?.image_url
		// })
	}

	// if (props.banner) {
	// 	form.banners.push({
	//         placement_id: props.banner.id,
	// 		dimension: props.banner.width + ' X ' + props.banner.height,
	//         url: props.banner.banner?.url,
	//         image: props.banner.banner?.image,
	// 		status: props.banner.banner?.status ?? false,
	// 		preview: props.banner.banner?.image_url
	//     });
	// } else {

	// }
});

const setStatusLabel = (status) => {
    return status ? 'Active' : 'Inactive';
};

// Delete entire ads
const deleteAds = async (id) => {
    const result = await Swal.fire({
        title: "Are you sure?",
        text: "This action cannot be undone! The data will be permanently deleted!",
        icon: "warning",
        showCancelButton: true,
        confirmButtonColor: "#ea5455",
        cancelButtonColor: "#6CC9CF",
        confirmButtonText: "Yes, Delete!",
        cancelButtonText: "Cancel",
    });

    if (result.isConfirmed) {
        try {
            const response = await axios.delete(`/ads/${id}`);
            if (response.data.success) {
                toast.success(response.data.message, {
                    theme: "colored",
                    type: "success",
                });
                router.visit(route('ads.index'));
            } else {
                toast.error(response.data.message, {
                    theme: "colored",
                    type: "error",
                });
            }
        } catch (error) {
            toast.error("An error occurred while deleting the ads.", {
                theme: "colored",
                type: "error",
            });
        }
    }
};

const placementsOptions = computed(() => [{
        value: '',
        title: 'Select Banner Placement'
    },
    ...props.placements.map(placement => ({
        value: placement.id,
        title: placement.name,
    }))
]);

function handleImagePreview(event, index) {
    const file = event.target?.files?.[0];
    const banner = form.banners[index];

    if (file) {
        banner.preview = URL.createObjectURL(file);
        banner.image = file;
    } else {
        banner.preview = null;
        banner.image = null;
    }
}

function deleteImage(index) {
    const banner = form.banners[index];

    if (banner.preview) {
        URL.revokeObjectURL(banner.preview);
        banner.preview = null;
    }
    banner.image = null;

    const fileInput = document.querySelector(`input[type="file"]:nth-of-type(${index + 1})`);
    if (fileInput) {
        fileInput.value = '';
    }
}

const deleteBanner = async (index) => {
	const banner = form.banners[index];

	if (banner.preview) {
		URL.revokeObjectURL(banner.preview);
	}

	form.banners.splice(index, 1);
};

const addBanner = () => {
    form.banners.push({
		id: null,
		image: null,
		url: null,
		preview: null
    });
};

onBeforeUnmount(() => {
    form.banners.forEach(banner => {
        if (banner.preview) {
            URL.revokeObjectURL(banner.preview);
        }
    });
});
</script>

<style scoped>
.position-relative {
    position: relative;
}

.position-absolute {
    position: absolute;
}

.cursor-pointer {
    cursor: pointer;
}

.gap-2 {
    gap: 0.5rem;
}

td.banner-column,
th.banner-column {
  width: 40%;
  max-width: 40%;
}

</style>
