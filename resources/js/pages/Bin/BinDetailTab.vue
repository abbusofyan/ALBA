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
    binData: {
        type: Object,
        required: true,
    },
})
</script>

<template>
<VRow>
    <VCol cols="12">
        <VCard title="Bin Information :">
            <VCardText>
                <VRow>
					<VCol cols="6">
						<div class="w-[67px] h-[20px] font-[400] text-[13px] leading-[20px] flex items-center flex-none order-0 grow-0 mb-5">
							Created Date :
							<div class="text-h6">
								{{$filters.formatDate(binData.created_at)}}
							</div>
						</div>
						<div class="w-[67px] h-[20px] font-[400] text-[13px] leading-[20px] flex items-center flex-none order-0 grow-0 mb-5">
							ID :
							<div class="text-h6">
								{{binData.code}}
							</div>
						</div>
						<div class="w-[67px] h-[20px] font-[400] text-[13px] leading-[20px] flex items-center flex-none order-0 grow-0 mb-5">
							Organization :
							<div class="text-h6">
								{{binData.organization?.name}}
							</div>
						</div>
						<div class="w-[67px] h-[20px] font-[400] text-[13px] leading-[20px] flex items-center flex-none order-0 grow-0 mb-5">
							Type of Bin :
							<div class="text-h6">
								{{binData.type.name}}
							</div>
						</div>
						<div class="w-[67px] h-[20px] font-[400] text-[13px] leading-[20px] flex items-center flex-none order-0 grow-0 mb-5">
							Address :
							<div class="text-h6">
								{{binData.address}}
							</div>
						</div>

					</VCol>
					<VCol cols="6">
						<div class="w-[67px] h-[20px] font-[400] text-[13px] leading-[20px] flex items-center flex-none order-0 grow-0 mb-5">
							Accepted Recyclables :
							<div class="text-h6">
								<li v-for="type in binData.type.waste_types">
									{{type.name}}
								</li>
							</div>
						</div>
						<div class="w-[67px] h-[20px] font-[400] text-[13px] leading-[20px] flex items-center flex-none order-0 grow-0 mb-5">
							Remark :
							<div class="text-h6">
								{{binData.remark}}
							</div>
						</div>
					</VCol>

                </VRow>
            </VCardText>
        </VCard>
    </VCol>
</VRow>
</template>
