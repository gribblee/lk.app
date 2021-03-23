<template>
  <a-layout-content>
    <a-page-header
      :title="`Товар ${order.title}`"
      sub-title="Последние новости"
      @back="() => $router.go(-1)"
    >
    </a-page-header>
    <a-layout class="setting-layout-sider">
      <a-layout-content class="setting-layout-content">
        <a-row :gutter="[16, 16]" type="flex" justify="space-around">
          <a-col :xs="22" :md="10" :lg="10">
            <a-layout-content class="company-layout-content">
              <div class="company-layout-content__description">
                <a-card :loading="loading" title="Описание">
                  <a-card-meta :title="order.title">
                    <template slot="description">
                      <p>{{ order.description }}</p>
                      <p>{{ order.price }} ₽</p>
                    </template>
                  </a-card-meta>
                  <template slot="actions" class="ant-card-actions">
                    <a-popconfirm
                      title="Вы действительно хотите купить?"
                      ok-text="Купить"
                      cancel-text="нет"
                      @confirm="orderBuy()"
                    >
                      <a-button v-if="user.balance >= order.price"
                        >Купить</a-button
                      >
                    </a-popconfirm>
                  </template>
                </a-card>
              </div>
              <div></div>
            </a-layout-content>
          </a-col>
        </a-row>
      </a-layout-content>
    </a-layout>
  </a-layout-content>
</template>
<script lang="ts">
import Vue from "vue";
export default Vue.extend({
  name: "store-id",
  head() {
    return {
      title: "Просмотреть товар",
    };
  },
  validate({ params }) {
    return /^\d+$/.test(params.id);
  },
  data() {
    return {
      order: {
        title: "",
        short_description: "",
        description: "",
        tags: "",
        price: 0,
      },
    };
  },

  created() {
    const app: any = this;

    app.$axios
      .get(`/store/${app.$route.params.id}`)
      .then(({ data }: any) => {
        app.order = data;
      })
      .catch((err: any) => {
        console.error(err);
      });
  },
  methods: {
    orderBuy() {
      const app: any = this;
      app.$axios
        .post(`/store/${app.$route.params.id}/buy`)
        .then(({ data }: any) => {
          if (data.success) {
            app.$message.success(data.message);
          } else {
            app.$message.error(data.message);
          }
        })
        .catch((err: any) => {
          console.error(err);
        });
    },
  },
});
</script>