<template>
  <div>
    <a-page-header title="Личный кабинет" />
    <div :style="{ padding: '24px', background: '#fff', minHeight: '360px' }">
      <a-alert
        type="error"
        :message="messageBalance"
        banner
        v-if="alertBalance"
      />
      <div :style="{ padding: '20px 0 0 0' }">
        <div style="padding: 24px margin: 0 -24px">
          <a-row :gutter="24">
            <a-col :span="6">
              <a-card style="border: 1px solid #ececec border-radius: 5px">
                <a-statistic
                  title="Бюджет"
                  :value="userBalance"
                  :precision="2"
                  suffix="₽"
                  :value-style="{ color: '#3f8600' }"
                  style="margin-right: 50px"
                >
                  <template #prefix>
                    <a-icon type="wallet" />
                  </template>
                </a-statistic>
              </a-card>
            </a-col>
            <a-col :span="6">
              <a-card style="border: 1px solid #ececec border-radius: 5px">
                <a-statistic
                  title="Количество заявок за неделю"
                  :value="statistic.DEALS_COUNT"
                  :precision="0"
                  suffix
                  class="demo-class"
                  :value-style="{
                    color:
                      statistic.DEALS_COUNT - statistic.DEALS_OLD_COUNT < 0
                        ? '#cf1322'
                        : '#3f8600',
                  }"
                >
                  <template #prefix>
                    <template
                      v-if="
                        statistic.DEALS_COUNT - statistic.DEALS_OLD_COUNT < 0
                      "
                    >
                      <a-icon type="arrow-down" />
                    </template>
                    <template
                      v-if="
                        statistic.DEALS_COUNT - statistic.DEALS_OLD_COUNT > 0
                      "
                    >
                      <a-icon type="arrow-up" />
                    </template>
                  </template>
                </a-statistic>
              </a-card>
            </a-col>
            <a-col :span="6">
              <a-card style="border: 1px solid #ececec border-radius: 5px">
                <a-statistic
                  title="Средняя стоимость заявки"
                  :value="statistic.AVG_RATE"
                  :precision="2"
                  suffix="₽"
                  class="demo-class"
                  :value-style="{
                    color:
                      statistic.AVG_RATE - statistic.AVG_OLD_RATE > 0
                        ? '#3f8600'
                        : '#cf1322',
                  }"
                >
                  <template
                    #prefix
                    v-if="statistic.AVG_RATE - statistic.AVG_OLD_RATE <= 0"
                  >
                    <a-icon type="arrow-up" />
                  </template>
                  <template
                    #prefix
                    v-if="statistic.AVG_RATE - statistic.AVG_OLD_RATE > 0"
                  >
                    <a-icon type="arrow-down" />
                  </template>
                </a-statistic>
              </a-card>
            </a-col>
            <a-col :span="6">
              <a-card style="border: 1px solid #ececec border-radius: 5px">
                <a-statistic
                  title="Потрачено сегодня"
                  :value="statistic.SPENT_TODAY"
                  :precision="2"
                  suffix="₽"
                  class="demo-class"
                  :value-style="{
                    color:
                      statistic.SPENT_TODAY - statistic.SPENT_OLDDAY < 0
                        ? '#3f8600'
                        : '#cf1322',
                  }"
                >
                  <template #prefix>
                    <a-icon
                      type="arrow-down"
                      v-if="statistic.SPENT_TODAY - statistic.SPENT_OLDDAY < 0"
                    />
                    <a-icon
                      type="arrow-up"
                      v-if="statistic.SPENT_TODAY - statistic.SPENT_OLDDAY > 0"
                    />
                  </template>
                </a-statistic>
              </a-card>
            </a-col>
          </a-row>
        </div>
        <div :style="{ padding: '20px 0 0 0' }">
          <div :style="{ display: 'flex' }">
            <div class="action-button">
              <a-button
                type="primary"
                :style="{ zIndex: '99' }"
                size="large"
                @click="handleCreatePackage"
              >
                <a-icon type="plus" />
                Запустить рекламную компанию
              </a-button>
              <a-tooltip
                :visible="helpTooltip == '1'"
                title="Чтобы начать, нажмите"
              >
                <a-button
                  type="default"
                  :style="{ zIndex: '99' }"
                  size="large"
                  @click="handleCreateBid"
                >
                  <a-icon type="plus" />
                  Получить клиентов
                </a-button>
              </a-tooltip>
              <div
                v-if="helpTooltip == '1'"
                :style="{
                  position: 'fixed',
                  left: '0',
                  top: '0',
                  width: '100%',
                  height: '100%',
                  zIndex: '98',
                  backgroundColor: 'rgba(34, 34, 34, 0.4)',
                }"
              ></div>
            </div>
            <div :style="{ marginLeft: '20px' }">
              <a-dropdown>
                <a-menu slot="overlay" @click="handleStatusClick">
                  <a-menu-item key="1" value="Активные направления"
                    >Активные направления</a-menu-item
                  >
                  <a-menu-item key="2" value="Направления на паузе"
                    >Направления на паузе</a-menu-item
                  >
                  <!-- <a-menu-item key="3" value="Успешные по количеству заявок">
                    Успешные по количеству заявок
                  </a-menu-item>
                  <a-menu-item key="4" value="Успешные по стоимости">
                    Успешные по стоимости
                  </a-menu-item> -->
                  <a-menu-divider />
                  <a-menu-item key="5" value="Все направления"
                    >Все направления</a-menu-item
                  >
                </a-menu>
                <a-button size="large">
                  {{ directionPPtext }}
                  <a-icon type="down" />
                </a-button>
              </a-dropdown>
            </div>
            <div :style="{ marginLeft: '20px' }">
              <a-dropdown :trigger="['click']" :disabled="eventDisabled">
                <a-menu slot="overlay" @click="handleActionClick">
                  <a-menu-item :key="1">
                    <a-icon type="pause-circle" />
                    Отключить
                  </a-menu-item>
                  <a-menu-item :key="2">
                    <a-icon type="play-circle" />
                    Включить
                  </a-menu-item>
                  <a-menu-divider />
                  <a-menu-item :key="3">
                    <a-icon type="delete" />
                    Удалить
                  </a-menu-item>
                </a-menu>
                <a-button type="danger" size="large">
                  Действия
                  <a-icon type="down" />
                </a-button>
              </a-dropdown>
            </div>
          </div>
          <div :style="{ marginTop: '20px' }">
            <template
              v-if="user.role == 'ROLE_MANAGER' || user.role == 'ROLE_ADMIN'"
            >
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
                      placeholder="Направление"
                      prop-label-prop="label"
                      v-model="search.direction_id"
                      :style="{ width: '100%' }"
                    >
                      <a-select-option :key="0" value="" label="Не выбрано"
                        >Не выбрано</a-select-option
                      >
                      <a-select-option
                        :key="direction.id"
                        :value="direction.id"
                        v-for="direction in directory.directions"
                        >{{ direction.name }}</a-select-option
                      >
                    </a-select>
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
                      <a-select-option
                        :key="1"
                        value="ROLE_USER"
                        label="Пользователь"
                        >Пользователь</a-select-option
                      >
                      <a-select-option
                        :key="1"
                        value="ROLE_MANAGER"
                        label="Менеджер"
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
            </template>
            <a-config-provider>
              <template #renderEmpty>
                <div style="text-align: center; padding: 20px">
                  <a-icon type="container" style="font-size: 32px" />
                  <p>Нет данных</p>
                </div>
              </template>
              <a-table
                :columns="columns"
                :data-source="data"
                :row-selection="{
                  selectedRowKeys: selectedRowKeys,
                  onChange: onChange,
                }"
                :loading="isLoading"
                :pagination="pagination"
                @change="handleTableChange"
              >
                <template slot="day_limit" slot-scope="text, record">
                  <template v-if="record.day_limit <= 0"
                    >Неограниченно</template
                  >
                  <template v-else>{{ text }}</template>
                </template>
                <div slot="status" slot-scope="text, record">
                  <template v-if="record.status == '0'">
                    <a-tooltip title="Запустить">
                      <a-button
                        type="link"
                        @click="handleStatusChange(record, true)"
                      >
                        <a-icon
                          type="pause-circle"
                          :style="{ color: '#E8523F' }"
                        />
                      </a-button>
                    </a-tooltip>
                  </template>
                  <template v-if="record.status == '1'">
                    <a-tooltip title="Остановить">
                      <a-button
                        type="link"
                        @click="handleStatusChange(record, false)"
                      >
                        <a-icon
                          type="play-circle"
                          :style="{ color: '#0A1428' }"
                        />
                      </a-button>
                    </a-tooltip>
                  </template>
                </div>
                <nuxt-link
                  slot="direction"
                  slot-scope="text, record"
                  :to="{ path: `/bids/${record.id}` }"
                >
                  {{ record.direction.name }}
                </nuxt-link>
                <span slot="spent_money" slot-scope="text, record">
                  {{ record.max_rate * record.deals_count }}
                </span>
                <template slot="user" slot-scope="text, record">
                  <template v-if="record.user">
                    <nuxt-link :to="{ path: `/users/${record.user.id}` }">
                      {{ record.user.name }}
                    </nuxt-link>
                  </template>
                </template>
              </a-table>
            </a-config-provider>
          </div>
        </div>
      </div>
    </div>
  </div>
</template>
<script>
const columns = [
  {
    title: "Направление",
    dataIndex: "direction",
    key: "direction",
    scopedSlots: { customRender: "direction" },
  },
  {
    title: "Статус",
    dataIndex: "status",
    key: "status",
    scopedSlots: { customRender: "status" },
  },
  {
    title: "Макс. Ставка",
    dataIndex: "max_rate",
    key: "max_rate",
  },
  {
    title: "Дн. лимит заявок",
    dataIndex: "day_limit",
    key: "day_limit",
    scopedSlots: { customRender: "day_limit" },
  },
  {
    title: "Заявок",
    dataIndex: "deals_count",
    key: "deals_count",
  },
  {
    title: "Потрачено",
    dataIndex: "spent_money",
    key: "spent_money",
    scopedSlots: { customRender: "spent_money" },
  },
  {
    title: "Клиент",
    dataIndex: "user",
    key: "user",
    scopedSlots: { customRender: "user" },
  },
];

export default {
  name: "home",
  head() {
    return {
      title: "Управление",
    };
  },

  head() {
    return {
      title: "Личный кабинет",
    };
  },
  data() {
    return {
      welcomeTooltip: true,
      helpTooltip: "0",
      data: [],
      pagination: {
        current: 1,
        total: 0,
      },
      columns,
      isLoading: true,
      directionPPtext: "Все направления",
      selectedRows: [],
      selectedRowKeys: [],
      userBalance: 0,
      statistic: {},
      alertBalance: false,
      eventDisabled: true,
      directory: {},
      // rowSelection: {
      //   // onChange: (selectedRowKeys, selectedRows) => {
      //   //   console.log(
      //   //     `selectedRowKeys:${selectedRowKeys}`,
      //   //     "selectedRows: ",
      //   //     selectedRows
      //   //   );
      //   //   this.eventDisabled = selectedRows.length === 0;
      //   //   this.selectedRows = selectedRows;
      //   //   this.selectedRowKeys = selectedRowKeys;
      //   // },
      //   // onSelect: (record, selected, selectedRows) => {
      //   //   console.log(record, selected, selectedRows);
      //   // },
      //   // onSelectAll: (selected, selectedRows, changeRows) => {
      //   //   console.log(selected, selectedRows, changeRows);
      //   //   this.selectedRows = selectedRows;
      //   // },
      // },
      search: {
        id: "",
        name: "",
        email: "",
        phone: "",
        email: "",
        role: "",
        direction_id: "",
        manager: "",
      },
    };
  },
  created() {
    this.$axios
      .get("/directory")
      .then(({ data }) => {
        this.directory = data;
      })
      .catch((_err) => {
        console.error(_err);
      });
    if (this.user.role != "ROLE_MANAGER" && this.user.role != "ROLE_ADMIN") {
      if (columns.length == 7) {
        columns.splice(-1, 1);
      }
    }
    this.loadTable();
  },
  mounted() {
    this.$axios.get("/directory").then(({ data }) => {
      this.alertBalance = this.user.balance < data.options.threshold_balance;
      this.messageBalance = data.options.message_balance;
    });
  },
  methods: {
    handleCreatePackage() {
      this.$router.push("/bids/package/create");
    },
    onChange(selectedRowKeys, selectedRows) {
      console.log(
        `selectedRowKeys:${selectedRowKeys}`,
        "selectedRows: ",
        selectedRows
      );
      this.eventDisabled = selectedRows.length === 0;
      this.selectedRows = selectedRows;
      this.selectedRowKeys = selectedRowKeys;
    },
    handleOkTooltip() {},
    handleEndTooltip() {},
    handleSearch() {
      this.loadTable({
        search: this.search,
        is_search: true,
      });
    },
    handleTableChange(pagination, filters, sorter) {
      this.pagination.current = pagination.current;
      this.loadTable();
    },
    loadTable(postData = {}) {
      this.isLoading = true;
      if (typeof this.$route.query.ids != "undefined") {
        postData["ids"] = this.$route.query.ids;
      }
      this.$axios
        .post(`/bids?page=${this.pagination.current}`, postData)
        .then(({ data }) => {
          this.data = data.bids.data;
          this.userBalance = data.balance;
          this.statistic = data.statistic;
          this.isLoading = false;
          this.pagination.total = data.bids.total;
          this.selectedRowKeys = [];
        })
        .catch((_err) => {
          console.error(_err);
        });
    },
    handleStatusClick(e) {
      this.directionPPtext = e.item.value;
      this.loadTable({
        status: e.key,
      });
    },
    handleActionClick(e) {
      this.$axios
        .post("/bids/action_update", {
          action: e.key,
          ids: this.selectedRows.map((curr) => {
            return curr.id;
          }),
        })
        .then(({ data }) => {
          if (data.success == true) {
            this.$message.success(data.message);
          } else {
            this.$message.error(data.message);
          }
          this.loadTable();
        })
        .catch(({ response }) => {
          if (typeof response.data.message != "undefined") {
            this.$message.error(response.data.message);
          } else {
            this.$message.error(response.data);
          }
        });
    },
    handleCreateBid(_e) {
      this.$router.push("/bids/create");
      // this.$axios
      //   .post("/bid/create")
      //   .then(({ data }) => {
      //     if (data.STATUS === "OK") {
      //       this.$router.push(`/bids/${data.ID}`);
      //     } else {
      //       this.$message.error(data.message);
      //     }
      //   })
      //   .catch((_err) => {
      //     console.error(_err);
      //   });
    },
    handleStatusChange(el, is_launch) {
      this.$axios
        .post(`/bid/${el.id}/launch`)
        .then(({ data }) => {
          if (data.code === 1004) {
            this.$error({
              title: "Ошибка",
              content: (h) => <div style="color:red;">{data.msg}</div>,
              okText: "ОК",
            });
          } else {
            el.status = data.is_launch;
            this.$message.success(
              el.status == "1" ? "Запущено" : "Остановлено"
            );
          }
        })
        .catch((_err) => {
          console.error(_err);
        });
    },
  },
};
</script>