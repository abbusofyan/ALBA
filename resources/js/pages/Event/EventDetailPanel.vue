<script setup>
import {
	router
} from "@inertiajs/vue3";
const props = defineProps({
    eventData: {
        type: Object,
        required: true,
    },
})

const isUserInfoEditDialogVisible = ref(false)
const isUpgradePlanDialogVisible = ref(false)

const editEvent = async () => {
    router.visit(route('events.edit', props.eventData.id));
};

const dialog = ref(false)
const selectedImage = ref('')

const showImage = (src) => {
    selectedImage.value = src
    dialog.value = true
}

</script>

<template>
	<VRow>
		<VCol cols="12">
			<VCard>
				<VCardText class="pt-5 d-flex align-center flex-wrap justify-center">
					<div>
						<img
							:src="eventData.image_url"
							alt=""
							style="height: auto; max-height:300px; max-width: 100%;"
							class="cursor-pointer"
							@click="showImage(eventData.image_url)"
						/>

						<h5 class="text-h5 mt-4 d-flex justify-center">
							#{{ props.eventData.code }}
						</h5>
						<div class="mt-2 d-flex justify-center text-body-1 ml-2">
							<VChip color="info" size="small" label class="text-capitalize">
								{{ eventData.type.name }}
							</VChip>
						</div>
					</div>

					<v-dialog v-model="dialog" max-width="600px">
						<v-card>
							<img :src="selectedImage" alt="" style="max-width: 100%; height: auto;" />
							<v-card-actions>
								<v-spacer></v-spacer>
								<v-btn text @click="dialog = false">Close</v-btn>
							</v-card-actions>
						</v-card>
					</v-dialog>
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
									<VChip :color="$filters.resolveStatusVariant(eventData.status)" size="small" label class="text-capitalize">
				                        {{ eventData.status_text }}
				                    </VChip>
	                            </div>
	                        </div>
	                    </VListItem>
	                </VList>
	            </VCardText>
				<VCardText v-if="$filters.can('update-event')">
					<VBtn @click="editEvent">
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
