<template>
  <Head title="Notifications"/>
  <Layout>
    <VCard>
      <VCardItem>
        <VCardTitle>{{ notification ? 'Update' : 'Create' }} Notification</VCardTitle>
      </VCardItem>
      <VCardText>
        <VForm @submit.prevent="submitForm">
          <VRow>
            <VCol cols="12">
              <Select2
                label="Select User"
                v-model="form.user_id"
                :class="['custom-select', { 'is-invalid': form.errors.user_id }]"
                :options="userOptions"
                :settings="select2Settings"
              />
            </VCol>

            <VCol cols="12">
              <AppTextField
                v-model="form.title"
                :error-messages="form.errors.title"
                autofocus
                label="Title"
                type="text"
                placeholder="Title"
              />
            </VCol>

            <VCol cols="12">
              <AppTextarea
                v-model="form.message"
                :error-messages="form.errors.message"
                autofocus
                label="Message"
                type="text"
                placeholder="Message"
              />
            </VCol>

            <VCol cols="6">
              <VFileInput
                label="File input"
                @change="handleIconUpload"
                accept="image/*"
              />
            </VCol>
            <VCol cols="6">
              <VImg
                :src="iconPreview"
                :rounded="true"
                :max-width="200"
              />
            </VCol>

            <VCol cols="12">
              <VCheckbox
                v-model="form.is_active"
                label="Active?"
              />
            </VCol>
          </VRow>

          <VBtn
            class="mt-5"
            type="submit"
            :disabled="form.processing"
            color="primary"
          >
            {{ notification ? 'Update' : 'Create' }}
          </VBtn>
        </VForm>
      </VCardText>
    </VCard>
  </Layout>
</template>

<script setup>
import {Head, useForm} from "@inertiajs/vue3";
import {computed} from "vue";
import Layout from "../../layouts/blank.vue";
import AppTextField from "@core/components/app-form-elements/AppTextField.vue";
import AppTextarea from "@core/components/app-form-elements/AppTextarea.vue";

const props = defineProps({
  csrf: String,
  notification: Object,
});

const form = useForm({
  user_id: null,
  title: null,
  message: null,
  icon_url: null,
  is_active: true,
});

const userOptions = props.notification
  ? [{id: props.notification.user_id, text: props.notification.user?.name}]
  : [];

const select2Settings = computed(() => ({
  allowClear: true,
  ajax: {
    url: "/data/users",
    dataType: "json",
    method: "POST",
    data: (params) => ({
      search: params.term,
      _token: props.csrf,
    }),
    processResults: (data) => ({
      results: data.map((item) => ({
        text: `${item.id} | ${item.name}`,
        id: item.id,
        data: item,
      })),
    }),
  },
}));

const submitForm = () => {
  if (props.notification) {
    form.post(`/notifications/${props.notification.id}`);
  } else {
    form.post('/notifications');
  }
};


const iconPreview = ref(null);
const handleIconUpload = (event) => {
  const file = event.target.files[0];
  if (file) {
    form.icon_url = file;
    const reader = new FileReader();
    reader.onload = (e) => {
      iconPreview.value = e.target.result;
    };
    reader.readAsDataURL(file);
  } else {
    form.icon_url = null;
    iconPreview.value = null;
  }
};

onMounted(() => {
  if (props.notification) {
    form.title = props.notification.title;
    form.message = props.notification.message;
    form.is_active = props.notification.is_active;
    form.user_id = props.notification.user_id;
    if (props.notification.icon_url) {
      iconPreview.value = `/storage/${props.notification.icon_url}`;
    }
  }
});
</script>
