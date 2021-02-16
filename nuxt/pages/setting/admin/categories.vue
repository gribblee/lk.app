<template>
  <a-layout-content>
    <a-page-header
      title="Настройки"
      sub-title="Категории"
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
          <template v-for="item in catEach">
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
                <a-card-meta
                  :title="item.name"
                  :description="['Описание: ', item.description]"
                />
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
                      <!-- Source ID -->
                      <a-form-model-item
                        has-feedback
                        label="ID источник(Bitrix)"
                        prop="name"
                      >
                        <a-input-number
                          v-model="item.source_id"
                          autocomplete="off"
                          class="input-full"
                        />
                      </a-form-model-item>
                      <!-- Описание -->
                      <a-form-model-item
                        has-feedback
                        label="Описание"
                        prop="description"
                      >
                        <a-textarea
                          v-model="item.description"
                          autocomplete="off"
                          :auto-size="{ minRows: 2, maxRows: 6 }"
                        />
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
<style scoped>
.input-full {
  width: 100%;
}
</style>
<script lang="ts">
import Vue from "vue";
export default Vue.extend({
  name: "categories",
  head() {
    return {
      title: "Категории",
    };
  },
  data() {
    return {
      catEach: [],
      rulesForm: {
        name: [
          {
            required: true,
            message: "Поле ссылки должно быть заполнено",
          },
        ],
        description: [
          {
            required: false,
          },
        ],
        source_id: [
          {
            required: false,
          },
        ],
      },
    };
  },
  created() {
    const { loadCategories } = this;
    loadCategories();
  },
  methods: {
    handleCreate(e: any) {
      const app: any = this;
      app.$axios.put("/category/create").then(({ data }: any) => {
        if (data.success) {
          this.loadCategories();
        } else {
          this.$message.error(data.error);
        }
      });
    },
    handleEdit(id: number, e: Event) {
      const newData = [...this.catEach];
      const target: any = newData.find((item: any) => id === item.id);
      if (typeof target.editable === undefined) {
        target.editable = true;
      } else {
        target.editable = !target.editable;
      }
      this.catEach = newData;
    },
    handleDelete(id: number, e: Event) {
      const app: any = this;
      app.$axios.delete(`/category/${id}/delete`).then(({ data }: any) => {
        if (data.success) {
          this.$message.success(data.message);
          this.loadCategories();
        } else {
          this.$message.error(data.error);
        }
      });
    },
    loadCategories() {
      const app: any = this;
      app.$axios.get("/categories").then(({ data }: any) => {
        this.catEach = data.map((each: any) => {
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
            .post(`category/${id}/update`, form)
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