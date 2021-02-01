<template>
  <div class="form-sign">
    <div class="form">
      <div class="form__type" :class="classAuth">
        <button class="form__type-auth" @click="handleAction('reg', $event)">
          Регистрация
        </button>
        <button class="form__type-reg" @click="handleAction('auth', $event)">
          Войти
        </button>
      </div>
      <div class="forms">
        <div class="forms__slider" :class="classSlider">
          <div class="form__reg">
            <div class="form-area">
              <b-textfield
                label="Имя"
                class="textfield-group"
                v-model="formReg.name"
              />
              <div class="form-error">
                <span
                  v-for="(errName, index) in errorsData.name"
                  :key="index"
                  >{{ errName }}</span
                >
              </div>
              <b-textfield
                label="Email"
                class="textfield-group"
                v-model="formReg.email"
              />
              <div class="form-error">
                <span
                  v-for="(errEmail, index) in errorsData.email"
                  :key="index"
                  >{{ errEmail }}</span
                >
              </div>
              <b-textfield
                label="Номер телефона без 8"
                v-model="formReg.phone"
                class="textfield-group"
                type="phone"
              />
              <div class="form-error">
                <span
                  v-for="(errPhone, index) in errorsData.phone"
                  :key="index"
                  >{{ errPhone }}</span
                >
              </div>
              <div class="form-submit">
                <b-button title="Далее" @click="handleSubmit" />
              </div>
            </div>
          </div>
          <div class="form__auth">
            <div class="form-area">
              <b-textfield
                label="Номер телефона без 8"
                v-model="formAuth.phone"
                class="textfield-group"
                type="phone"
              />
              <div class="form-error">
                <span
                  v-for="(errPhone, index) in errorsData.phone"
                  :key="index"
                  >{{ errPhone }}</span
                >
              </div>
              <div class="form-error" v-if="isError">{{ errorMsg }}</div>
              <div class="form-submit">
                <b-button title="Далее" @click="handleSubmit" />
              </div>
            </div>
          </div>
          <div class="form__code">
            <div class="form-area">
              <b-textfield
                label="Код из смс"
                v-model="formToken.passphrase"
                class="textfield-group"
                type="code"
              />
              <div class="form-error" v-if="isError">{{ errorMsg }}</div>
              <div class="form-submit">
                <b-button title="Получить клиентов" @click="handleCodeSubmit" />
              </div>
            </div>
          </div>
        </div>
      </div>
      <span :style="{ marginTop: '25px' }">
        <b-button type="link" :title="hasCodeTitle" @click="handleHasCode" />
      </span>
    </div>
  </div>
</template>
<script>
import BButton from "../../components/b-button.vue";
import BRadioGroup from "../../components/b-radio-group.vue";
import BTextfield from "../../components/b-textfield.vue";
export default {
  components: { BTextfield, BButton, BRadioGroup },
  name: "sign",
  layout: "signInUp",
  head() {
    return {
      title: "Войти/Регистрация",
    };
  },
  data() {
    return {
      hasCodeTitle: "У меня уже есть код",
      backState: {
        typeAuth: "",
        classSlider: "",
      },
      isError: false,
      errorMsg: "",
      errorsData: {},
      typeAuth: "reg",
      classAuth: "form__type--l",
      classSlider: "forms__slider--1",
      formReg: {
        name: "",
        email: "",
        phone: "",
      },
      formAuth: {
        phone: "",
      },
      formToken: {
        passphrase: "",
      },
    };
  },
  created() {},
  mounted() {},
  methods: {
    handleAction(type, e) {
      this.resetBackState();
      this.typeAuth = type;
      if (type == "auth") {
        this.classAuth = "form__type--r";
        this.classSlider = "forms__slider--2";
      } else {
        this.classAuth = "form__type--l";
        this.classSlider = "forms__slider--1";
      }
    },
    handleSubmit(e) {
      if (this.typeAuth == "reg") {
        /**
         * Регистрация
         */
        this.$axios
          .post("/register", this.formReg)
          .then(({ data }) => {
            if (data.success == true) {
              this.typeAuth = "code";
              this.classSlider = "form__slider--3";
              localStorage.setItem("auth.validation", data.token);
              this.typeAuth = "code";
              this.classSlider = "forms__slider--3";
            } else {
              this.isError = true;
              this.errorsData = data.errors;
            }
          })
          .catch((err) => {
            console.error(err);
          });
      } else {
        /**
         * Авторизация
         */
        this.$axios
          .post("/login", this.formAuth)
          .then(({ data }) => {
            if (data.success == true) {
              this.typeAuth = "code";
              this.classSlider = "form__slider--3";
              localStorage.setItem("auth.validation", data.token);
              this.errorsData = data.errors;
              this.typeAuth = "code";
              this.classSlider = "forms__slider--3";
            } else {
              this.isError = true;
              this.errorsData = data.errors;
            }
          })
          .catch((err) => {
            console.error(err);
          });
      }
    },
    handleCodeSubmit(e) {
      /**
       * Верификация кода
       */
      if (localStorage.getItem("auth.validation")) {
        this.$axios.setHeader(
          "Authorize-Validation",
          localStorage.getItem("auth.validation")
        );
        this.$auth
          .login({
            data: this.formToken,
          })
          .then(({ data }) => {
            if (data.success == true) {
              this.$metrika.reachGoal("client_register");
              this.$router.push("/");
            } else {
              this.isError = true;
              this.errorMsg = data.error;
            }
          })
          .catch((err) => {
            console.error(err);
          });
      }
    },
    handleHasCode(e) {
      if (this.typeAuth != "code") {
        this.backState.typeAuth = this.typeAuth;
        this.backState.classSlider = this.classSlider;
        this.hasCodeTitle = "Вернуться";

        this.typeAuth = "code";
        this.classSlider = "forms__slider--3";
      } else {
        this.typeAuth = this.backState.typeAuth;
        this.classSlider = this.backState.classSlider;
        this.resetBackState();
      }
    },

    resetBackState() {
      this.hasCodeTitle = "У меня уже есть код";
      this.backState = {};
    },
  },
};
</script>