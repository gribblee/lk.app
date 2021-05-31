<template>
  <div
    :style="{
      fontSize: '14px',
      lineHeight: '22px',
      marginBottom: '7px',
      color: 'rgba(0, 0, 0, 0.65)',
    }"
  >
    <p
      :style="{
        marginRight: '8px',
        display: 'inline-block',
        color: 'rgba(0, 0, 0, 0.85)',
      }"
    >
      {{ title }}
    </p>
    {{ content }}
  </div>
</template>
<script>
export default {
  name: "b-description-item",
  props: {
    title: {
      type: String | Number,
      default: "",
    },
    content: {
      type: String | Array | Object,
      default: "",
    },
  },
  created() {
    if (Array.isArray(this.content)) {
      this.content = this.content.join(" ");
    }
  },
};
</script>