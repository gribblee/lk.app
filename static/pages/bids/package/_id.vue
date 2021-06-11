<template>
  <a-layout-content>
    <a-page-header
      title="Создание заявок"
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
              v-if="direction.iframe_url != ''"
              class="second-iframe"
              :src="direction.iframe_url"
              frameborder="0"
              allow="accelerometer; autoplay; clipboard-write; encrypted-media; gyroscope; picture-in-picture"
              allowfullscreen
            ></iframe>
            <div class="second-iframe" v-else>Изображение отсутсвует</div>
          </div>
          <div class="statistic-row">
            <a-row :gutter="[24, 24]">
              <a-col :span="24">
                <a-card title="Поулчено заявок">
                  <a-statistic
                    :value="recivedEmployee"
                    suffix="заявок"
                    :value-style="{ color: '#3f8600' }"
                  />
                  <a-progress
                    :percent="
                      recivedEmployee > 0
                        ? (recivedEmployee / employeeTarget) * 100
                        : 0
                    "
                  />
                </a-card>
              </a-col>
              <a-col :span="24">
                <a-card>
                  <a-statistic
                    title="Ваш бюджет"
                    :precision="2"
                    :value="computedFunel.totalBudget"
                    suffix="₽"
                    :value-style="{ color: '#3f8600' }"
                  />
                </a-card>
              </a-col>
              <a-col :span="24">
                <a-card>
                  <a-statistic
                    title="Вам вернётся кэшбек"
                    :precision="2"
                    :value="computedFunel.discount"
                    suffix=""
                    :value-style="{ color: '#3f8600' }"
                  />
                </a-card>
              </a-col>
              <a-col :span="24">
                <a-card>
                  <a-statistic
                    title="Гарантировано договоров"
                    :value="computedFunel.totalContract"
                    suffix=""
                    :value-style="{ color: '#3f8600' }"
                  />
                </a-card>
              </a-col>
              <a-col :span="24">
                <a-card>
                  <a-statistic
                    title="Себестоимость договора"
                    :precision="2"
                    :value="computedFunel.costContract"
                    suffix="₽"
                    :value-style="{ color: '#3f8600' }"
                  />
                </a-card>
              </a-col>
              <a-col :span="24">
                <a-card>
                  <a-statistic
                    title="Ваш доход"
                    :precision="2"
                    :value="computedFunel.income"
                    suffix=""
                    :value-style="{ color: '#3f8600' }"
                  />
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
                  :disabled="true"
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
                  :disabled="true"
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
                <div class="bid-label">Вы получите платящих клиентов:</div>
                <a-input-number
                  v-model="computedVMCountEmployee"
                  :min="5"
                  :max="500"
                  :disabled="true"
                  size="large"
                  @blur="handlecostPerRate"
                />
              </div>
              <div class="bid-item">
                <div class="bid-label">Вы платите за одну заявку:</div>
                <a-input-number
                  v-model="computedVMcostPerRate"
                  :min="computedMincostPerRate"
                  :max="computedMaxcostPerRate"
                  :disabled="true"
                  size="large"
                  @blur="handlecostPerRate"
                />
                <div class="bid-info" v-if="computedcostPerRate.status">
                  <a-alert
                    :show-icon="true"
                    :type="computedcostPerRate.type"
                    :message="computedcostPerRate.message"
                  />
                </div>
              </div>
              <div class="bid-item">
                <div class="bid-label">Страховка</div>
                <div class="bid-container">
                  <div class="bid-content">
                    <div class="bid-content-switch">
                      <a-switch
                        :disabled="true"
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
                          :style="{
                            marginTop: '10px',
                            display: 'inline-block',
                          }"
                          >Прочитать условия</a
                        >
                      </div>
                    </div>
                  </div>
                </div>
              </div>
              <div class="bid-item">
                <div class="bid-label">Гарантия договоров</div>
                <div class="bid-container">
                  <div class="bid-content">
                    <div class="bid-content-switch">
                      <a-switch
                        :disabled="true"
                        v-model="isDealGarant"
                        @change="handleDealGarantBuy(id, $event)"
                      />

                      <span class="bid-insurance-label">
                        Подключить гарантию
                      </span>
                      <div :style="{ marginTop: '20px' }">
                        <b
                          >Бесплатный возврат денег в случае некачественной
                          заявки (битый номер, дубль, конкурент, клиенту не
                          интересно). Стоимость: +{{ dealGarantRate }}% к цене
                          заявки. Возврат денег осуществляется на Ваш бонусный
                          счет</b
                        >
                        <a
                          href="https://docs.google.com/document/d/1q5-y6dntdafDKXjkEwujbfWnHE9Q2JwYbD3qZu0JCoM/edit"
                          target="_blank"
                          :style="{
                            marginTop: '10px',
                            display: 'inline-block',
                          }"
                          >Прочитать условия</a
                        >
                      </div>
                    </div>
                  </div>
                </div>
              </div>
            </div>
          </a-card>
        </a-col>
      </a-row>
    </div>
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
<script lang="ts">
import Vue from "vue";
export default Vue.extend({
  data() {
    return {
      directory: [],
      isLaunch: false,
      totalBudget: 1000, // Общий бюджет который нужн опополнить
      direction: {
        description: "",
      },
      regions: [],
      countEmployee: 0, //Количество платящих клиентов
      costPerRate: 1, // сколько готовы платить за клиента
      isInsurance: false,
      isDealGarant: false,
      insuranceRate: 1, //Страховка
      garantRate: 1, //Гарантия
      discountRate: 1, //Суммая скидки %
      discountStep: 50000, // Скидка с каким шагом в ставку должна добавлятся
      discountMax: 50, //Максимальная сумма скидки
      dealGarantRate: 0,
      recivedEmployee: 0,
      employeeTarget: 0,
      is_launch: false,
    };
  },
  created() {
    const app: any = this;
    app.user_id = app.user.id;
    app.$axios
      .get("/directory")
      .then(({ data }: any) => {
        app.directory = data;
        if (data.directions.length > 0) {
          app.direction = data.directions[0];
        }
        app.insuranceRate = Number(data.options.insurance_rate);
        app.garantRate = Number(data.options.garant_rate);
        app.StepDiscount = Number(data.options.step_discount); //Шаг скидки
        app.DiscountRate = Number(data.options.discount_rate); //Размер скидки
        app.MaxDiscount = Number(data.options.max_discount); //Максимальаня скидка
        app.dealGarantRate = Number(data.options.garant_rate);
      })
      .catch((_err: any) => {
        console.error(_err);
      });
    app.$axios.post(`/bid/${this.$route.params.id}`).then(({ data }: any) => {
      app.setData(data);
      //this.rate_fix = data.rate_fix;
    });
  },
  mounted() {},
  methods: {
    //Обдщие методы
    setData(data: any) {
      const app: any = this;
      app.id = data.id;
      app.is_launch = data.is_launch;
      app.daily_limit = data.daily_limit;
      app.insurance = data.insurance;
      app.isInsurance = data.is_insurance;
      app.regions = data.regions;
      app.direction = data.direction;
      app.options = data.options;
      app.consumption = Number(data.consumption);
      app.max_rate = Number(data.max_rate);
      app.is_notification = data.is_notification;
      app.itsUser = data.user;
      app.user_id = data.user_id;
      app.countEmployee = data.ads_employee_count;
      app.totalBudget = data.total_budget;
      app.discount = data.discount;
      app.recivedEmployee = data.employee_count;
      app.employeeTarget = data.employee_target;
      app.is_launch = data.is_launch;
    },
    handleCreate() {},
    handlePayBalance() {},
    handleLaunch() {
      const app: any = this;
      app.$axios
        .post(`/bid/ads/create`, {
          directionId: app.direction.id,
          costPerRate: app.costPerRate,
          regions: app.regions.map((r: any) => r.id),
          isDealGarant: app.isDealGarant,
          isDealInsurance: app.isInsurance,
          countEmployee: app.countEmployee,
        })
        .then(({ data }: any) => {});
    },
    handleIBuy(id: any, e: any) {},
    handleDealGarantBuy(id: any, e: any) {},
    handleDirection(e: any) {},
    handleUpdate(arg: any) {},
  },
  computed: {
    computedStatus() {
      const app: any = this;
      return app.is_launch
        ? {
            color: "green",
            text: "Запущено",
            type: "danger",
            button: "Остановить",
          }
        : {
            color: "red",
            text: "Завершено",
            type: "primary",
            button: "Запустить",
          };
    },
    computedRecommend() {
      const app: any = this;
      const recommendReturn = {
        status: false,
        costPerRate: app.max_rate === 0 ? app.directory.maxRate : app.max_rate,
      };
      recommendReturn.status = app.costPerRate < recommendReturn.costPerRate;
      return recommendReturn;
    },
    computedFunel() {
      const app: any = this;
      let totalBudget: any;
      let conversionMeetings = Number(app.direction.conversion_meetings) / 100;
      let conversionContract = Number(app.direction.conversion_contract) / 100;
      let costPerRate = app.costPerRate;
      let conversionCall = conversionMeetings * conversionContract;
      let countEmployee = Math.ceil(app.countEmployee / conversionCall);
      let costContract = 0;
      let discount = 0;
      let income = 0;
      if (app.isInsurance) {
        costPerRate = costPerRate + costPerRate * (app.insuranceRate / 100);
      }
      if (app.isDealGarant) {
        costPerRate = costPerRate + costPerRate * (app.garantRate / 100);
      }
      totalBudget = countEmployee * costPerRate;
      costContract = Math.ceil(totalBudget / app.countEmployee);
      let discountRate =
        Math.floor(totalBudget / app.StepDiscount) * app.DiscountRate;

      if (discountRate > app.MaxDiscount) {
        discount = totalBudget * (app.MaxDiscount / 100);
        discountRate = app.MaxDiscount;
      } else {
        discount = totalBudget * (discountRate / 100);
      }

      let total = totalBudget - discount;
      app.totalBudget = total;
      income = Number(app.direction.average_check) * app.countEmployee;
      return {
        budget: totalBudget, //Общий бюджет
        totalBudget: total, //Нужен бюджет
        totalContract: `От ${app.countEmployee} До ${app.countEmployee * 2}`, //Гарантированно договоров
        count: `От ${countEmployee} До ${countEmployee * 2}`, // Количество сколько нужно
        costContract: isNaN(costContract) ? 0 : costContract, //Себестоимость договора
        discount: `${String(discount).replace(/\B(?=(\d{3})+(?!\d))/g, " ")} ₽ (${discountRate}%)`, //Скидка
        income: isNaN(income) ? 0 : income, //Доход
      };
    },
    computedIsRegions() {
      const app: any = this;
      return app.regions.length === 0;
    },
    computedIsBalance() {
      const app: any = this;
      return app.user.balance >= app.totalBudget;
    },
    computedDaily() {
      const app: any = this;
      const dailyReturn = {
        status: false,
        message: "",
        type: "info",
      };
      if (app.daily_limit <= 0 || app.daily_limit === "") {
        dailyReturn.status = true;
        dailyReturn.type = "info";
        dailyReturn.message =
          "Вы будете получать неограниченное количество заявок";
      } else {
        if (app.daily_limit > 0 && app.daily_limit < 5) {
          dailyReturn.status = true;
          dailyReturn.type = "error";
          dailyReturn.message =
            "Укажите от 5 лидов в день, либо оставьте поле пустым";
        }
      }
      return dailyReturn;
    },
    computedMincostPerRate() {
      const app: any = this;
      if (Object.keys(app.direction).length > 0) {
        const cost_price = Number(app.direction.cost_price);
        const extra = Number(app.direction.extra);
        return Number(cost_price) + cost_price * (extra / 100);
      } else {
        return Number(900);
      }
    },
    computedMaxcostPerRate() {
      const app: any = this;
      return Number(app.user.balance) + 10000;
    },
    computedcostPerRate() {
      const app: any = this;
      const costPerRateReturn = {
        status: false,
        message: "",
        type: "info",
      };
      const cost_price = Number(app.direction.cost_price);
      const extra = Number(app.direction.extra);
      if (
        app.costPerRate <
        Number(cost_price) + Number(cost_price * (extra / 100))
      ) {
        costPerRateReturn.status = true;
        costPerRateReturn.message =
          "При такой ставке Вы не будете получать заявки";
        costPerRateReturn.type = "error";
      }
      return costPerRateReturn;
    },
    computedIsInsurances() {
      const app: any = this;
      return app.insurancesData.length > 0;
    },
    computedRegionsRate() {
      const app: any = this;
      return app.regions.length > 0;
    },
    computedVMcostPerRate: {
      set(value: any) {
        const app: any = this;
        if (typeof value === "number") {
          app.costPerRate = Number(value);
        }
      },
      get() {
        const app: any = this;
        return app.costPerRate;
      },
    },
    computedVMCountEmployee: {
      set(value: any) {
        const app: any = this;
        app.countEmployee = Number(value);
      },
      get() {
        const app: any = this;
        return app.countEmployee;
      },
    },
    computedRegions: {
      set(value: any) {
        const app: any = this;
        const { regions } = app.directory;
        app.regions = value.map((v: any) => {
          let region = regions.find((r: any) => r.id === v);
          return {
            ...region,
            bid_id: app.id,
            rate: app.costPerRate,
          };
        });
      },
      get() {
        const app: any = this;
        return app.regions.map((r: any) => {
          return r.id;
        });
      },
    },
    computedDirection: {
      set(value: any) {
        const app: any = this;
        app.direction = app.directory.directions.find(
          (f: any) => f.id === value
        );
      },
      get() {
        const { direction }: any = this;
        return direction.id;
      },
    },
  },
});
</script>