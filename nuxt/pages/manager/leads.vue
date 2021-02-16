<template>
  <a-layout-content>
    <a-page-header
      title="Новые пользователи"
      sub-title="Здесь отображаются зарегистрированные пользователи"
      @back="() => $router.go(-1)"
    ></a-page-header>

    <a-layout class="setting-layout-sider">
      <a-layout-content class="setting-layout-content">
        <a-row :gutter="[16, 16]" type="flex">
          <a-col
            :xs="24"
            :md="6"
            :lg="6"
            v-for="lead in newLeads"
            :key="lead.id"
          >
            <a-card :title="lead.name">
              <a-button
                slot="extra"
                type="primary"
                @click="takeLead(lead.id, $event)"
                >Забрать</a-button
              >
              <p><b>Телефон:</b> {{ lead.phone }}</p>
              <p><b>Email:</b> {{ lead.email }}</p>
              <p>
                <b>Категория:</b>
                {{
                  lead.category == null ? "Не определено" : lead.category.name
                }}
              </p>
              <p>
                <b>Регион:</b>
                {{ lead.region == null ? "Не определено" : lead.region.name }}
              </p>
              <p><b>Баланс:</b> {{ lead.balance }}</p>
              <p><b>Дата регистрации:</b> {{ dateFormat(user.created_at) }}</p>
              <p><b>Последний визит:</b> {{ dateFormat(user.was_online) }}</p>
            </a-card>
          </a-col>
        </a-row>
      </a-layout-content>
    </a-layout>
  </a-layout-content>
</template>
<script lang="ts">
import moment from "moment";
import Vue from "vue";

export default Vue.extend({
  name: "leads",
  head() {
    return {
      title: "Новые пользователи",
    };
  },
  data() {
    return {
      newLeads: [],
      pagination: {
        current: 1,
        total: 10,
      },
    };
  },
  mounted() {
    this.getLeads();
  },
  methods: {
    dateFormat(date) {
      return moment(date).format("DD-MM-YYYY HH:mm:ss", true);
    },
    onChange(pageNumber: number) {
      this.pagination.current = pageNumber;
      this.getLeads();
    },
    getLeads() {
      const { $axios, pagination } = this;
      $axios
        .get(`/manager/leads?page=${pagination.current}`)
        .then(({ data }: any) => {
          this.newLeads = data.data;
        })
        .catch((err: any) => {
          this.$message.error(err.response.message);
        });
    },
    takeLead(userId: number, e: Event) {
      const { $axios } = this;
      $axios
        .post(`/manager/lead/${userId}/take`)
        .then(({ data }: any) => {
          this.$message.success(data.message);
        })
        .catch((err: any) => {
          this.$message.error(err.response.message);
        })
        .finally(() => {
          this.getLeads();
        });
    },
  },
});
</script>