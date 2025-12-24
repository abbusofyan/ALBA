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
    binTypeData: {
        type: Object,
        required: true,
    },
})
</script>

<template>
<VRow>
    <VCol cols="12">
        <VCard title="Bin Type Accepted Recyclables :">
            <VCardText>
                <VRow>
					<VCol cols="4">
						<div class="w-[67px] h-[20px] font-[400] text-[13px] leading-[20px] flex items-center flex-none order-0 grow-0 mb-5">
							<div class="text-h6">
								<li v-for="type in binTypeData.waste_types">
									{{type.name}}
								</li>
							</div>
						</div>
					</VCol>
                </VRow>
            </VCardText>
        </VCard>
    </VCol>
</VRow>
</template>
