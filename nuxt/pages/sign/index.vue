<template>
  <div class="form-signInUp">
    <a-card title="Вход в панель">
      <a-spin :spinning="isLoading" :delay="delayTime">
        <a-icon slot="indicator" type="loading" style="font-size: 24px" spin />
        <a-tabs default-active-key="1">
          <a-tab-pane key="1">
            <span slot="tab">
              <a-icon type="login" />
              Авторизация
            </span>
            <a-form-model
              :model="formSignIn"
              :rules="rules.signIn"
              ref="formSignIn"
            >
              <a-form-model-item
                label="Код из СМС"
                prop="code"
                v-show="signIsCode"
              >
                <a-input
                  v-model="formSignIn.code"
                  placeholder="Код из смс"
                  v-mask="maskCode"
                />
              </a-form-model-item>
              <a-form-model-item
                has-feedback
                label="Телефон"
                prop="phone"
                v-show="!signIsCode"
              >
                <a-input
                  v-model="formSignIn.phone"
                  placeholder="Телефон"
                  v-mask="maskPhone"
                />
              </a-form-model-item>
            </a-form-model>
            <a-form-model-item>
              <a-alert :message="errorMsg" type="error" v-show="isError" />
            </a-form-model-item>
            <a-form-model-item>
              <a-button type="primary" @click="handleSignIn">
                <template v-if="!signIsCode">Далее</template>
                <template v-if="signIsCode">Войти</template>
              </a-button>
              {{ signIsCode ? "true" : "false" }}
              <a-button type="link" @click="() => (signIsCode = !signIsCode)">
                <template v-if="!signIsCode">У меня уже есть код</template>
                <template v-if="signIsCode">Вернуться</template></a-button
              >
            </a-form-model-item>
          </a-tab-pane>
          <a-tab-pane key="2">
            <span slot="tab">
              <a-icon type="profile" />
              Регистрация
            </span>
            <a-form-model
              :model="formSignUp"
              :rules="rules.signUp"
              ref="formSignUp"
            >
              <template v-show="!signUpIsCode">
                <a-form-model-item has-feedback label="Ваше ФИО" prop="name">
                  <a-input v-model="formSignUp.name" placeholder="Имя" />
                </a-form-model-item>
                <a-form-model-item has-feedback label="Email" prop="email">
                  <a-input v-model="formSignUp.email" placeholder="Email" />
                </a-form-model-item>
                <a-form-model-item
                  has-feedback
                  label="Телефон"
                  prop="phone"
                  v-mask="maskPhone"
                >
                  <a-input v-model="formSignUp.phone" placeholder="Телефон" />
                </a-form-model-item>
                <a-form-model-item prop="isOfferta">
                  <a-checkbox v-model="formSignUp.isOfferta"
                    >Я согласен с
                    <a
                      href="https://docs.google.com/document/d/1jQdxmXkYby1lG2IJ_wJdp8xzkdIhMsIswxI2OSAgr9o/edit"
                      target="_blank"
                      >правилами пользования платформой</a
                    ></a-checkbox
                  >
                </a-form-model-item>
              </template>
              <a-form-model-item
                has-feedback
                label="Код из СМС"
                prop="code"
                v-show="signUpIsCode"
              >
                <a-input
                  v-model="formSignUp.code"
                  placeholder="Код из смс"
                  v-mask="maskCode"
                />
              </a-form-model-item>
              <a-form-model-item>
                <a-alert
                  type="error"
                  :message="errorMsg"
                  banner
                  v-show="isError"
                />
              </a-form-model-item>
              <a-form-model-item>
                <a-button type="primary" @click="handleSignUp">
                  <template v-if="!signUpIsCode">Далее</template>
                  <template v-if="signUpIsCode">Войти</template>
                </a-button>
                <a-button
                  type="link"
                  @click="() => (signUpIsCode = !signUpIsCode)"
                >
                  <template v-if="!signUpIsCode">У меня уже есть код</template>
                  <template v-if="signUpIsCode">Вернуться</template></a-button
                >
              </a-form-model-item>
            </a-form-model>
          </a-tab-pane>
        </a-tabs>
      </a-spin>
    </a-card>
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
    let ValidateEmail = (rule, value, callback) => {
      const re = /^(([^<>()[\]\\.,;:\s@"]+(\.[^<>()[\]\\.,;:\s@"]+)*)|(".+"))@((\[[0-9]{1,3}\.[0-9]{1,3}\.[0-9]{1,3}\.[0-9]{1,3}\])|(([a-zA-Z\-0-9]+\.)+[a-zA-Z]{2,}))$/;
      if (!re.test(String(value).toLowerCase()) && String(value).length > 0) {
        callback(new Error("Пожалуйста введите email адрес"));
      } else {
        callback();
      }
    };
    let ValidateName = (rule, value, callback) => {
      const re = /^[а-яё]{3,}([-][а-яё]{2,})?\s[а-яё]{2,}\s[а-яё]{3,}$/;
      if (!re.test(String(value).toLowerCase())) {
        callback(new Error("Введите ФИО"));
      }
      callback();
    };
    return {
      isLoading: false,
      signIsCode: false,
      signUpIsCode: false,
      delayTime: 500,
      rules: {
        signIn: {
          phone: {
            required: true,
            message: "Поле телефон обязательно",
          },
          code: {
            required: this.signIsCode,
            message: "Поле с кодом обязательно",
          },
        },
        signUp: {
          name: [
            {
              required: true,
              message: "Поле имя обязательно",
            },
            { validator: ValidateName, trigger: "change" },
          ],
          email: [
            {
              required: true,
              message: "Поле Email обязательно",
            },
            { validator: ValidateEmail, trigger: "change" },
          ],
          phone: [
            {
              required: true,
              message: "Поле телефон обязательно",
            },
          ],
          isOfferta: [
            {
              required: true,
              message: "Для регистрации вы должны согласиться с условиями",
            },
          ],
          code: [
            {
              required: this.signUpIsCode,
              message: "Поле с кодом обязательно",
            },
          ],
        },
      },
      formSignUp: {
        email: "",
        name: "",
        phone: "",
        code: "",
      },
      formSignIn: {
        phone: "",
        code: "",
      },
      maskPhone: "+7 (###) ###-##-##",
      maskCode: "###-###",
      isError: false,
      errorMsg: "",
      errorsData: {},
    };
  },
  created() {},
  mounted() {},
  methods: {
    handleSignUp() {
      this.$refs.formSignUp.validate((valid) => {
        if (valid) {
          if (!this.signUpIsCode) {
            this.isLoading = true;
            const { name, email, phone } = this.formSignUp;
            this.$axios
              .post("/register", {
                name: name,
                email: email,
                phone: phone,
              })
              .then(({ data }) => {
                if (data.success == true) {
                  localStorage.setItem("auth.validation", data.token);
                } else {
                  this.isError = true;
                  this.errorsData = data.errors;
                }
                this.signUpIsCode = true;
                this.isLoading = false;
              })
              .catch((err) => {
                console.error(err);
              });
          } else {
            this.handleCodeSubmit(this.formSignUp);
          }
        }
      });
    },
    handleSignIn() {
      this.$refs.formSignIn.validate((valid) => {
        if (valid) {
          this.isLoading = true;
          if (!this.signIsCode) {
            const { phone } = this.formSignIn;
            this.$axios
              .post("/login", {
                phone: phone,
              })
              .then(({ data }) => {
                if (data.success == true) {
                  this.signIsCode = true;
                  localStorage.setItem("auth.validation", data.token);
                  this.errorsData = data.errors;
                } else {
                  this.isError = true;
                  this.errorsData = data.errors;
                }
                this.isLoading = false;
              })
              .catch((err) => {
                console.error(err);
              });
          } else {
            this.handleCodeSubmit(this.formSignIn);
          }
        } else {
          console.log("error submit!!");
          return false;
        }
      });
    },
    handleCodeSubmit(form) {
      /**
       * Верификация кода
       */
      this.isLoading = true;
      if (localStorage.getItem("auth.validation")) {
        this.$axios.setHeader(
          "Authorize-Validation",
          localStorage.getItem("auth.validation")
        );
        this.$auth
          .login({
            data: {
              passphrase: form.code,
            },
          })
          .then(({ data }) => {
            if (data.success == true) {
              this.$metrika.reachGoal("client_register");
              this.$router.push("/");
            } else {
              this.isError = true;
              this.errorMsg = data.error;
            }
            this.isLoading = false;
          })
          .catch((err) => {
            console.error(err);
            this.isLoading = false;
          });
      }
    },
  },
};
</script>