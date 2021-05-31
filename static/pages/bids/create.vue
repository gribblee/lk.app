<template>
  <a-layout-content>
    <a-page-header
      title="Создание направления"
      sub-title=""
      @back="() => $router.go(-1)"
    >
      <template slot="tags">
        <a-tag :color="computedStatus.color">{{ computedStatus.text }}</a-tag>
      </template>
      <template slot="extra"> </template>
    </a-page-header>
    <div>
      <a-row type="flex" justify="space-around">
        <a-col :xs="22" :md="10" :lg="10">
          <a-alert
            message="Информация"
            :description="direction.description"
            type="info"
            show-icon
          />
          <div class="iframe-container">
            <iframe
              class="second-iframe"
              :src="direction.iframe_url"
              frameborder="0"
              allow="accelerometer; autoplay; clipboard-write; encrypted-media; gyroscope; picture-in-picture"
              allowfullscreen
            ></iframe>
          </div>
          <div class="statistic-row">
            <a-row :gutter="[24, 24]">
              <a-col :span="24">
                <a-card>
                  <a-statistic
                    title="Вы получите"
                    :precision="2"
                    :value="computedFunel.total"
                    suffix="₽"
                    :value-style="{ color: '#3f8600' }"
                  />
                </a-card>
              </a-col>
              <a-col :span="24">
                <a-card>
                  <a-statistic
                    title="Заявок"
                    :value="computedFunel.count"
                    suffix=""
                    :value-style="{ color: '#3f8600' }"
                    style="margin-right: 50px"
                  >
                  </a-statistic>
                </a-card>
              </a-col>
              <a-col :span="12">
                <a-card>
                  <a-statistic
                    title="Встреч"
                    :value="computedFunel.mettings"
                    suffix=""
                    :value-style="{ color: '#3f8600' }"
                    style="margin-right: 50px"
                  >
                  </a-statistic>
                </a-card>
              </a-col>
              <a-col :span="12">
                <a-card>
                  <a-statistic
                    title="Сделок"
                    :value="computedFunel.deals"
                    suffix=""
                    :value-style="{ color: '#3f8600' }"
                    style="margin-right: 50px"
                  >
                  </a-statistic>
                </a-card>
              </a-col>
            </a-row>
          </div>
        </a-col>
        <a-col :xs="22" :md="8" :lg="8">
          <a-card title="Настройка получения клиентов">
            <div class="bid-form">
              <div class="bid-item">
                <div class="bid-label">Укажите какие клиенты нужны:</div>
                <a-select
                  size="large"
                  placeholder="Выберите направление"
                  v-model="computedDirection"
                  @change="handleDirection"
                  option-label-prop="label"
                >
                  <template v-for="direction in directory.directions">
                    <a-select-option
                      :key="direction.id"
                      :value="direction.id"
                      :label="direction.name"
                      v-if="direction.categories.indexOf(user.category_id) > -1"
                    >
                      {{ direction.name }}
                    </a-select-option>
                  </template>
                </a-select>
              </div>
              <div class="bid-item">
                <div class="bid-label">Выберите регионы:</div>
                <a-select
                  size="large"
                  placeholder="Выберите регионы"
                  mode="multiple"
                  v-model="computedRegions"
                  @change="(e) => handleUpdate('regions')"
                  option-label-prop="label"
                >
                  <a-select-option
                    v-for="region in directory.regions"
                    :key="region.id"
                    :value="region.id"
                    :label="region.name_with_type"
                  >
                    {{ region.name_with_type }}
                  </a-select-option>
                </a-select>
                <div class="bid-info" v-if="computedIsRegions">
                  <a-alert
                    :show-icon="true"
                    message="Вы будете получать заявки по всем регионам"
                    type="info"
                  />
                </div>
                <div class="bid-content" v-if="computedRegionsRate">
                  <a-tooltip
                    title="Вы можете настроить отдельно ставку по каждому региону. ДЛЯ ОПЫТНЫХ!"
                  >
                    <a-button
                      size="large"
                      type="primary"
                      @click="(e) => (visibleRates = true)"
                      >Настроить ставки</a-button
                    >
                  </a-tooltip>
                </div>
              </div>
              <div class="bid-item">
                <div class="bid-label">Сколько заявок в день вам нужно:</div>
                <a-input-number
                  :min="0"
                  :max="1000"
                  size="large"
                  v-model="daily_limit"
                  @blur="handleDaily"
                />
                <div class="bid-info" v-if="computedDaily.status">
                  <a-alert
                    :show-icon="true"
                    :message="computedDaily.message"
                    :type="computedDaily.type"
                  />
                </div>
              </div>
              <div class="bid-item">
                <div class="bid-label">
                  Сколько Вы готовы платить за клиента:
                </div>
                <a-input-number
                  v-model="computedVMConsumption"
                  :min="computedMinConsumption"
                  :max="computedMaxConsumption"
                  size="large"
                  @blur="handleConsumption"
                />
                <a-slider
                  v-model="computedVMConsumption"
                  :min="computedMinConsumption"
                  :max="computedMaxConsumption"
                  :step="1"
                  :tip-formatter="null"
                  @afterChange="handleConsumption"
                />
                <div class="bid-info" v-if="computedRecommend.status">
                  <div class="bid-recommend">
                    <span class="bid-recommend-c">
                      <a
                        href="javascript:void(0)"
                        @click="
                          (e) => {
                            consumption = computedRecommend.consumption;
                            this.handleConsumption(e);
                          }
                        "
                      >
                        Рекомендуемая ставка
                        {{ computedRecommend.consumption }} ₽
                      </a>
                    </span>
                    <span class="bid-recommend-c">
                      <a-tooltip title="Выше ставка - больше заявок">
                        <a-icon
                          type="exclamation-circle"
                          :style="{ cursor: 'pointer' }"
                        />
                      </a-tooltip>
                    </span>
                  </div>
                </div>
                <div class="bid-info" v-if="computedConsumption.status">
                  <a-alert
                    :show-icon="true"
                    :type="computedConsumption.type"
                    :message="computedConsumption.message"
                  />
                </div>
              </div>
              <div
                class="bid-item"
                v-if="
                  user.role === 'ROLE_MANAGER' || user.role === 'ROLE_ADMIN'
                "
              >
                <template v-if="Object(itsUser).hasOwnProperty('name')">
                  <span class="bid-user-id"
                    ><nuxt-link :to="{ path: `/users/${itsUser.id}` }">
                      Просмотреть пользователя {{ itsUser.name }}</nuxt-link
                    ></span
                  >
                </template>
                <a-button type="primary" size="large" @click="openItsUser">
                  <template v-if="Object(itsUser).hasOwnProperty('name')">{{
                    itsUser.name
                  }}</template>
                  <template v-else>Выбрать пользователя</template>
                </a-button>
              </div>
              <div class="bid-item">
                <div class="bid-label">Страховка</div>
                <div class="bid-container">
                  <!-- <div class="bid-content bc-space-bwn">
                    <span>Количество заявок по страховке: {{ insurance }}</span>
                    <span>
                      <a-tooltip
                        title="Покупка страховки позволит Вам заменить ВСЕ нецелевые заявки БЕСПЛАТНО"
                      >
                        <a-icon
                          type="exclamation-circle"
                          :style="{ cursor: 'pointer' }"
                        /> </a-tooltip
                    ></span>
                  </div> -->
                  <div class="bid-content">
                    <div class="bid-content-switch">
                      <a-switch
                        v-model="isInsurance"
                        @change="handleIBuy(id, $event)"
                      />

                      <span class="bid-insurance-label">
                        Подключить страховку
                      </span>
                      <div :style="{ marginTop: '20px' }">
                        <b
                          >Бесплатный возврат денег в случае некачественной
                          заявки (битый номер, дубль, конкурент, клиенту не
                          интересно). Стоимость: +{{ insuranceRate }}% к цене
                          заявки. Возврат денег осуществляется на Ваш бонусный
                          счет</b
                        >
                        <a
                          href="https://docs.google.com/document/d/1q5-y6dntdafDKXjkEwujbfWnHE9Q2JwYbD3qZu0JCoM/edit"
                          target="_blank"
                          :style="{ marginTop: '10px', display: 'inline-block' }"
                          >Прочитать условия</a
                        >
                      </div>
                    </div>
                    <!-- <div class="bid-info" v-if="isInsurance">
                      <a-list
                        item-layout="horizontal"
                        :data-source="insurancesData"
                      >
                        <a-list-item slot="renderItem" slot-scope="item">
                          <a-list-item-meta>
                            <span slot="description"
                              >Стоимость: {{ item.price }}₽</span
                            >
                            <a slot="title"
                              >{{ item.name }} {{ item.count }} заявок</a
                            >
                          </a-list-item-meta>
                          <a-popconfirm
                            title="Вы уверены, что покупаете страховку"
                            ok-text="Да"
                            cancel-text="Нет"
                            @confirm="handleIBuy(item.id, $event)"
                            slot="actions"
                          >
                            <a>Подключить</a></a-popconfirm
                          >
                        </a-list-item>
                      </a-list>
                    </div> -->
                  </div>
                </div>
              </div>
              <div class="bid-item">
                <a-button size="large" type="primary" @click="handleCreate"
                  >Создать</a-button
                >
              </div>
              <div class="bid-item">
                <a-button size="large" type="danger" @click="handleLaunch"
                  >Создать и запустить</a-button
                >
              </div>
            </div>
          </a-card>
        </a-col>
      </a-row>
    </div>
    <a-modal
      v-model="visibleRates"
      title="Настройка ставки по регионам"
      @ok="
        (e) => {
          visibleRates = false;
          handleUpdate('regions');
        }
      "
      cancelText="Отмена"
      okText="Сохранить"
    >
      <a-form>
        <a-form-item
          v-for="region in regions"
          :key="region.id"
          :label="region.name_with_type"
        >
          <a-input suffix="₽" v-model="region.rate" />
        </a-form-item>
      </a-form>
    </a-modal>
    <a-modal v-model="errorLaunch" title="Ошибка!" centred closable>
      <a-space align="start" :size="20">
        <a-icon
          type="close-circle"
          :style="{
            color: 'red',
            fontSize: '24px',
          }"
        />
        <span
          :style="{
            color: 'red',
          }"
          >Для получения клиентов надо пополнить бюджет. Вы можете это сделать
          по карте либо по расчётному счёту юрлица или ИП. Выберите способ
          пополнения</span
        >
      </a-space>
      <template slot="footer">
        <a-space align="center" :size="20">
          <a-button
            type="primary"
            @click="
              (e) => {
                errorLaunch = false;
                $store.dispatch('payment/setVisiblePayRequisite', true);
              }
            "
            >По Р/С</a-button
          >
          <a-button
            type="primary"
            @click="
              (e) => {
                errorLaunch = false;
                $store.dispatch('payment/setVisiblePayCard', true);
              }
            "
            >По карте</a-button
          >
        </a-space>
      </template>
    </a-modal>
    <a-modal
      v-model="visibleItsUser"
      title="Выбрать пользователя"
      @ok="handleItsUser"
      okText="Сохранить"
      cancelText="Отменить"
    >
      <div class="bid-users-header"></div>
      <div class="bid-users-list">
        <a-radio-group v-model="user_id" @change="onChangeUsers">
          <a-radio
            v-for="(item, index) in meUsers"
            :key="index"
            :style="radioStyle"
            :value="item.id"
            >{{ item.name }}</a-radio
          >
        </a-radio-group>
        <div class="bid-users-pagination">
          <a-pagination
            v-model="usersPagination.current"
            :pageSize="usersPagination.pageSize"
            :total="usersPagination.total"
            @change="onChangePagination"
            show-less-items
          />
        </div>
      </div>
    </a-modal>
  </a-layout-content>
</template>
<style scoped>
.bid-user-id {
  margin: 10px 0;
}

.bid-users-header {
}

.bid-users-list {
  padding: 10px 0;
}

.bid-users-pagination {
  margin-top: 15px;
}

.bid-form {
  display: flex;
  flex-direction: column;
  width: 100%;
}

.bid-item {
  margin-top: 20px;
  padding-top: 10px;
  border-top: 1px solid #ccc;
}

.bid-content > *,
.bid-item > * {
  width: 100%;
}

.bid-item:first-child {
  border-top: none;
  padding-top: 0;
  margin-top: 0;
}

.bid-info {
  margin-top: 10px;
}

.bid-label {
  padding-bottom: 10px;
  font-weight: 800;
}

.bid-recommend {
  display: flex;
  justify-content: space-between;
}

.bid-recommend-c {
}

.bid-container {
}

.bid-content {
  margin-top: 20px;
}

.bid-content.bc-space-bwn {
  display: flex;
  justify-content: space-between;
}
.bid-content.bc-space-bwn > * {
  width: inherit;
}

.bid-content:first-child {
  margin-top: 0;
}

.bid-insurance-label {
  margin-left: 20px;
  font-weight: 700;
}

.iframe-container {
  position: relative;
  display: flex;
  width: 100%;
  padding-top: 56%;
  flex-direction: column;
  background-color: blue;
  overflow: hidden;
  margin-top: 30px;
}

.second-iframe {
  position: absolute;
  left: 0;
  top: 0;
  width: 100%;
  height: 100%;
  flex-grow: 1;
  border: none;
  margin: 0;
  padding: 0;
}

.statistic-row {
  margin-top: 30px;
}

.bid-content-switch {
  width: inherit;
}
</style>
<script>
export default {
  head() {
    return {
      title: "Создание направления",
    };
  },
  // validate({ params }) {
  //   // return /^\d+$/.test(params.id);
  // },
  data() {
    return {
      directory: [],
      isInsurance: false,
      insurancesData: [],
      visibleRates: false,
      errorLaunch: false,
      //Данные формы
      id: 0,
      is_launch: false,
      daily_limit: 0,
      insurance: -1,
      regions: [],
      regionsRate: [],
      direction: {},
      options: {},
      consumption: 0,
      max_rate: 0,
      rate_fix: false,
      is_notification: false,
      itsUser: {},
      meUsers: [],
      user_id: 0,
      insuranceRate: "",
      visibleItsUser: false,
      usersPagination: {
        current: 1,
        total: 1,
        pageSize: 10,
      },
      radioStyle: {
        display: "block",
        height: "30px",
        lineHeight: "30px",
      },
    };
  },
  created() {
    const { bidStorage } = this.$store.state;
    this.user_id = this.user.id;
    this.$axios
      .get("/directory")
      .then(({ data }) => {
        this.directory = data;
        if (data.directions.length > 0) {
          this.direction = data.directions[0];
        }
        this.insuranceRate = data.options.insurance_rate;
      })
      .catch((_err) => {
        console.error(_err);
      });
    // this.$axios.post(`/bid/${this.$route.params.id}`).then(({ data }) => {
    //   this.setData(data);
    //   //this.rate_fix = data.rate_fix;
    // });
    this.$axios
      .get("/insurance")
      .then(({ data }) => (this.insurancesData = data));
  },
  mounted() {},
  methods: {
    onChangePagination(page) {
      this.loadManagerUsers(page);
    },
    onChangeUsers(e) {},
    openItsUser(e) {
      this.loadManagerUsers(this.current);
      this.visibleItsUser = true;
    },
    loadManagerUsers(page = 1) {
      this.$axios
        .post(`/manager/users?page=${page}`)
        .then(({ data }) => {
          this.meUsers = data.data;
          this.usersPagination.total = data.total;
        })
        .catch((err) => {
          console.error(err);
        });
    },
    handleItsUser(e) {
      // this.$axios
      //   .post(`/manager/bid/${this.id}/update`, {
      //     user_id: this.user_id,
      //   })
      //   .then(({ data }) => {
      //     this.itsUser = data;
      //     this.visibleItsUser = false;
      //   })
      //   .catch((err) => {
      //     console.error(err);
      //   });
    },
    //Обдщие методы
    setData(data) {
      this.id = data.id;
      this.is_launch = data.is_launch;
      this.daily_limit = data.daily_limit;
      this.insurance = data.insurance;
      this.isInsurance = data.is_insurance;
      this.regions = data.regions;
      this.direction = data.direction;
      this.options = data.options;
      this.consumption = Number(data.consumption);
      this.max_rate = Number(data.max_rate);
      this.is_notification = data.is_notification;
      this.itsUser = data.user;
      this.user_id = data.user_id;
    },
    //Методы событий
    handleUpdate(arg) {
      // const postData = {};
      // switch (arg) {
      //   case "direction":
      //     postData.direction_id = this.direction.id;
      //     break;
      //   case "daily":
      //     postData.daily_limit = this.daily_limit;
      //     break;
      //   case "consumption":
      //     postData.consumption = this.consumption;
      //     break;
      //   case "regions":
      //     postData.regions = this.regions;
      //     break;
      // }
      // this.$axios.post(`/bid/${this.id}/update`, postData).then(({ data }) => {
      //   this.setData(data.data);
      //   this.$message.success(data.message);
      // });
    },
    handleDelete(e) {},
    handleRegion(e) {},
    handleRates(e) {},
    handleCreate(e) {
      this.$axios
        .post(`/bid/create`, {
          is_launch: false,
          user_id: this.user_id,
          direction_id: this.direction.id,
          regions: this.regions,
          is_insurance: this.isInsurance,
          daily_limit: this.daily_limit,
          consumption: this.consumption,
        })
        .then(({ data }) => {
          if (data.code === 1004) {
            this.errorLaunch = true;
            this.$message.error(data.message);
          }
          if (data.code == 1003) {
            this.$message.error(data.msg);
          }
          this.is_launch = data.is_launch;
          if (data.code === 1001) {
            this.$message.success(data.msg);
            this.$router.push(`/bids/${data.id}`);
          }
        });
    },
    handleLaunch(e) {
      this.$axios
        .post(`/bid/create`, {
          is_launch: true,
          user_id: this.user_id,
          direction_id: this.direction.id,
          regions: this.regions,
          is_insurance: this.isInsurance,
          daily_limit: this.daily_limit,
          consumption: this.consumption,
        })
        .then(({ data }) => {
          if (data.code === 1004) {
            this.errorLaunch = true;
            this.$message.error(data.message);
          }
          if (data.code == 1003) {
            this.$message.error(data.msg);
          }
          this.is_launch = data.is_launch;
          if (data.code === 1001) {
            this.$message.success(data.msg);
            this.$router.push(`/bids/${data.id}`);
          }
        });
    },
    handleRecommend(e) {},
    handleDaily(e) {
      this.handleUpdate("daily");
    },
    handleDirection(e) {
      if (this.direction.max_rate != null) {
        this.max_rate =
          Number(this.direction.max_rate.consumption) +
          Math.ceil((Number(this.direction.max_rate.consumption) / 100) * 5);
      } else {
        this.max_rate = this.directory.maxRate;
      }
      // this.handleUpdate("direction");
    },
    handleConsumption(e) {
      // this.handleUpdate("consumption");
    },
    handleInsurance(e) {},
    handleIBuy(id, e) {
      // this.$axios
      //   .post(`/bid/${this.id}/buy_insurance`, { insurance_id: id })
      //   .then(({ data }) => {
      //     if (data.success == true) {
      //       this.setData(data.data);
      //     } else {
      //       this.$message.error(data.message);
      //     }
      //   });
    },
  },
  computed: {
    computedStatus() {
      return this.is_launch
        ? {
            color: "green",
            text: "Запущено",
            type: "danger",
            button: "Остановить",
          }
        : {
            color: "red",
            text: "Остановлено",
            type: "primary",
            button: "Запустить",
          };
    },
    computedRecommend() {
      const recommendReturn = {
        status: false,
        consumption:
          this.max_rate === 0 ? this.directory.maxRate : this.max_rate,
      };
      recommendReturn.status = this.consumption < recommendReturn.consumption;

      return recommendReturn;
    },
    computedFunel() {
      const _count = Math.ceil(
        this.daily_limit <= 0 ? 100 : this.daily_limit * 30
      );
      const _mettings = Math.ceil(
        _count * (this.direction.conversion_meetings / 100)
      );
      const _deals = Math.ceil(
        _mettings * (this.direction.conversion_contract / 100)
      );
      const _total = Math.ceil(_deals * this.direction.average_check);
      return {
        count: isNaN(_count) ? 0 : _count,
        mettings: isNaN(_mettings) ? 0 : _mettings,
        deals: isNaN(_deals) ? 0 : _deals,
        total: isNaN(_total) ? 0 : _total,
      };
    },
    computedIsRegions() {
      return this.regions.length === 0;
    },
    computedDaily() {
      const dailyReturn = {
        status: false,
        message: "",
        type: "info",
      };

      if (this.daily_limit <= 0 || this.daily_limit === "") {
        dailyReturn.status = true;
        dailyReturn.type = "info";
        dailyReturn.message =
          "Вы будете получать неограниченное количество заявок";
      } else {
        if (this.daily_limit > 0 && this.daily_limit < 5) {
          dailyReturn.status = true;
          dailyReturn.type = "error";
          dailyReturn.message =
            "Укажите от 5 лидов в день, либо оставьте поле пустым";
        }
      }
      return dailyReturn;
    },
    computedMinConsumption() {
      if (Object.keys(this.direction).length > 0) {
        const cost_price = Number(this.direction.cost_price);
        const extra = Number(this.direction.extra);
        return Number(cost_price) + cost_price * (extra / 100);
      } else {
        return Number(900);
      }
    },
    computedMaxConsumption() {
      return Number(this.user.balance) + 10000;
    },
    computedConsumption() {
      const consumptionReturn = {
        status: false,
        message: "",
        type: "info",
      };
      const cost_price = Number(this.direction.cost_price);
      const extra = Number(this.direction.extra);
      if (
        this.consumption <
        Number(cost_price) + Number(cost_price * (extra / 100))
      ) {
        consumptionReturn.status = true;
        consumptionReturn.message =
          "При такой ставке Вы не будете получать заявки";
        consumptionReturn.type = "error";
      }
      return consumptionReturn;
    },
    computedIsInsurances() {
      return this.insurancesData.length > 0;
    },
    computedRegionsRate() {
      return this.regions.length > 0;
    },
    computedVMConsumption: {
      set(value) {
        if (typeof value === "number") {
          this.consumption = Number(value);
        }
      },
      get() {
        return this.consumption;
      },
    },
    computedRegions: {
      set(value) {
        const { regions } = this.directory;

        this.regions = value.map((v) => {
          let region = regions.find((r) => r.id === v);
          return {
            ...region,
            bid_id: this.id,
            rate: this.consumption,
          };
        });
      },
      get() {
        return this.regions.map((r) => {
          return r.id;
        });
      },
    },
    computedDirection: {
      set(value) {
        this.direction = this.directory.directions.find((f) => f.id === value);
      },
      get() {
        return this.direction.id;
      },
    },
  },
};
</script>