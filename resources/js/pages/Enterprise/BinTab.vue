<script setup>
import {
    useForm
} from '@inertiajs/vue3';
import {
    toast
} from "vue3-toastify";

const props = defineProps({
    binsData: {
        type: Object,
        required: true,
    },
})
</script>

<template>
<VRow>
    <VCol cols="12">
        <VCard title="Bins :">
            <VCardText>
                <VRow>
					<VTable class="w-100">
			            <thead>
			                <tr>
			                    <th>ID</th>
			                    <th>Type</th>
			                    <th>Address</th>
								<th>Remark</th>
								<th>Status</th>
			                </tr>
			            </thead>
			            <tbody>
			                <tr v-for="(bin, i) in binsData" :key="i">
			                    <td>
									<a :href="`/bins/${bin.id}`" class="font-weight-medium text-link">
										{{ bin.code }}
									</a>
								</td>
								<td>{{bin.type.name}}</td>
								<td>{{bin.address}}</td>
								<td>{{bin.remark}}</td>
								<td>
									<VChip :color="$filters.resolveStatusVariant(bin.status)" size="small" label class="text-capitalize">
										{{ bin.status_text }}
									</VChip>
								</td>
			                </tr>
			            </tbody>
			        </VTable>
                </VRow>
            </VCardText>
        </VCard>
    </VCol>
</VRow>
</template>
