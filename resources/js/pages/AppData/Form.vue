<template>
  <Layout>
    <VCard>
      <VCardItem>
        <VCardTitle>{{ appData ? 'Edit' : 'Create' }} App Data</VCardTitle>
      </VCardItem>
      <VCardText>
        <VForm @submit.prevent="submitForm">
          <VRow>
            <VCol cols="12">
              <AppTextField
                v-model="form.key"
                :error-messages="form.errors.key"
                label="Key"
                placeholder="Key"
                type="text"
              />
            </VCol>

            <VCol cols="12">
              <AppSelect
                v-model="form.type"
                :error-messages="form.errors.type"
                label="Type"
                :items="typeOptions"
              />
            </VCol>


            <VCol cols="12">
              <hr>
            </VCol>


            <VCol cols="12" v-if="form.type === 'Text'">
              <AppTextarea
                v-model="form.value"
                :error-messages="form.errors.value"
                label="Value"
                placeholder="Enter text value"
                type="text"
              />
            </VCol>

            <VCol cols="12" v-if="form.type === 'Image'">
              <VRow>
                <VCol cols="6">
                  <VFileInput
                    label="Image"
                    :error-messages="form.errors.file"
                    accept="image/*"
                    @change="handleFileChange"
                  />
                </VCol>
                <VCol cols="6">
                  <VImg
                    v-if="previewImage"
                    :src="previewImage"
                    :rounded="true"
                    :max-width="200"
                  />
                </VCol>
              </VRow>
            </VCol>

            <VCol cols="12" v-if="form.type === 'Repeater Text'">
              <VRow align="end" v-for="(item, index) in form.values" :key="index">
                <VCol cols="9">
                  <AppTextField
                    v-model="form.values[index]"
                    :error-messages="form.errors[`values.${index}`]"
                    label="Text"
                    placeholder="Enter text"
                  />
                </VCol>
                <VCol cols="3">
                  <VBtn icon size="small" color="medium-emphasis" class="mb-1" variant="text" @click="removeTextField(index)">
                    <VIcon size="22" icon="tabler-trash"/>
                  </VBtn>
                </VCol>
              </VRow>
              <VBtn
                class="mt-4"
                type="button"
                color="primary"
                @click="addTextField"
              >
                <i class="fa-solid fa-plus"></i> Add Text Field
              </VBtn>
            </VCol>

            <VCol cols="12" v-if="form.type === 'Repeater Image'">
              <VRow align="end" v-for="(file, index) in form.files" :key="index">
                <VCol cols="9">
                  <VFileInput
                    label="Image"
                    :error-messages="form.errors[`files.${index}`]"
                    accept="image/*"
                    @change="handleRepeaterFileChange($event, index)"
                  />
                </VCol>
                <VCol cols="3">
                  <VBtn icon size="small" color="medium-emphasis" class="mb-1" variant="text" @click="removeFileField(index)">
                    <VIcon size="22" icon="tabler-trash"/>
                  </VBtn>
                </VCol>
              </VRow>
              <VBtn
                type="button"
                color="primary"
                class="mt-4"
                @click="addFileField"
              >
                <i class="fa-solid fa-plus"></i> Add Image Field
              </VBtn>
            </VCol>

            <VCol cols="12" class="mt-5">
              <VBtn
                class="mt-5"
                type="submit"
                :disabled="form.processing"
                color="primary"
              >
                {{ appData ? 'Update' : 'Create' }}
              </VBtn>
            </VCol>
          </VRow>
        </VForm>
      </VCardText>
    </VCard>
  </Layout>
</template>

<script setup>
import Layout from "../../layouts/blank.vue";
import {useForm} from "@inertiajs/vue3";
import {computed, ref, reactive, onMounted, watch} from "vue";
import AppTextField from "@core/components/app-form-elements/AppTextField.vue";
import AppTextarea from "@core/components/app-form-elements/AppTextarea.vue";
import AppSelect from "@core/components/app-form-elements/AppSelect.vue";

const props = defineProps({
  appData: Object,
});

const form = useForm({
  key: props.appData ? props.appData.key : '',
  type: props.appData ? props.appData.type : '',
  value: props.appData && props.appData.type === 'Text' ? props.appData.value : '',
  values: props.appData && props.appData.type === 'Repeater Text' ? JSON.parse(props.appData.value) : [],
  files: [],
  file: null,
  _method: props.appData ? 'PUT' : null,
});

const previewImage = ref(null);
const previewImages = reactive({});

const typeOptions = [
  "Text",
  "Image",
  "Repeater Text",
  "Repeater Image",
];

onMounted(() => {
  if (props.appData) {
    if (props.appData.type === 'Image' && props.appData.value) {
      previewImage.value = `/storage/${props.appData.value}`;
    }

    if (props.appData.type === 'Repeater Image' && props.appData.value) {
      const imagePaths = JSON.parse(props.appData.value);
      imagePaths.forEach((path, index) => {
        previewImages[index] = `/storage/${path}`;
        form.files[index] = null; // Initialize the files array
      });
    }
  }
});

const handleFileChange = (event) => {
  const file = event.target.files[0];
  if (file) {
    form.file = file;
    const reader = new FileReader();
    reader.onload = (e) => {
      previewImage.value = e.target.result;
    };
    reader.readAsDataURL(file);
  }
};

const handleRepeaterFileChange = (event, index) => {
  const file = event.target.files[0];
  if (file) {
    form.files[index] = file;
    const reader = new FileReader();
    reader.onload = (e) => {
      previewImages[index] = e.target.result;
    };
    reader.readAsDataURL(file);
  }
};

const addTextField = () => {
  form.values.push('');
};

const removeTextField = (index) => {
  form.values.splice(index, 1);
};

const addFileField = () => {
  form.files.push(null);
};

const removeFileField = (index) => {
  form.files.splice(index, 1);
  delete previewImages[index];
};

const submitForm = () => {
  form.clearErrors();

  // Prepare FormData
  const data = new FormData();
  data.append('key', form.key);
  data.append('type', form.type);

  if (form.type === 'Text') {
    data.append('value', form.value);
  } else if (form.type === 'Image') {
    if (form.file) {
      data.append('file', form.file);
    } else if (!props.appData) {
      // If creating and no file selected
      form.setError('file', 'Image is required.');
      return;
    }
  } else if (form.type === 'Repeater Text') {
    data.append('values', JSON.stringify(form.values));
  } else if (form.type === 'Repeater Image') {
    form.files.forEach((file, index) => {
      if (file) {
        data.append(`files[${index}]`, file);
      }
    });
  }

  // For edit form, include _method
  if (form._method) {
    data.append('_method', 'PUT');
  }

  const url = props.appData ? `/app-data/${props.appData.id}` : '/app-data';

  form.processing = true;

  form.post(url, {
    data,
    onError: () => {
      form.processing = false;
    },
    onSuccess: () => {
      form.processing = false;
      if (!props.appData) {
        form.reset();
        previewImage.value = null;
        for (const key in previewImages) {
          delete previewImages[key];
        }
      }
    },
    onFinish: () => {
      form.processing = false;
    },
    forceFormData: true,
  });
};
</script>
