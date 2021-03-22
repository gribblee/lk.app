<template>
  <a-layout-content>
    <a-page-header
      title="Создать новость"
      sub-title="Последние новости"
      @back="() => $router.go(-1)"
    ></a-page-header>

    <a-layout class="setting-layout-sider">
      <a-layout-content class="setting-layout-content"> </a-layout-content>
    </a-layout>
  </a-layout-content>
</template>
<script lang="ts">
import Vue from "vue";
export default Vue.extend({
  name: "news-create",

  data() {
    return {
      news: {
          title: '',
          short_description: '',
          description: '',
          tags: '',
      },
    };
  },

  created() {
    const { $axios }: any = this;
  },
  methods: {},
});
</script>