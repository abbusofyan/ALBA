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

import QrcodeVue from "qrcode.vue";

const isUserInfoEditDialogVisible = ref(false)
const isUpgradePlanDialogVisible = ref(false)

const editlUser = async () => {
    router.visit(route('users.edit', props.userData.id));
};

</script>

<template>
	<VRow>
		<VCol cols="12">
			<VCard>
				<VCardText class="pt-5 d-flex align-center flex-wrap justify-center">
	                <div>
	                    <qrcode-vue ref="qrRef" id="qrCanvas" :value="userData.qrcode_content" :size="200" render-as="canvas" />

						<h5 class="text-h5 mt-4 d-flex justify-center">
							{{ userData.name }}
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
	                            Nickname :
	                            <div class="d-inline-block text-body-1">
	                                {{ props.userData.display_name }}
	                            </div>
	                        </div>
	                    </VListItem>
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
	                            Contact :
	                            <div class="d-inline-block text-body-1">
	                                {{ props.userData.phone }}
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
						<VListItem>
	                        <div class="text-h6">
	                            Point :
								<div class="d-inline-block text-body-1">
 								   {{ props.userData.point }}
 							   </div>
	                        </div>
	                    </VListItem>
	                </VList>
	            </VCardText>
				<VCardText>
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
