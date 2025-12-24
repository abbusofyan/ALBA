<template>
  <Head title="Notifications"/>
  <Layout>
    <VCard>
      <VCardText class="d-flex align-center justify-space-between flex-wrap gap-4">
        <div class="d-flex gap-2 align-center">
          <p class="text-body-1 mb-0">Show</p>
          <AppSelect
            :model-value="itemsPerPage"
            :items="[
              { value: 5, title: '5' },
              { value: 25, title: '25' },
              { value: 50, title: '50' },
              { value: 100, title: '100' },
              { value: -1, title: 'All' }
            ]"
            style="inline-size: 5.5rem"
            @update:model-value="itemsPerPage = parseInt($event, 10)"
          />
        </div>
        <div class="d-flex align-center gap-4 flex-wrap">
          <AppTextField
            v-model="search"
            placeholder="Search Notification"
            style="inline-size: 15.625rem"
          />
          <Link href="/notifications/create">
            <VBtn density="default" prepend-icon="tabler-plus">
              Add Notification
            </VBtn>
          </Link>
        </div>
      </VCardText>
      <VDivider/>
      <VDataTableServer
        v-model:items-per-page="itemsPerPage"
        v-model:page="page"
        :items-length="notifications.total"
        :items-per-page-options="[
          { value: 5, title: '5' },
          { value: 10, title: '10' },
          { value: -1, title: '$vuetify.dataFooter.itemsPerPageAll' }
        ]"
        :headers="headers"
        :items="notifications.data"
        item-value="id"
        class="text-no-wrap"
        @update:options="updateOptions"
      >
        <template #item.created_at="{ item }">
          <div class="text-body-2 text-medium-emphasis">
            {{ $filters.formatDateTime(item.created_at) }}
          </div>
        </template>
        <template #item.full_icon_url="{ item }">
          <VAvatar :image="item.full_icon_url"/>
        </template>
        <template #item.actions="{ item }">
          <VBtn icon size="small" color="medium-emphasis" variant="text">
            <Link :href="`/notifications/${item.id}/edit`">
              <VIcon size="22" icon="tabler-edit"/>
            </Link>
          </VBtn>
          <VBtn icon size="small" color="medium-emphasis" variant="text" @click="pushNotification(item.id)">
            <VIcon size="22" icon="tabler-send"/>
          </VBtn>
          <VBtn icon size="small" color="medium-emphasis" variant="text" @click="destroy(item.id)">
            <VIcon size="22" icon="tabler-trash"/>
          </VBtn>
        </template>
        <template #bottom>
          <TablePagination
            v-model:page="page"
            :items-per-page="itemsPerPage"
            :total-items="notifications.total"
          />
        </template>
      </VDataTableServer>
    </VCard>
  </Layout>
</template>

<script setup>
import {ref, watch} from "vue"
import {Head, Link, router} from "@inertiajs/vue3"
import debounce from "lodash/debounce"
import Layout from "../../layouts/blank.vue"
import TablePagination from "@core/components/TablePagination.vue"
import AppTextField from "@core/components/app-form-elements/AppTextField.vue"
import AppSelect from "@core/components/app-form-elements/AppSelect.vue"
import Swal from "sweetalert2";

const props = defineProps({
  notifications: Object,
  filters: Object
})

const headers = [
  {title: "Title", key: "title"},
  {title: "message", key: "message", sortable: false},
  {title: "Assigned To", key: "user.name", sortable: false},
  {title: "Icon", key: "full_icon_url", sortable: false},
  {title: "Created Date", key: "created_at", sortable: false},
  {title: "Actions", key: "actions", sortable: false}
]

const search = ref(props.filters.search || "")
const page = ref(props.notifications.current_page)
const itemsPerPage = ref(props.notifications.per_page)

const fetchData = () => {
  router.get(
    "/notifications",
    {
      search: search.value,
      page: page.value, paginate:
      itemsPerPage.value
    },
    {
      preserveState: true,
      preserveScroll: true,
      replace: true
    }
  )
}

watch(search, debounce(fetchData, 300))
watch([page, itemsPerPage], fetchData)

const updateOptions = options => {
  page.value = options.page
  itemsPerPage.value = options.itemsPerPage
}

const pushNotification = (id) => {
  Swal.fire({
    title: "Are you sure you want to push this notification?",
    icon: "question",
    showCancelButton: true,
    confirmButtonColor: "#28c76f",
    cancelButtonColor: "#6c757d",
    confirmButtonText: "Yes, Push it!",
    cancelButtonText: "Cancel",
  }).then((result) => {
    if (result.isConfirmed) {
      router.post(`/notifications/${id}/push`, {}, {
        preserveScroll: true,
        onSuccess: (data) => {
          if (data.props.flash.success) {
            Swal.fire(
              "Pushed!",
              data.props.flash.success,
              "success"
            );
          } else if (data.props.flash.error) {
            Swal.fire(
              "Error!",
              data.props.flash.error,
              "error"
            );
          }
        },
      });
    }
  });
};

const destroy = (id) => {
  Swal.fire({
    title: "Are you sure? <br> <i class='fa-solid fa-trash-can'></i>",
    text: "You won't be able to revert this",
    icon: "warning",
    showCancelButton: true,
    confirmButtonColor: "#ea5455",
    cancelButtonColor: "#6CC9CF",
    confirmButtonText: "Yes, Proceed!",
    cancelButtonText: "Cancel",
  }).then((result) => {
    if (result.isConfirmed) {
      router.delete(`/notifications/${id}`, {
        preserveScroll: true,
      });
    }
  });
};
</script>
