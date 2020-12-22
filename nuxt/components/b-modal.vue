<template>
  <div class="b-modal" :class="{ open: open }">
    <div class="b-modal__form">
      <div class="b-modal__title">{{ title }}</div>
      <div class="b-modal__body">
        <slot name="body"></slot>
      </div>
      <div class="b-modal__action">
        <b-button :title="okTitle" @click="handleOk" />
      </div>
    </div>
  </div>
</template>
<script>
export default {
  name: "b-modal",
  props: {
    title: {
      type: String,
      default: "",
    },
    open: {
      type: Boolean,
      default: false,
    },
    okTitle: {
      type: String,
      default: "",
    },
  },
  model: {},

  created() {},
  mounted() {},

  methods: {
    handleOk(e) {
      this.$emit("onOk", e);
    },
  },
};
</script>