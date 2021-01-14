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
              <a-form-model-item has-feedback label="Имя" prop="name">
                <a-input
                  v-model="userForm.name"
                  type="text"
                  autocomplete="off"
                />
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

    return {
      mask: "+7 (###) ###-##-##",
      rules: {
        name: [
          { required: true, message: "Имя обязателен", trigger: "change" },
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
      },
      userForm: {
        name: "",
        email: "",
        emailNotification: "",
        phone: "",
      },
    };
  },
  mounted() {
    const { name, email, email_notification, phone } = this.user;
    this.userForm = {
      name,
      email,
      emailNotification: email_notification,
      phone,
    };
  },
  methods: {
    submitForm(formName) {
      this.$refs[formName].validate((valid) => {
        if (valid) {
          $axios.post("/user/update", this.userForm).then(({ data}) => {
            if (data.success) {
              this.$message.success(data.message);
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