<script setup>
import {
	router
} from "@inertiajs/vue3";
const props = defineProps({
    rewardData: {
        type: Object,
        required: true,
    },
})

const editReward = async () => {
    router.visit(route('rewards.edit', props.rewardData.id));
};

</script>

<template>
	<VRow>
		<VCol cols="12">
			<VCard>
				<VCardText class="pt-5 d-flex align-center flex-wrap justify-center">
					<div style="width: 150px; max-width: 100%;">
						<img
							:src="`/storage/images/reward/${props.rewardData.image}`"
							alt=""
							style="height: auto; max-height:300px; max-width: 100%;"
							class="cursor-pointer"
							@click="showImage(`/storage/images/reward/${props.rewardData.image}`)"
						/>

						<h5 class="text-h5 mt-4 d-flex justify-center">
							{{ props.rewardData.code }}
						</h5>
					</div>
				</VCardText>
				<VDivider />

				<VCardText>
	                <p class="text-sm text-disabled">
	                    DETAILS
	                </p>

	                <VList class="card-list mt-2">
	                    <VListItem>
	                        <div class="text-h6">
	                            Status :
	                            <div class="d-inline-block text-body-1 ml-2">
									<VChip :color="$filters.resolveStatusVariant(rewardData.status)" size="small" label class="text-capitalize">
				                        {{ rewardData.status_text }}
				                    </VChip>
	                            </div>
	                        </div>
	                    </VListItem>
	                </VList>
	            </VCardText>
				<VCardText v-if="$filters.can('update-reward')">
					<VBtn @click="editReward">
						Edit
						<VIcon end icon="tabler-edit" />
					</VBtn>
				</VCardText>

			</VCard>
		</VCol>
	</VRow>

</template>

<style lang="scss" scoped>
.card-list {
    --v-card-list-gap: 0.5rem;
}

.text-capitalize {
    text-transform: capitalize !important;
}
</style>
