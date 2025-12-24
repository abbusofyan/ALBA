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
    recyclingData: {
        type: Object,
        required: true,
    },
})
</script>

<template>
<VRow>
    <VCol cols="12">
        <VCard title="Recycling Information :">
            <VCardText>
                <VRow>
					<VCol cols="6">
						<div class="w-[67px] h-[20px] font-[400] text-[13px] leading-[20px] flex items-center flex-none order-0 grow-0 mb-5">
							Date/Time :
							<div class="text-h6">
								{{$filters.formatDate(recyclingData.created_at)}}
							</div>
						</div>

						<div class="w-[67px] h-[20px] font-[400] text-[13px] leading-[20px] flex items-center flex-none order-0 grow-0 mb-5">
							Type of Bin :
							<div class="text-h6">
								{{ recyclingData.bin_type.name }}
							</div>
						</div>
						<div class="w-[67px] h-[20px] font-[400] text-[13px] leading-[20px] flex items-center flex-none order-0 grow-0 mb-5">
							Address :
							<div class="text-h6">
								{{ recyclingData.bin?.address || '-' }}
							</div>
						</div>

						<div class="w-[67px] h-[20px] font-[400] text-[13px] leading-[20px] flex items-center flex-none order-0 grow-0 mb-5">
							Reward :
							<div class="text-h6">
								{{recyclingData.reward}} Pts
							</div>
						</div>

						<div class="w-[67px] h-[20px] font-[400] text-[13px] leading-[20px] flex items-center flex-none order-0 grow-0 mb-5">
							User :
							<div class="text-h6">
								{{recyclingData.user.name}}<br>
								{{recyclingData.user.email}}
							</div>
						</div>

					</VCol>
                </VRow>
            </VCardText>
        </VCard>
    </VCol>
</VRow>
</template>
