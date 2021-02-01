<template>
  <aside class="b-demo-sider">
    <slot></slot>
  </aside>
</template>
<style scoped>
.b-demo-sider {
  flex: 0 0 300px;
  width: 300px;
  max-width: 300px;
  background-color: #fff;
  padding: 0 20px;
}
@media screen and (max-width: 764px) {
  .b-demo-sider {
    flex: 0 0 100%;
    width: 100%;
    max-width: 100%;
    padding: 20px;
  }
}
</style>
<script lang="ts">
import Vue from "vue";
export default Vue.extend({
  name: "b-demo-sider",
  data() {
    return {};
  },
});
</script>