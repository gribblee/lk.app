<template>
  <a-layout-content>
    <a-page-header
      title="Чек листы"
      sub-title=""
      @back="() => $router.go(-1)"
    ></a-page-header>
    <a-layout class="setting-layout-sider" ref="siderSetting">
      <b-layout-sider
        menu-key="setting-admin-checklist"
        :open-keys="['setting-admin-directory']"
      />
      <a-layout-content class="setting-layout-content">
        <a-row :gutter="[16, 16]" type="flex" justify="space-around">
          <a-col :xs="24" :md="10" :lg="10">
            <h1 class="typhography-header">Чек листы</h1>
          </a-col>
        </a-row>
      </a-layout-content>
    </a-layout>
  </a-layout-content>
</template>
<style scoped>
.typhography-header {
  font-size: 24px;
  font-weight: bold;
  text-align: center;
}
</style>
<script lang="ts">
import Vue from "vue";
export default Vue.extend({
  name: "setting-admin-checklist",
  head() {
    return {
      title: "Создание чек-листов",
    };
  },
  data() {
    return {
    };
  },
  mounted() {},
  methods: {
  },
});
</script>