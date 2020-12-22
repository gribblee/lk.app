<template>
  <div class="demo-row">
    <div class="demo-left">
      <div class="demo-left__header">Информация</div>
      <div class="demo-left__container">
        <div>
          <div class="demo-left__title">{{ $demoTitle }}</div>
          <div class="demo-left__description">
            <p>
              {{ $demoDescription }}
            </p>
          </div>
        </div>
        <div class="demo-left__button">
          <button>Пропустить</button>
        </div>
      </div>
    </div>
    <div class="demo-main">
      <div class="demo-header">
        <svg class="demo-logo">
          <use xlink:href="/icons-pack.svg#svg-logo"></use>
        </svg>
      </div>
      <nuxt />
      <span class="demo-bg-circe"></span>
    </div>
  </div>
</template>
<script>
export default {
  name: "demo-layout",
  data() {
    return {};
  },
};
</script>