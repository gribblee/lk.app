<template>
  <div class="b-modal" :class="{ open: open }">
    <div class="b-modal__form">
      <div class="b-modal__title">{{ title }}</div>
      <div class="b-modal__body">
        <slot name="body"></slot>
      </div>
      <div class="b-modal__action">
        <b-button :title="okTitle" @click="handleOk" />
        <b-button :title="cancelTitle" @click="handleCancel" />
      </div>
    </div>
  </div>
</template>
<style lang="scss"scoped>
.b-modal__action {
  & > button {
    margin-left: 20px;
    &:first-child {
      margin-left: 0;
    }
  } 
}
</style>
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
    cancelTitle: {
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
    handleCancel(e) {
      this.$emit("onCancel", e);
    },
  },
};
</script>