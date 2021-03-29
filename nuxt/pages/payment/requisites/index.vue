<template>
  <a-layout-content style="margin: 20px 16px 0 16px">
    <a-page-header title="Счета по реквизитам" @back="() => $router.go(-1)" />
    <div :style="{ padding: '24px', background: '#fff', minHeight: '360px' }">
      <div :style="{ padding: '20px 0 0 0' }">
        <a-row :gutter="[20, 4]">
          <a-col :span="4" v-for="(field, index) in searchField" :key="index">
            <a-form-item :label="searchField[index].label">
              <a-input type="text" v-model="searchField[index].value" />
            </a-form-item>
          </a-col>
        </a-row>
        <a-row :gutter="[24, 4]">
          <a-col :span="24" :style="{ textAlign: 'right' }">
            <a-button type="primary" @click="submitSearch"
              >Искать <a-icon type="search"
            /></a-button>
          </a-col>
        </a-row>
        <div :style="{ display: 'flex', width: '100%', marginTop: '20px' }">
          <div>
            <a-dropdown :trigger="['click']">
              <a-menu slot="overlay" @click="handleActionClick">
                <a-menu-item :key="1" value="Сортировка создано"
                  >Сортировка создано</a-menu-item
                >
                <a-menu-item :key="2" value="Сортировка частичная оплата"
                  >Сортировка частичная оплата</a-menu-item
                >
                <a-menu-item :key="7" value="Фильтр оплачено"
                  >Фильтр оплачено</a-menu-item
                >
                <a-menu-divider />
                <a-menu-item
                  :key="3"
                  value="Частичная оплата"
                  :disabled="eventDisabled"
                  >Частичная оплата</a-menu-item
                >
                <a-menu-item :key="4" value="Оплачено" :disabled="eventDisabled"
                  >Оплачено</a-menu-item
                >
                <a-menu-item :key="5" value="Отменить" :disabled="eventDisabled"
                  >Отменить</a-menu-item
                >
                <a-menu-divider />
                <a-menu-item :key="6" value="Все реквизиты"
                  >Все реквизиты</a-menu-item
                >
              </a-menu>
              <a-button type="danger" size="large">
                {{ actionPPtext }}
                <a-icon type="down" />
              </a-button>
            </a-dropdown>
          </div>
        </div>
        <div :style="{ margin: '20px -24px 0 -24px' }">
          <a-table
            :columns="columns"
            :row-selection="rowSelection"
            :data-source="data"
            :loading="isLoading"
          >
            <template slot="requisite_payment_id" slot-scope="text, record">
              {{ record.requisite.requisite_payment_id }}
            </template>
            <template slot="status" slot-scope="text">
              <a-tag :color="tagLabel[text].color">
                {{ tagLabel[text].title }}
              </a-tag>
            </template>
            <template slot="user" slot-scope="record">
              <nuxt-link
                :to="{ name: 'users-id', params: { id: record.id } }"
                >{{ record.name }}</nuxt-link
              >
            </template>
            <template slot="requisite" slot-scope="record">
              <a-button type="link" @click="showRequisite(record)">{{
                record.name
              }}</a-button>
            </template>
            <template slot="created_at" slot-scope="text">
              {{ text }}
            </template>
            <template slot="updated_at" slot-scope="text">
              {{ text }}
            </template>
          </a-table>
        </div>
      </div>
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
          <b-description-item title="Банк" :content="requisiteData.bank" />
        </a-col>
        <a-col :span="24">
          <b-description-item title="БИК" :content="requisiteData.bik" />
        </a-col>
        <a-col :span="24">
          <b-description-item
            title="Директор"
            :content="requisiteData.director"
          />
        </a-col>
        <a-col :span="24">
          <b-description-item title="ИНН" :content="requisiteData.inn" />
        </a-col>
        <a-col :span="24">
          <b-description-item
            title="Юр. адрес"
            :content="requisiteData.jour_address"
          />
        </a-col>
        <a-col :span="24">
          <b-description-item title="КПП" :content="requisiteData.kpp" />
        </a-col>
        <a-col :span="24">
          <b-description-item title="К/СЧ" :content="requisiteData.ksch" />
        </a-col>
        <a-col :span="24">
          <b-description-item title="ОГРН" :content="requisiteData.ogrn" />
        </a-col>
        <a-col :span="24">
          <b-description-item
            title="Почтовый адрес"
            :content="requisiteData.poste_address"
          />
        </a-col>
      </a-row>
    </a-drawer>
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
    dataIndex: "requisite_payment_id",
    key: "requisite_payment_id",
    scopedSlots: { customRender: "requisite_payment_id" },
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
    title: "Баланс до пополнения",
    dataIndex: "before_balance",
    key: "before_balance",
    scopedSlots: { customRender: "before_balance" },
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
      data: [],
      columns,
      isLoading: true,
      actionPPtext: "Все реквизиты",
      visible: false,
      selectedRows: [],
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
      searchField: {
        name: {
          label: "Имя",
          value: "",
        },
        bank: {
          label: "Банк",
          value: "",
        },
        bik: {
          label: "БИК",
          value: "",
        },
        director: {
          label: "Директор",
          value: "",
        },
        inn: {
          label: "ИНН",
          value: "",
        },
        jour_address: {
          label: "Юр. адрес",
          value: "",
        },
        kpp: {
          label: "КПП",
          value: "",
        },
        ksch: {
          label: "К/СЧ",
          value: "",
        },
        ogrn: {
          label: "ОГРН",
          value: "",
        },
        poste_address: {
          label: "Почтовый адрес",
          value: "",
        },
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
      tagLabel: {
        101: {
          title: "Создано",
          color: "geekblue",
        },
        102: {
          title: "Частичная оплата",
          color: "red",
        },
        105: {
          title: "Оплачено",
          color: "green",
        },
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
      eventDisabled: true,
    };
  },
  created() {
    this.loadTable();
  },
  mounted() {
    this.isLoading = false;
  },
  methods: {
    updateTable(postData) {
      this.$axios
        .post("/payment/requisites/update", postData)
        .then(({ data }) => {
          if (data.success) {
            this.loadTable();
            this.$message.success(data.message);
          } else {
            this.$message.error(data.message);
          }
        })
        .catch((err) => {
          if (typeof err.response.data.message != "undefined") {
            this.$message.error(err.response.data.message);
          } else {
            this.$message.error(err.response.data);
          }
        });
    },
    loadTable(postData = {}) {
      this.$axios
        .post("/payment/requisites", postData)
        .then(({ data }) => {
          this.data = data.data;
        })
        .catch((err) => {
          if (typeof err.response.data.message != "undefined") {
            this.$message.error(err.response.data.message);
          } else {
            this.$message.error(err.response.data);
          }
        });
    },
    submitSearch(e) {
      let searchData = {};
      Object.keys(this.searchField).forEach((key) => {
        if (this.searchField[key].value.length > 0) {
          return (searchData[key] = this.searchField[key].value);
        }
      });
      if (Object.keys(searchData).length > 0) {
        this.loadTable({
          search: searchData,
        });
        this.actionPPtext = "Поиск по форме";
      }
    },
    clearSearch(e) {
      this.loadTable();
    },
    handleActionClick(e) {
      if (e.key == 3 || e.key == 4 || e.key == 5) {
        this.updateTable({
          action: e.key,
          ids: this.selectedRows.map((curr) => {
            return curr.id;
          }),
        });
      } else {
        this.actionPPtext = e.item.value;
        this.loadTable({
          action: e.key,
        });
      }
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
