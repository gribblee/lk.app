<template>
  <a-layout-content style="margin: 20px 16px 0 16px">
    <a-page-header title="Клиенты" sub-title="" @back="() => $router.go(-1)">
      <div slot="extra"></div>
    </a-page-header>
    <div
      :style="{
        padding: '24px',
        background: '#fff',
        minHeight: '360px',
      }"
    >
      <div :style="{ margin: '20px 0px 0 0px' }">
        <a-form>
          <a-row :gutter="24">
            <a-col :span="6">
              <a-form-item label="ФИО">
                <a-input
                  size="large"
                  v-model="searchField.name"
                  @keyup.enter="handleSearch"
                  @blur="handleSearch"
                />
              </a-form-item>
            </a-col>
            <a-col :span="6">
              <a-form-item label="Статус">
                <a-select
                  size="large"
                  v-model="searchField.status_id"
                  @change="handleSearch"
                >
                  <a-select-option value>Любой статус</a-select-option>
                  <a-select-option
                    v-for="(item, index) in $directory.status"
                    v-if="
                      item.type.toString() != '1004' &&
                      item.type.toString() != '1003'
                    "
                    :key="index"
                    :value="item.id"
                    >{{ item.name }}</a-select-option
                  >
                  <!-- <a-select-option value="1">Начальная</a-select-option>
                  <a-select-option value="2">Заключён договор</a-select-option>
                  <a-select-option value="3">Спорная</a-select-option>
                  <a-select-option value="4">Закрыто</a-select-option> -->
                </a-select>
              </a-form-item>
            </a-col>
            <a-col :span="6">
              <a-form-item label="Регион">
                <a-select
                  size="large"
                  v-model="searchField.region_id"
                  @change="handleSearch"
                >
                  <a-select-option value>Все регионы</a-select-option>
                  <a-select-option
                    v-for="(item, index) in $directory.regions"
                    :key="index"
                    :value="item.id"
                    >{{ item.name }}</a-select-option
                  >
                </a-select>
              </a-form-item>
            </a-col>
            <a-col :span="6">
              <a-form-item label="Направление">
                <a-select
                  size="large"
                  v-model="searchField.direction_id"
                  @change="handleSearch"
                >
                  <a-select-option value>Все направления</a-select-option>
                  <a-select-option
                    v-for="(item, index) in $directory.directions"
                    :key="index"
                    :value="item.id"
                    >{{ item.name }}</a-select-option
                  >
                </a-select>
              </a-form-item>
            </a-col>
          </a-row>
          <a-row
            v-if="user.role == 'ROLE_MANAGER' || user.role == 'ROLE_ADMIN'"
          >
            <a-col :span="6">
              <a-form-item label="На кого распределён">
                <a-input
                  size="large"
                  v-model="searchField.user_name"
                  @blur="handleSearch"
                />
              </a-form-item>
            </a-col>
            <a-col :span="6">
              <a-form-item label="Дата" :style="{marginLeft: '20px'}">
                <a-range-picker
                  size="large"
                  v-model="searchField.datePicker"
                  @blur="handleSearch"
                  @keyup.enter="handleSearch"
                />
              </a-form-item>
            </a-col>
          </a-row>
        </a-form>
      </div>
      <div :style="{ margin: '20px -24px 0 -24px' }">
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
            :loading="dealsIsLoading"
            :pagination="pagination"
            @change="handleTableChange"
          >
            <a
              @click.prevent="showDrawer(record.deal_id, record, $event)"
              href="javascript:;"
              slot="name"
              slot-scope="text, record"
              :to="{ path: `/deals/${record.deal_id}` }"
              >{{ record.name }}</a
            >
            <span slot="status_name" slot-scope="text, record">
              <a-dropdown
                :trigger="['click']"
                v-if="user.role != 'ROLE_MANAGER'"
              >
                <a class="ant-dropdown-link" @click="(e) => e.preventDefault()">
                  {{ record.status.name }}
                </a>
                <a-menu
                  slot="overlay"
                  @click="handleItemStatus(record, $event)"
                >
                  <a-menu-item
                    v-for="(item, index) in $directory.status"
                    :key="index"
                    :value="item"
                    v-if="
                      (item.type === 1003 &&
                        record.disput_count === 0 &&
                        record.is_insurance == 1) ||
                      (item.type !== 1003 && item.type !== 1004)
                    "
                    >{{ item.name }}</a-menu-item
                  >
                </a-menu>
              </a-dropdown>
              <span v-else>
                {{ record.status.name }}
              </span>
            </span>
            <span slot="region" slot-scope="text, record">
              <template v-if="record.region">
                {{ record.region && record.region.name_with_type }}
              </template>
              <template v-else> Регион не определён </template>
            </span>
            <span slot="direction" slot-scope="text, record">{{
              record.direction.name
            }}</span>
            <span slot="is_view" slot-scope="text, record">
              <a-tag v-if="record.is_view == false" color="volcano"
                >Новое</a-tag
              >
              <a-tag v-if="record.is_view == true" color="green"
                >Просмотрено</a-tag
              >
              <a-tag v-if="record.disput_count > 0" color="geekblue"
                >Спор закрыт</a-tag
              >
              <a-tag v-if="record.is_insurance" color="blue"
                >По страховке</a-tag
              >
            </span>
            <template slot="bids" slot-scope="text, record">
              <template
                v-if="user.role == 'ROLE_ADMIN' || user.role == 'ROLE_MANAGER'"
              >
                <template v-if="record.bids === null">
                  Не распределён
                </template>
                <template v-else>
                  <nuxt-link
                    :to="{
                      name: 'users-id',
                      params: { id: record.bids.user.id },
                    }"
                    >{{ record.bids.user.name }}</nuxt-link
                  >
                </template>
              </template>
            </template>
            <template slot="created_at" slot-scope="text, record">
              {{ getDate(record.created_at) }}
            </template>
            <template slot="amount" slot-scope="text, record">
              <template
                v-if="user.role == 'ROLE_ADMIN' || user.role == 'ROLE_MANAGER'"
              >
                {{ record.amount }} ₽
              </template>
            </template>
          </a-table>
        </a-config-provider>
      </div>
    </div>
    <a-drawer
      width="640"
      placement="right"
      :closable="false"
      :visible="visible"
      @close="onClose"
    >
      <p :style="[pStyle, pStyle2]">Клиент #{{ dealData.deal_id }}</p>
      <p :style="pStyle">Информация</p>
      <a-row>
        <a-col :span="24" v-if="user.role != 'ROLE_MANAGER'">
          <div v-if="dealData.status.type === 1003">
            <a-alert
              message="Заявка находиться в статусе спорная, принмается решением администратором"
              type="info"
              show-icon
            />
          </div>
          <div v-else>
            <a-radio-group v-model="dealStatus" @change="onHandleStatus">
              <a-radio-button
                v-for="(status_item, index) in $directory.status"
                :value="status_item"
                :key="index"
                v-if="
                  (status_item.type === 1003 &&
                    dealData.disput === null &&
                    dealData.is_insurance) ||
                  (status_item.type !== 1000 &&
                    status_item.type !== 1003 &&
                    status_item.type !== 1004)
                "
                color="red"
                >{{ status_item.name }}</a-radio-button
              >
              <!-- <a-radio-button value="2">Заключён договор</a-radio-button>
              <a-radio-button value="3" v-if="dealData.disput === null">Спорная</a-radio-button>
              <a-radio-button value="4">Закрыто</a-radio-button>-->
            </a-radio-group>
          </div>
        </a-col>
      </a-row>
      <a-skeleton
        :loading="dealData.status.type === 1003"
        :paragraph="{ rows: 3 }"
      >
        <a-row :style="{ marginTop: '20px' }">
          <a-col
            :span="24"
            v-if="dealData.is_insurance > 0 && dealData.disput === null"
            :style="{ padding: '20px 0' }"
          >
            <a-alert
              message="Даная заявка получена по страховке, это означает что если заявка некачественная вы можете отправить её в спор"
              type="info"
              show-icon
            />
          </a-col>
          <a-col :span="12">
            <b-description-item title="ФИО" :content="dealData.name" />
          </a-col>
          <a-col :span="12">
            <b-description-item title="E-mail" :content="dealData.email" />
          </a-col>
        </a-row>
        <a-row>
          <a-col :span="12">
            <template v-if="dealData.region">
              <b-description-item
                title="Регион"
                :content="dealData.region.name_with_type"
              />
            </template>
            <template v-else>
              <b-description-item
                title="Регион"
                content="Регион не определён"
              />
            </template>
          </a-col>
          <a-col :span="12">
            <b-description-item
              title="Направление"
              :content="dealData.bids.direction.name"
            />
          </a-col>
        </a-row>
        <a-row>
          <a-col :span="12">
            <b-description-item title="Телефон" :content="dealData.phone" />
          </a-col>
          <a-col :span="12">
            <b-description-item
              title="Стоимость"
              :content="`${dealData.amount} ₽`"
            />
          </a-col>
        </a-row>
        <tempalte
          v-if="user.role === 'ROLE_ADMIN' || user.role === 'ROLE_WEBMASTER'"
        >
          <div :style="{ marginTop: '20px' }">
            <p :style="pStyle">UTM метки</p>
            <a-row :style="{ marginTop: '20px' }">
              <a-col :span="12" v-for="(item, index) in utms" :key="index">
                <b-description-item :title="index" :content="item" />
              </a-col>
            </a-row>
          </div>
          <div :style="{ marginTop: '20px' }">
            <p :style="pStyle">Источник/запросы</p>
            <a-row :style="{ marginTop: '20px' }">
              <a-col :span="12">
                <b-description-item title="Источник" :content="refererDeal" />
              </a-col>
            </a-row>
            <a-row>
              <a-col
                :span="12"
                v-for="(item, index) in requestDeal"
                :key="index"
              >
                <p :style="{ fontSize: '14px', fontWeight: 'bold' }">
                  {{ index }}
                </p>
                <b-description-item
                  v-for="(itm, idx) in item"
                  :key="idx"
                  :title="idx"
                  :content="itm"
                />
              </a-col>
            </a-row>
          </div>
        </tempalte>
      </a-skeleton>
      <a-divider />
      <p :style="pStyle">Запись звонков</p>
      <a-row>
        <a-col :span="24">
          <a-upload-dragger
            name="file"
            :multiple="true"
            :action="uploadURL"
            :file-list="fileList"
            :remove="handleRemoveFile"
            :headers="{ Authorization: $auth.getToken('local') }"
            @change="handleUpload"
          >
            <p class="ant-upload-drag-icon">
              <a-icon type="inbox" />
            </p>
            <p class="ant-upload-text">Нажмите или перекиньте файлы звонков</p>
            <p class="ant-upload-hint">Загрузить</p>
          </a-upload-dragger>
        </a-col>
      </a-row>
    </a-drawer>
    <a-modal
      v-model="modalVisible"
      title="Укажите причину статуса"
      on-ok="handleOk"
    >
      <div slot="footer">
        <a-button key="back" @click="handleCancel">Отмена</a-button>
        <a-button key="submit" type="primary" @click="handleOk"
          >Отправить</a-button
        >
      </div>
      <div>
        <p>Описание</p>
        <a-textarea
          placeholder="Текст коментария...."
          allow-clear
          v-model="disputComment"
          @change="onChangeComment"
        />
      </div>
      <div :style="{ marginTop: '20px' }">
        <p>Причина</p>
        <a-radio-group v-model="disputType">
          <a-radio-button
            v-for="disput_type_item in disputTypeData"
            :key="disput_type_item.id"
            :value="disput_type_item.id"
            >{{ disput_type_item.name }}</a-radio-button
          >
        </a-radio-group>
      </div>
      <div :style="{ marginTop: '20px' }">
        <span
          >Прикрепите запись звонков(если возможно). Без записи звонков заявка
          может быть признана качественной.</span
        >
      </div>
      <div
        :style="{
          marginTop: '20px',
        }"
      >
        <a-upload-dragger
          name="file"
          :multiple="true"
          :action="uploadURL"
          :file-list="fileList"
          :remove="handleRemoveFile"
          :headers="{ Authorization: $auth.getToken('local') }"
          @change="handleUpload"
        >
          <p class="ant-upload-drag-icon">
            <a-icon type="inbox" />
          </p>
          <p class="ant-upload-text">Нажмите или перекиньте файлы звонков</p>
          <p class="ant-upload-hint">Загрузить</p>
        </a-upload-dragger>
      </div>
    </a-modal>
  </a-layout-content>
</template>
<script>
import moment from "moment";
const columns = [
  {
    title: "ID",
    dataIndex: "deal_id",
    key: "deal_id",
  },
  {
    title: "Состояние",
    dataIndex: "is_view",
    key: "is_view",
    scopedSlots: { customRender: "is_view" },
  },
  {
    title: "ФИО",
    dataIndex: "name",
    key: "name",
    scopedSlots: { customRender: "name" },
  },
  {
    title: "Статус",
    dataIndex: "status_name",
    key: "status_name",
    scopedSlots: { customRender: "status_name" },
  },
  {
    title: "Регион",
    dataIndex: "region",
    key: "region",
    scopedSlots: { customRender: "region" },
  },
  {
    title: "Направление",
    dataIndex: "direction",
    key: "direction",
    scopedSlots: { customRender: "direction" },
  },
  {
    title: "Телефон",
    dataIndex: "phone",
    key: "phone",
  },
  {
    title: "E-mail",
    dataIndex: "email",
    key: "email",
  },
  {
    title: "Дата поступления",
    dataIdnex: "updated_at",
    key: "updated_at",
    scopedSlots: { customRender: "updated_at" },
    defaultSortOrder: "descend",
    sortDirections: ["descend", "ascend"],
    onFilter: (value, record) => {},
    sorter: (a, b) => {},
  },
  {
    title: "Доход",
    dataIndex: "amount",
    key: "amount",
    scopedSlots: { customRender: "amount" },
    defaultSortOrder: "descend",
    sortDirections: ["descend", "ascend"],
    onFilter: (value, record) => {},
    sorter: (a, b) => {},
  },
  {
    title: "На кого распределён",
    dataIndex: "bids",
    key: "bids",
    scopedSlots: { customRender: "bids" },
  },
];

export default {
  head() {
    return {
      title: "Поступившие заявки",
    };
  },
  data() {
    return {
      data: [],
      columns,
      fileList: [],
      uploadURL: "",
      disputComment: "",
      dealsIsLoading: true,
      utms: {},
      requestDeal: {},
      refererDeal: "",
      disputType: 1,
      disputTypeData: [],
      pagination: {},
      order_by: "DEF",
      order_field: "id",
      searchField: {
        name: '',
        status_id: null,
        region_id: null,
        direction_id: null,
        user_name: '',
        datePicker: [],
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
      visible: false,
      modalVisible: false,
      dealStatus: "",
      statusDisputId: 0,
      dealData: {
        deal_id: 1,
        bids: {
          direction: {
            name: "",
          },
        },
        status: {
          name: "",
          type: 0,
        },
        email: "",
        phone: "",
        region: {
          name: "",
        },
        direction: {
          name: "",
        },
      },
    };
  },
  created() {
    if (this.user.role != "ROLE_MANAGER" && this.user.role != "ROLE_ADMIN") {
      if (columns.length == 9) {
        columns.splice(-1, 1);
      }
    }
    this.loadTable();
    this.$axios
      .post("/disput/info")
      .then(({ data }) => {
        this.disputTypeData = data.disput_type;
      })
      .catch((_err) => {
        console.error(_err);
      });
  },
  methods: {
    getDate(date) {
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
        this.order_field = sorters.field;
      } else {
        this.order_field = "id";
      }
      this.loadTable({}, pager.current);
    },
    loadTable(postData = {}, currentPage = 1) {
      this.dealsIsLoading = true;
      this.$axios
        .post(`/deals?page=${currentPage}`, {
          search_: this.searchField,
          order_by: this.order_by,
          order_field: this.order_field,
        })
        .then(({ data }) => {
          const pagination = { ...this.pagination };
          this.data = data.data;
          pagination.total = data.total;
          this.pagination = pagination;
          this.dealsIsLoading = false;
        })
        .catch((_err) => {
          console.error(_err);
        });
    },
    handleMenuClick(_e) {},
    onHandleStatus(_e) {
      if (_e.target.value.type === 1003) {
        this.statusDisputId = _e.target.value.id;
        this.modalVisible = true;
      } else {
        this.$axios
          .post(`/deal/${this.dealData.deal_id}/status_update`, {
            status_id: _e.target.value.id,
          })
          .then(({ data }) => {
            if (data.status === "OK") {
              this.dealData.status = data.data;
              if (
                _e.target.value.type === 1001 ||
                _e.target.value.type === 1002
              ) {
                this.visible = false;
                this.loadTable();
              }
            }
          })
          .catch((_err) => {
            console.error(_err);
          });
      }
    },
    handleOk() {
      this.$axios
        .post(`/deal/${this.dealData.deal_id}/disput/create`, {
          message: this.disputComment,
          type_id: this.disputType,
        })
        .then(({ data }) => {
          if (data.status === "OK") {
            this.$axios
              .post(`/deal/${this.dealData.deal_id}/status_update`, {
                status_id: this.statusDisputId,
              })
              .then(({ data }) => {
                if (data.status === "OK") {
                  this.disputComment = "";
                  this.dealData.status = data.data;
                  this.modalVisible = false;
                  this.visible = false;
                  this.loadTable();
                } else {
                  this.$message.error(data.error);
                }
              })
              .catch((_err) => {
                console.error(_err);
              });
          } else {
            this.$message.error(data.error);
            this.modalVisible = false;
          }
        })
        .catch((_err) => {
          console.error(_err);
        });
    },
    handleDisputType() {},
    handleCancel() {
      this.dealStatus = this.dealData.status.id.toString();
      this.modalVisible = false;
    },
    showDrawer(_id, record, _e) {
      this.utms = {};
      this.requestDeal = {};
      this.referer = "";
      this.$axios
        .post(`/deal/${_id}`)
        .then(({ data }) => {
          this.dealData = data;
          this.uploadURL = `http://lk.leadz.monster/api/deal/${this.dealData.deal_id}/upload`;
          this.dealStatus = data.status.id.toString();
          this.fileList.splice(0, this.fileList.length);
          if (record.is_view == false) {
            //this.$store.dispatch("statuses/decCount", "dealsCount");
            record.is_view = true;
          }
          data.deal_files.forEach((_n) => {
            this.fileList.push({
              uid: _n.id,
              name: _n.name,
              status: "done",
              url: `http://lk.leadz.monster/api/deal/${data.deal_id}/storage/${_n.id}`,
            });
          });
          if (
            this.user.role === "ROLE_WEBMASTER" ||
            this.user.role === "ROLE_ADMIN"
          ) {
            this.utms = JSON.parse(data.utm);
            this.requestDeal = JSON.parse(data.request);
            this.refererDeal = data.referer;
          }
          this.visible = true;
        })
        .catch((_err) => {
          console.log(_err);
        });
    },
    handleUpload({ fileList }) {
      this.fileList = fileList;
    },
    handleRemoveFile(file) {
      const { response, uid } = this.fileList[this.fileList.indexOf(file)];
      this.deleteURL = "";
      if (response !== undefined) {
        this.deleteURL = `/deal/${this.dealData.deal_id}/storage/${response.uid}/delete`;
      } else {
        this.deleteURL = `/deal/${this.dealData.deal_id}/storage/${uid}/delete`;
      }

      this.$axios
        .post(this.deleteURL)
        .then(({ data }) => {
          console.log(data);
        })
        .catch((_err) => {
          console.error(_err);
        });
    },
    handleSearch(_e) {
      this.loadTable();
    },
    onChangeComment(_e) {},
    onClose() {
      this.visible = false;
    },
    handleStatusChange(e) {
      console.log(e);
    },
    handleItemStatus(record, e) {
      if (e.item.value.type === 1003) {
        this.statusDisputId = e.item.value.id;
        this.modalVisible = true;
        this.$axios
          .post(`/deal/${record.deal_id}`)
          .then(({ data }) => {
            this.dealData = data;
            this.uploadURL = `http://lk.leadz.monster/api/deal/${this.dealData.deal_id}/upload`;
            this.dealStatus = data.status.id.toString();
            this.fileList.splice(0, this.fileList.length);
          })
          .catch((err) => {
            console.error(err);
          });
      } else {
        this.$axios
          .post(`/deal/${record.deal_id}/status_update`, {
            status_id: e.item.value.id,
          })
          .then(({ data }) => {
            if (data.status === "OK") {
              record.status = data.data;
              if (e.item.value.type === 1001 || e.item.value.type === 1002) {
                this.visible = false;
                this.loadTable();
              }
            }
          })
          .catch((_err) => {
            console.error(_err);
          });
      }
    },
  },
};
</script>
