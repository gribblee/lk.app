<template>
  <a-layout-content>
    <a-page-header
      :title="headTitle"
      sub-title=""
      @back="() => $router.go(-1)"
    ></a-page-header>
    <a-layout class="setting-layout-sider" ref="sliderSetting">
      <a-layout-sider class="company-layout-sider">
        <div
          class="company-layout-sider__image"
          :style="`background-image: url('${avatar}');`"
        ></div>
        <div class="company-layout-sider__name">{{ headTitle }}</div>
        <div class="company-layout-sider__rating">
          <a-rate v-model="rating" allow-half :tooltips="desc" disabled />
          <span class="ant-rate-text">{{ rateDesc }}</span>
        </div>
      </a-layout-sider>
      <a-layout-content class="company-layout-content">
        <div class="company-layout-content__username">{{ contact.name }}</div>
        <div class="company-layout-content__contact">
          <template v-if="!isMobile">
            <a-tooltip placement="top" title="Копировать номер телефона">
              <a-button
                class="company-layout-content__phone"
                v-clipboard:copy="contact.phone"
                v-clipboard:success="successClipboard"
                size="large"
                >{{ contact.phone }} <a-icon type="copy"
              /></a-button>
            </a-tooltip>
            <a-tag
              color="#f50"
              :style="{
                lineHeight: '40px',
                fontSize: '16px',
                padding: '0 15px',
              }"
              >Свяжитесь со мной!</a-tag
            >
          </template>
          <a-button
            v-if="isMobile"
            size="large"
            type="primary"
            @click="callPhone"
            >Связаться со мной</a-button
          >
        </div>
        <div class="company-layout-content__description">
          <a-card :loading="loading" title="О компании">
            {{ description }}
          </a-card>
        </div>
        <div class="company-layout-content__issues">
          <a-card :loading="loading" title="По каким вопросам помогает">
            <a-list
              class="demo-loadmore-list"
              item-layout="horizontal"
              :data-source="issues"
            >
              <a-list-item slot="renderItem" slot-scope="item, index">
                <a-list-item-meta :description="item.description">
                  <a slot="title" href="https://www.antdv.com/"
                    >{{ item.title }} / {{ item.direction.name }}</a
                  >
                </a-list-item-meta>
                <div class="company-layout-content__price">
                  <template v-if="item.priceTo > 0">
                    <span>От {{ item.priceTo.toFixed(2) }} ₽</span>
                  </template>
                  <template
                    v-if="item.priceFrom > 0 && item.priceFrom > item.priceTo"
                  >
                    <span>До {{ item.priceTo.toFixed(2) }} ₽</span>
                  </template>
                  <template v-if="item.priceTo <= 0 && item.priceFrom <= 0">
                    <span>Цена договорная</span>
                  </template>
                </div>
              </a-list-item>
            </a-list>
          </a-card>
        </div>
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
      headTitle: "Компания",
      rating: 0,
      description: "",
      isMobile: isMobile,
      contact: {
        name: '',
        phone: '',
      },
      avatar:
        "https://gw.alipayobjects.com/zos/rmsportal/mqaQswcyDLcXyDKnZfES.png",
      issues: [],
      desc: ["Ужасно", "Плохо", "Нормально", "Хорошо", "Замечательно"],
      loading: true,
    };
  },
  computed: {
    rateDesc() {
      const app: any = this;
      return app.desc[Math.round(app.rating) - 1];
    },
  },
  methods: {
    onLoadMore() {},
    successClipboard(e: Event) {
      const app: any = this;
      app.$message.success("Номер телефона скопирован");
    },
    errorClipboard(e: Event) {
      const app: any = this;
      app.$message.error("Ошибка в браузере!");
    },
    callPhone(e: Event) {
      const app: any = this;
      window.open(`tel:${app.contact.phone}`, "_blank");
    },
  },
  head() {
    const app: any = this;
    return {
      title: app.headTitle,
    };
  },
  mounted() {
    const app: any = this;
    app.$axios
      .get(`/company/${app.$route.params.id}`)
      .then(({ data }: any) => {
        app.headTitle = data.name;
        app.rating = Number((data.rating / 2000).toFixed(1));
        app.description = data.description;
        app.issues = data.issues;
        app.contact = data.user;
        if (data.files) {
          data.files = JSON.parse(data.files);
          if (data.files != null) {
            if (typeof data.files != "undefined") {
              if (data.files.length > 0) {
                app.files = data.files;
                if (typeof data.files[0].url != "undefined") {
                  app.avatar = data.files[0].url;
                } else {
                  app.avatar = data.files[0].thumbUrl;
                }
              }
            }
          }
        }

        app.loading = false;
      })
      .catch((err: any) => {
        app.$message.error(err.response.error);
      });
  },
});
</script>