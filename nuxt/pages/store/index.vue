<template>
  <a-layout-content>
    <a-page-header
      title="Магазин"
      sub-title="Последние новости"
      @back="() => $router.go(-1)"
    >
    </a-page-header>
    <a-layout class="setting-layout-sider">
      <a-layout-content class="setting-layout-content">
        <a-row :gutter="[16, 16]" type="flex" justify="start">
          <a-col :xs="24" :md="24" :lg="24" v-if="user.role === 'ROLE_ADMIN'">
            <a-button
              type="primary"
              @click="() => $router.push(`/store/create`)"
            >
              Добавить позицию
              <a-icon type="plus" />
            </a-button>
          </a-col>
          <a-col
            :xs="24"
            :md="8"
            :lg="8"
            v-for="(item, index) in store"
            :key="index"
          >
            <a-card hoverable>
              <template slot="actions" class="ant-card-actions">
                <a-button type="primary" @click="() => $router.push(`/store/${item.id}`)">Просмотерть</a-button>
                <a-popconfirm
                  title="Вы действительно хотите купить?"
                  ok-text="Купить"
                  cancel-text="нет"
                  @confirm="orderBuy(item.id)"
                >
                  <a-button v-if="user.balance >= item.price">Купить</a-button>
                </a-popconfirm>
              </template>
              <a-card-meta :title="item.title">
                <template slot="description">
                  <p>{{ item.short_description }}</p>
                  <p>{{ item.price }} ₽</p>
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
  name: "store",

  data() {
    return {
      store: [],
    };
  },

  created() {
    const app: any = this;
    app.$axios
      .get(`/store`)
      .then(({ data }: any) => {
        app.store = data.data;
      })
      .catch((err: any) => {
        console.error(err);
      });
  },
  methods: {
    orderBuy(id: any) {
      const app: any = this;
      app.$axios
        .post(`/store/${id}/buy`)
        .then(({ data }: any) => {
          if (data.success)
          {
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