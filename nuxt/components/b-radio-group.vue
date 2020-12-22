<template>
  <div class="b-radio-group">
    <span class="b-radio-group__label">{{ label }}</span>
    <div class="b-radio">
      <button
        class="b-radio__button"
        v-for="(item, index) in items"
        :key="index"
        :class="{ 'is-active': item.id == value }"
        @click="updateValue(item.id)"
      >
        {{ item.title }}
      </button>
    </div>
  </div>
</template>
<script>
export default {
  name: "b-radio-group",
  props: {
    label: {
      type: String,
      default: "",
    },
    items: {
      type: Array,
      default: [],
    },
    value: {
      type: Number,
      default: 0,
    },
  },
  model: {
    prop: "value",
    event: "change",
  },
  data() {
    return {};
  },
  created() {},
  mounted() {},
  methods: {
    updateValue(value) {
      this.$emit("change", value);
    },
  },
};
</script>