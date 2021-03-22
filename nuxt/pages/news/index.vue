<template>
  <a-layout-content>
    <a-page-header
      title="Новости"
      sub-title="Последние новости"
      @back="() => $router.go(-1)"
    >
    </a-page-header>
    <a-layout class="setting-layout-sider">
      <a-layout-content class="setting-layout-content">
        <a-row :gutter="[16, 16]" type="flex" justify="space-around">
          <a-col :xs="24" :md="24" :lg="24" v-if="user.role === 'ROLE_ADMIN'">
            <a-button
              type="primary"
              @click="() => $router.push(`/news/create`)"
            >
              Добавить новость
              <a-icon type="plus" />
            </a-button>
          </a-col>
          <a-col :xs="8" :md="8" :lg="24" v-for="(item, index) in news" :key="index">
            <a-card hoverable>
              <a-card-meta :title="item.title">
                <template slot="description">
                  <p>{{ item.short_description }}</p> 
                  <p>{{ item.tags }}</p>
                </template>
              </a-card-meta>
            </a-card>
          </a-col>
        </a-row>
      </a-layout-content>
    </a-layout>
  </a-layout-content>
</template>
<script lang="ts">
import Vue from "vue";
export default Vue.extend({
  name: "news",

  data() {
    return {
      news: [],
    };
  },

  created() {
    const app : any = this;
    app.$axios
      .get(`/news`)
      .then(({ data }: any) => {
        app.news = data.data;
      })
      .catch((err: any) => {
        console.error(err);
      });
  },
  methods: {},
});
</script>