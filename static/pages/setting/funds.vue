<template>
  <a-layout-content>
    <a-page-header
      title="Вывод средств"
      sub-title="Реквизиты"
      @back="() => $router.go(-1)"
    ></a-page-header>
    <a-layout class="setting-layout-sider">
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
                  :description="['ФИО: ', item.requisite.name]"
                />
                <div v-show="item.editable">
                  <div class="requisite-card-body">
                    <a-form-model
                      :ref="['userForm', item.id].join('')"
                      :model="item"
                      :rules="rulesForm"
                    >
                      <!-- Номер карты -->
                      <a-form-model-item
                        has-feedback
                        label="Номер счёта"
                        prop="bill"
                      >
                        <a-input
                          v-model="item.requisite.bill"
                          type="text"
                          autocomplete="off"
                        />
                      </a-form-model-item>
                      <!-- ФИО -->
                      <a-form-model-item has-feedback label="ФИО" prop="name">
                        <a-input
                          v-model="item.requisite.name"
                          type="text"
                          autocomplete="off"
                        />
                      </a-form-model-item>
                      <!-- БИК банка -->
                      <a-form-model-item
                        has-feedback
                        label="БИК Банка"
                        prop="bik"
                      >
                        <a-input
                          v-model="item.bik"
                          type="text"
                          autocomplete="off"
                        />
                      </a-form-model-item>
                      <!-- ТИП реквизитов -->
                      <a-form-model-item
                        has-feedback
                        label="Тип реквизитов"
                        prop="type"
                      >
                        <a-select
                          v-model="item.type"
                          type="text"
                          autocomplete="off"
                        >
                          <a-select-option value="CARD">Карта</a-select-option>
                          <a-select-option value="REQUISITE">Расчётный счёт</a-select-option>
                        </a-select>
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
export default Vue.extend({
  data() {
    return {
      requisitesData: [],
      rulesForm: {},
    };
  },
  created() {
    const app: any = this;
    app.$axios
      .get("/me/requisites")
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
        .get("/me/requisite/create")
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
        .get(`/me/requisite/${id}/delete`)
        .then(({ data }: any) => {
          if (data.success === true) {
            const spliceIndex = this.requisitesData.findIndex(
              (item: any) => item.id === id
            );
            this.requisitesData.splice(spliceIndex, 1);
          }
        })
        .catch((_err: any) => {
          console.error(_err);
        });
    },
    submitForm(formName: any, id: Number, form: any) {
      const app: any = this;
      app.$refs[formName][0].validate((valid: any) => {
        if (valid) {
          const app: any = this;
          app.$axios
            .post(`requisite/${id}/update`, form)
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
