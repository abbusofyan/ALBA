<template>
<Head title="Create Menu" />
<Layout>
	<v-breadcrumbs
	  class="pt-0"
	  :items="user
	    ? [
	        { title: 'User', disabled: false, href: '/users' },
	        { title: user.name, disabled: false, href: `/users/${user.id}` },
	        { title: 'Edit', disabled: true }
	      ]
	    : [
	        { title: 'User', disabled: false, href: '/users' },
	        { title: 'Create User', disabled: true }
	      ]"
	>
	  <template v-slot:prepend></template>
	</v-breadcrumbs>
    <VCard class="mb-5">
		<VForm @keydown.enter.prevent @submit.prevent=" user && user.id ? form.put(route('users.update', user.id)) : form.post(route('users.store'))">
	        <VCardItem>
	            <VCardTitle>{{ user ? "Edit" : "Create" }} User</VCardTitle>
	        </VCardItem>

			<VCardItem>
	            <VCardTitle>Account Detail</VCardTitle>
	            <small>Enter Account Detail</small>
	        </VCardItem>

	    	<VCardText>
	            <VRow>
	                <VCol cols="6">
	                    <div class="flex items-center flex-none order-0 grow-0">
	                        <label class="v-label mb-1 text-body-2 text-wrap" for="v-67" style="line-height: 15px;">First Name <span class="ml-1" style="color: red">*</span></label>
	                        <AppTextField v-model="form.first_name" placeholder="John" />
							<div v-if="form.errors.first_name" class="invalid-feedback text-error">{{ form.errors.first_name }}</div>
	                    </div>
	                </VCol>
					<VCol cols="6">
	                    <div class="flex items-center flex-none order-0 grow-0">
	                        <label class="v-label mb-1 text-body-2 text-wrap" for="v-67" style="line-height: 15px;">Last Name<span class="ml-1" style="color: red">*</span></label>
	                        <AppTextField v-model="form.last_name" placeholder="Doe" />
							<div v-if="form.errors.last_name" class="invalid-feedback text-error">{{ form.errors.last_name }}</div>
	                    </div>
	                </VCol>
	                <VCol cols="6">
						<div class="flex items-center flex-none order-0 grow-0">
	                        <label class="v-label mb-1 text-body-2 text-wrap" for="v-67" style="line-height: 15px;">Email <span class="ml-1" style="color: red">*</span></label>
	                        <AppTextField v-model="form.email" label="" placeholder="test@mail.com" />
							<div v-if="form.errors.email" class="invalid-feedback text-error">{{ form.errors.email }}</div>
	                    </div>
	                </VCol>
					<VCol cols="6">
						<div class="flex items-center flex-none order-0 grow-0">
							<label class="v-label mb-1 text-body-2 text-wrap" for="v-67" style="line-height: 15px;">Contact <span class="ml-1" style="color: red">*</span></label>
							<AppTextField v-model="form.phone" placeholder="+61 8926772" />
							<div v-if="form.errors.phone" class="invalid-feedback text-error">{{ form.errors.phone }}</div>
						</div>
					</VCol>
	                <VCol cols="6">
	                    <div class="flex items-center flex-none order-0 grow-0">
	                        <label class="v-label mb-1 text-body-2 text-wrap" for="v-67" style="line-height: 15px;">Address <span class="ml-1" style="color: red">*</span></label>
	                        <AppTextField v-model="form.address" placeholder="Address" readonly />
							<div v-if="form.errors.address" class="invalid-feedback text-error">{{ form.errors.address }}</div>
	                    </div>
	                </VCol>
					<VCol cols="6">
	                    <div class="flex items-center flex-none order-0 grow-0">
	                        <label class="v-label mb-1 text-body-2 text-wrap" for="v-67" style="line-height: 15px;">Postal Code <span class="ml-1" style="color: red">*</span></label>
							<AppTextField v-model="form.postal_code" type="number" @input="onPostalCodeInput" />
							<div v-if="form.errors.postal_code" class="invalid-feedback text-error">{{ form.errors.postal_code }}</div>
	                    </div>
	                </VCol>
					<VCol cols="6">
	                    <div class="flex items-center flex-none order-0 grow-0">
	                        <label class="v-label mb-1 text-body-2 text-wrap" for="v-67" style="line-height: 15px;">Password</label>
	                        <AppTextField v-model="form.password" type="password" placeholder="Fill to change password" />
							<div v-if="form.errors.password" class="invalid-feedback text-error">{{ form.errors.password }}</div>
	                    </div>
	                </VCol>
					<VCol cols="6">
	                    <div class="flex items-center flex-none order-0 grow-0">
	                        <label class="v-label mb-1 text-body-2 text-wrap" for="v-67" style="line-height: 15px;">Confirm Password </label>
	                        <AppTextField v-model="form.password_confirmation" type="password" placeholder="Confirm new password" />
							<div v-if="form.errors.password_confirmation" class="invalid-feedback text-error">{{ form.errors.password_confirmation }}</div>
	                    </div>
	                </VCol>
					<VCol cols="6">
						<div class="flex items-center flex-none order-0 grow-0">
							<label class="v-label mb-1 text-body-2 text-wrap" for="v-67" style="line-height: 15px;">Display Name </label>
							<AppTextField v-model="form.display_name" type="text" placeholder="Display Name" />
							<div v-if="form.errors.display_name" class="invalid-feedback text-error">{{ form.errors.display_name }}</div>
						</div>
					</VCol>
					<VCol cols="6">
						<div class="flex items-center flex-none order-0 grow-0">
							<label class="v-label mb-1 text-body-2 text-wrap" for="v-67" style="line-height: 15px;">Point </label>
							<AppTextField v-model="form.point" type="number" placeholder="User point" />
							<div v-if="form.errors.point" class="invalid-feedback text-error">{{ form.errors.point }}</div>
						</div>
					</VCol>
	            </VRow>
	        </VCardText>


			<VCardText>

				<VSwitch v-model="form.status" :label="setStatusLabel(form.status)" />

				<VBtn class="mt-5" type="submit" :disabled="form.processing" color="primary">
					{{ user ? "Update" : "Create" }}
				</VBtn>
	        </VCardText>
		</VForm>
    </VCard>

	<VCard v-if="user">
		<VCardItem>
			<VCardTitle class="my-2">Delete Account</VCardTitle>
			<VAlert type="warning" variant="tonal" density="default">
				<p>Are you sure to delete this account?</p>
				<span>Once you delete this account, there is no going back. Please be certain.</span>
			</VAlert>
			<VCheckbox v-model="confirmDelete" label="I confirm to delete this account."/>
			<VBtn class="mt-5" type="submit" :disabled="!confirmDelete" color="error" @click="deleteUser(user.id)">
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
import { usePostalCode } from '@/composables/usePostalCode'

const props = defineProps({
    user: Object,
});

const form = useForm({
    first_name: null,
	last_name: null,
	email: null,
    password: null,
	password_confirmation: null,
    phone: null,
    dob: null,
    status: true,
    address: null,
	postal_code: null,
	point: null,
	display_name: null
});

const { onPostalCodeInput, selectedLocation } = usePostalCode(form)

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

watch(form, () => {
	if (!form.password) {
		form.password_confirmation = null
	}
})

onMounted(() => {
    if (props.user) {
        form.first_name = props.user.first_name;
		form.last_name = props.user.last_name;
		form.email = props.user.email;
		form.phone = props.user.phone;
		form.dob = props.user.dob;
		form.status = props.user.status
		form.address = props.user.address;
		form.postal_code = props.user.postal_code;
		form.point = props.user.point;
		form.display_name = props.user.display_name;
    }

});

const toggleSwitch = ref(true)
const toggleFalseSwitch = ref(false)

const setStatusLabel = (status) => {
    return status ? 'Active' : 'Inactive'
}

const deleteUser = async (id) => {
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
            const response = await axios.delete(`/users/${id}`);
            if (response.data.success) {
                toast.success(response.data.message, {
                    theme: "colored",
                    type: "success",
                });
				router.visit(route('users.index'));
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

</script>
