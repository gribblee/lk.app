<template>
  <a-layout-content style="margin: 20px 16px 0 16px">
    <a-page-header title="Мои клиенты" @back="() => $router.go(-1)" />
    <div :style="{ background: '#fff' }">
      <div :style="{ padding: '20px 20px 0 20px' }">
        <a-form>
          <a-row :gutter="24">
            <a-col :span="6">
              <a-form-item label="ФИО">
                <a-input
                  size="large"
                  v-model="searchField.name"
                  @blur="handleSearch"
                />
              </a-form-item>
            </a-col>
            <a-col :span="6">
              <a-form-item label="Email">
                <a-input
                  size="large"
                  v-model="searchField.email"
                  @blur="handleSearch"
                />
              </a-form-item>
            </a-col>
            <a-col :span="6">
              <a-form-item label="Телефон">
                <a-input
                  size="large"
                  v-model="searchField.phone"
                  @blur="handleSearch"
                />
              </a-form-item>
            </a-col>
          </a-row>
        </a-form>
      </div>
      <div>
        <a-table :columns="columns" :data-source="data" :loading="isLoading" :pagination="pagination" @change="handleTableChange">
          <template slot="name" slot-scope="text, record">
            <nuxt-link :to="{ name: 'users-id', params: { id: record.id } }">{{
              text
            }}</nuxt-link>
          </template>
          <template slot="type_transaction" slot-scope="text, record">
            <a-tag
              :color="typeTransaction_title[record.type_transaction].color"
            >
              {{ typeTransaction_title[record.type_transaction].title }}
            </a-tag>
          </template>
          <a-table
            slot="expandedRowRender"
            slot-scope="record"
            :columns="innerColumns"
            :data-source="record.subtable"
            :pagination="false"
          >
            <template slot="type_transaction" slot-scope="text, record">
              <a-tag
                :color="typeTransaction_title[record.type_transaction].color"
              >
                {{ typeTransaction_title[record.type_transaction].title }}
              </a-tag>
            </template>
          </a-table>
        </a-table>
      </div>
    </div>
  </a-layout-content>
</template>
<script>
const innerColumns = [
  {
    title: "Тип транзакции",
    dataIndex: "type_transaction",
    key: "type_transaction",
    scopedSlots: { customRender: "type_transaction" },
  },
  {
    title: "Сумма",
    dataIndex: "paysum",
    key: "paysum",
  },
  {
    title: "Сумма бонуса",
    dataIndex: "paybonus",
    key: "paybonus",
  },
  {
    title: "Баланс до",
    dataIndex: "before_balance",
    key: "before_balance",
  },
  {
    title: "Баланс после",
    dataIndex: "after_balance",
    key: "after_balance",
  },
  {
    title: "Бонусы до",
    dataIndex: "before_bonus",
    key: "before_bonus",
  },
  {
    title: "Бонусы после",
    dataIndex: "after_bonus",
    key: "after_bonus",
  },
  {
    title: "Дата создания",
    dataIndex: "date_at",
    key: "date_at",
    scopedSlots: { customRender: "date_at" },
  },
];
const columns = [
  {
    title: "ID",
    dataIndex: "id",
    key: "id",
    scopedSlots: { customRender: "id" },
  },
  {
      title: "Статус",
      dataIndex: "status_lead",
      key: "status_lead",
      scopedSlots: { customRender: "status_lead" }
  },
  {
    title: "Имя",
    dataIndex: "name",
    key: "name",
    scopedSlots: { customRender: "name" },
  },
  {
    title: "Email",
    dataIndex: "email",
    key: "email",
    scopedSlots: { customRender: "email" },
  },
  {
    title: "Телефон",
    dataIndex: "phone",
    key: "phone",
    scopedSlots: { customRender: "phone" },
  },
  {
    title: "Баланс",
    dataIndex: "balance",
    key: "balance",
    scopedSlots: { customRender: "balance" },
    defaultSortOrder: 'descend',
    sortDirections: ['descend', 'ascend'],
    onFilter: (value, record) => {},
    sorter: (a, b) => {}
  },
  {
    title: "Бонус",
    dataIndex: "bonus",
    key: "bonus",
    scopedSlots: { customRender: "bonus" },
  },
  {
      title: 'Активность',
      dataIndex: 'date_online',
      key: 'date_online'
  },
  {
    title: "Кол-во. заявок",
    dataIndex: "bids_count",
    key: "bids_count",
    scopedSlots: { customRender: "bids_count" },
  },
  {
      title: 'Регистрация',
      dataIndex: "created_at",
      key: "created_at",
      scopedSlots: { customRender: "created_at" },
      defaultSortOrder: 'descend',
      sortDirections: ['descend', 'ascend'],
      onFilter: (value, record) => {},
      sorter: (a, b) => {}
  }
];
export default {
  data() {
    return {
      pagination: {},
      columns,
      innerColumns,
      data: [],
      isLoading: true,
      order_by: 'DEF',
      order_by_register: 'DEF',
      searchField: {
        name: null,
        email: null,
        phone: null,
      },
      typeTransaction_title: {
        0: {
          title: "Общее",
          color: "volcano",
        },
        10: {
          title: "Пополение с карты",
          color: "green",
        },
        11: {
          title: "Пополение с расчётного счёта",
          color: "green",
        },
        12: {
          title: "Покупка заявки",
          color: "red",
        },
        13: {
          title: "Покупка страховки",
          color: "blue",
        },
        14: {
          title: "Изменено администратором",
          color: "geekblue",
        },
      },
    };
  },
  created() {
    this.loadUsers();
  },
  methods: {
    handleTableChange(pagination, filters, sorters) {
      const pager = { ...this.pagination };
      pager.current = pagination.current;
      this.pagination = pager;
      if (typeof sorters.order != 'undefined' && sorters.field == 'balance') {
          this.order_by = sorters.order == 'ascend' ? 'ASC' : 'DESC';
      } else {
          this.order_by = 'DEF';
      }
      if (typeof sorters.order != 'undefined' && sorters.field == 'created_at') {
          this.order_by_register = sorters.order == 'ascend' ? 'ASC' : 'DESC';
      } else {
          this.order_by_register = 'DEF';
      }
      this.loadUsers({}, pager.current);
    },
    loadUsers(postData = {}, currentPage = 1) {
      this.isLoading = true;
      this.$axios
        .post(`/manager/users?page=${currentPage}`, {
          search: this.searchField,
          order_by: this.order_by,
          order_by_register: this.order_by_register,
        })
        .then(({ data }) => {
          const pagination = { ...this.pagination };
          this.data = data.data;
          this.isLoading = false;
          pagination.total = data.total;
          this.pagination = pagination;
        })
        .catch((err) => {
          if (typeof err.response.data.message != "undefined") {
            this.$message.error(err.response.data.message);
          } else {
            this.$message.error(err.response.data);
          }
        });
    },
    handleSearch() {
      this.loadUsers();
    },
  },
};
</script>
