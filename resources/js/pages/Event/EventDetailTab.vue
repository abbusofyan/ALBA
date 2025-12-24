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
    eventData: {
        type: Object,
        required: true,
    },
})

const resolveSchoolRoleVariant = () => {
    return;
}
</script>

<template>
	<VCard title="Event Information :">
		<VCardText>
			<VRow v-if="eventData.event_type_id == 3 || eventData.event_type_id == 4">
				<VCol cols="5">
					<div class="w-[67px] h-[20px] font-[400] text-[13px] leading-[20px] flex items-center flex-none order-0 grow-0 mb-5">
						Date Created :
						<div class="text-h6">
							{{eventData.created_at}}
						</div>
					</div>
					<div class="w-[67px] h-[20px] font-[400] text-[13px] leading-[20px] flex items-center flex-none order-0 grow-0 mb-5">
						ID :
						<div class="text-h6">
							{{eventData.code}}
						</div>
					</div>
					<div class="w-[67px] h-[20px] font-[400] text-[13px] leading-[20px] flex items-center flex-none order-0 grow-0 mb-5">
						Address :
						<div class="text-h6">
							{{eventData.address ?? 'Entire Singapore'}}
						</div>
					</div>
					<div class="w-[67px] h-[20px] font-[400] text-[13px] leading-[20px] flex items-center flex-none order-0 grow-0 mb-5">
						Date :
						<div class="text-h6">
							{{$filters.formatDate(eventData.date_start)}} - {{$filters.formatDate(eventData.date_end)}}
						</div>
					</div>
					<div class="w-[67px] h-[20px] font-[400] text-[13px] leading-[20px] flex items-center flex-none order-0 grow-0 mb-5">
						Time :
						<div class="text-h6">
							{{eventData.time_start_formatted}} - {{eventData.time_end_formatted}}
						</div>
					</div>
					<div class="w-[67px] h-[20px] font-[400] text-[13px] leading-[20px] flex items-center flex-none order-0 grow-0 mb-5">
						Recurrence Settings :
						<div class="text-h6">
							<!-- {{eventData.name}} -->
						</div>
					</div>
				</VCol>

				<VCol cols="6">
					<div v-if="eventData.event_type_id == 3" class="w-[67px] h-[20px] font-[400] text-[13px] leading-[20px] flex items-center flex-none order-0 grow-0 mb-5">
						Related School/Enterprise :
						<div class="d-flex align-center gap-x-4">
							<VAvatar size="34" :variant="!eventData.avatar ? 'tonal' : undefined" :color="!eventData.avatar ? resolveSchoolRoleVariant(eventData.roles)?.color : undefined">
								<VImg v-if="eventData.avatar" :src="eventData.avatar" />
								<span v-else>{{ avatarText(eventData.fullName) }}</span>
							</VAvatar>
							<div class="d-flex flex-column">
								<h6 class="text-base">
									<a :href="`#`" class="font-weight-medium text-link">
										{{ eventData.user?.name }}
									</a>
								</h6>
								<div class="text-sm">
									{{ eventData.user?.email }}
								</div>
							</div>
						</div>
					</div>
					<div class="w-[67px] h-[20px] font-[400] text-[13px] leading-[20px] flex items-center flex-none order-0 grow-0 mb-5">
						Description :
						<div class="text-h6">
							{{eventData.description}}
						</div>
					</div>
					<div v-if="eventData.event_type_id == 3" class="w-[67px] h-[20px] font-[400] text-[13px] leading-[20px] flex items-center flex-none order-0 grow-0 mb-5">
						Code :
						<div class="text-h6">
							{{eventData.secret_code}}
						</div>
					</div>
				</VCol>
			</VRow>

			<VRow v-else>
				<VCol cols="5">
					<div class="w-[67px] h-[20px] font-[400] text-[13px] leading-[20px] flex items-center flex-none order-0 grow-0 mb-5">
						Date Created :
						<div class="text-h6">
							{{$filters.formatDate(eventData.created_at)}}
						</div>
					</div>
					<div class="w-[67px] h-[20px] font-[400] text-[13px] leading-[20px] flex items-center flex-none order-0 grow-0 mb-5">
						ID :
						<div class="text-h6">
							{{eventData.code}}
						</div>
					</div>
					<div class="w-[67px] h-[20px] font-[400] text-[13px] leading-[20px] flex items-center flex-none order-0 grow-0 mb-5">
						District :
						<div class="text-h6">
							{{eventData.district?.name}}
						</div>
					</div>
					<div class="w-[67px] h-[20px] font-[400] text-[13px] leading-[20px] flex items-center flex-none order-0 grow-0 mb-5">
						Address :
						<div class="text-h6">
							{{eventData.address}}
						</div>
					</div>
				</VCol>

				<VCol cols="6">
					<div class="w-[67px] h-[20px] font-[400] text-[13px] leading-[20px] flex items-center flex-none order-0 grow-0 mb-5">
						Date :
						<div class="text-h6">
							{{$filters.formatDate(eventData.date_start)}} - {{$filters.formatDate(eventData.date_end)}}
						</div>
					</div>
					<div class="w-[67px] h-[20px] font-[400] text-[13px] leading-[20px] flex items-center flex-none order-0 grow-0 mb-5">
						Time :
						<div class="text-h6">
							{{eventData.time_start_formatted}} - {{eventData.time_end_formatted}}
						</div>
					</div>
					<div class="w-[67px] h-[20px] font-[400] text-[13px] leading-[20px] flex items-center flex-none order-0 grow-0 mb-5">
						Recurrence Settings :
						<div class="text-h6">
							<!-- {{eventData.name}} -->
						</div>
					</div>
				</VCol>
			</VRow>
		</VCardText>

		<VDivider />

		<VCardText>
			<VRow>
				<VCol cols="6">
					<VCard>
						<div class="stat-item">
							<div class="stat-icon stat-icon-info">
								<VIcon :size="24" icon="tabler-stairs" />
							</div>
							<div class="stat-content">
								<h5 class="text-h5">
									Total Activity
								</h5>
								{{eventData.total_recycling.toLocaleString()}}
							</div>
						</div>
					</VCard>
				</VCol>

				<VCol cols="6">
					<VCard>
						<div class="stat-item">
							<div class="stat-icon stat-icon-danger">
								<VIcon :size="24" icon="tabler-history" color="error" />
							</div>
							<div class="stat-content">
								<h5 class="text-h5">
									Total CO2 Saved
								</h5>
								{{eventData.total_reward.toLocaleString()}}
							</div>
						</div>
					</VCard>
				</VCol>
			</VRow>

			<div v-if="eventData.event_type_id == 3 || eventData.event_type_id == 4">
				<div class="v-card-title my-5 pl-0"> {{eventData.bin? 'Selected Bins : ' : 'Selected Bin Types : '}} </div>

				<VTable class="text-no-wrap">
					<thead>
						<tr>
							<th>{{eventData.bin ? 'Bin Name' : 'Bin Type Name'}}</th>
							<th>POINT</th>
						</tr>
					</thead>
					<tbody>
						<tr v-for="(eventBins, i) in eventData.event_bins" :key="i">
							<td>
								<div class="d-flex align-center gap-x-4">
									<v-avatar size="34" color="#EDF2F8">
										<v-icon icon="tabler-recycle" color="primary" />
									</v-avatar>
									<div v-if="eventBins.bin" class="d-flex flex-column">{{ eventBins.bin.type.name }} | {{eventBins.bin.code}}</div>
									<div v-else class="d-flex flex-column">{{ eventBins.bin_type.name }}</div>
								</div>
							</td>
							<td>
								{{eventBins.point}}
							</td>
						</tr>
					</tbody>
				</VTable>
			</div>

			<div v-else>
				<div class="v-card-title my-5 pl-0">Accepted Recyclables :</div>

				<VTable class="text-no-wrap">
					<thead>
						<tr>
							<th>RECYCLABLES</th>
							<th v-if="eventData.event_type_id == 2">PRICE</th>
						</tr>
					</thead>
					<tbody>
						<tr v-for="(event_waste_type, i) in eventData.event_waste_type" :key="i">
							<td>
								<div class="d-flex align-center gap-x-4">
									<v-avatar size="34" color="#EDF2F8">
										<v-icon icon="tabler-recycle" color="primary" />
									</v-avatar>
									<div class="d-flex flex-column">{{ event_waste_type.waste_type.name }}</div>
								</div>
							</td>
							<td v-if="eventData.event_type_id == 2">
								${{event_waste_type.price}}
							</td>
						</tr>
					</tbody>
				</VTable>
			</div>
		</VCardText>
	</VCard>
</template>

<style scoped>
.stat-item {
	display: flex;
	align-items: center;
	gap: 12px;
	padding: 16px;
	border-radius: 8px;
	transition: all 0.2s ease;
}

.stat-item:hover {
	background: #f3f4f6;
	transform: translateY(-1px);
	box-shadow: 0 4px 12px rgba(0, 0, 0, 0.1);
}

.stat-icon {
	width: 40px;
	height: 40px;
	border-radius: 8px;
	display: flex;
	align-items: center;
	justify-content: center;
	color: white;
	flex-shrink: 0;
}

.stat-icon-info {
	background-color: #06b6d4;
}

.stat-icon-danger {
	background-color: #FDF1F1;
}

.stat-content {
	flex: 1;
}

.stat-value {
	font-size: 24px;
	font-weight: 700;
	color: #1f2937;
	line-height: 1;
}

.stat-label {
	font-size: 14px;
	color: #6b7280;
	margin-top: 2px;
}
</style>
