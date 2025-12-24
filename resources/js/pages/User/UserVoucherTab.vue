<script setup>
import { ref, computed, onMounted, watch } from 'vue'
import {
    debounce
} from "lodash";
import {
    router
} from "@inertiajs/vue3";
const props = defineProps({
	userData: {
		type: Object,
		required: true
	}
})

const vouchers = ref({
    data: [],
    total: 0,
});
const loading = ref(true);
const search = ref("");
const page = ref(1);
const itemsPerPage = ref(10);
const sortBy = ref([]);

const headers = [
    { title: 'Redeemed At', key: 'redeemed_at' },
	{ title: 'Reward Name', key: 'reward_name' },
	{ title: 'Points', key: 'point' },
	{ title: 'Voucher Code', key: 'code' },
]

const fetchData = async () => {
    loading.value = true;
    try {
        const response = await axios.get(
            route("users.vouchers", props.userData.id), {
                params: {
                    page: page.value,
                    paginate: itemsPerPage.value,
                    sort_by: sortBy.value[0]?.key,
                    sort_order: sortBy.value[0]?.order,
                    search: search.value,
                },
            }
        );
        vouchers.value = response.data;
    } catch (error) {
        console.error("Error fetching user vouchers :", error);
    } finally {
        loading.value = false;
    }
};

watch(search, debounce(fetchData, 300));

const detailUser = async (id) => {
    router.visit(route('users.show', id));
};

</script>

<template>
<VRow>
    <VCol cols="12">
        <VCard title="User Redeemed Vouchers :">
            <VCardText>
				<VRow>
					<VCol md="10">
						<VTextField
							v-model="search"
							label="Search..."
							class="mb-4"
							prepend-inner-icon="mdi-magnify"
						/>
					</VCol>
					<!-- <VCol md="2">
						<v-btn :href="`/events/download-participant/${eventData.id}`" color="info" class="me-2 mb-2">
							<v-icon end icon="tabler-download" class="me-2" />
							Download
						</v-btn>
					</VCol> -->
				</VRow>

				<VDataTableServer v-model:items-per-page="itemsPerPage" v-model:page="page" :items-length="vouchers.total" :headers="headers" :items="vouchers.data" :loading="loading" class="text-no-wrap" v-model:sort-by="sortBy"
                    @update:options="fetchData">
                    <template #item.redeemed_at="{ item }">
						{{$filters.formatDateTime(item.created_at)}}
                    </template>
					<template #item.reward_name="{ item }">
						{{item.reward.name}}
                    </template>

					<template #item.point="{ item }">
						{{item.reward.price}}
                    </template>
					<template #item.code="{ item }">
						<div style="white-space: normal; word-break: break-word">
							{{item.voucher.code}}
	                    </div>
                    </template>
				</VDataTableServer>
            </VCardText>
        </VCard>
    </VCol>
</VRow>
</template>
