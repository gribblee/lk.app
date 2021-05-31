<template>
  <a-layout-content>
    <a-page-header
      title="Настройки"
      sub-title=""
      @back="() => $router.go(-1)"
    ></a-page-header>
    <a-layout class="setting-layout-sider" ref="siderSetting">
      <b-layout-sider
        menu-key="setting-admin-regions"
        :open-keys="['setting-admin-directory']"
      />
      <a-layout-content class="setting-layout-content">
        <a-spin :spinning="isLoading" :delay="delayTime">
          <a-row :gutter="[16, 16]" type="flex">
            <a-col :xs="24" :md="22" :lg="22">
              <a-button type="primary" @click="handleCreate">
                Добавить регион
                <a-icon type="plus" />
              </a-button>
            </a-col>
            <template v-for="item in regEach">
              <a-col :xs="24" :md="8" :lg="8" :key="item.id">
                <a-card hoverable class="regions-card">
                  <template slot="actions" class="ant-card-actions">
                    <a-popconfirm
                      title="Действительно хотите удалить регион"
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
                    :description="['Описание: ', item.name_with_type]"
                  />
                  <div v-show="item.editable">
                    <div class="regions-card-body">
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
                        <!-- Тип -->
                        <a-form-model-item has-feedback label="Тип" prop="type">
                          <a-input
                            v-model="item.type"
                            type="text"
                            autocomplete="off"
                          />
                        </a-form-model-item>
                        <!-- Полное название -->
                        <a-form-model-item
                          has-feedback
                          label="name_with_type"
                          prop="name_with_type"
                        >
                          <a-input
                            v-model="item.name_with_type"
                            type="text"
                            autocomplete="off"
                          />
                        </a-form-model-item>
                        <!-- Федеральный район -->
                        <a-form-model-item
                          has-feedback
                          label="Федерьальный район"
                          prop="federal_district"
                        >
                          <a-input
                            v-model="item.federal_district"
                            type="text"
                            autocomplete="off"
                          />
                        </a-form-model-item>
                        <!-- Кладр ID -->
                        <a-form-model-item
                          has-feedback
                          label="Кладр ID"
                          prop="kladr_id"
                        >
                          <a-input
                            v-model="item.kladr_id"
                            type="text"
                            autocomplete="off"
                          />
                        </a-form-model-item>
                        <!-- Фиас ID -->
                        <a-form-model-item
                          has-feedback
                          label="Фиас ID"
                          prop="fias_id"
                        >
                          <a-input
                            v-model="item.fias_id"
                            type="text"
                            autocomplete="off"
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
            <a-col :xs="24" :lg="24" :md="24">
              <a-pagination
                v-model="currentPage"
                :total="totalPagination"
                :pageSize="pageSize"
                @change="changePagination"
                show-less-items
              />
            </a-col>
          </a-row>
        </a-spin>
      </a-layout-content>
    </a-layout>
  </a-layout-content>
</template>
<style scoped>
.regions-card {
  width: 100%;
}
.regions-card-body {
  margin-top: 20px;
}
</style>
<script lang="ts">
import Vue from "vue";
export default Vue.extend({
  name: "setting-admin-regions",
  head() {
    return {
      title: "Администрирование регионы",
    };
  },
  data() {
    return {
      regEach: [],
      regions: [],
      isDeleteModal: false,
      rulesForm: {},
      currentPage: 1,
      totalPagination: 1,
      pageSize: 12,
      isLoading: true,
      delayTime: 5,
    };
  },
  mounted() {
    this.loadRegions();
  },
  methods: {
    changePagination(pageNumber: number) {
      this.loadRegions(pageNumber);
    },
    loadRegions(page: number = 1) {
      this.isLoading = true;
      const app: any = this;
      app.$axios.get(`/directory/region?page=${page}`).then(({ data }: any) => {
        this.regEach = data.data;
        this.totalPagination = data.total;
        this.isLoading = false;
      });
    },
    handleEdit(id: number, e: Event) {
      const newData = [...this.regEach];
      const target: any = newData.find((item: any) => id === item.id);
      if (typeof target.editable === undefined) {
        target.editable = true;
      } else {
        target.editable = !target.editable;
      }
      this.regEach = newData;
    },
    handleDelete(id: number, e: Event) {
      const app: any = this;
      app.$axios
        .post(`/directory/region/${id}/delete`)
        .then(({ data }: any) => {
          if (data.success) {
            this.$message.success(data.message);
            this.loadRegions();
          } else {
            this.$message.error(data.error);
          }
        });
    },
    handleCreate(e: Event) {
      const app: any = this;
      app.$axios.put("/directory/region/create").then(({ data }: any) => {
        if (data.success) {
          this.loadRegions();
        } else {
          this.$message.error(data.error);
        }
      });
    },
    submitForm(formName: any, id: Number, form: any) {
      const app: any = this;
      app.$refs[formName][0].validate((valid: any) => {
        if (valid) {
          app.$axios
            .post(`directory/region/${id}/update`, form)
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