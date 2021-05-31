<template>
  <label class="b-checkbox" :class="{ 'is-checked': isChecked }">
    <span class="b-checkbox__outline">
      <svg class="b-checkbox__check">
        <use xlink:href="/icons-pack.svg#svg-check"></use>
      </svg>
    </span>
    <span class="b-checkbox__label">{{ label }}</span>
    <input
      type="checkbox"
      class="b-checkbox__input"
      v-model="isChecked"
      @change="handleChange"
    />
  </label>
</template>
<script>
export default {
  name: "b-checkbox",
  mounted() {},
  created() {},
  data() {
    return {
      isChecked: false,
    };
  },
  props: {
    label: {
      type: String,
      default: "",
    },
    id: {
      type: Number,
      default: 0,
    },
  },
  methods: {
    handleChange(e) {
      this.$emit("change", {
        id: this.id,
        checked: this.isChecked,
      });
    },
  },
};
</script>