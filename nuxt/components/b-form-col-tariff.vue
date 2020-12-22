<template>
  <div class="b-form-col">
    <div class="b-form-col-tariff">
      <div class="b-form-col-tariff__title">{{ title }}</div>
      <div class="b-form-col-tariff__subtitle">{{ subtitle }}</div>
      <div class="b-form-col-tariff__price">₽{{ price }}</div>
      <div class="b-form-col-tariff__button">
        <b-button :title="titleButton" @click="handleClick" />
      </div>
    </div>
  </div>
</template>
<script>
export default {
  name: "b-form-col-tariff",
  props: {
    title: {
      type: String,
      default: "",
    },
    subtitle: {
      type: String,
      default: "",
    },
    price: {
      type: Number,
      default: 0,
    },
    titleButton: {
      type: String,
      default: "",
    },
    id: {
      type: Number,
      default: 0
    },
  },

  created() {},
  mounted() {},
  methods: {
    handleClick(e) {
      this.$emit("change", {
        id: this.id,
        price: this.price,
        event: e
      });
    }
  },
};
</script>