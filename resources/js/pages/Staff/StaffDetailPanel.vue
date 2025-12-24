<script setup>
import {
    router
} from "@inertiajs/vue3";
const props = defineProps({
    userData: {
        type: Object,
        required: true,
    },
})

const isUserInfoEditDialogVisible = ref(false)
const isUpgradePlanDialogVisible = ref(false)

const editlUser = async () => {
    router.visit(route('staffs.edit', props.userData.id));
};

</script>

<template>
	<VRow>
		<VCol cols="12">
			<VCard>
				<VCardText class="pt-5 d-flex align-center flex-wrap justify-center">
					<div>
						<VAvatar rounded :size="100" :color="`primary`" :variant="'tonal'">
							<span class="text-5xl font-weight-medium">
								{{ avatarText(props.userData.name) }}
							</span>
						</VAvatar>

						<h5 class="text-h5 mt-4 d-flex justify-center">
							{{ props.userData.name }}
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
	                            Email :
	                            <div class="d-inline-block text-body-1">
	                                {{ props.userData.email }}
	                            </div>
	                        </div>
	                    </VListItem>
	                    <VListItem>
	                        <div class="text-h6">
	                            Status :
	                            <div class="d-inline-block text-body-1 ml-2">
									<VChip :color="$filters.resolveStatusVariant(userData.status_text)" size="small" label class="text-capitalize">
				                        {{ userData.status_text }}
				                    </VChip>
	                            </div>
	                        </div>
	                    </VListItem>
	                </VList>
	            </VCardText>
				<VCardText v-if="$filters.can('update-staff')">
					<VBtn @click="editlUser">
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
