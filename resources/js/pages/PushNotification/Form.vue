<script setup>
import Layout from "@/layouts/blank.vue";
import {
    Head,
    useForm,
    usePage
} from "@inertiajs/vue3";
import {
    onMounted,
    ref,
    watch
} from "vue";
import {
    toast
} from "vue3-toastify";

const props = defineProps({
    notification: Object,
    errors: Object,
});

const form = useForm({
    title: null,
    body: null,
    scheduled_at: null,
    send_now: true, // new field
});

onMounted(() => {
    if (props.notification) {
        form.title = props.notification.title
        form.body = props.notification.body
		form.send_now = props.notification.send_now ? true : false
		form.scheduled_at = props.notification.scheduled_at
    }
});

const currentTab = ref("points");

watch(
    () => usePage().props.flash,
    (flash) => {
        if (flash.success) {
            toast.success(flash.success, {
                theme: "colored",
                type: "success",
            });
        } else if (flash.created) {
            toast.success(flash.created, {
                theme: "colored",
                type: "success",
            });
        } else if (flash.error) {
            toast.error(flash.error, {
                theme: "colored",
                type: "error",
            });
        }
    }, {
        deep: true,
    }
);
</script>

<template>
<Head title="Notification" />
<Layout>
    <v-breadcrumbs class="pt-0" :items="[
				{
					title: 'Notification',
					disabled: false,
					href: '/push-notifications',
				},
				{
					title: 'Create',
					disabled: true,
				},
			]">
        <template v-slot:prepend></template>
    </v-breadcrumbs>

    <VCard>
		<VForm @submit.prevent=" notification && notification.id ? form.put(route('push-notifications.update', notification.id)) : form.post(route('push-notifications.store'))">
            <VCardItem>
                <VCardTitle>Push Notification</VCardTitle>
                <VCardSubtitle>
                    Push notifications to ALBA Mobile App
                </VCardSubtitle>
            </VCardItem>
            <VCardText>
                <VRow>
                    <VCol cols="12">
                        <label>Title <span class="text-error">*</span></label>
                        <AppTextField v-model="form.title" placeholder="Notification title" />
                        <div v-if="form.errors.title" class="text-error">
                            {{ form.errors.title }}
                        </div>
                    </VCol>

                    <VCol cols="12">
                        <label>Body <span class="text-error">*</span></label>
						<AppTextarea v-model="form.body" />
                        <div v-if="form.errors.body" class="text-error">
                            {{ form.errors.body }}
                        </div>
                    </VCol>

                    <!-- Send now checkbox -->
                    <VCol cols="12">
                        <VCheckbox
                            v-model="form.send_now"
                            label="Send Now"
                        />
                    </VCol>

                    <!-- Only show date if not send_now -->
                    <VCol cols="12" v-if="!form.send_now">
                        <label>Scheduled For <span class="text-error">*</span></label>
                        <AppDateTimePicker
                            v-model="form.scheduled_at"
                            placeholder="Select date & time"
                            :config="{
                                enableTime: true,
                                dateFormat: 'Y-m-d H:i',
                                mode: 'single',
                                minDate: 'today'
                            }"
                        />
                        <div v-if="form.errors.scheduled_at" class="invalid-feedback text-error">{{ form.errors.scheduled_at }}</div>
                    </VCol>
                </VRow>
            </VCardText>

            <VCardText v-if="$filters.can('update-setting')">
                <div class="d-flex gap-4 mt-5">
                    <VBtn type="submit" :loading="form.processing">
                        Save
                    </VBtn>
                    <VBtn color="secondary" variant="tonal" type="button" @click="form.reset()">
                        Cancel
                    </VBtn>
                </div>
            </VCardText>
        </VForm>
    </VCard>
</Layout>
</template>
