<template>
<Head title="Create Menu" />
<Layout>
	<v-breadcrumbs
	  class="pt-0"
	  :items="staff
	    ? [
	        { title: 'Staff', disabled: false, href: '/staffs' },
	        { title: staff.name, disabled: false, href: `/staffs/${staff.id}` },
	        { title: 'Edit', disabled: true }
	      ]
	    : [
	        { title: 'Staff', disabled: false, href: '/staffs' },
	        { title: 'Create Staff', disabled: true }
	      ]"
	>
	  <template v-slot:prepend></template>
	</v-breadcrumbs>
    <VCard class="mb-5">
		<VForm @submit.prevent=" staff && staff.id ? form.put(route('staffs.update', staff.id)) : form.post(route('staffs.store'))">
	        <VCardItem>
	            <VCardTitle>{{ staff ? "Edit" : "Create" }} Staff</VCardTitle>
	        </VCardItem>

	    	<VCardText>
	            <VRow>
	                <VCol cols="6">
	                    <div class="flex items-center flex-none order-0 grow-0">
	                        <label class="v-label mb-1 text-body-2 text-wrap" for="v-67" style="line-height: 15px;">Name <span class="ml-1" style="color: red">*</span></label>
	                        <AppTextField v-model="form.name" placeholder="Name" />
							<div v-if="form.errors.name" class="invalid-feedback text-error">{{ form.errors.name }}</div>
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
	                        <label class="v-label mb-1 text-body-2 text-wrap" for="v-67" style="line-height: 15px;">Password <span class="ml-1" style="color: red">{{staff ? '' : '*'}}</span></label>
	                        <AppTextField v-model="form.password" placeholder="Password" type="password" />
							<div v-if="form.errors.password" class="invalid-feedback text-error">{{ form.errors.password }}</div>
					    </div>
	                </VCol>
					<VCol cols="6">
	                    <div class="flex items-center flex-none order-0 grow-0">
	                        <label class="v-label mb-1 text-body-2 text-wrap" for="v-67" style="line-height: 15px;">Confirm Password <span class="ml-1" style="color: red">{{staff ? '' : '*'}}</span></label>
	                        <AppTextField v-model="form.password_confirmation" placeholder="Password" type="password" />
							<div v-if="form.errors.password_confirmation" class="invalid-feedback text-error">{{ form.errors.password_confirmation }}</div>
					    </div>
	                </VCol>

	            </VRow>
	        </VCardText>

	        <VCardItem>
	            <VCardTitle>Access Level</VCardTitle>
	            <small>Please define the access level for this staff member</small>
	        </VCardItem>
			<VCardText>
				<VTable class="text-no-wrap border-collapse border border-gray-400">
					<thead>
						<tr>
							<th>Module</th>
							<th>Permission</th>
						</tr>
					</thead>

					<tbody>

						<tr v-for="(permissions, module) in modules_with_permissions" :key="module">
							<td>{{ module }}</td>
							<td>
								<VRow>
									<VCol cols="3" v-for="(permission, permissionIndex) in permissions" :key="permissionIndex">
										<VCheckbox v-model="form.permissions[module][permissionIndex].value" :label="form.permissions[module][permissionIndex].name"/>
									</VCol>
								</VRow>
							</td>
						</tr>
					</tbody>
				</VTable>

				<VSwitch v-model="form.status" :label="setStatusLabel(form.status)" />

				<VBtn class="mt-5" type="submit" :disabled="form.processing" color="primary">
					{{ staff ? "Update" : "Create" }}
				</VBtn>
	        </VCardText>
		</VForm>
    </VCard>

	<VCard v-if="staff && $filters.can('delete-staff')">
		<VCardItem>
			<VCardTitle class="my-2">Delete Account</VCardTitle>
			<VAlert type="warning" variant="tonal" density="default">
				<p>Are you sure to delete this account?</p>
				<span>Once you delete this account, there is no going back. Please be certain.</span>
			</VAlert>
			<VCheckbox v-model="confirmDelete" label="I confirm to delete this account."/>
			<VBtn class="mt-5" type="submit" :disabled="!confirmDelete" color="error" @click="deleteStaff(staff.id)">
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

const props = defineProps({
    staff: Object,
    modules_with_permissions: Object,
});

const form = useForm({
    name: null,
    password: null,
	password_confirmation: null,
    email: null,
    status: true,
	permissions: {}
});

const confirmDelete = ref(false)

Object.entries(props.modules_with_permissions).forEach(([module, permissions]) => {
	form.permissions[module] = {};
	permissions.forEach((permission, i) => {
		form.permissions[module][i] = {
			id: permission.id,
			name: permission.name,
			value: false
		}
	});
});

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
    if (props.staff) {
        form.name = props.staff.name;
		form.email = props.staff.email;
		form.phone = props.staff.phone;
		form.dob = props.staff.dob;
		form.status = props.staff.status
		form.address = props.staff.address;

		const grantedIds = new Set(props.staff.permissions.map(p => p.id));

		for (const module in form.permissions) {
			for (const key in form.permissions[module]) {
				const permission = form.permissions[module][key];
				if (grantedIds.has(permission.id)) {
					permission.value = true;
				}
			}
		}
    }

});

const toggleSwitch = ref(true)
const toggleFalseSwitch = ref(false)

const setStatusLabel = (status) => {
    return status ? 'Active' : 'Inactive'
}

const deleteStaff = async (id) => {
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
            const response = await axios.delete(`/staffs/${id}`);
            if (response.data.success) {
                toast.success(response.data.message, {
                    theme: "colored",
                    type: "success",
                });
				router.visit(route('staffs.index'));
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

</script>
