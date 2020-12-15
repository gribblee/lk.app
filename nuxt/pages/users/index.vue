<template>
  <a-layout-content style="margin: 20px 16px 0 16px">
    <a-page-header
      title="Пользователи"
      sub-title="пояснение"
      @back="() => $router.go(-1)"
    >
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
                    <a-icon type="user" />
                    Удалить
                  </a-menu-item>
                  <a-menu-item :key="2">
                    <a-icon type="user" />
                    Востановить
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
                    <a-input placeholder="Имя или Email менеджера" v-model="search.manager" />
                </a-col>
                <a-col :span="3">
                    <a-select placeholder="Роль" prop-label-prop="label" v-model="search.role" :style="{ width: '100%' }">
                        <a-select-option :key="0" value="" label="Не выбрано">Не выбрано</a-select-option>
                        <a-select-option :key="1" value="ROLE_USER" label="Пользователь">Пользователь</a-select-option>
                        <a-select-option :key="1" value="ROLE_MANAGER" label="Менеджер">Менеджер</a-select-option>
                        <a-select-option :key="1" value="ROLE_WEBMASTER" label="Вебмастер">Вебмастер</a-select-option>
                        <a-select-option :key="1" value="ROLE_ADMIN" label="Администратор">Администратор</a-select-option>
                    </a-select>
                </a-col>
                <a-col :span="3">
                    <a-button type="primary" @click="handleSearch">Искать <a-icon type="search" /></a-button>
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
                  <nuxt-link :to="{ name: 'users-id', params: { id: record.manager.id } }">{{ record.manager.name }}</nuxt-link>
                </template>
                <template v-else>Без менеджера</template>
              </span>
            </a-table>
          </div>
        </div>
      </div>
    </a-page-header>
  </a-layout-content>
</template>
<script>
const columns = [
  {
    title: 'ID',
    dataIndex: 'id',
    key: 'id',
  },
  {
    title: 'Имя',
    dataIndex: 'name',
    key: 'name',
    scopedSlots: { customRender: 'name' },
  },
  {
    title: 'Телефон',
    dataIndex: 'phone',
    key: 'phone',
    scopedSlots: { customRender: 'phone' },
  },
  {
    title: 'E-mail',
    dataIndex: 'email',
    key: 'email',
  },
  {
    title: 'Роль',
    dataIndex: 'role',
    key: 'role',
    scopedSlots: { customRender: 'role' },
  },
  {
    title: 'Баланс',
    dataIndex: 'balance',
    key: 'balance',
    scopedSlots: { customRender: 'balance' },
  },
  {
    title: 'Количество заявок',
    dataIndex: 'bids_count',
    key: 'bids_count',
  },
  {
      title: 'Активность',
      dataIndex: 'date_online',
      key: 'date_online'
  },
  {
    title: 'Менеджер',
    dataIndex: 'manager',
    key: 'manager',
    scopedSlots: { customRender: 'manager' },
  },
]
export default {
  middleware: 'roleAdmin',
  data() {
    return {
      data: [],
      columns,
      selectedRows: [],
      eventDisabled: true,
      pagination: {},
      loading: false,
      search: {
          id: '',
          name: '',
          email: '',
          phone: '',
          email: '',
          role: '',
          manager: '',
      },
      titleRole: {
        ROLE_ADMIN: 'Администратор',
        ROLE_WEBMASTER: 'Вебмастер',
        ROLE_USER: 'Пользователь',
        ROLE_MANAGER: 'Менеджер',
        ROLE_ACCCOUNTANT: 'Бухгалтер'
      },
      rowSelection: {
        onChange: (selectedRowKeys, selectedRows) => {
          console.log(
            `selectedRowKeys:${selectedRowKeys}`,
            'selectedRows: ',
            selectedRows
          )
          this.eventDisabled = selectedRows.length === 0
          this.selectedRows = selectedRows
        },
        onSelect: (record, selected, selectedRows) => {
          console.log(record, selected, selectedRows)
        },
        onSelectAll: (selected, selectedRows, changeRows) => {
          console.log(selected, selectedRows, changeRows)
        },
      },
    }
  },
  created() {
    this.loadUsers()
  },
  methods: {
    handleTableChange(pagination, filters, sorters) {
      const pager = { ...this.pagination };
      pager.current = pagination.current;
      this.pagination = pager;
      this.loadUsers({}, pager.current);
    },
    addUser() {
      this.$axios
        .post('/user/add')
        .then(({ data }) => {
          if (data.success === true) {
            this.$router.push(`/users/${data.user_id}`)
          } else {
            this.$message.error(data.error)
          }
        })
        .catch((err) => {
          console.error(err)
        })
    },
    handleMenuClick(_e) {
      switch (_e.key) {
        case 1:
          this.loadUsers({
            is_delete: true,
          })
          break
        case 2:
          this.loadUsers({
            is_delete: false,
          })
          break
      }
    },
    handleActionClick(_e) {
      if (_e.key == 1) {
        this.$axios
          .post('/user/delete', {
            ids: this.selectedRows,
          })
          .then(({ data }) => {
            if (data.success == true) {
              this.loadUsers({
                is_delete: false,
              })
            }
          })
      }
            if (_e.key == 2) {
        this.$axios
          .post('/user/active', {
            ids: this.selectedRows,
          })
          .then(({ data }) => {
            if (data.success == true) {
              this.loadUsers({
                is_delete: false,
              })
            }
          })
      }
    },
    handleSearch(e) {
        this.loadUsers({
            search: this.search
        });
    },
    loadUsers(postData = {}, currentPage = 1) {
      this.loading = true;
      this.$axios
        .post(`/users?page=${currentPage}`, postData)
        .then(({ data }) => {
          const pagination = { ...this.pagination };
          this.data = data.data;
          pagination.total = data.total;
          this.pagination = pagination;
          this.loading = false;
        })
        .catch((_err) => {
          console.error(_err)
        })
    },
  },
}
</script>
