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
        <VCard title="Account Detail :">
            <VCardText>
                <VRow>
					<VCol cols="4">
						<div class="w-[67px] h-[20px] font-[400] text-[13px] leading-[20px] flex items-center flex-none order-0 grow-0 mb-5">
							Name :
							<div class="text-h6">
								{{userData.name}}
							</div>
						</div>
						<div class="w-[67px] h-[20px] font-[400] text-[13px] leading-[20px] flex items-center flex-none order-0 grow-0 mb-5">
							Status :
							<div class="text-h6">
								{{userData.status_text}}
							</div>
						</div>
					</VCol>

					<VCol cols="6">
						<div class="w-[67px] h-[20px] font-[400] text-[13px] leading-[20px] flex items-center flex-none order-0 grow-0 mb-5">
							Email :
							<div class="text-h6">
								{{userData.email}}
							</div>
						</div>
					</VCol>
                </VRow>
            </VCardText>
        </VCard>
    </VCol>
</VRow>
</template>
