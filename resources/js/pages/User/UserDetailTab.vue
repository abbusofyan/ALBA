<script setup>
import {
    useForm
} from '@inertiajs/vue3';
import {
    toast
} from "vue3-toastify";
const isNewPasswordVisible = ref(false)
const isConfirmPasswordVisible = ref(false)
const isTwoFactorDialogOpen = ref(false);

const props = defineProps({
    userData: {
        type: Object,
        required: true,
    },
})
</script>

<template>
<VRow>
    <VCol cols="12">
        <VCard title="Personal Details :">
            <VCardText>
                <VRow>
					<VCol cols="4">
						<div class="w-[67px] h-[20px] font-[400] text-[13px] leading-[20px] flex items-center flex-none order-0 grow-0 mb-5">
							First Name :
							<div class="text-h6">
								{{userData.first_name}}
							</div>
						</div>
						<div class="w-[67px] h-[20px] font-[400] text-[13px] leading-[20px] flex items-center flex-none order-0 grow-0 mb-5">
							Last Name :
							<div class="text-h6">
								{{userData.last_name}}
							</div>
						</div>
						<div class="w-[67px] h-[20px] font-[400] text-[13px] leading-[20px] flex items-center flex-none order-0 grow-0 mb-5">
							Address :
							<div class="text-h6">
								{{userData.address}}
							</div>
						</div>
						<div class="w-[67px] h-[20px] font-[400] text-[13px] leading-[20px] flex items-center flex-none order-0 grow-0 mb-5">
							Postal Code :
							<div class="text-h6">
								{{userData.postal_code}}
							</div>
						</div>
						<div class="w-[67px] h-[20px] font-[400] text-[13px] leading-[20px] flex items-center flex-none order-0 grow-0 mb-5">
							Date of Birth :
							<div class="text-h6">
								{{$filters.formatDate(userData.dob)}}
							</div>
						</div>

					</VCol>

					<VCol cols="6">
						<div class="w-[67px] h-[20px] font-[400] text-[13px] leading-[20px] flex items-center flex-none order-0 grow-0 mb-5">
							Contact :
							<div class="text-h6">
								{{userData.phone}}
							</div>
						</div>
						<div class="w-[67px] h-[20px] font-[400] text-[13px] leading-[20px] flex items-center flex-none order-0 grow-0 mb-5">
							Email :
							<div class="text-h6">
								{{userData.email}}
							</div>
						</div>
						<div class="w-[67px] h-[20px] font-[400] text-[13px] leading-[20px] flex items-center flex-none order-0 grow-0 mb-5">
							Status :
							<div class="text-h6">
								{{userData.status_text}}
							</div>
						</div>
					</VCol>
                </VRow>
            </VCardText>
        </VCard>
    </VCol>
</VRow>
</template>
