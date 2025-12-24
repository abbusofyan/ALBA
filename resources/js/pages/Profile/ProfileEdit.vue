<script setup>
import { Head } from "@inertiajs/vue3";
import Layout from "../../layouts/blank.vue";
import AccountSettingsAccount from '../Profile/AccountSettingsAccount.vue';
import AccountSettingsSecurity from '../Profile/AccountSettingsSecurity.vue';

const props = defineProps({
  user: Object
});

const currentTab = ref('account')
const tabs = [
  {
    title: 'Account',
    icon: 'tabler-users',
    tab: 'account',
  },
  {
    title: 'Security',
    icon: 'tabler-lock',
    tab: 'security',
  }
]
</script>

<template>

  <Head title="Edit Profile" />
  <Layout>
    <VTabs v-model="currentTab" class="v-tabs-pill">
      <VTab v-for="item in tabs" :key="item.icon" :value="item.tab">
        <VIcon size="20" start :icon="item.icon" />
        {{ item.title }}
      </VTab>
    </VTabs>
    <VCard class="mt-5">
      <VCardText>
        <VWindow v-model="currentTab">
          <!-- SettingsAccount -->
          <VWindowItem value="account">
            <AccountSettingsAccount :user="props.user"/>
          </VWindowItem>

          <!-- Security -->
          <VWindowItem value="security">
            <AccountSettingsSecurity :user="props.user"/>
          </VWindowItem>
        </VWindow>
      </VCardText>
    </VCard>
  </Layout>
</template>
