<template>
  <div class="b-step">
    <div class="b-step__item" v-for="(item, index) in items" :key="index">
      <div class="b-step__number" v-if="index + 1 > step">{{ index + 1 }}</div>
      <div class="b-step__complete" v-if="index + 1 <= step">
        <svg width="14px" height="10px" fill="#FFFFFF">
          <use xlink:href="/icons-pack.svg#svg-complete"></use>
        </svg>
      </div>
      <div class="b-step__container">
        <div class="b-step__title">{{ item.title }}</div>
        <div class="b-step__description">{{ item.description }}</div>
      </div>
    </div>
  </div>
</template>
<script>
export default {
  name: "b-step",
  props: {
    items: {
      type: Array,
    },
    step: {
      type: Number,
      default: 0,
    },
  },
};
</script>