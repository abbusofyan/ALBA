<script setup>
import {
    useForm, router
} from '@inertiajs/vue3'
import {
    toast
} from "vue3-toastify"

const props = defineProps({
    events: {
        type: Object,
        required: true,
    },
})

const detailEvent = async (id) => {
  router.visit(route("events.show", id));
};

</script>

<template>
<VRow>
    <VCol cols="12">
        <VCard title="User joined events :">
            <VCardText>
                <VTable class="w-100">
                    <thead>
                        <tr>
                            <th>Date/Time</th>
                            <th>Event ID</th>
                            <th>Event Type</th>
                        </tr>
                    </thead>
                    <tbody>
                        <tr v-for="(event, i) in events" :key="i">
                            <td>
								{{ $filters.formatDate(event.date_start) + ' - ' + $filters.formatDate(event.date_end) }}<br>
								{{ event.time_start }} - {{event.time_end}}
							</td>
                            <td>
								<a :href="`#`" @click.prevent="detailEvent(event.id)" class="font-weight-medium text-link">
									{{ event.code }}
								</a>
							</td>
                            <td>{{ event.type.name }}</td>
                        </tr>
                    </tbody>
                </VTable>
            </VCardText>
        </VCard>
    </VCol>
</VRow>
</template>
