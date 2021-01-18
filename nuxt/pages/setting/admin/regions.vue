<template>
  <a-layout-content>
    <a-page-header
      title="Настройки"
      sub-title=""
      @back="() => $router.go(-1)"
    ></a-page-header>
    <a-layout class="setting-layout-sider" ref="siderSetting">
      <b-layout-sider
        menu-key="setting-admin-regions"
        :open-keys="['setting-admin-directory']"
      />
      <a-layout-content class="setting-layout-content">
        <a-row :gutter="[16, 16]" type="flex" justify="space-around">
          <a-col :xs="22" :md="10" :lg="10"> </a-col>
        </a-row>
      </a-layout-content>
    </a-layout>
  </a-layout-content>
</template>
<script lang="ts">
import Vue from "vue";
export default Vue.extend({
  name: "setting-admin-regions",
  head() {
    return {
      title: "Администрирование регионы",
    };
  },
  data() {
    return {};
  },
});
</script>