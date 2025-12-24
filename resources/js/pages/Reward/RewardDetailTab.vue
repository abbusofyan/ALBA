<script setup>
import {
    useForm
} from '@inertiajs/vue3';
import {
    toast
} from "vue3-toastify";

const props = defineProps({
    rewardData: {
        type: Object,
        required: true,
    },
})
</script>

<template>
<VRow>
    <VCol cols="12">
        <VCard title="Reward Information :">
            <VCardText>
                <VRow>
					<VCol cols="4">
						<div class="w-[67px] h-[20px] font-[400] text-[13px] leading-[20px] flex items-center flex-none order-0 grow-0 mb-5">
							Date Created :
							<div class="text-h6">
								{{$filters.formatDateTime(rewardData.created_at)}}
							</div>
						</div>
						<div class="w-[67px] h-[20px] font-[400] text-[13px] leading-[20px] flex items-center flex-none order-0 grow-0 mb-5">
							ID :
							<div class="text-h6">
								{{rewardData.code}}
							</div>
						</div>
						<div class="w-[67px] h-[20px] font-[400] text-[13px] leading-[20px] flex items-center flex-none order-0 grow-0 mb-5">
							Reward Name :
							<div class="text-h6">
								{{rewardData.name}}
							</div>
						</div>
						<div class="w-[67px] h-[20px] font-[400] text-[13px] leading-[20px] flex items-center flex-none order-0 grow-0 mb-5">
							CO2 Point Required :
							<div class="text-h6">
								{{rewardData.price}}
							</div>
						</div>
						<div class="w-[67px] h-[20px] font-[400] text-[13px] leading-[20px] flex items-center flex-none order-0 grow-0 mb-5">
							Reward Label :
							<div class="text-h6">
								{{rewardData.label}}
							</div>
						</div>
					</VCol>

					<VCol cols="6">
						<div class="w-[67px] h-[20px] font-[400] text-[13px] leading-[20px] flex items-center flex-none order-0 grow-0 mb-5">
							Event :
							<div class="text-h6" v-if="rewardData.event_id">
								#{{rewardData.event?.code}}
							</div>
						</div>
						<div class="w-[67px] h-[20px] font-[400] text-[13px] leading-[20px] flex items-center flex-none order-0 grow-0 mb-5">
							Schedule :
							<div class="text-h6" v-if="rewardData.start_date">
								{{ $filters.formatDate(rewardData.start_date) + ' - ' + $filters.formatDate(rewardData.end_date) }}<br>
								{{ $filters.formatTime(rewardData.start_time) }} - {{$filters.formatTime(rewardData.end_time)}}
							</div>
						</div>
						<div class="w-[67px] h-[20px] font-[400] text-[13px] leading-[20px] flex items-center flex-none order-0 grow-0 mb-5">
							Description :
							<div class="text-h6">
								{{rewardData.description}}
							</div>
						</div>
						<div class="w-[67px] h-[20px] font-[400] text-[13px] leading-[20px] flex items-center flex-none order-0 grow-0 mb-5">
							Terms and Conditions :
							<div class="text-h6">
								{{rewardData.tnc}}
							</div>
						</div>
					</VCol>
                </VRow>
            </VCardText>
        </VCard>
    </VCol>
</VRow>
</template>
