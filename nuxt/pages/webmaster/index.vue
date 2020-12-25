<template>
  <a-layout-content style="margin: 20px 16px 0 16px">
    <a-page-header
      title="API Приложения"
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
              <a-button
                type="primary"
                size="large"
                @click="() => (visibleDirection = true)"
              >
                <a-icon type="plus" />
                Добавить ключ
              </a-button>
            </div>
            <div :style="{ marginLeft: '20px' }">
              <a-dropdown :trigger="['click']" :disabled="eventDisabled">
                <a-menu slot="overlay" @click="handleActionClick">
                  <a-menu-item key="1">
                    <a-icon type="user" />
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
          <div :style="{ margin: '20px -24px 0 -24px' }">
            <a-table
              :columns="columns"
              :data-source="data"
              :row-selection="rowSelection"
              :loading="loading"
            >
              <nuxt-link
                slot="name"
                slot-scope="text, record"
                :to="{ path: `/users/${record.USER_ID}` }"
              >
                {{ record.name }}
              </nuxt-link>
              <template slot="direction" slot-scope="text, record">
                {{ record.direction.name }}
              </template>
            </a-table>
          </div>
        </div>
      </div>
    </a-page-header>
    <a-modal
      v-model="visibleDirection"
      title="Настройки"
      ok-text="Добавить"
      cancel-text="Отмена"
      @ok="handleCreate"
    >
      <div class="wm-item">
        <div class="wm-label">Направление:</div>
        <a-select
          size="large"
          placeholder="Выберите направление"
          v-model="directionId"
          option-label-prop="label"
          class="wm-select"
        >
          <a-select-option
            v-for="direction in directory.directions"
            :key="direction.id"
            :value="direction.id"
            :label="direction.name"
          >
            {{ direction.name }}
          </a-select-option>
        </a-select>
      </div>
    </a-modal>
  </a-layout-content>
</template>
<style scoped>
.wm-item {
}
.wm-label {
}
.wm-select {
  width: 100%;
  margin-top: 15px;
}
</style>
<script>
const isEmpty = (x) => !Object.keys(x).length;
const columns = [
  {
    title: "ID",
    dataIndex: "id",
    key: "id",
  },
  {
    title: "API Ключ",
    dataIndex: "hash",
    key: "hash",
    scopedSlots: { customRender: "hash" },
  },
  {
    title: "Количество заявок",
    dataIndex: "count_deals",
    key: "count_deals",
    scopedSlots: { customRender: "bids_count" },
  },
  {
    title: "Направление",
    dataIndex: "direction",
    key: "direction",
    scopedSlots: { customRender: "direction" },
  },
  {
    title: "Дата создания",
    dataIndex: "created_at",
    key: "created_at",
    scopedSlots: { customRender: "created_at" },
  },
];
export default {
  data() {
    return {
      visibleDirection: false,
      data: [],
      columns,
      eventDisabled: true,
      loading: true,
      directionId: 1,
      directory: [],
      rowSelection: {
        onChange: (selectedRowKeys, selectedRows) => {
          this.selectedRows = selectedRows;
          this.eventDisabled = selectedRows.length === 0;
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
    this.$axios
      .get("/directory")
      .then(({ data }) => {
        this.directory = data;
      })
      .catch((_err) => {
        console.error(_err);
      });
    this.loadTokens();
  },
  methods: {
    loadTokens() {
      this.$axios
        .get("/webmaster/token/load")
        .then(({ data }) => {
          this.data = data;
          this.loading = false;
        })
        .catch((_err) => {
          console.error(_err);
        });
    },
    handleMenuClick(_e) {},
    handleActionClick(_v) {
      if (_v.key === "1") {
        this.loading = true;
        this.$axios
          .post("/webmaster/token/delete", {
            selectedRows: this.selectedRows,
          })
          .then(({ data }) => {
            this.loadTokens();
          })
          .catch((_err) => {
            console.error(_err);
          });
      }
    },
    handleCreate(_e) {
      this.loading = true;
      this.$axios
        .post("/webmaster/token/create", {
          directionId: this.directionId,
        })
        .then(({ data }) => {
          this.visibleDirection = false;
          this.loadTokens();
        })
        .catch((_err) => {
          console.error(_err);
        });
    },
  },
};
</script>
