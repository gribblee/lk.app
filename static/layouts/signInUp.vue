<template>
  <main class="main-signInUp">
    <div class="main-signInUp__logo">
      <img src="/logo_start.png">
    </div>
    <Nuxt />
  </main>
</template>
<script>
export default {
  name: "signInUp",
  middleware: "guest",
  mounted() {},
};
</script>