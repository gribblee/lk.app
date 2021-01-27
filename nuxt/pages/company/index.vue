<template>
  <a-layout-content>
    <a-page-header
      title="Мои компании"
      sub-title=""
      @back="() => $router.go(-1)"
    ></a-page-header>
    <a-layout class="setting-layout-sider-2">
      <a-layout-content class="setting-layout-content">
        <a-row :gutter="[16, 16]" type="flex" justify="space-around">
          <a-col :xs="24" :md="24" :lg="24">
            <a-button type="primary" @click="handleCreate">
              Добавить компанию
              <a-icon type="plus" />
            </a-button>
          </a-col>
          <a-col :xs="24" :md="24" :lg="24">
            <a-list
              item-layout="vertical"
              size="large"
              :pagination="pagination"
              :data-source="listData"
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
                  <span key="edit">
                    <nuxt-link :to="{ path: `/company/edit/${item.id}` }">
                      <a-icon type="edit" />
                      Редактировать
                    </nuxt-link>
                  </span>
                </template>
                <template slot="actions">
                  <a-popconfirm
                    title="Вы децйствительно хотите удалить компанию"
                    ok-text="Да"
                    cancel-text="Нет"
                    @confirm="confirmDelete(item.id, $event)"
                  >
                    <span key="delete">
                      <a-icon type="delete" />
                      Удалить
                    </span>
                  </a-popconfirm>
                </template>
                <img slot="extra" width="272" alt="logo" :src="item.avatar" />
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
          </a-col>
        </a-row>
      </a-layout-content>
    </a-layout>
  </a-layout-content>
</template>
<script lang="ts">
import Vue from "vue";
export default Vue.extend({
  data() {
    return {
      listData: [],
      pagination: {
        onChange: (page: any) => {
          const app: any = this;
          app.loadCompanies(page);
        },
        pageSize: 10,
        total: 1,
      },
    };
  },
  computed: {},
  head() {
    return {
      title: "Мои компании",
    };
  },
  methods: {
    handleCreate(e: Event) {
      const app: any = this;
      app.$axios
        .put("/company/create")
        .then(({ data }: any) => {
          app.$router.push(`/company/edit/${data.id}`);
        })
        .catch((err: any) => {
          app.$message.error(err.response.message);
        });
    },
    confirmDelete(id: number, e: Event) {
      const app: any = this;
      app.$axios.delete(`/company/${id}`).then(({ data }: any) => {
        app.$message.success(data.message);
        app.loadCompanies();
      });
    },
    loadCompanies(page: number = 1) {
      const app: any = this;
      app.$axios
        .get(`/me/companies?page=${page}`)
        .then(({ data }: any) => {
          app.pagination.total = data.total;
          app.listData = data.data.map((each: any) => {
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
        })
        .catch((err: any) => {});
    },
  },
  mounted() {
    const app: any = this;
    app.loadCompanies();
  },
});
</script>