<template>
  <div class="form-signInUp">
    <a-card title="Вход в панель по паролю">
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
              <a-form-model-item has-feedback label="Телефон" prop="phone">
                <a-input
                  v-model="formSignIn.phone"
                  placeholder="Телефон"
                  v-mask="maskPhone"
                />
              </a-form-model-item>
              <a-form-model-item has-feedback label="Пароль" prop="password">
                <a-input
                  v-model="formSignIn.password"
                  placeholder="Пароль"
                  type="password"
                />
              </a-form-model-item>
            </a-form-model>
            <a-form-model-item>
              <a-alert :message="errorMsg" type="error" v-show="isError" />
            </a-form-model-item>
            <a-form-model-item>
              <a-button type="primary" @click="handleSignIn">
                <template>Войти</template>
              </a-button>
            </a-form-model-item>
            <a-form-model-item>
              <nuxt-link to="/sign">Войти по смс</nuxt-link>
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
              <template>
                <a-form-model-item has-feedback label="Ваше ФИО" prop="name">
                  <a-input v-model="formSignUp.name" placeholder="Имя" />
                </a-form-model-item>
                <a-form-model-item has-feedback label="Email" prop="email">
                  <a-input v-model="formSignUp.email" placeholder="Email" />
                </a-form-model-item>
                <a-form-model-item
                  has-feedback
                  label="Ваша специализация"
                  prop="category_id"
                >
                  <a-select v-model="formSignUp.category_id">
                    <a-select-option
                      v-for="(category, index) in $directory.categories"
                      :key="index"
                      :value="category.id"
                    >
                      {{ category.name }}
                    </a-select-option>
                  </a-select>
                </a-form-model-item>
                <a-form-model-item
                  has-feedback
                  label="Телефон"
                  prop="phone"
                  v-mask="maskPhone"
                >
                  <a-input v-model="formSignUp.phone" placeholder="Телефон" />
                </a-form-model-item>
                <a-form-model-item has-feedback label="Пароль" prop="password">
                  <a-input
                    v-model="formSignUp.password"
                    placeholder="Пароль"
                    type="password"
                  />
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
                  <template>Регистрация</template>
                </a-button>
              </a-form-model-item>
              <a-form-model-item>
                <nuxt-link to="/sign">Регистрация по смс</nuxt-link>
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
  name: "sign-login",
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
      delayTime: 500,
      rules: {
        signIn: {
          phone: {
            required: true,
            message: "Поле телефон обязательно",
          },
          password: {
            required: true,
            message: "Поле с паролем обязательно",
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
          password: [
            {
              required: true,
              message: "Поле с паролем обязательно",
            },
          ],
          isOfferta: [
            {
              required: true,
              message: "Для регистрации вы должны согласиться с условиями",
            },
          ],
          category_id: [
            {
              required: true,
              message: "Обязательно нужно выбрать специализацию",
            },
          ],
        },
      },
      formSignUp: {
        email: "",
        name: "",
        phone: "",
        password: "",
        category_id: "",
      },
      formSignIn: {
        phone: "",
        password: "",
      },
      maskPhone: "+7 (###) ###-##-##",
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
          this.isLoading = true;
          const { name, email, phone, password } = this.formSignUp;
          this.$axios
            .post("/sign/register", {
              name: name,
              email: email,
              phone: phone,
              password: password,
            })
            .then(({ data }) => {
              this.errorMsg = data.errorsData;
              this.errorData = data.errors;
              if (data.success == true) {
                this.$auth
                  .setUserToken(data.token, false)
                  .then(() => this.$toast.success("User set!"));
              } else {
                this.isError = true;
              }
              this.isLoading = false;
            })
            .catch((err) => {
              this.isLoading = false;
              console.error(err);
            });
        }
      });
    },
    handleSignIn() {
      this.$refs.formSignIn.validate((valid) => {
        if (valid) {
          this.isLoading = true;
          const { phone, password } = this.formSignIn;
          this.$axios
            .post("/sign/login", {
              phone: phone,
              password: password,
            })
            .then(({ data }) => {
              this.errorMsg = data.errors;
              this.errorData = data.errors;
              if (data.success == true) {
                this.$auth
                  .setUserToken(data.token, data.token)
                  .then(() => this.$toast.success("User set!"));
              } else {
                this.isError = true;
              }
              this.isLoading = false;
            })
            .catch((err) => {
              this.isError = true;
              this.isLoading = false;
              console.error(err);
            });
        } else {
          console.log("error submit!!");
          this.isLoading = false;
          return false;
        }
      });
    },
  },
};
</script>