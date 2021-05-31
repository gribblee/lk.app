<template>
  <button class="button" :type="type" @click="onClick">
    {{ title }}
  </button>
</template>
<script>
export default {
  name: "b-button",
  props: {
    title: {
      type: String,
      default: "",
    },
    type: {
      type: String,
      default: "button",
    },
  },
  data() {
    return {
      typeClass: {
        link: "button--link",
      },
    };
  },
  methods: {
    onClick(e) {
      this.$emit("click", e);
    },
  },
};
</script>