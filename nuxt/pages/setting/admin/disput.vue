<template>
  <a-layout-content>
    <a-page-header
      title="Настройки"
      sub-title="Статусы спора"
      @back="() => $router.go(-1)"
    ></a-page-header>
    <a-layout class="setting-layout-sider">
      <b-layout-sider :menu-key="$route.name" />
      <a-layout-content class="setting-layout-content">
        Content
      </a-layout-content>
    </a-layout>
  </a-layout-content>
</template>
<script lang="ts">
import Vue from "vue";
export default Vue.extend({
  name: "disput",
  head() {
    return {
      title: "Статусы спора",
    };
  },
});
</script>