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
        <a-row :gutter="[24, 16]" type="flex" justify="space-around">
          <a-col :xs="24" :md="24" :lg="24" v-if="user.role === 'ROLE_ADMIN'">
            <a-button
              type="primary"
              @click="() => $router.push(`/news/create`)"
            >
              Добавить новость
              <a-icon type="plus" />
            </a-button>
          </a-col>
          <a-col
            :xs="24"
            :md="8"
            :lg="8"
            v-for="(item, index) in news"
            :key="index"
          >
            <a-card hoverable>
              <img
                slot="cover"
                alt="example"
                :src="`/${item.images[0]}`"
                v-if="item.images.length > 0"
              />
              <a-card-meta>
                <template slot="title">
                  <nuxt-link :to="`/news/${item.id}`">{{
                    item.title
                  }}</nuxt-link>
                </template>
                <template slot="description">
                  <div class="news-description">{{ item.short_description }}</div>
                  <div class="news-tags">
                    <div
                      v-for="(tag, index) in item.tags
                        .split(/(?:#|,| )+/)
                        .filter((el) => {
                          return (
                            String(el).length !== 0 &&
                            String(el).indexOf('#') === -1 &&
                            String(el).indexOf(',') === -1
                          );
                        })"
                      :key="index"
                      class="news-tag-inner"
                    >
                      <span class="news-tag">#{{ tag }}</span>
                    </div>
                  </div>
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
    const app: any = this;
    app.$axios
      .get(`/news`)
      .then(({ data }: any) => {
        app.news = data;
      })
      .catch((err: any) => {
        console.error(err);
      });
  },
  methods: {},
});
</script>