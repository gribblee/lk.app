<template>
  <a-layout-content>
    <a-page-header
      title="Настройки"
      sub-title="Реквизиты"
      @back="() => $router.go(-1)"
    ></a-page-header>
    <a-layout class="setting-layout-sider">
      <b-layout-sider :menu-key="$route.name" />
      <a-layout-content class="setting-layout-content">
        <a-row :gutter="[16, 16]" type="flex">
          <a-col :xs="24" :md="22" :lg="22">
            <a-button type="primary" @click="handleCreate">
              Добавить Реквизиты
              <a-icon type="plus" />
            </a-button>
          </a-col>
          <template v-for="item in requisitesData">
            <a-col :xs="24" :md="6" :lg="8" :key="item.id">
              <a-card hoverable class="requisite-card">
                <template slot="actions" class="ant-card-actions">
                  <a-popconfirm
                    title="Действительно хотите удалить реквизиты"
                    ok-text="Да"
                    cancel-text="Нет"
                    @confirm="handleDelete(item.id, $event)"
                  >
                    <a key="delete">
                      <a-icon type="delete" :style="{ color: '#FF0000' }" />
                    </a>
                  </a-popconfirm>
                  <a key="edit" @click="handleEdit(item.id, $event)">
                    <a-icon type="edit" />
                  </a>
                </template>
                <a-card-meta
                  :title="item.name"
                  :description="['ИНН: ', item.inn]"
                />
                <div v-show="item.editable">
                  <div class="requisite-card-body">
                    <a-form-model
                      :ref="['userForm', item.id].join('')"
                      :model="item"
                      :rules="rulesForm"
                    >
                      <!-- Имя -->
                      <a-form-model-item
                        has-feedback
                        label="Полное название"
                        prop="name"
                      >
                        <a-input
                          v-model="item.name"
                          type="text"
                          autocomplete="off"
                        />
                      </a-form-model-item>
                      <!-- ОГРН -->
                      <a-form-model-item has-feedback label="ОГРН" prop="ogrn">
                        <a-input
                          v-model="item.ogrn"
                          type="text"
                          autocomplete="off"
                        />
                      </a-form-model-item>
                      <!-- ИНН -->
                      <a-form-model-item has-feedback label="ИНН" prop="inn">
                        <a-input
                          v-model="item.inn"
                          type="text"
                          autocomplete="off"
                        />
                      </a-form-model-item>
                      <!-- КПП -->
                      <a-form-model-item has-feedback label="КПП" prop="kpp">
                        <a-input
                          v-model="item.kpp"
                          type="text"
                          autocomplete="off"
                        />
                      </a-form-model-item>
                      <!-- БИК -->
                      <a-form-model-item has-feedback label="БИК" prop="bik">
                        <a-input
                          v-model="item.bik"
                          type="text"
                          autocomplete="off"
                        />
                      </a-form-model-item>
                      <!-- Банк -->
                      <a-form-model-item has-feedback label="Банк" prop="bank">
                        <a-input
                          v-model="item.bank"
                          type="text"
                          autocomplete="off"
                        />
                      </a-form-model-item>
                      <!-- К/СЧ -->
                      <a-form-model-item has-feedback label="К/СЧ" prop="ksch">
                        <a-input
                          v-model="item.ksch"
                          type="text"
                          autocomplete="off"
                        />
                      </a-form-model-item>
                      <!-- Р/СЧ -->
                      <a-form-model-item has-feedback label="Р/СЧ" prop="rsch">
                        <a-input
                          v-model="item.rsch"
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
                          v-model="item.jour_address"
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
                          v-model="item.poste_address"
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
                          v-model="item.director"
                          type="text"
                          autocomplete="off"
                        />
                      </a-form-model-item>
                    </a-form-model>
                    <a-form-model-item>
                      <a-button
                        type="primary"
                        @click="
                          submitForm(
                            ['userForm', item.id].join(''),
                            item.id,
                            item
                          )
                        "
                      >
                        Сохранить
                      </a-button>
                    </a-form-model-item>
                  </div>
                </div>
              </a-card>
            </a-col>
          </template>
        </a-row>
      </a-layout-content>
    </a-layout>
  </a-layout-content>
</template>
<style scoped>
.requisite-card {
  width: 100%;
}
.requisite-card-body {
  margin-top: 20px;
}
</style>
<script lang="ts">
import Vue from "vue";
import bLayoutSider from "../../components/b-layout-sider.vue";
export default Vue.extend({
  components: { bLayoutSider },
  name: "requisite",
  head() {
    return {
      title: "Реквизиты",
    };
  },
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
      requisitesData: [],
      rulesForm: {
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
    };
  },
  created() {
    const app: any = this;
    app.$axios
      .get("/requisites")
      .then(({ data }: any) => {
        this.requisitesData = data;
      })
      .catch((_err: any) => {
        console.error(_err);
      });
  },
  methods: {
    handleCreate(e: Event) {
      const app: any = this;
      app.$axios
        .get("/requisite/create")
        .then(({ data }: never) => {
          this.requisitesData.unshift(data);
        })
        .catch((_err: any) => {
          console.error(_err);
        });
    },
    handleEdit(id: Number, e: Event) {
      const newData = [...this.requisitesData];
      const target: any = newData.find((item: any) => id === item.id);
      if (typeof target.editable === undefined) {
        target.editable = true;
      } else {
        target.editable = !target.editable;
      }
      this.requisitesData = newData;
    },
    handleDelete(id: any, e: Event) {
      const app: any = this;
      app.$axios
        .get(`/requisite/${id}/delete`)
        .then(({ data }: any) => {
          if (data.success === true) {
            const spliceIndex = this.requisitesData.findIndex((item : any) => item.id === id);
            this.requisitesData.splice(spliceIndex, 1);
          }
        })
        .catch((_err: any) => {
          console.error(_err);
        });
    },
    submitForm(formName: any, id: Number, form: any) {
      this.$refs[formName][0].validate((valid: any) => {
        if (valid) {
          const app: any = this;
          app.$axios
            .post(`requisite/${id}/update`, form)
            .then(({ data }: any) => {
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