<template>
  <a-layout-content>
    <a-page-header
      title="Настройки"
      sub-title=""
      @back="() => $router.go(-1)"
    ></a-page-header>
    <a-layout class="setting-layout-sider" ref="siderSetting">
      <b-layout-sider menu-key="setting-admin-directions" :open-keys="['setting-admin-directory']"/>
      <a-layout-content class="setting-layout-content">
        <a-row :gutter="[16, 16]" type="flex">
          <a-col :xs="24" :md="22" :lg="22">
            <a-button type="primary" @click="handleCreate">
              Добавить направление
              <a-icon type="plus" />
            </a-button>
          </a-col>
          <template v-for="item in dirEach">
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
                      <!-- Себестоимость -->
                      <a-form-model-item
                        has-feedback
                        label="Себестоимость в руб."
                        prop="cost_price"
                      >
                        <a-input
                          v-model="item.cost_price"
                          type="text"
                          addon-before="₽"
                          autocomplete="off"
                        />
                      </a-form-model-item>
                      <!-- Себестоимость -->
                      <a-form-model-item
                        has-feedback
                        label="Наценка"
                        prop="extra"
                      >
                        <a-input
                          v-model="item.extra"
                          type="text"
                          addon-before="%"
                          autocomplete="off"
                        />
                      </a-form-model-item>
                      <!-- Конверсия договоров -->
                      <a-form-model-item
                        has-feedback
                        label="Конверсия заключения договоров"
                        prop="conversion_contract"
                      >
                        <a-input
                          v-model="item.conversion_contract"
                          type="text"
                          addon-before="%"
                          autocomplete="off"
                        />
                      </a-form-model-item>
                      <!-- Конверсия встреч -->
                      <a-form-model-item
                        has-feedback
                        label="Конверсия встреч"
                        prop="conversion_meetings"
                      >
                        <a-input
                          v-model="item.conversion_meetings"
                          type="text"
                          addon-before="%"
                          autocomplete="off"
                        />
                      </a-form-model-item>
                      <!-- Средний чек -->
                      <a-form-model-item
                        has-feedback
                        label="Средний чек"
                        prop="average_check"
                      >
                        <a-input
                          v-model="item.average_check"
                          type="text"
                          addon-before="₽"
                          autocomplete="off"
                        />
                      </a-form-model-item>
                      <!-- Ссылка на видео -->
                      <a-form-model-item
                        has-feedback
                        label="Ссылка на видео"
                        prop="iframe_url"
                      >
                        <a-input
                          v-model="item.iframe_url"
                          type="text"
                          addon-before="http://"
                          autocomplete="off"
                        />
                      </a-form-model-item>
                      <!-- Категории -->
                      <a-form-model-item
                        has-feedback
                        label="Категории"
                        prop="categories"
                      >
                        <a-select
                          v-model="item.categories"
                          mode="multiple"
                          option-label-prop="label"
                        >
                          <a-select-option
                            v-for="cat in catEach"
                            :key="cat.id"
                            :value="cat.id"
                            :label="cat.name"
                            >{{ cat.name }}</a-select-option
                          >
                        </a-select>
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
    <a-modal
      v-model="isDeleteModal"
      title="На какое направление заменить"
      ok-text="Удалить"
      cancel-text="Отмена"
      @ok="deleteDirection"
      @cancel="hideModal"
    >
      <label>
        <div class="select-label">
          Укажите направление на которое нужно заменить
        </div>
        <a-select class="select-full" v-model="deleteForm.newDirectionId">
          <a-select-option :key="0">Ничего не выбрано</a-select-option>
          <a-select-option
            v-for="dir in dirEach.filter((f) => f.id != deleteForm.directionId)"
            :key="dir.id"
            >{{ dir.name }}</a-select-option
          >
        </a-select>
      </label>
    </a-modal>
  </a-layout-content>
</template>
<style scoped>
.directions-card {
  width: 100%;
}
.directions-card-body {
  margin-top: 20px;
}
</style>
<script lang="ts">
import Vue from "vue";
export default Vue.extend({
  name: "setting-admin-directions",
  head() {
    return {
      title: 'Администрирование направлений'
    }
  },
  data() {
    return {
      dirEach: [],
      catEach: [],
      isDeleteModal: false,
      deleteForm: { directionId: 0, newDirectionId: 0 },
      rulesForm: {
        iframe_url: [
          {
            required: true,
            message: "Поле ссылки должно быть заполнено",
          },
        ],
      },
    };
  },
  mounted() {
    this.loadDirections();
  },
  methods: {
    loadDirections() {
      const app: any = this;
      app.$axios.get("/directory").then(({ data }: any) => {
        this.catEach = data.categories;
        this.dirEach = data.directions.map((each: any) => {
          each.editable = false;
          each.categories = JSON.parse(each.categories);
          return each;
        });
      });
    },
    handleEdit(id: number, e: Event) {
      const newData = [...this.dirEach];
      const target: any = newData.find((item: any) => id === item.id);
      if (typeof target.editable === undefined) {
        target.editable = true;
      } else {
        target.editable = !target.editable;
      }
      this.dirEach = newData;
    },
    handleDelete(id: number, e: Event) {
      this.deleteForm.newDirectionId = 0;
      this.deleteForm.directionId = id;
      this.isDeleteModal = true;
    },
    hideModal(e: any) {
      this.isDeleteModal = false;
    },
    deleteDirection(e: any) {
      const { deleteForm } = this;
      if (
        deleteForm.newDirectionId !== 0 &&
        deleteForm.directionId !== deleteForm.newDirectionId
      ) {
        const app: any = this;
        app.$axios
          .post(`/directory/direction/${deleteForm.directionId}/delete`, {
            newDirectionId: deleteForm.newDirectionId,
          })
          .then(({ data }: any) => {
            if (data.success) {
              this.isDeleteModal = false;
              this.$message.success(data.message);
              this.loadDirections();
            } else {
              this.$message.error(data.error);
            }
          });
      } else {
        this.$message.error("Укажите направление");
      }
    },
    handleCreate(e: Event) {
      const app: any = this;
      app.$axios.put("/directory/direction/create").then(({ data }: any) => {
        if (data.success) {
          this.loadDirections();
        } else {
          this.$message.error(data.error);
        }
      });
    },
    submitForm(formName: any, id: Number, form: any) {
      const app : any = this;
      app.$refs[formName][0].validate((valid: any) => {
        if (valid) {
          app.$axios
            .post(`directory/direction/${id}/update`, form)
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