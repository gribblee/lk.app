<template>
  <a-layout-content>
    <a-page-header
      title="Настройки"
      sub-title=""
      @back="() => $router.go(-1)"
    ></a-page-header>
    <a-layout class="setting-layout-sider" ref="siderSetting">
      <b-layout-sider menu-key="general" />
      <a-layout-content class="setting-layout-content">
        <a-row :gutter="[16, 16]" type="flex" justify="space-around">
          <a-col :xs="22" :md="10" :lg="10">
            <a-form-model ref="userForm" :model="userForm" :rules="rules">
              <a-form-model-item has-feedback label="Ваше ФИО" prop="name">
                <a-input
                  v-model="userForm.name"
                  type="text"
                  autocomplete="off"
                />
              </a-form-model-item>
              <a-form-model-item
                has-feedback
                label="Ваша специализация"
                prop="category_id"
              >
                <a-select v-model="userForm.category_id">
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
                label="Ваш регион"
                prop="region"
              >
                <a-select v-model="userForm.region_id">
                  <a-select-option
                    v-for="(region, index) in $directory.regions"
                    :key="index"
                    :value="region.id"
                  >
                    {{ region.name_with_type }}
                  </a-select-option>
                </a-select>
              </a-form-model-item>
              <a-form-model-item has-feedback label="Email" prop="email">
                <a-input
                  v-model="userForm.email"
                  type="email"
                  autocomplete="off"
                />
              </a-form-model-item>
              <a-form-model-item
                has-feedback
                label="Email для уведомлений"
                prop="emailNotification"
              >
                <a-input
                  v-model="userForm.emailNotification"
                  type="email"
                  autocomplete="off"
                />
              </a-form-model-item>
              <a-form-model-item has-feedback label="Телефон" prop="phone">
                <a-input
                  v-model="userForm.phone"
                  type="phone"
                  autocomplete="off"
                  v-mask="mask"
                />
              </a-form-model-item>
              <a-form-model-item
                has-feedback
                label="Старый пароль"
                prop="password"
              >
                <a-input
                  v-model="userForm.password"
                  type="password"
                  autocomplete="off"
                />
                <a-alert
                  type="error"
                  :message="errorPass"
                  v-if="errorPass.length > 0"
                />
              </a-form-model-item>
              <a-form-model-item
                has-feedback
                label="Новый пароль"
                prop="password_new"
              >
                <a-input
                  v-model="userForm.password_new"
                  type="password"
                  autocomplete="off"
                />
              </a-form-model-item>
              <a-form-model-item>
                <a-button type="primary" @click="submitForm('userForm')">
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
<script lang="js">
import Vue from "vue";
import bLayoutSider from "../../components/b-layout-sider.vue";

export default Vue.extend({
  components: { bLayoutSider },
  name: "setting",
  head() {
    return {
      title: "Настройки",
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
    let ValidatePhone = (rule, value, callback) => {
      const re = /(\+7)([ .-]?)\(?([0-9]{3})\)?([ .-]?)([0-9]{3})\2?([ .-]?)([0-9]{2})([ .-]?)([0-9]{2})/;
      if (!re.test(String(value).toLowerCase())) {
        callback(new Error("Введите коректный телефон"));
      }
      callback();
    };
    let ValidateName = (rule, value, callback) => {
      const re = /^[а-яё]{3,}([-][а-яё]{2,})?\s[а-яё]{2,}\s[а-яё]{3,}$/;
      if (!re.test(String(value).toLowerCase())) {
        callback(new Error("Введите ФИО"));
      }
      callback();
    };

    return {
      mask: "+7 (###) ###-##-##",
      errorPass: '',
      rules: {
        name: [
          { required: true, message: "Имя обязателен", trigger: "change" },
          { validator: ValidateName, trigger: "change"}
        ],
        email: [
          { validator: ValidateEmail, trigger: "change" },
          { required: true, message: "Email обязателен", trigger: "change" },
        ],
        emailNotification: [{ validator: ValidateEmail, trigger: "change" }],
        phone: [
          { validator: ValidatePhone, trigger: "change" },
          { required: true, message: "Телефон обязателен", trigger: "change" },
        ],
        password: [],
        password_new: [],
        category_id: [],
        region_id: []
      },
      userForm: {
        name: "",
        category_id: "",
        region_id: "",
        email: "",
        emailNotification: "",
        phone: "",
        password: "",
        password_new: ""
      },
    };
  },
  mounted() {
    console.log(this.user);
    const { name, email, email_notification, phone, category_id, region } = this.user;
    this.userForm = {
      name,
      email,
      emailNotification: email_notification,
      phone,
      category_id,
      region_id: region.id
    };
  },
  methods: {
    submitForm(formName) {
      this.$refs[formName].validate((valid) => {
        if (valid) {
          this.$axios.post("/user/update", this.userForm).then(({ data }) => {
            if (data.success) {
              this.$message.success(data.message);
              if (data.errors.pass) {
                this.errorPass = data.errors.pass;
              } else {
                this.errorPass = '';
              }
            } else {
              this.$message.error(data.error);
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