<script setup>
import {
    router
} from "@inertiajs/vue3";
import QrcodeVue from "qrcode.vue";
import {
    ref
} from "vue";

const props = defineProps({
    binData: {
        type: Object,
        required: true,
    },
});

const editBin = async () => {
    router.visit(route("bins.edit", props.binData.id));
};

const handleDownload = (bin) => {
    downloadQR(bin);
};

const qrRef = ref(null);
</script>

<template>
<VRow>
    <VCol cols="12">
        <VCard>
            <VCardText class="pt-5 d-flex align-center flex-wrap justify-center">
                <div>
                    <qrcode-vue v-if="binData.bin_type_id != 1" ref="qrRef" id="qrCanvas" :value="binData.qrcode_content" :size="200" render-as="canvas" />

                    <h5 class="text-h5 mt-2 d-flex justify-center">
                        {{ binData.code }}
                    </h5>
                    <div class="mt-2 d-flex justify-center text-body-1 ml-2">
                        <VChip color="info" size="small" label class="text-capitalize">
                            {{ binData.type.name }}
                        </VChip>
                    </div>

                    <div class="mt-2 d-flex justify-center" v-if="binData.bin_type_id != 2">
                        <VBtn v-if="binData.bin_type_id != 1" @click="handleDownload(binData)" variant="outlined">
                            Download QR
                            <VIcon end icon="tabler-download" />
                        </VBtn>
                    </div>
                </div>
            </VCardText>

            <VDivider />

            <VCardText>
                <p class="text-sm text-disabled">DETAILS</p>

                <VList class="card-list mt-2">
                    <VListItem>
                        <div class="text-h6">
                            Status :
                            <div class="d-inline-block text-body-1 ml-2">
                                <VChip :color="$filters.resolveStatusVariant(binData.status)" size="small" label class="text-capitalize">
                                    {{ binData.status_text }}
                                </VChip>
                            </div>
                        </div>
                    </VListItem>
                </VList>
            </VCardText>
            <VCardText v-if="$filters.can('update-bin')">
                <VBtn @click="editBin">
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
