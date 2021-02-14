<template>
  <a-layout-content>
    <a-page-header
      title="Настройки"
      sub-title="Статусы спора"
      @back="() => $router.go(-1)"
    ></a-page-header>
    <a-layout class="setting-layout-sider">
      <b-layout-sider :menu-key="$route.name" />
      <a-layout-content class="setting-layout-content">
        <a-row :gutter="[16, 16]" type="flex">
          <a-col :xs="24" :md="22" :lg="22">
            <a-button type="primary" @click="handleCreate">
              Добавить категорию
              <a-icon type="plus" />
            </a-button>
          </a-col>
          <template v-for="item in disputEach">
            <a-col :xs="24" :md="10" :lg="10" :key="item.id">
              <a-card hoverable class="directions-card">
                <template slot="actions" class="ant-card-actions">
                  <a-popconfirm
                    title="Действительно хотите удалить направление"
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
                <a-card-meta :title="item.name" />
                <div v-show="item.editable">
                  <div class="directions-card-body">
                    <a-form-model
                      :ref="['userForm', item.id].join('')"
                      :model="item"
                      :rules="rulesForm"
                    >
                      <!-- Имя -->
                      <a-form-model-item
                        has-feedback
                        label="Название"
                        prop="name"
                      >
                        <a-input
                          v-model="item.name"
                          type="text"
                          autocomplete="off"
                        />
                      </a-form-model-item>
                      <!-- Описание -->
                      <a-form-model-item
                        has-feedback
                        label="Тип спора"
                        prop="disput_type_id"
                      >
                        <a-select v-model="item.disput_type_id">
                          <a-select-option
                            v-for="disputType in disputTypes"
                            :key="disputType.id"
                          >
                            {{ disputType.name }}
                          </a-select-option>
                        </a-select>
                      </a-form-model-item>
                      <!-- Сортировка -->
                      <a-form-model-item
                        has-feedback
                        label="Сортировка"
                        prop="order_by"
                      >
                        <a-slider v-model="item.order_by" :max="10000" />
                      </a-form-model-item>
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
                    </a-form-model>
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
<script lang="ts">
import Vue from "vue";
export default Vue.extend({
  name: "categories",
  head() {
    return {
      title: "Статусы спора",
    };
  },
  data() {
    return {
      disputEach: [],
      disputTypes: [
        {
          id: 1,
          name: "Общие",
        },
        {
          id: 2,
          name: "Важные",
        },
        {
          id: 3,
          name: "Принятые",
        },
        {
          id: 4,
          name: "Спорные",
        },
        {
          id: 5,
          name: "Другие",
        },
      ],
      rulesForm: {
        name: [
          {
            required: true,
            message: "Поле ссылки должно быть заполнено",
          },
        ],
        disput_type_id: [
          {
            required: true,
          },
        ],
      },
    };
  },
  created() {
    const { disputStatuses } = this;
    disputStatuses();
  },
  methods: {
    handleCreate(e: any) {
      const app: any = this;
      app.$axios.put("/disput_statuscreate").then(({ data }: any) => {
        if (data.success) {
          this.disputStatuses();
        } else {
          this.$message.error(data.error);
        }
      });
    },
    handleEdit(id: number, e: Event) {
      const newData = [...this.disputEach];
      const target: any = newData.find((item: any) => id === item.id);
      if (typeof target.editable === undefined) {
        target.editable = true;
      } else {
        target.editable = !target.editable;
      }
      this.disputEach = newData;
    },
    handleDelete(id: number, e: Event) {
      const app: any = this;
      app.$axios.delete(`/disput_status/${id}/delete`).then(({ data }: any) => {
        if (data.success) {
          this.$message.success(data.message);
          this.disputStatuses();
        } else {
          this.$message.error(data.error);
        }
      });
    },
    disputStatuses() {
      const app: any = this;
      app.$axios.get("/disput_status").then(({ data }: any) => {
        this.disputEach = data.map((each: any) => {
          each.editable = false;
          return each;
        });
      });
    },
    submitForm(formName: any, id: Number, form: any) {
      const app: any = this;
      app.$refs[formName][0].validate((valid: any) => {
        if (valid) {
          app.$axios
            .post(`disput_status/${id}/update`, form)
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