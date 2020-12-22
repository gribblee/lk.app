<template>
  <div class="b-form-payment" :class="{ show: isShow }">
    <div class="b-form-payment__container">
      <div class="b-form-payment__header"></div>
      <div class="b-form-payment__title">
        Вот и всё осталось только оплатить и получать клиентов уже сегодня
      </div>
      <div class="b-form-payment__description">
        <p>
          Вы можете начать получать клиентов даже БЕЗ вложений! Для этого
          оформите беспроцентную рассрочку в банке Тинькофф, привлекайте
          клиентов и зарабатывайте уже сегодня, а оплачивайте только через
          месяц!
        </p>
        <p>
          При оформлении рассрочки СЕЙЧАС Вы также получите БОНУС 10% от суммы
          пополнения на Ваш бонусный счет
        </p>
      </div>
      <div class="b-form-payment__action">
        <div>
          <button class="b-form-payment__link" @click="handleSpecial">
            Специальное предложение
          </button>
        </div>
        <div>
          <b-radio-group
            :value="form.target"
            :items="radioItems"
            @change="updateTarget"
          />
        </div>
      </div>
      <div class="b-form-payment__more">
        <b-button title="Оплатить" @click="handlePayment" />
      </div>
    </div>
    <b-modal
      :open="isRequisiteModal"
      title="Заполните реквезиты"
      okTitle="Сохранить"
      @onOk="handleRSave"
    >
      <!-- Оплата по реквизитам -->
      <template #body>
        <div class="b-form-row">
          <div
            v-for="(field, index) in form.requisite"
            :key="index"
            class="b-form__field col"
          >
            <input
              type="text"
              :class="{ error: field.error.length > 0 }"
              :placeholder="field.title"
              v-model="field.value"
            />
            <div class="error" v-if="field.error.length > 0">
              {{ field.error }}
            </div>
          </div>
        </div>
      </template>
    </b-modal>
  </div>
</template>
<script>
import Index from "../pages/index.vue";
import bModal from "./b-modal.vue";
export default {
  components: { bModal, Index },
  name: "b-form-end",
  props: {
    isShow: {
      type: Boolean,
      default: false,
    },
  },
  model: {
    prop: "isShow",
  },
  data() {
    return {
      isRequisiteModal: false,
      form: {
        target: 0,
        tariffId: "1001",
        requisiteId: 0,
        requisite: [
          {
            title: "Имя компании",
            name: "name",
            value: "",
            error: "",
          },
          {
            title: "ОГРН",
            name: "ogrn",
            value: "",
            error: "",
          },
          {
            title: "ИНН",
            name: "inn",
            value: "",
            error: "",
          },
          {
            title: "КПП",
            name: "kpp",
            value: "",
            error: "",
          },
          {
            title: "БИК",
            name: "bik",
            value: "",
            error: "",
          },
          {
            title: "БАНК",
            name: "bank",
            value: "",
            error: "",
          },
          {
            title: "К/СЧ",
            name: "ksch",
            value: "",
            error: "",
          },
          {
            title: "Р/СЧ",
            name: "rsch",
            value: "",
            error: "",
          },
          {
            title: "Юр. адрес",
            name: "jour_address",
            value: "",
            error: "",
          },
          {
            title: "Почтовый адрес",
            name: "poste_address",
            value: "",
            error: "",
          },
          {
            title: "ФИО директора / ИП",
            name: "director",
            value: "",
            error: "",
          },
        ],
      },
      radioItems: [
        {
          id: 0,
          title: "Банковская карта",
        },
        {
          id: 1,
          title: "Расчётный счёт",
        },
      ],
    };
  },
  created() {},
  mounted() {
    var requisiteId = localStorage.getItem("demo.form.requisite_id");
    if (requisiteId) {
      this.$axios
        .post("/demo/requisite", {
          id: requisiteId,
        })
        .then(({ data }) => {
          /**
           * Костыль поправить
           */
          this.form.requisiteId = requisiteId;
          for (let k in data.data) {
            this.form.requisite.map((c) => {
              if (c.name == k) {
                c.value = data.data[k];
              }
            });
          }
          /**
           * Конец
           */
        })
        .catch((err) => {
          console.error(err);
        });
    }
  },
  methods: {
    updateTarget(value) {
      this.form.target = value;
      this.isRequisiteModal =
        this.form.target == 1 &&
        this.form.requisite.filter((c) => {
          return c.value.length > 0;
        }).length < this.form.requisite.length;
    },
    handleSpecial(e) {
      this.form.target = 2;
    },
    handlePayment(e) {
      this.$axios
        .post("/demo/create", {
          bid: JSON.parse(localStorage.getItem("demo.form.bid")),
          tariffId: localStorage.getItem("demo.form.tariff_id"),
          requisiteId: this.form.requisiteId,
          target: this.form.target,
        })
        .then(({ data }) => {
          if (data.success) {
            switch (data.status) {
              case "card":
                window.open(data.payment_url, "_blank");
                break;
              case "credit":
                window.open(data.payment_url, "_blank");
                break;
              case "requisite":
                break;
            }
            this.$emit("onEnd", true);
          }
        })
        .catch((err) => {
          console.error(err);
        });
    },
    handleRSave(e) {
      var postData = {};
      this.form.requisite.forEach((n) => {
        postData[n.name] = n.value;
      });
      this.$axios
        .post("/demo/requisite/create", {
          requisiteData: postData,
        })
        .then(({ data }) => {
          if (data.success) {
            localStorage.setItem("demo.form.requisite_id", data.requisiteId);
            this.form.requisiteId = data.requisiteId;
            this.isRequisiteModal = false;
          } else {
            /**
             * Костыль поправить
             */
            this.form.requisite.map((c) => {
              c.error = "";
            });
            for (let key in data.error) {
              this.form.requisite.map((c) => {
                if (c.name == key) {
                  c.error = data.error[key].join(",");
                }
              });
            }
            /**
             * Конец
             */
          }
        })
        .catch((err) => {
          console.error(err);
        });
    },
  },
};
</script>