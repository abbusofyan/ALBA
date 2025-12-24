<script setup>
import {
    debounce
} from "lodash";
import {
    onMounted,
    ref,
    watch
} from "vue";
import {
    router
} from "@inertiajs/vue3";

const props = defineProps({
    eventData: {
        type: Object,
        required: true,
    },
});

const checkin_bins = ref({
    data: [],
    total: 0,
});
const loading = ref(true);
const search = ref("");
const page = ref(1);
const itemsPerPage = ref(10);
const sortBy = ref([]);

const headers = [{
        title: "User",
        key: "user"
    },
    {
        title: "Nickname",
        key: "display_name"
    },
    {
        title: "Phone Number",
        key: "phone"
    },
    {
        title: "Total Unique Bins",
        key: "total_unique_bins",
    },
    {
        title: 'Bin Type Breakdown',
        key: 'bin_breakdown',
        sortable: false
    }
];

const fetchData = async () => {
    loading.value = true;
    try {
        const response = await axios.get(
            route("events.checkin-bins", props.eventData.id), {
                params: {
                    page: page.value,
                    paginate: itemsPerPage.value,
                    sort_by: sortBy.value[0]?.key,
                    sort_order: sortBy.value[0]?.order,
                    search: search.value,
                },
            }
        );
        checkin_bins.value = response.data;
    } catch (error) {
        console.error("Error fetching event activities :", error);
    } finally {
        loading.value = false;
    }
};

watch(search, debounce(fetchData, 300));

const detailUser = async (id) => {
    router.visit(route("users.show", id));
};

const detailRecycling = async (id) => {
    router.visit(route('recyclings.show', id));
};

const detailBin = async (id) => {
    router.visit(route('bins.show', id));
};

const dialog = ref(false)
const selectedImage = ref('')

const showImage = (src) => {
    selectedImage.value = src
    dialog.value = true
}

function handleClick(event, userId) {
    // if user used Ctrl+click, Cmd+click, or middle click → let browser handle it
    if (
        event.metaKey || // Cmd (Mac)
        event.ctrlKey || // Ctrl (Win/Linux)
        event.shiftKey || // Shift+Click
        event.button !== 0 // not left click
    ) {
        return
    }

    // normal left-click → prevent full reload
    event.preventDefault()
    router.visit(`/users/${userId}`)
}
</script>

<template>
<VRow>
    <VCol cols="12">
        <VCard title="Event Check-in Bin :">
            <VCardText>
                <VRow>
                    <VCol md="10">
                        <VTextField v-model="search" label="Search Participant" class="mb-4" prepend-inner-icon="mdi-magnify" />
                    </VCol>
                    <VCol md="2">
                        <v-btn :href="`/events/download-checkin-bins/${eventData.id}`" color="info" class="me-2 mb-2">
                            <v-icon end icon="tabler-download" class="me-2" />
                            Download
                        </v-btn>
                    </VCol>
                </VRow>
                <VDataTableServer v-model:items-per-page="itemsPerPage" v-model:page="page" :items-length="checkin_bins.total" :headers="headers" :items="checkin_bins.data" :loading="loading" class="text-no-wrap" v-model:sort-by="sortBy"
                    @update:options="fetchData">
                    <template #item.created_at="{ item }">
                        {{$filters.formatDateTime(item.created_at)}}
                    </template>

                    <template #item.user="{ item }">
                        <div class="d-flex align-center gap-x-4">
                            <div class="d-flex flex-column">
                                <a :href="`/users/${item.user_id}`" class="text-link" @click="handleClick($event, item.user_id)">
                                    {{ item.user_name }}<br>
                                    {{ item.email }}
                                </a>
                            </div>
                        </div>
                    </template>

                    <template #item.bin_breakdown="{ item }">
                        <div class="d-flex flex-wrap">
                            <v-chip v-for="(totalRecycling, binType, index) in item.bin_type_breakdown" :key="index" class="ma-1" color="primary" small>
                                {{ binType }} : {{ totalRecycling }}
                            </v-chip>
                        </div>
                    </template>

                    <template #bottom>
                        <TablePagination v-model:page="page" :items-per-page="itemsPerPage" :total-items="checkin_bins.total" />
                    </template>
                </VDataTableServer>

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
