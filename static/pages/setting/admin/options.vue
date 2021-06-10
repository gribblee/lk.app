<template>
  <a-layout-content>
    <a-page-header
      title="Настройки"
      sub-title="Опции"
      @back="() => $router.go(-1)"
    ></a-page-header>
    <a-layout class="setting-layout-sider">
      <b-layout-sider :menu-key="$route.name" />
      <a-layout-content class="setting-layout-content">
        <a-row :gutter="[16, 16]" type="flex" justify="space-around">
          <a-col :xs="24" :md="10" :lg="10">
            <h1 class="typhography-header">Опции</h1>
            <a-form-model ref="optionsForm" :model="optionsForm" :rules="rules">
              <a-form-model-item
                has-feedback
                label="Пороговый баланс"
                prop="thresholdBalance"
              >
                <a-input
                  v-model="optionsForm.threshold_balance"
                  type="text"
                  autocomplete="off"
                />
              </a-form-model-item>
              <a-form-model-item
                has-feedback
                label="Минимальная сумма пополнения"
                prop="minPayment"
              >
                <a-input
                  v-model="optionsForm.min_payment"
                  type="text"
                  autocomplete="off"
                />
              </a-form-model-item>
              <a-form-model-item
                has-feedback
                label="Процент страховки"
                prop="insuranceRate"
              >
                <a-input
                  v-model="optionsForm.insurance_rate"
                  type="text"
                  autocomplete="off"
                />
              </a-form-model-item>
              <a-form-model-item
                has-feedback
                label="Процент гарантии"
                prop="garantRate"
              >
                <a-input
                  v-model="optionsForm.garant_rate"
                  type="text"
                  autocomplete="off"
                />
              </a-form-model-item>
              <a-form-model-item
                has-feedback
                label="Шаг прибавления скидки"
                prop="stepDiscount"
              >
                <a-input
                  v-model="optionsForm.step_discount"
                  type="text"
                  autocomplete="off"
                />
              </a-form-model-item>
              <a-form-model-item
                has-feedback
                label="Максимальная скидка"
                prop="maxDiscount"
              >
                <a-input
                  v-model="optionsForm.max_discount"
                  type="text"
                  autocomplete="off"
                />
              </a-form-model-item>
              <a-form-model-item
                has-feedback
                label="Размер скидки"
                prop="discountRate"
              >
                <a-input
                  v-model="optionsForm.discount_rate"
                  type="text"
                  autocomplete="off"
                />
              </a-form-model-item>
              <a-form-model-item
                has-feedback
                label="Процент списания бонусов"
                prop="billBonus"
              >
                <a-input
                  v-model="optionsForm.bill_bonus"
                  type="text"
                  autocomplete="off"
                />
              </a-form-model-item>
              <a-form-model-item
                has-feedback
                label="Бонусы от"
                prop="billBonusFrom"
              >
                <a-input
                  v-model="optionsForm.bill_bonus_from"
                  type="text"
                  autocomplete="off"
                />
              </a-form-model-item>
              <a-form-model-item
                has-feedback
                label="Бонусы до"
                prop="billBonusTo"
              >
                <a-input
                  v-model="optionsForm.bill_bonus_to"
                  type="text"
                  autocomplete="off"
                />
              </a-form-model-item>
              <a-form-model-item
                has-feedback
                label="Бонусы 1"
                prop="billBonusЩту"
              >
                <a-input
                  v-model="optionsForm.bill_bonus_one"
                  type="text"
                  autocomplete="off"
                />
              </a-form-model-item>
              <a-form-model-item
                has-feedback
                label="Бонусы 2"
                prop="billBonusTwo"
              >
                <a-input
                  v-model="optionsForm.bill_bonus_two"
                  type="text"
                  autocomplete="off"
                />
              </a-form-model-item>
              <a-form-model-item
                has-feedback
                label="Процент бонуса за товар"
                prop="bonusStore"
              >
                <a-input
                  v-model="optionsForm.bonus_store"
                  type="text"
                  autocomplete="off"
                />
              </a-form-model-item>
              <a-form-model-item
                has-feedback
                label="Регистрация балы"
                prop="scoreRegistration"
              >
                <a-input
                  v-model="optionsForm.score_registration"
                  type="text"
                  autocomplete="off"
                />
              </a-form-model-item>
              <a-form-model-item
                has-feedback
                label="Балы за пополнение"
                prop="scorePayment"
              >
                <a-input
                  v-model="optionsForm.score_payment"
                  type="text"
                  autocomplete="off"
                />
              </a-form-model-item>
              <a-form-model-item
                has-feedback
                label="Сообщение при мин. балансе"
                prop="messageBalance"
              >
                <a-textarea
                  v-model="optionsForm.message_balance"
                  type="text"
                  autocomplete="off"
                  placeholder="Сообщение"
                  :rows="5"
                />
              </a-form-model-item>
              <a-form-model-item
                has-feedback
                label="Время для автозакрытия заявки в днях"
                prop="dayClaimClose"
              >
                <a-input
                  v-model="optionsForm.day_claim_close"
                  type="text"
                  autocomplete="off"
                />
              </a-form-model-item>
              <a-form-model-item>
                <a-button type="primary" @click="submitForm('optionsForm')">
                  Сохранить
                </a-button>
              </a-form-model-item>
            </a-form-model>
          </a-col>
        </a-row>
      </a-layout-content>
    </a-layout>
  </a-layout-content>
</template>
<script lang="ts">
import Vue from "vue";
export default Vue.extend({
  name: "options",
  head() {
    return {
      title: "Опции",
    };
  },
  data() {
    return {
      rules: {
        thresholdBalance: [],
        messageBalance: [],
        minPayment: [],
        insuranceRate: [],
        billBonus: [],
      },
      optionsForm: {
        min_payment: "10000",
        insurance_rate: "0",
        message_balance: "",
        threshold_balance: "0",
        bill_bonus: "0",
        bill_bonus_from: "0",
        bill_bonus_to: "0",
        bill_bonus_one: "0",
        bill_bonus_two: "0",
        bonus_store: "0",
        score_registration: "0",
        score_payment: "0",
        day_claim_close: "5",
        garant_rate: "20",
        step_discount: "50000",
        max_discount: "20",
        discount_rate: "5",
      },
    };
  },
  created() {
    const { optionsForm, $axios }: any = this;
    $axios.get("/directory").then(({ data }: any) => {
      Object.keys(optionsForm).forEach((eachKey: any) => {
        if (
          typeof data.options[eachKey] != "undefined" &&
          data.options[eachKey] != null
        ) {
          optionsForm[eachKey] = data.options[eachKey];
        }
      });
    });
  },
  methods: {
    submitForm(formName: any) {
      const app: any = this;
      app.$refs[formName].validate((valid: any) => {
        if (valid) {
          app.$axios
            .post("/option/save", { formOptions: this.optionsForm })
            .then(({ data }: any) => {
              if (data.success) {
                app.$message.success(data.message);
              } else {
                app.$message.error(data.error);
              }
            });
        } else {
          console.log("error submit!!");
          return false;
        }
      });
    },
  },
});
</script>