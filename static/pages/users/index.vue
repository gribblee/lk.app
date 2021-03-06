<template>
  <a-layout-content>
    <a-page-header
      title="Пользователи"
      sub-title="пояснение"
      @back="() => $router.go(-1)"
    />
    <div
      :style="{
        padding: '24px',
        background: '#fff',
        minHeight: '360px',
      }"
    >
      <div :style="{ padding: '20px 0 0 0' }">
        <div :style="{ display: 'flex' }">
          <div class="action-button">
            <a-button type="primary" size="large" @click="addUser">
              <a-icon type="plus" />
              Добавить пользователя
            </a-button>
          </div>
          <div :style="{ marginLeft: '20px' }">
            <a-dropdown :trigger="['click']">
              <a-menu slot="overlay" @click="handleMenuClick">
                <a-menu-item :key="1">Удалённые пользователи</a-menu-item>
                <a-menu-item :key="2">Активные пользователи</a-menu-item>
                <a-menu-item :key="3">Заблокированые пользователи</a-menu-item>
              </a-menu>
              <a-button size="large">
                Активные пользователи
                <a-icon type="down" />
              </a-button>
            </a-dropdown>
          </div>
          <div :style="{ marginLeft: '20px' }">
            <a-dropdown :trigger="['click']" :disabled="eventDisabled">
              <a-menu slot="overlay" @click="handleActionClick">
                <a-menu-item :key="1">
                  <a-icon type="delete" />
                  Удалить
                </a-menu-item>
                <a-menu-item :key="2">
                  <a-icon type="user" />
                  Востановить
                </a-menu-item>
                <a-menu-item :key="3" :style="{ backgroundColor: '#FF00000F' }">
                  <a-icon type="lock" />
                  Заблокировать
                </a-menu-item>
                <a-menu-item :key="4" :style="{ backgroundColor: '#F0F0000F' }">
                  <a-icon type="key" />
                  Разблокировать
                </a-menu-item>
              </a-menu>
              <a-button type="danger" size="large">
                Действия
                <a-icon type="down" />
              </a-button>
            </a-dropdown>
          </div>
        </div>
        <div :style="{ marginTop: '30px' }">
          <a-row :gutter="[21, 21]">
            <a-col :span="2">
              <a-input placeholder="ID" v-model="search.id" />
            </a-col>
            <a-col :span="3">
              <a-input placeholder="Имя" v-model="search.name" />
            </a-col>
            <a-col :span="3">
              <a-input placeholder="Телефон" v-model="search.phone" />
            </a-col>
            <a-col :span="4">
              <a-input placeholder="Email" v-model="search.email" />
            </a-col>
            <a-col :span="4">
              <a-input
                placeholder="Имя или Email менеджера"
                v-model="search.manager"
              />
            </a-col>
            <a-col :span="3">
              <a-select
                placeholder="Роль"
                prop-label-prop="label"
                v-model="search.role"
                :style="{ width: '100%' }"
              >
                <a-select-option :key="0" value="" label="Не выбрано"
                  >Не выбрано</a-select-option
                >
                <a-select-option :key="1" value="ROLE_USER" label="Пользователь"
                  >Пользователь</a-select-option
                >
                <a-select-option :key="1" value="ROLE_MANAGER" label="Менеджер"
                  >Менеджер</a-select-option
                >
                <a-select-option
                  :key="1"
                  value="ROLE_WEBMASTER"
                  label="Вебмастер"
                  >Вебмастер</a-select-option
                >
                <a-select-option
                  :key="1"
                  value="ROLE_ADMIN"
                  label="Администратор"
                  >Администратор</a-select-option
                >
              </a-select>
            </a-col>
            <a-col :span="3">
              <a-button type="primary" @click="handleSearch"
                >Искать <a-icon type="search"
              /></a-button>
            </a-col>
          </a-row>
        </div>
        <div :style="{ margin: '20px -24px 0 -24px' }">
          <a-table
            :columns="columns"
            :data-source="data"
            :row-selection="rowSelection"
            :loading="loading"
            :pagination="pagination"
            @change="handleTableChange"
          >
            <nuxt-link
              slot="name"
              slot-scope="text, record"
              :to="{ path: `/users/${record.id}` }"
            >
              {{ record.name }}
            </nuxt-link>
            <span slot="role" slot-scope="text">
              {{ titleRole[text] }}
            </span>
            <span slot="manager" slot-scope="text, record">
              <template v-if="record.manager != null">
                <nuxt-link
                  :to="{
                    name: 'users-id',
                    params: { id: record.manager.id },
                  }"
                  >{{ record.manager.name }}</nuxt-link
                >
              </template>
              <template v-else>Без менеджера</template>
            </span>
            <template slot="was_online" slot-scope="text">
              {{ dateFormat(text) }}
            </template>
            <template slot="created_at" slot-scope="text">
              {{ dateFormat(text) }}
            </template>
            <template slot="status" slot-scope="text, record">
              <a-tag color="red" v-if="record.is_delete">Удалён</a-tag>
              <a-tag color="purple" v-if="record.is_block">Заблокирован</a-tag>
              <a-tag color="green" v-if="!record.is_delete && !record.is_block"
                >Активен</a-tag
              >
            </template>
          </a-table>
        </div>
      </div>
    </div>
  </a-layout-content>
</template>
<script>
import moment from "moment";
const columns = [
  {
    title: "ID",
    dataIndex: "id",
    key: "id",
    defaultSortOrder: "descend",
    sortDirections: ["descend", "ascend"],
    onFilter: (value, record) => {},
    sorter: (a, b) => {},
  },
  {
    title: "Имя",
    dataIndex: "name",
    key: "name",
    scopedSlots: { customRender: "name" },
    defaultSortOrder: "descend",
    sortDirections: ["descend", "ascend"],
    onFilter: (value, record) => {},
    sorter: (a, b) => {},
  },
  {
    title: "Телефон",
    dataIndex: "phone",
    key: "phone",
    scopedSlots: { customRender: "phone" },
  },
  {
    title: "E-mail",
    dataIndex: "email",
    key: "email",
  },
  {
    title: "Роль",
    dataIndex: "role",
    key: "role",
    scopedSlots: { customRender: "role" },
  },
  {
    title: "Баланс",
    dataIndex: "balance",
    key: "balance",
    scopedSlots: { customRender: "balance" },
    defaultSortOrder: "descend",
    sortDirections: ["descend", "ascend"],
    onFilter: (value, record) => {},
    sorter: (a, b) => {},
  },
  {
    title: "Количество заявок",
    dataIndex: "bids_count",
    key: "bids_count",
    defaultSortOrder: "descend",
    sortDirections: ["descend", "ascend"],
    onFilter: (value, record) => {},
    sorter: (a, b) => {},
  },
  {
    title: "Статус",
    dataIndex: "status",
    key: "status",
    scopedSlots: { customRender: "status" },
  },
  {
    title: "Активность",
    dataIndex: "was_online",
    key: "was_online",
    scopedSlots: { customRender: "was_online" },
    defaultSortOrder: "descend",
    sortDirections: ["descend", "ascend"],
    onFilter: (value, record) => {},
    sorter: (a, b) => {},
  },
  {
    title: "Дата регистрации",
    dataIndex: "created_at",
    key: "created_at",
    scopedSlots: { customRender: "created_at" },
    defaultSortOrder: "descend",
    sortDirections: ["descend", "ascend"],
    onFilter: (value, record) => {},
    sorter: (a, b) => {},
  },
  {
    title: "Менеджер",
    dataIndex: "manager",
    key: "manager",
    scopedSlots: { customRender: "manager" },
  },
];
export default {
  middleware: "roleAdmin",
  head() {
    return {
      title: "Пользователи",
    };
  },
  data() {
    return {
      data: [],
      columns,
      selectedRows: [],
      eventDisabled: true,
      pagination: {},
      loading: false,
      order_by: "DEF",
      orderField: "id",
      search: {
        id: "",
        name: "",
        email: "",
        phone: "",
        email: "",
        role: "",
        manager: "",
      },
      titleRole: {
        ROLE_ADMIN: "Администратор",
        ROLE_WEBMASTER: "Вебмастер",
        ROLE_USER: "Пользователь",
        ROLE_MANAGER: "Менеджер",
        ROLE_ACCCOUNTANT: "Бухгалтер",
      },
      rowSelection: {
        onChange: (selectedRowKeys, selectedRows) => {
          console.log(
            `selectedRowKeys:${selectedRowKeys}`,
            "selectedRows: ",
            selectedRows
          );
          this.eventDisabled = selectedRows.length === 0;
          this.selectedRows = selectedRows;
        },
        onSelect: (record, selected, selectedRows) => {
          console.log(record, selected, selectedRows);
        },
        onSelectAll: (selected, selectedRows, changeRows) => {
          console.log(selected, selectedRows, changeRows);
        },
      },
    };
  },
  created() {
    this.loadUsers();
  },
  methods: {
    dateFormat(date) {
      return moment(date).format("DD-MM-YYYY HH:mm:ss", true);
    },
    handleTableChange(pagination, filters, sorters) {
      const pager = { ...this.pagination };
      pager.current = pagination.current;
      this.pagination = pager;
      if (typeof sorters.order != "undefined") {
        this.order_by = sorters.order == "ascend" ? "ASC" : "DESC";
      } else {
        this.order_by = "DEF";
      }
      if (typeof sorters.field != "undefined") {
        this.orderField = sorters.field;
      }
      this.loadUsers({}, pager.current);
    },
    addUser() {
      this.$axios
        .post("/user/add")
        .then(({ data }) => {
          if (data.success === true) {
            this.$router.push(`/users/${data.user_id}`);
          } else {
            this.$message.error(data.error);
          }
        })
        .catch((err) => {
          console.error(err);
        });
    },
    handleMenuClick(_e) {
      switch (_e.key) {
        case 1:
          this.loadUsers({
            is_delete: true,
          });
          break;
        case 2:
          this.loadUsers({
            is_delete: false,
          });
          break;
        case 3:
          this.loadUsers({
            is_block: true,
          });
          break;
      }
    },
    handleActionClick(_e) {
      if (_e.key == 1) {
        this.$axios
          .post("/user/delete", {
            ids: this.selectedRows,
          })
          .then(({ data }) => {
            if (data.success == true) {
              this.loadUsers({
                is_delete: false,
              });
            }
          });
      }
      if (_e.key == 2) {
        this.$axios
          .post("/user/active", {
            ids: this.selectedRows,
          })
          .then(({ data }) => {
            if (data.success == true) {
              this.loadUsers({
                is_delete: false,
              });
            }
          });
      }
      if (_e.key == 3) {
        this.$axios
          .post("/user/blocked", {
            ids: this.selectedRows,
          })
          .then(({ data }) => {
            if (data.success == true) {
              this.loadUsers({
                is_delete: false,
              });
            }
          });
      }
      if (_e.key == 4) {
        this.$axios
          .post("/user/unblocked", {
            ids: this.selectedRows,
          })
          .then(({ data }) => {
            if (data.success == true) {
              this.loadUsers({
                is_delete: false,
              });
            }
          });
      }
    },
    handleSearch(e) {
      this.loadUsers({});
    },
    loadUsers(postData = {}, currentPage = 1) {
      this.loading = true;
      this.$axios
        .post(`/users?page=${currentPage}`, {
          ...postData,
          search: this.search,
          order_by: this.order_by,
          order_field: this.orderField
        })
        .then(({ data }) => {
          const pagination = { ...this.pagination };
          this.data = data.data;
          pagination.total = data.total;
          this.pagination = pagination;
          this.loading = false;
        })
        .catch((_err) => {
          console.error(_err);
        });
    },
  },
};
</script>