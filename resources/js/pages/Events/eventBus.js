// eventBus.js
import { ref } from "vue";

const EventBus = ref({});

export default {
  on(event, callback) {
    EventBus.value[event] = callback;
  },
  emit(event, data) {
    if (EventBus.value[event]) {
      EventBus.value[event](data);
    }
  },
};
