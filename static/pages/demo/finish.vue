<template>
  <div class="demo-form">
    <div class="demo-header-1">
      Вот и всё осталось только оплатить и получать клиентов уже сегодня
    </div>
    <div class="demo-content-1">
      <p>
        Вы можете начать получать клиентов даже БЕЗ вложений! Для этого оформите
        беспроцентную рассрочку в банке Тинькофф, привлекайте клиентов и
        зарабатывайте уже сегодня, а оплачивайте только через месяц!
      </p>
      <p>
        При оформлении рассрочки СЕЙЧАС Вы также получите БОНУС 10% от суммы
        пополнения на Ваш бонусный счет
      </p>
    </div>
    <div class="demo-action">
      <div class="demo-action__item">
        <a-button type="dashed" @click="onPaymentCredit" size="large"
          >Оформить рассроку сейчас</a-button
        >
      </div>
      <div class="demo-action__item">
        <a-radio-group v-model="paymentId" button-style="solid" size="large">
          <a-radio-button :value="0">По карте</a-radio-button>
          <a-radio-button :value="1">По реквизитам</a-radio-button>
        </a-radio-group>
      </div>
    </div>
    <div class="demo-submit" v-if="paymentId < 2">
      <a-button type="danger" shape="round" size="large" @click="onPayment"
        >Получить клиентов</a-button
      >
    </div>
    <a-modal
      v-model="isRequisite"
      title="Добавить реквизиты"
      @ok="requisiteOk"
      okText="Завершить"
      cancelText="Отмена"
      width="940px"
    >
      <a-form-model
        :model="requisiteForm"
        :rules="rulesRequisiteForm"
        ref="requisiteForm"
      >
        <a-row :gutter="[16, 16]">
          <a-col :xs="24" :md="12" :lg="12">
            <!-- Имя -->
            <a-form-model-item has-feedback label="Полное название" prop="name">
              <a-input
                v-model="requisiteForm.name"
                type="text"
                autocomplete="off"
              />
            </a-form-model-item>
            <!-- ОГРН -->
            <a-form-model-item has-feedback label="ОГРН" prop="ogrn">
              <a-input
                v-model="requisiteForm.ogrn"
                type="text"
                autocomplete="off"
              />
            </a-form-model-item>
            <!-- ИНН -->
            <a-form-model-item has-feedback label="ИНН" prop="inn">
              <a-input
                v-model="requisiteForm.inn"
                type="text"
                autocomplete="off"
              />
            </a-form-model-item>
            <!-- КПП -->
            <a-form-model-item has-feedback label="КПП" prop="kpp">
              <a-input
                v-model="requisiteForm.kpp"
                type="text"
                autocomplete="off"
              />
            </a-form-model-item>
            <!-- БИК -->
            <a-form-model-item has-feedback label="БИК" prop="bik">
              <a-input
                v-model="requisiteForm.bik"
                type="text"
                autocomplete="off"
              />
            </a-form-model-item>
          </a-col>
          <a-col :xs="24" :md="12" :lg="12">
            <!-- Банк -->
            <a-form-model-item has-feedback label="Банк" prop="bank">
              <a-input
                v-model="requisiteForm.bank"
                type="text"
                autocomplete="off"
              />
            </a-form-model-item>
            <!-- К/СЧ -->
            <a-form-model-item has-feedback label="К/СЧ" prop="ksch">
              <a-input
                v-model="requisiteForm.ksch"
                type="text"
                autocomplete="off"
              />
            </a-form-model-item>
            <!-- Р/СЧ -->
            <a-form-model-item has-feedback label="Р/СЧ" prop="rsch">
              <a-input
                v-model="requisiteForm.rsch"
                type="text"
                autocomplete="off"
              />
            </a-form-model-item>
            <!-- Юр. Адрес -->
            <a-form-model-item
              has-feedback
              label="Юридический адрес"
              prop="jour_address"
            >
              <a-input
                v-model="requisiteForm.jour_address"
                type="text"
                autocomplete="off"
              />
            </a-form-model-item>
            <!-- Почтовый адрес -->
            <a-form-model-item
              has-feedback
              label="Почтовый адрес"
              prop="poste_address"
            >
              <a-input
                v-model="requisiteForm.poste_address"
                type="text"
                autocomplete="off"
              />
            </a-form-model-item>
            <!-- ФИО Директора -->
            <a-form-model-item
              has-feedback
              label="ФИО Директора"
              prop="director"
            >
              <a-input
                v-model="requisiteForm.director"
                type="text"
                autocomplete="off"
              />
            </a-form-model-item>
          </a-col>
          <a-col :xs="24" :md="24" :lg="24">
            <a-form-model-item>
              <a-alert message="Внимание" type="info" show-icon
                ><template #description>
                  На указанную вами почту <b>{{ user.email }}</b> будет выслан
                  документ с реквизитами. После пополнения деньги автоматически
                  будут зачислены вам на баланс в течении 3-х рабочих дней после пополнения.</template
                ></a-alert
              >
            </a-form-model-item>
          </a-col>
        </a-row>
      </a-form-model>
    </a-modal>
    <a-modal
      v-model="isRequisiteSuccess"
      title="Реквизиты добавлены"
      @ok="() => (isRequisteSuccess = false)"
    ></a-modal>
  </div>
</template>
<style scoped>
.demo-form {
  margin-top: 30px;
}
.demo-header-1 {
  font-size: 40px;
  border-bottom: 1px solid #222;
  padding-bottom: 20px;
}

.demo-content-1 {
  font-size: 17px;
  margin-top: 80px;
}

.demo-action {
  display: flex;
  justify-content: space-between;
  flex-direction: row;
  margin-top: 80px;
}

.demo-submit {
  display: flex;
  justify-content: center;
  margin-top: 30px;
}

@media screen and (max-width: 764px) {
  .demo-header-1 {
    font-size: 20px;
    padding: 0 20px 15px 20px;
    font-weight: 800;
  }
  .demo-content-1 {
    margin-top: 20px;
    padding: 0 20px;
    font-size: 16px;
  }

  .demo-action__item {
    margin-top: 15px;
  }
  .demo-action__item:first-child {
    margin-top: 0;
  }

  .demo-action {
    margin-top: 20px;
    flex-direction: column;
    align-items: center;
    padding: 0 20px;
  }

  .demo-submit {
    justify-content: center;
  }
}
</style>
<script lang="ts">
import Vue from "vue";
export default Vue.extend({
  name: "demo-finish",
  layout: "demo-layout",
  data() {
    let ValidateNumber = (rule: any, value: String, callback: any) => {
      const re = /^([0-9]*)$/;
      if (!re.test(String(value)) && String(value).length > 0) {
        callback(new Error("Должны быть числа"));
      } else {
        callback();
      }
    };
    return {
      paymentId: 2,
      isRequisite: false,
      isRequisiteSuccess: false,
      rulesRequisiteForm: {
        name: [
          {
            required: true,
            message: "Поле имя обязательно для заполнения",
            trigger: "change",
          },
        ],
        ogrn: [
          {
            required: true,
            message: "ОГРН обязателен",
            trigger: "change",
          },
          {
            validator: ValidateNumber,
            trigger: "change",
          },
        ],
        inn: [
          {
            required: true,
            message: "ИНН обязателен",
            trigger: "change",
          },
          {
            validator: ValidateNumber,
            trigger: "change",
          },
        ],
        kpp: [
          {
            validator: ValidateNumber,
            trigger: "change",
          },
        ],
        bik: [
          {
            validator: ValidateNumber,
            trigger: "change",
          },
        ],
        bank: [],
        ksch: [
          {
            validator: ValidateNumber,
            trigger: "change",
          },
        ],
        rsch: [
          {
            validator: ValidateNumber,
            trigger: "change",
          },
        ],
        jour_address: [],
        poste_address: [],
        director: [],
      },
      requisiteForm: {
        name: "",
        ogrn: "",
        inn: "",
        kpp: "",
        bik: "",
        bank: "",
        ksch: "",
        rsch: "",
        jour_address: "",
        poste_address: "",
        director: "",
      },
      requisiteId: 0,
    };
  },
  methods: {
    demoCreatePayment(target: number) {
      const { $axios, $store, $message, $router, requisiteId }: any = this;
      const form = $store.state.localStorage.demoForm;
      const tariffId = $store.state.localStorage.tariffId;

      $axios
        .post("/demo/create", {
          directions: form.directions,
          allRegion: form.allRegion,
          regions: form.regions,
          target: target,
          tariffId: tariffId,
          requisiteId: requisiteId,
        })
        .then(({ data }: any) => {
          if (data.success) {
            if (typeof data.payment_url != "undefined") {
              window.open(data.payment_url, "_blank");
            }
            $router.push("/");
          } else {
            $message.error("Произошла ошибка");
          }
        });
    },
    onPayment(e: any) {
      const {
        $axios,
        $store,
        $message,
        $router,
        paymentId,
        demoCreatePayment,
      }: any = this;
      const app: any = this;
      const form = $store.state.localStorage.demoForm;
      const tariffId = $store.state.localStorage.tariffId;
      switch (paymentId) {
        case 0:
          demoCreatePayment(0);
          break;
        case 1:
          app.isRequisite = true;
          break;
      }
    },
    onPaymentCredit(e: any) {
      const { demoCreatePayment }: any = this;
      demoCreatePayment(2);
    },
    requisiteOk(e: any) {
      const { $refs, $axios, requisiteForm, demoCreatePayment }: any = this;
      $refs["requisiteForm"].validate((valid: any) => {
        if (valid) {
          $axios
            .post("/demo/requisite/create", {
              requisiteData: requisiteForm,
            })
            .then(({ data }: any) => {
              const app: any = this;
              app.requisiteId = data.requisiteId;
              demoCreatePayment(1);
              app.isRequisite = false;
            })
            .catch((err: any) => {
              console.error(err);
            });
        } else {
          console.error("Requisite Form InValid");
        }
      });
    },
  },
  head() {
    return {
      title: "Завершение",
    };
  },
  mounted() {
    const { $store }: any = this;
  },
});
</script>