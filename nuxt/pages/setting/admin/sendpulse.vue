<template>
  <a-layout-content>
    <a-page-header
      title="Настройки"
      sub-title=""
      @back="() => $router.go(-1)"
    ></a-page-header>
    <a-layout class="setting-layout-sider" ref="siderSetting">
      <b-layout-sider
        menu-key="setting-admin-sendpulse"
        :open-keys="['setting-admin-directory']"
      />
      <a-layout-content class="setting-layout-content">
        <a-row :gutter="[16, 16]" type="flex" justify="space-around">
          <a-col :xs="24" :md="10" :lg="10">
            <h1 class="typhography-header">Адресные книги</h1>
            <a-form-model ref="userForm" :model="userForm" :rules="rules">
              <a-form-model-item>
                <a-form-model-item
                  has-feedback
                  label="Регистрация"
                  prop="bookIdRegister"
                >
                  <a-input
                    v-model="userForm.bookIdRegister"
                    type="text"
                    autocomplete="off"
                  />
                </a-form-model-item>
                <a-form-model-item
                  has-feedback
                  label="Выставление счёта"
                  prop="bookIdBill"
                >
                  <a-input
                    v-model="userForm.bookIdBill"
                    type="text"
                    autocomplete="off"
                  />
                </a-form-model-item>
                <a-form-model-item
                  has-feedback
                  label="Баланс пополнен"
                  prop="bookIdBalance"
                >
                  <a-input
                    v-model="userForm.bookIdBalance"
                    type="text"
                    autocomplete="off"
                  />
                </a-form-model-item>
                <a-form-model-item
                  has-feedback
                  label="Неактивность клиента"
                  prop="bookIdWasOnline"
                >
                  <a-input
                    v-model="userForm.bookIdWasOnline"
                    type="text"
                    autocomplete="off"
                  />
                </a-form-model-item>
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
<style scoped>
.typhography-header {
  font-size: 24px;
  font-weight: bold;
}
</style>
<script lang="ts">
import Vue from "vue";
export default Vue.extend({
  name: "setting-admin-sendpulse",
  head() {
    return {
      title: "Опции SendPulse",
    };
  },
  data() {
    return {
      rules: {},
      userForm: {
        bookIdRegister: "",
        bookIdBill: "",
        bookIdBalance: "",
        bookIdWasOnline: "",
      },
    };
  },
  mounted() {
    const app: any = this;
    app.$axios.get("/directory").then(({ data }: any) => {
      let options = data.options;
      if (
        typeof options.bookIdRegister != "undefined" &&
        typeof options.bookIdBill != "undefined" &&
        typeof options.bookIdBalance != "undefined" &&
        typeof options.bookIdWasOnline != "undefined"
      ) {
        app.userForm.bookIdRegister = options.bookIdRegister;
        app.userForm.bookIdBill = options.bookIdBill;
        app.userForm.bookIdBalance = options.bookIdBill;
        app.userForm.bookIdWasOnline = options.bookIdWasOnline;
      }
    });
  },
  methods: {
    submitForm(formName: any) {
      const app: any = this;
      app.$refs[formName].validate((valid: any) => {
        if (valid) {
          app.$axios
            .post("/option/save", { formOptions: this.userForm })
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