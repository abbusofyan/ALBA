<template>
<Head title="Create Menu" />
<Layout>
	<v-breadcrumbs class="pt-0" :items="[
		{
			title: 'Bin Type',
			disabled: false,
			href: '/bin-types',
		},
		{
			title: binType ? 'Edit Bin Type' : 'Create Bin Type',
			disabled: true,
		}
	]">
		<template v-slot:prepend></template>
	</v-breadcrumbs>
    <VCard class="mb-5">
		<VForm @submit.prevent="binType && binType.id ? form.post(route('bin-types.update', binType.id)) : form.post(route('bin-types.store'))">
			<VCardItem>
	            <VCardTitle>Bin Type Detail</VCardTitle>
	            <small>Enter Bin Type Detail</small>
	        </VCardItem>

	    	<VCardText>
	            <VRow>
	                <VCol cols="6">
	                    <div class="flex items-center flex-none order-0 grow-0 mb-3">
	                        <label class="v-label mb-1 text-body-2 text-wrap" for="v-67" style="line-height: 15px;">Bin Type Name <span class="ml-1" style="color: red">*</span></label>
	                        <AppTextField v-model="form.name" placeholder="Name" />
							<div v-if="form.errors.name" class="invalid-feedback text-error">{{ form.errors.name }}</div>
	                    </div>

						<div class="flex flex-col items-start">
							<label class="v-label mb-1 text-body-2 text-wrap" style="line-height: 15px;">
								Image <span class="ml-1" style="color: red">*</span>
							</label>
							<VFileInput
								label="Choose Image"
								@change="handleImagePreview"
								accept="image/*"
							/>
							<div v-if="form.errors.image" class="invalid-feedback text-error">{{ form.errors.image }}</div>

							<div v-if="imagePreview" class="mt-3 position-relative d-inline-block">
								<img
									:src="imagePreview"
									alt="Image Preview"
									class="rounded border"
									style="max-width: 100%; max-height: 200px;"
								/>
								<VBtn
									class="position-absolute"
									type="button"
									color="error"
									style="top: 5px; right: 5px;"
									@click="deleteImage"
									size="x-small"
								>
									X
								</VBtn>
						    </div>
						</div>
	                </VCol>
					<VCol cols="6">
						<div class="flex items-center flex-none order-0 grow-0 mb-3">
							<label class="v-label mb-1 text-body-2 text-wrap" for="v-67" style="line-height: 15px;">Accepted Recyclables <span class="ml-1" style="color: red">*</span></label>
							<v-combobox v-model="form.bin_type_waste" clearable chips multiple :items="wasteTypeOptions"></v-combobox>
							<div v-if="form.errors.bin_type_waste" class="invalid-feedback text-error">{{ form.errors.bin_type_waste }}</div>
						</div>
	                    <div class="flex items-center flex-none order-0 grow-0 mb-3">
	                        <label class="v-label mb-1 text-body-2 text-wrap" for="v-67" style="line-height: 15px;">QR Code Type <span class="ml-1" style="color: red">*</span></label>
							<AppSelect v-model="form.fixed_qrcode" :items="[
								{ value: 1, title: 'Fixed QR Code' },
								{ value: 0, title: 'Unique QR Code' },
							]"/>
							<div v-if="form.errors.fixed_qrcode" class="invalid-feedback text-error">{{ form.errors.fixed_qrcode }}</div>
	                    </div>
						<div class="flex items-center flex-none order-0 grow-0 mb-3">
	                        <label class="v-label mb-1 text-body-2 text-wrap" for="v-67" style="line-height: 15px;">CO2 Points <span class="ml-1" style="color: red">*</span></label>
	                        <AppTextField v-model="form.point" placeholder="100" />
							<div v-if="form.errors.point" class="invalid-feedback text-error">{{ form.errors.point }}</div>
	                    </div>
	                </VCol>

	            </VRow>

				<VBtn class="mt-5" type="submit" :disabled="form.processing" color="primary">
					<v-icon end icon="tabler-check" class="me-2" />
					{{ binType ? "Update" : "Create" }}
				</VBtn>

	        </VCardText>
		</VForm>
    </VCard>

	<VCard v-if="binType && $filters.can('delete-bin-type')">
		<VCardItem>
			<VCardTitle class="my-2">Delete Bin Type</VCardTitle>
			<VAlert type="warning" variant="tonal" density="default">
				<p>Are you sure to delete this bin type?</p>
				<span>Once you delete this bin type, all the related bin will be inactive. Please be certain.</span>
			</VAlert>
			<VCheckbox v-model="confirmDelete" label="I confirm to delete this bin."/>
			<VBtn class="mt-5" type="submit" :disabled="!confirmDelete" color="error" @click="deleteBinType(binType.id)">
				Delete Bin Type
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
    ref,
    watch
} from "vue";
import Layout from "../../layouts/blank.vue";
import {
    toast
} from "vue3-toastify";
import { Inertia } from '@inertiajs/inertia';
import Swal from "sweetalert2";

const confirmDelete = ref(false)

const props = defineProps({
    binType: Object,
	waste_types: Object,
	unique_id: String
});

const wasteTypeOptions = computed(() => props.waste_types.map(type => type.name))

const form = useForm({
    name: null,
    image: null,
	icon: null,
	fixed_qrcode: null,
	point: null,
	bin_type_waste: [],
	_method: null
});

const imagePreview = ref(null);

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
    if (props.binType) {
        form.name = props.binType.name;
		form.fixed_qrcode = props.binType.fixed_qrcode
		form.point = props.binType.point
		form.bin_type_waste = props.binType.waste_types.map(waste_type => waste_type.name)
		form._method = 'put';
		form.image = props.binType.image
		if (props.binType.image) {
			imagePreview.value = `/storage/images/bin-types/${props.binType.image}`;
		}
    }
});

// function handleImagePreview(event) {
// 	const file = event.target?.files?.[0];
// 	if (file) {
// 		imagePreview.value = URL.createObjectURL(file);
// 		form.image = file;
// 	} else {
// 		imagePreview.value = null;
// 		form.image = null;
// 	}
// }

const compressedIcon = ref(null);
async function handleImagePreview(event) {
	const file = event.target?.files?.[0];

	if (!file) {
		imagePreview.value = null;
		form.image = null;
		form.icon = null;
		return;
	}

	// Show original preview
	imagePreview.value = URL.createObjectURL(file);
	form.image = file;

	try {
		// Compress and convert to WebP
		const webpIcon = await compressAndConvertToWebP(file, 0.7, 200);
		form.icon = webpIcon;
		compressedIcon.value = URL.createObjectURL(webpIcon);
	} catch (error) {
		console.error('Error converting to WebP:', error);
		toast.error('Failed to compress and convert image to WebP');
	}
}

async function compressAndConvertToWebP(file, quality = 0.7, maxSize = 200) {
	return new Promise((resolve, reject) => {
		const reader = new FileReader();

		reader.onload = () => {
			const img = new Image();
			img.onload = () => {
				const canvas = document.createElement('canvas');
				const ctx = canvas.getContext('2d');

				// Resize logic
				let width = img.width;
				let height = img.height;
				if (width > maxSize || height > maxSize) {
					if (width > height) {
						height *= maxSize / width;
						width = maxSize;
					} else {
						width *= maxSize / height;
						height = maxSize;
					}
				}

				canvas.width = width;
				canvas.height = height;
				ctx.drawImage(img, 0, 0, width, height);

				// Convert to WebP
				canvas.toBlob(
					(blob) => {
						if (blob) {
							const webpFile = new File(
								[blob],
								file.name.replace(/\.\w+$/, '.webp'),
								{ type: 'image/webp' }
							);
							resolve(webpFile);
						} else {
							reject(new Error('Canvas toBlob failed'));
						}
					},
					'image/webp',
					quality
				);
			};
			img.onerror = reject;
			img.src = reader.result;
		};

		reader.onerror = reject;
		reader.readAsDataURL(file);
	});
}



const deleteBinType = async (id) => {
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
            const response = await axios.delete(`/bin-types/${id}`);
            if (response.data.success) {
                toast.success(response.data.message, {
                    theme: "colored",
                    type: "success",
                });
                router.visit(route('bin-types.index'));
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

function deleteImage() {
	imagePreview.value = null;
	form.image = null;

	if (props.binType?.image) {
		form.remove_existing_image = true;
	}
}


</script>
