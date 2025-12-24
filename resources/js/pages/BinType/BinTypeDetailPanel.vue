<script setup>
import {
    router
} from "@inertiajs/vue3";
const props = defineProps({
    binTypeData: {
        type: Object,
        required: true,
    },
})

const isUserInfoEditDialogVisible = ref(false)
const isUpgradePlanDialogVisible = ref(false)

const editBinType = async () => {
    router.visit(route('bin-types.edit', props.binTypeData.id));
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
					<div style="width: 100%; max-width: 100%;">
						<img
							:src="`/storage/images/bin-types/${props.binTypeData.image}`"
							alt=""
							style="height: auto; max-height:300px; max-width: 100%;"
							class="cursor-pointer"
							@click="showImage(`/storage/images/bin-types/${props.binTypeData.image}`)"
						/>

						<h5 class="text-h5 mt-4 d-flex justify-center">
							{{ props.binTypeData.name }}
						</h5>
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
	                            QR Code :
								<div class="d-inline-block text-body-1 ml-2">
							   		{{binTypeData.qrcode_type}}
							    </div>
	                        </div>
	                    </VListItem>
						<VListItem>
	                        <div class="text-h6">
	                            CO2 Points value :
								<div class="d-inline-block text-body-1 ml-2">
							   		{{binTypeData.point}}
							    </div>
	                        </div>
	                    </VListItem>
	                </VList>
	            </VCardText>
				<VCardText v-if="$filters.can('update-bin-type')">
					<VBtn @click="editBinType">
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
