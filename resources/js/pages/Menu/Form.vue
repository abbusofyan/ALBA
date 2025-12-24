<template>
  <Head title="Create Menu" />
  <Layout>
    <VCard>
      <VCardItem>
        <VCardTitle>{{ menu_detail ? "Edit" : "Create" }} Menu</VCardTitle>
      </VCardItem>
      <VCardText>
        <VForm
          @submit.prevent="
            menu_detail && menu_detail.id
              ? form.post(route('menu.update', menu_detail.id))
              : form.post(route('menu.store'))
          "
        >
          <AppTextField v-model="form.name" label="Name" placeholder="Name" />
          <div v-if="form.errors.name">
            {{ form.errors.name }}
          </div>
          <!-- :error-messages="form.errors.message" -->
          <VCheckbox
            class="mt-5"
            v-model="form.is_heading"
            label="Is Heading"
          />
          <div v-if="form.errors.is_heading">{{ form.errors.is_heading }}</div>

          <AppTextField
            class="mt-5"
            v-model="form.link"
            label="Link"
            placeholder="Link"
          />
          <div v-if="form.errors.link">
            {{ form.errors.link }}
          </div>

          <AppTextField
            class="mt-5"
            v-model="form.order"
            label="Order"
            placeholder="Order"
          />
          <div v-if="form.errors.order">
            {{ form.errors.order }}
          </div>

          <AppSelect
            class="mt-5"
            v-model="form.parent"
            :items="processedMenus"
            chips
            closable-chips
            label="Parent"
            placeholder="Select Parent"
          />
          <div v-if="form.errors.parent">
            {{ form.errors.parent }}
          </div>
          <AppSelect
            class="mt-5"
            v-model="form.roles"
            :items="processedRoles"
            placeholder="Select Role"
            label="Apply for"
            chips
            multiple
            closable-chips
          />
          <div v-if="form.errors.roles">
            {{ form.errors.roles }}
          </div>

          <div class="mt-5">
            <label>Image</label>
            <VFileInput label="File input" @change="onFileChange" />
          </div>

          <VBtn
            class="mt-5"
            type="submit"
            :disabled="form.processing"
            color="primary"
          >
            {{ menu_detail ? "Update" : "Create" }}
          </VBtn>
        </VForm>
      </VCardText>
    </VCard>
  </Layout>
</template>

<script setup>
import { Head, Link, useForm } from "@inertiajs/vue3";
import { onMounted, computed } from "vue";
import Layout from "../../layouts/blank.vue";

const props = defineProps({
  menu_detail: Object,
  menus: Object,
  roles: Object,
});

const processedMenus = computed(() =>
  props.menus.map((menu) => ({
    title: menu.name,
    value: menu.id,
  }))
);

const processedRoles = computed(() =>
  props.roles.map((role) => ({
    title: role.name,
    value: role.id,
  }))
);

const form = useForm({
  name: null,
  link: null,
  order: null,
  parent: null,
  roles: [],
  is_heading: false,
  image: null,
});

const onFileChange = (event) => {
  form.image = event.target.files[0];
};

onMounted(() => {
  if (props.menu_detail) {
    form.name = props.menu_detail.name;
    form.link = props.menu_detail.link;
    form.order = props.menu_detail.order;
    form.parent = props.menu_detail.parent
      ? props.menu_detail.parent.id || props.menu_detail.parent
      : null;
    form.roles = props.menu_detail.roles
      ? props.menu_detail.roles.map((role) => role.id)
      : [];
    form.is_heading = props.menu_detail.is_heading ?? false;
  }
});
</script>
