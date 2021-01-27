<template>
  <a-layout-content>
    <a-page-header
      title="Топ компаний"
      sub-title="Эти профессионалы могут Вам помочь"
      @back="() => $router.go(-1)"
    ></a-page-header>
    <a-layout class="setting-layout-sider-2">
      <a-layout-content class="setting-layout-content">
        <a-row :gutter="[16, 16]" type="flex" justify="space-around">
          <a-col :xs="24" :md="24" :lg="24">
            <div class="company-layout-content__actions">
              <a-select
                placeholder="Выберите регион"
                v-model="regionId"
                option-label-prop="label"
                class="company-layout-content__action"
                @change="handleRegion"
              >
                <a-select-option
                  v-for="region in regions"
                  :key="region.id"
                  :value="region.id"
                  :label="region.name_with_type"
                >
                  {{ region.name_with_type }}
                </a-select-option>
              </a-select>
            </div>
          </a-col>
          <a-col :xs="24" :md="12" :lg="12">
            <div class="company-layout-content__left">
              <span class="company-layout-content__header"
                >По {{ withRegion.name_with_type }}</span
              >
              <a-list
                item-layout="vertical"
                size="large"
                :pagination="paginationRegion"
                :data-source="listWithRegion"
              >
                <a-list-item
                  slot="renderItem"
                  :key="item.id"
                  slot-scope="item, index"
                >
                  <template slot="actions">
                    <span key="stars">
                      <a-tooltip placement="top" title="Рейтинг">
                        <a-icon type="star" />
                        {{ item.rating }}
                      </a-tooltip>
                    </span>
                  </template>
                  <template slot="actions">
                    <div class="company-layout-content__contact">
                      <template v-if="!isMobile">
                        <a-tooltip
                          placement="top"
                          title="Копировать номер телефона"
                        >
                          <a-button
                            v-clipboard:copy="item.user.phone"
                            v-clipboard:success="successClipboard"
                            >{{ item.user.phone }} <a-icon type="copy"
                          /></a-button>
                        </a-tooltip>
                        <a-tag
                          color="#f50"
                          :style="{
                            lineHeight: '32px',
                            fontSize: '16px',
                            padding: '0 15px',
                          }"
                          >Свяжитесь со мной!</a-tag
                        >
                      </template>
                      <a-button
                        v-if="isMobile"
                        type="primary"
                        @click="callPhone(item.user.phone, $event)"
                        >Связаться со мной</a-button
                      >
                    </div>
                  </template>
                  <template slot="actions" v-if="user.role === 'ROLE_ADMIN'">
                    <span key="edit">
                      <nuxt-link :to="{ path: `/company/edit/${item.id}` }">
                        <a-icon type="edit" />
                        Редактировать
                      </nuxt-link>
                    </span>
                  </template>
                  <img slot="extra" width="200" alt="logo" :src="item.avatar" />
                  <a-list-item-meta :description="item.address">
                    <nuxt-link
                      slot="title"
                      :to="{ path: `/company/${item.id}` }"
                      >{{ item.name }}</nuxt-link
                    >
                  </a-list-item-meta>
                  {{ item.description }}
                </a-list-item>
              </a-list>
            </div>
          </a-col>
          <a-col :xs="24" :md="12" :lg="12">
            <div class="company-layout-content__right">
              <span class="company-layout-content__header">По всей России</span>
              <a-list
                item-layout="vertical"
                size="large"
                :pagination="paginationCountry"
                :data-source="listWithConutry"
              >
                <a-list-item
                  slot="renderItem"
                  :key="item.id"
                  slot-scope="item, index"
                >
                  <template slot="actions">
                    <span key="stars">
                      <a-tooltip placement="top" title="Рейтинг">
                        <a-icon type="star" />
                        {{ item.rating }}
                      </a-tooltip>
                    </span>
                  </template>
                  <template slot="actions">
                    <div class="company-layout-content__contact">
                      <template v-if="!isMobile">
                        <a-tooltip
                          placement="top"
                          title="Копировать номер телефона"
                        >
                          <a-button
                            v-clipboard:copy="item.user.phone"
                            v-clipboard:success="successClipboard"
                            >{{ item.user.phone }} <a-icon type="copy"
                          /></a-button>
                        </a-tooltip>
                        <a-tag
                          color="#f50"
                          :style="{
                            lineHeight: '32px',
                            fontSize: '16px',
                            padding: '0 15px',
                          }"
                          >Свяжитесь со мной!</a-tag
                        >
                      </template>
                      <a-button
                        v-if="isMobile"
                        type="primary"
                        @click="callPhone(item.user.phone, $event)"
                        >Связаться со мной</a-button
                      >
                    </div>
                  </template>
                  <template slot="actions" v-if="user.role === 'ROLE_ADMIN'">
                    <span key="edit">
                      <nuxt-link :to="{ path: `/company/edit/${item.id}` }">
                        <a-icon type="edit" />
                        Редактировать
                      </nuxt-link>
                    </span>
                  </template>
                  <img slot="extra" width="200" alt="logo" :src="item.avatar" />
                  <a-list-item-meta :description="item.address">
                    <nuxt-link
                      slot="title"
                      :to="{ path: `/company/${item.id}` }"
                      >{{ item.name }}</nuxt-link
                    >
                  </a-list-item-meta>
                  {{ item.description }}
                </a-list-item>
              </a-list>
            </div>
          </a-col>
        </a-row>
      </a-layout-content>
    </a-layout>
  </a-layout-content>
</template>
<script lang="ts">
import Vue from "vue";
import { isMobile } from "mobile-device-detect";

export default Vue.extend({
  data() {
    return {
      isMobile: isMobile,
      regionId: 61,
      regions: [],
      withRegion: {
        name_with_type: "",
      },
      listWithRegion: [],
      listWithConutry: [],
      paginationCountry: {
        onChange: (page: any) => {
          const app: any = this;
          app.loadListCountry(page);
        },
        pageSize: 10,
        total: 1,
      },
      paginationRegion: {
        onChange: (page: any) => {
          const app: any = this;
          app.loadListRegion(page);
        },
        pageSize: 10,
        total: 1,
      },
    };
  },
  computed: {},
  methods: {
    callPhone(phone: any, e: Event) {
      const app: any = this;
      window.open(`tel:${phone}`, "_blank");
    },
    successClipboard(e: Event) {
      const app: any = this;
      app.$message.success("Номер телефона скопирован");
    },
    errorClipboard(e: Event) {
      const app: any = this;
      app.$message.error("Ошибка в браузере!");
    },
    loadListCountry(page: number = 1) {
      const app: any = this;
      app.$axios.get(`/companies?page=${page}`).then(({ data }: any) => {
        app.paginationCountry.total = data.total;
        app.listWithConutry = data.data.map((each: any) => {
          if (each.description.length > 200) {
            each.description = each.description.substr(0, 187) + "...";
          }
          each.avatar =
            "https://gw.alipayobjects.com/zos/rmsportal/mqaQswcyDLcXyDKnZfES.png";
          if (each.files) {
            each.files = JSON.parse(each.files);
            if (each.files != null) {
              if (typeof each.files[0] != "undefined") {
                if (typeof each.files[0].url != "undefined") {
                  each.avatar = each.files[0].url;
                } else {
                  each.avatar = each.files[0].thumbUrl;
                }
              }
            }
          }
          return each;
        });
      });
    },
    loadListRegion(page: number = 1) {
      const app: any = this;
      app.$axios
        .get(`/companies/${app.regionId}?page=${page}`)
        .then(({ data }: any) => {
          app.listWithRegion = data.data.map((each: any) => {
            if (each.description.length > 200) {
              each.description = each.description.substr(0, 187) + "...";
            }
            each.avatar =
              "https://gw.alipayobjects.com/zos/rmsportal/mqaQswcyDLcXyDKnZfES.png";
            if (each.files) {
              each.files = JSON.parse(each.files);
              if (each.files != null) {
                if (typeof each.files[0] != "undefined") {
                  if (typeof each.files[0].url != "undefined") {
                    each.avatar = each.files[0].url;
                  } else {
                    each.avatar = each.files[0].thumbUrl;
                  }
                }
              }
            }
            return each;
          });
        });
    },
    handleRegion(e: Event) {
      const app: any = this;
      app.withRegion = app.regions.find((f: any) => f.id === app.regionId);
      app.loadListRegion();
    },
  },
  head() {
    return {
      title: "Компании",
    };
  },
  mounted() {
    const app: any = this;
    app.regionId = app.user.region.id;
    app.withRegion = app.user.region;
    app.$axios
      .get("/directory")
      .then(({ data }: any) => {
        app.regions = data.regions;
      })
      .catch((_err: any) => {
        console.error(_err);
      });

    app.loadListCountry();
    app.loadListRegion();
  },
});
</script>