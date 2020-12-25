<template>
  <a-layout-content style="margin: 20px 16px 0 16px">
    <a-page-header title="Оплаты" @back="() => $router.go(-1)" />
    <div :style="{ background: '#fff' }">
      <div :style="{ padding: '20px 0 0 0' }">
        <a-table :columns="columns" :data-source="data" :loading="isLoading" :pagination="pagination" @change="handleTableChange">
          <template slot="status" slot-scope="text">
            <a-tag :color="tagLabel[text].color">
              {{ tagLabel[text].title }}
            </a-tag>
          </template>
          <template slot="user" slot-scope="record">
            <nuxt-link :to="{ name: 'users-id', params: { id: record.id } }">{{
              record.name
            }}</nuxt-link>
          </template>
          <template slot="requisite" slot-scope="text, record">
            <span v-if="record.requisite != null">
              <a-button type="link" @click="showRequisite(record.requisite)">{{
                record.name
              }}</a-button>
            </span>
            <span v-else>{{ record.card }}</span>
          </template>
          <template slot="created_at" slot-scope="text">
            {{ text }}
          </template>
          <template slot="updated_at" slot-scope="text">
            {{ text }}
          </template>
        </a-table>
      </div>
      <a-drawer
        width="640"
        placement="right"
        :closable="false"
        :visible="visible"
        @close="onClose"
      >
        <p :style="[pStyle, pStyle2]">Реквизиты {{ requisiteData.name }}</p>
        <a-row>
          <a-col :span="24">
            <description-item title="Банк" :content="requisiteData.bank" />
          </a-col>
          <a-col :span="24">
            <description-item title="БИК" :content="requisiteData.bik" />
          </a-col>
          <a-col :span="24">
            <description-item
              title="Директор"
              :content="requisiteData.director"
            />
          </a-col>
          <a-col :span="24">
            <description-item title="ИНН" :content="requisiteData.inn" />
          </a-col>
          <a-col :span="24">
            <description-item
              title="Юр. адрес"
              :content="requisiteData.jour_address"
            />
          </a-col>
          <a-col :span="24">
            <description-item title="КПП" :content="requisiteData.kpp" />
          </a-col>
          <a-col :span="24">
            <description-item title="К/СЧ" :content="requisiteData.ksch" />
          </a-col>
          <a-col :span="24">
            <description-item title="ОГРН" :content="requisiteData.ogrn" />
          </a-col>
          <a-col :span="24">
            <description-item
              title="Почтовый адрес"
              :content="requisiteData.poste_address"
            />
          </a-col>
        </a-row>
      </a-drawer>
    </div>
  </a-layout-content>
</template>
<script>
const columns = [
  {
    title: "ID",
    dataIndex: "id",
    key: "id",
  },
  {
    title: "Статус",
    dataIndex: "status",
    key: "status",
    scopedSlots: { customRender: "status" },
  },
  {
    title: "Идентификатор платежа",
    dataIndex: "payment_id",
    key: "payment_id",
  },
  {
    title: "Пользователь",
    dataIndex: "user",
    key: "user",
    scopedSlots: { customRender: "user" },
  },
  {
    title: "Реквизиты",
    dataIndex: "requisite",
    key: "requisite",
    scopedSlots: { customRender: "requisite" },
  },
  {
    title: "Сумма пополнения",
    dataIndex: "paysum",
    key: "paysum",
    scopedSlots: { customRender: "paysum" },
  },
  {
    title: "Баланс до",
    dataIndex: "before_balance",
    key: "before_balance",
    scopedSlots: { customRender: "before_balance" },
  },
  {
    title: "Баланс после",
    dataIndex: "after_balance",
    key: "after_balance",
    scopedSlots: { customRender: "after_balance" },
  },
  {
    title: "Дата создания",
    dataIndex: "created_at",
    key: "created_at",
    scopedSlots: { customRender: "created_at" },
  },
  {
    title: "Дата обновления",
    dataIndex: "updated_at",
    key: "updated_at",
    scopedSlots: { customRender: "updated_at" },
  },
];
export default {
  data() {
    return {
      columns,
      data: [],
      isLoading: true,
      pagination: {},
      tagLabel: {
        101: {
          title: "Не оплачено",
          color: "red",
        },
        102: {
          title: "Частичная оплата",
          color: "blue",
        },
        103: {
          title: "Отменён",
          color: "volcano",
        },
        105: {
          title: "Оплачено",
          color: "green",
        },
        201: {
          title: "Не оплачено",
          color: "red",
        },
        205: {
          title: "Оплачено",
          color: "green",
        },
        300: {
          title: "Изменено администратором",
          color: "geekblue",
        },
        401: {
          title: "Заявка рассрочки",
          color: "yellow",
        },
        402: {
          title: "Рассрочка одобрена",
          color: "geekblue",
        },
        403: {
          title: "Отказ в рассрочке",
          color: "red",
        },
        404: {
          title: "Заявка отменена",
          color: "blue",
        },
        405: {
          title: "Договор подписан",
          color: "green",
        },
      },
      visible: false,
      requisiteData: {
        name: "",
        bank: "",
        bik: "",
        created_at: "",
        director: "",
        inn: "",
        jour_address: "",
        kpp: "",
        ksch: "",
        ogrn: "",
        poste_address: "",
      },
      pStyle: {
        fontSize: "16px",
        color: "rgba(0,0,0,0.85)",
        lineHeight: "24px",
        display: "block",
        marginBottom: "16px",
      },
      pStyle2: {
        marginBottom: "24px",
      },
    };
  },
  created() {
    this.loadStory();
  },
  methods: {
    handleTableChange(pagination, filters, sorters) {
      const pager = { ...this.pagination };
      pager.current = pagination.current;
      this.pagination = pager;
      this.loadStory({}, pager.current);
    },
    loadStory(psotData = {}, current = 1) {
      this.isLoading = true;
      this.$axios
        .get(`/payment/history?page=${current}`)
        .then(({ data }) => {
          const pagination = { ...this.pagination };
          this.data = data.data;
          pagination.total = data.total;
          this.pagination = pagination;
          this.isLoading = false;
        })
        .catch((err) => {
          if (typeof err.response.data.message != "undefined") {
            this.$message.error(err.response.data.message);
          } else {
            this.$message.error(err.response.data);
          }
        });
    },
    showRequisite(data) {
      this.requisiteData = data;
      this.visible = true;
    },
    onClose() {
      this.visible = false;
    },
  },
};
</script>
