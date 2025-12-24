<template>
  <Head title="Api logs"/>
  <Layout>
    <div class="container-xxl flex-grow-1 container-p-y">
      <div class="row">
        <div v-for="(log, index) in reversedLogs" :key="index" class="col-md-12 mb-3">
          <div class="card">
            <div class="card-body">
              <h5 class="card-title">{{ log.timestamp }}</h5>
              <pre class="p-3 rounded text-start language-json" style="background-color: #14171a">
                                <code v-html="highlightJson(log)"></code>
                            </pre>
            </div>
          </div>
        </div>
      </div>
    </div>
  </Layout>
</template>

<script setup>
import Layout from "../../layouts/blank.vue";
import Prism from "prismjs";
import "prismjs/components/prism-json";
import "prismjs/themes/prism-okaidia.css";
import {computed} from "vue";
import {Head} from "@inertiajs/vue3";

const props = defineProps({
  logs: Array,
});

const reversedLogs = computed(() => [...props.logs].reverse());

const highlightJson = (log) => {
  const json = {
    payload: log.payload,
    response: log.response,
  };

  return Prism.highlight(JSON.stringify(json, null, 2), Prism.languages.json, "json");
};
</script>

<style>
pre {
  white-space: pre-wrap;
  word-wrap: break-word;
  font-family: "Fira Code", monospace;
  font-size: 14px;
}
</style>
