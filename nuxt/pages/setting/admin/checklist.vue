<template>
  <a-layout-content>
    <a-page-header
      title="Чек листы"
      sub-title=""
      @back="() => $router.go(-1)"
    ></a-page-header>
    <a-layout class="setting-layout-sider" ref="siderSetting">
      <b-layout-sider
        menu-key="setting-admin-checklist"
        :open-keys="['setting-admin-directory']"
      />
      <a-layout-content class="setting-layout-content">
        <a-row :gutter="[16, 16]" type="flex" justify="space-around">
          <a-col :xs="24" :md="10" :lg="10">
            <h1 class="typhography-header">Чек листы</h1>
            <a-form-model ref="clForm" :model="clForm">
              <a-form-model-item
                v-for="(item, index) in clForm.list"
                :key="index"
              >
                <a-space>
                  <div>
                    <a-input v-model="item.value" placeholder="Название" />
                    <a-input v-model="item.score" placeholder="Баллы" />
                  </div>
                  <a-icon
                    v-if="clForm.list.length > 1"
                    class="dynamic-delete-button"
                    type="minus-circle-o"
                    :disabled="clForm.list.length === 1"
                    @click="removeItem(index)"
                  />
                </a-space>
              </a-form-model-item>
              <a-form-model-item>
                <a-button type="dashed" style="width: 60%" @click="addItem">
                  <a-icon type="plus" /> Добавить чек лист
                </a-button>
              </a-form-model-item>
              <a-form-model-item>
                <a-button
                  type="primary"
                  html-type="submit"
                  @click="submitForm('clForm')"
                >
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
  text-align: center;
}
</style>
<script lang="ts">
import Vue from "vue";
export default Vue.extend({
  name: "setting-admin-checklist",
  head() {
    return {
      title: "Создание чек-листов",
    };
  },
  data() {
    return {
      clForm: {
        list: [],
      },
    };
  },
  mounted() {},
  methods: {
    submitForm(formName: any) {
      const { $refs, clForm }: any = this;
      $refs[formName].validate((valid: any) => {
        if (valid) {
          console.log(clForm.list);
        } else {
          console.log("error submit!!");
          return false;
        }
      });
    },
    addItem(e: any) {
      const { clForm }: any = this;
      clForm.list.push({
        id: clForm.list.length + 1,
        value: "",
        score: "",
      });
    },
    removeItem(i: any) {
      const { clForm }: any = this;
      clForm.list.splice(i, 1);
    },
  },
});
</script>