<script setup>
import { router } from "@inertiajs/vue3";
import { ref, onMounted, nextTick } from "vue"
import QrcodeVue from 'qrcode.vue'
import QRCode from 'qrcode'

const props = defineProps({
    recyclingData: {
        type: Object,
        required: true,
    },
})

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
							:src="recyclingData.photo_url"
							alt=""
							style="height: auto; max-height:300px; max-width: 100%;"
							class="cursor-pointer"
							@click="showImage(recyclingData.photo_url)"
						/>
						<h5 class="text-h5 mt-2 d-flex justify-center">
							Recycle Submission
						</h5>

						<div class="mt-2 d-flex justify-center">
							<VBtn @click="showImage(recyclingData.photo_url)" variant="outlined">
							View
							<VIcon end icon="tabler-eye" />
							</VBtn>
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
