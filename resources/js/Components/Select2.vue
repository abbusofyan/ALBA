<template>
  <div
    class="app-select flex-grow-1"
    :class="$attrs.class"
  >
    <VLabel
      v-if="label"
      :for="elementId"
      class="mb-1 text-body-2"
      style="line-height: 15px;"
      :text="label"
    />
    <div>
      <select class="form-control" :class="'app-inner-list app-select__content v-select__content'" :id="id" :name="name" :disabled="disabled" :required="required"></select>
    </div>
  </div>
</template>

<script setup>
defineOptions({
  name: 'AppSelect',
  inheritAttrs: false,
})

const elementId = computed(() => {
  const attrs = useAttrs()
  const _elementIdToken = attrs.id
  const _id = useId()

  return _elementIdToken ? `app-select-${_elementIdToken}` : _id
})

const label = computed(() => useAttrs().label)
</script>

<script>
import 'select2/dist/css/select2.min.css'
import 'select2/dist/js/select2.full';

export default {
  name: 'Select2',
  data() {
    return {
      select2: null
    };
  },
  emits: ['update:modelValue'],
  props: {
    modelValue: [String, Array],
    id: {
      type: String,
      default: ''
    },
    name: {
      type: String,
      default: ''
    },
    placeholder: {
      type: String,
      default: ''
    },
    options: {
      type: Array,
      default: () => []
    },
    disabled: {
      type: Boolean,
      default: false
    },
    required: {
      type: Boolean,
      default: false
    },
    settings: {
      type: Object,
      default: () => {
      }
    },
  },
  watch: {
    options: {
      handler(val) {
        this.setOption(val);
      },
      deep: true
    },
    modelValue: {
      handler(val) {
        this.setValue(val);
      },
      deep: true
    },
  },
  methods: {
    setOption(val = []) {
      this.select2.empty();
      this.select2.select2({
        placeholder: this.placeholder,
        ...this.settings,
        data: val
      });
      this.setValue(this.modelValue);
    },
    setValue(val) {
      if (val instanceof Array) {
        this.select2.val([...val]);
      } else {
        this.select2.val([val]);
      }
      this.select2.trigger('change');
    },
    reset() {
      this.$emit('update:modelValue', null);
      this.select2.val(null).trigger('change');
    }
  },
  mounted() {
    this.select2 = $(this.$el)
      .find('select')
      .select2({
        placeholder: this.placeholder,
        ...this.settings,
        data: this.options
      })
      .on('select2:select select2:unselect', ev => {
        this.$emit('update:modelValue', this.select2.val());
        this.$emit('select', ev['params']['data']);
      });
    this.setValue(this.modelValue);
  },
  beforeUnmount() {
    this.select2.select2('destroy');
  }
};
</script>
<style>
.select2-container {
  width: 100% !important;
}

body .select2-container--default .select2-selection--single {
  font-size: 0.9375rem;
  line-height: 1.5rem;
  background-color: transparent;
  height: 38px;
}

body .select2-container .select2-selection--single .select2-selection__rendered {
  height: 100%;
  line-height: 38px;
  color: rgba(var(--v-theme-on-surface), var(--v-high-emphasis-opacity));
}

body .select2-dropdown {
  background: rgb(var(--v-theme-background));
  color: rgba(var(--v-theme-on-background), var(--v-high-emphasis-opacity));
}

body .select2-container--default .select2-selection--single .select2-selection__arrow {
  height: 100%;
}

body .select2-container--default .select2-search--dropdown .select2-search__field {
  border-color: rgb(var(--v-theme-surface));
}

body .select2-container--default .select2-selection--single {
  border-color: rgba(var(--v-border-color), 0.22);
}

body .select2-container--default .select2-search--dropdown .select2-search__field:hover,
body .select2-container--default .select2-search--dropdown .select2-search__field:active,
body .select2-container--default .select2-search--dropdown .select2-search__field:focus {
  outline: none;
}

body .select2-container--default .select2-results__option--highlighted[aria-selected] {
  background-color: rgb(var(--v-global-theme-primary));
}

body .select2-results__option {
  padding: 10px;
}
</style>
