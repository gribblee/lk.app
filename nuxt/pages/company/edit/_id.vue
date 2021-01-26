<template>
  <a-layout-content>
    <a-page-header
      title="Редактировании компании"
      sub-title=""
      @back="() => $router.go(-1)"
    ></a-page-header>
    <a-layout class="setting-layout-sider-2">
      <a-layout-content class="setting-layout-content">
        <a-row :gutter="[16, 16]" type="flex" justify="center">
          <a-col :xs="24" :md="10" :lg="10">
            <span class="company-layout-content__link"
              ><a-button
                type="primary"
                @click="(e) => $router.push(`/company/${dataForm.id}`)"
                >Просмотреть компанию</a-button
              ></span
            >
            <a-form-model
              ref="companyEdit"
              :model="dataForm"
              :rules="rulesForm"
            >
              <!-- Имя -->
              <a-form-model-item has-feedback label="Название" prop="name">
                <a-input
                  v-model="dataForm.name"
                  type="text"
                  autocomplete="off"
                />
              </a-form-model-item>
              <!-- Регион -->
              <a-form-model-item has-feedback label="Регион" prop="region_id">
                <a-select
                  placeholder="Выберите регион"
                  v-model="dataForm.region_id"
                  @change="handleRegion"
                  option-label-prop="label"
                >
                  <a-select-option
                    v-for="region in regionsEach"
                    :key="region.id"
                    :value="region.id"
                    :label="region.name"
                  >
                    {{ region.name }}
                  </a-select-option>
                </a-select>
              </a-form-model-item>
              <!-- Рейтинг -->
              <template v-if="user.role === 'ROLE_ADMIN'">
                <a-form-model-item has-feedback label="Ретйинг" prop="rating">
                  <div class="icon-wrapper">
                    <a-icon :style="{ color: preColor }" type="frown-o" />
                    <a-slider
                      :min="1"
                      :max="10000"
                      :value="dataForm.rating"
                      @change="ratingChange"
                    />
                    <a-icon :style="{ color: nextColor }" type="smile-o" />
                  </div>
                </a-form-model-item>
              </template>
              <!-- Адрес -->
              <a-form-model-item has-feedback label="Адрес" prop="address">
                <a-input
                  v-model="dataForm.address"
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
                  v-model="dataForm.description"
                  autocomplete="off"
                  :auto-size="{ minRows: 2, maxRows: 6 }"
                />
              </a-form-model-item>
              <!-- Изображения -->
              <a-form-model-item has-feedback label="Картинки" prop="files">
                <div class="clearfix">
                  <a-upload
                    :action="uploadURL"
                    list-type="picture-card"
                    :file-list="dataForm.files"
                    @preview="handlePreview"
                    @change="handleChange"
                  >
                    <div>
                      <a-icon type="plus" />
                      <div class="ant-upload-text">Добавить</div>
                    </div>
                  </a-upload>
                  <a-modal
                    :visible="previewVisible"
                    :footer="null"
                    @cancel="handleCancel"
                  >
                    <img
                      alt="example"
                      style="width: 100%"
                      :src="previewImage"
                    />
                  </a-modal>
                </div>
              </a-form-model-item>
              <!-- По каким вопросам помогает -->
              <template v-for="(issue, index) in dataIssues">
                {{ issue.title }}
                <a-form-model-item
                  :key="'title-' + index"
                  label="Навзание"
                  :prop="'title-' + index"
                >
                  <a-input v-model="issue.title" placeholder="Заголовок" />
                </a-form-model-item>
                <a-form-model-item
                  :key="'description-' + index"
                  label="Описание"
                  :prop="'description-' + index"
                >
                  <a-textarea
                    v-model="issue.description"
                    placeholder="Заголовок"
                  />
                </a-form-model-item>
                <!-- Направление -->
                <a-form-model-item
                  :key="'direction-' + index"
                  has-feedback
                  label="Направление"
                  :prop="'direction' + index + 'value'"
                >
                  <a-select
                    placeholder="Выберите направление"
                    v-model="issue.direction_id"
                    @change="handleDirection"
                    option-label-prop="label"
                  >
                    <a-select-option
                      v-for="direction in directionsEach"
                      :key="direction.id"
                      :value="direction.id"
                      :label="direction.name"
                    >
                      {{ direction.name }}
                    </a-select-option>
                  </a-select>
                </a-form-model-item>
                <a-form-model-item
                  :key="'priceTo-' + index"
                  label="Цена от"
                  :prop="'priceTo' + index + 'value'"
                >
                  <a-input-number
                    v-model="issue.priceTo"
                    placeholder="Цена от"
                    class="form-price"
                    :prop="'priceTo' + index + 'value'"
                  />
                </a-form-model-item>
                <a-form-model-item :key="'priceFrom-' + index" label="Цена до">
                  <a-input-number
                    v-model="issue.priceFrom"
                    placeholder="Цена до"
                    class="form-price"
                    :prop="'priceFrom' + index + 'value'"
                  />
                </a-form-model-item>
                <a-form-model-item :key="'saveIssue-' + index">
                  <a-space :size="10">
                    <a-button
                      class="dynamic-save-button"
                      @click="saveIssue(issue)"
                    >
                      <a-icon type="save" /> Сохранить решение</a-button
                    >
                    <a-button
                      type="danger"
                      class="dynamic-delete-button"
                      @click="removeIssue(issue)"
                    >
                      <a-icon type="minus-circle-o" /> Удалить</a-button
                    >
                  </a-space>
                </a-form-model-item>
              </template>
              <a-form-model-item>
                <a-button type="dashed" style="width: 100%" @click="addIssue">
                  <a-icon type="plus" /> Добавить решение
                </a-button>
              </a-form-model-item>
              <!-- Кнопка -->
              <a-form-model-item>
                <a-button type="primary" @click="submitForm()">
                  Сохранить компанию
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
.form-price {
  width: 100%;
}
.icon-wrapper {
  position: relative;
  padding: 0px 30px;
}

.icon-wrapper .anticon {
  position: absolute;
  top: -2px;
  width: 16px;
  height: 16px;
  line-height: 1;
  font-size: 16px;
  color: rgba(0, 0, 0, 0.25);
}

.icon-wrapper .anticon:first-child {
  left: 0;
}

.icon-wrapper .anticon:last-child {
  right: 0;
}
/* tile uploaded pictures */
.upload-list-inline >>> .ant-upload-list-item {
  float: left;
  width: 200px;
  margin-right: 8px;
}
.upload-list-inline >>> .ant-upload-animate-enter {
  animation-name: uploadAnimateInlineIn;
}
.upload-list-inline >>> .ant-upload-animate-leave {
  animation-name: uploadAnimateInlineOut;
}
</style>
<script lang="ts">
import Vue from "vue";

function getBase64(file: any) {
  return new Promise((resolve, reject) => {
    const reader = new FileReader();
    reader.readAsDataURL(file);
    reader.onload = () => resolve(reader.result);
    reader.onerror = (error) => reject(error);
  });
}

export default Vue.extend({
  validate({ params }) {
    return /^\d+$/.test(params.id);
  },
  data() {
    return {
      headTitle: "Редактирование компании",
      regionsEach: [],
      directionsEach: [],
      min: 1,
      max: 10000,
      previewVisible: false,
      previewImage: "",
      dataIssues: [],
      dataForm: {
        name: "",
      },
      rulesForm: {
        name: [{ required: true, message: "Поле имя обязательно" }],
        description: [{ required: true, message: "Поле описание обязательно" }],
      },
    };
  },
  methods: {
    handleRegion(v: any) {},
    handleDirection(v: any) {},
    saveIssue(issue: any) {
      const app: any = this;
      app.$axios
        .post(`/company/${app.$route.params.id}/issues/${issue.id}`, issue)
        .then(({ data }: any) => {
          app.$message.success(data.message);
        })
        .catch((err: any) => {
          app.$message.error(err.response.error);
        });
    },
    removeIssue(item: any) {
      const app: any = this;
      let index = app.dataIssues.indexOf(item);
      if (index !== -1) {
        app.dataIssues.splice(index, 1);
      }
      app.$axios
        .delete(`/company/${app.$route.params.id}/issues/${item.id}`)
        .then(({ data }: any) => {
          app.$message.success(data.message);
        })
        .catch((err: any) => {
          app.$message.error(err.response.error);
        });
    },
    addIssue() {
      const app: any = this;
      app.$axios
        .put(`/company/${app.$route.params.id}/issues/create`)
        .then(({ data }: any) => {
          app.dataIssues.push(data);
        });
    },
    ratingChange(value: number) {
      const app: any = this;
      app.dataForm.rating = value;
    },
    submitForm() {
      const app: any = this;
      app.$refs["companyEdit"].validate((valid: any) => {
        if (valid) {
          app.$axios
            .post(`/company/${app.$route.params.id}`, app.dataForm)
            .then(({ data }: any) => {
              app.$message.success(data.message);
            })
            .catch((err: any) => {
              app.$message.error(err.response.error);
            });
        } else {
          console.log("error submit!!");
          return false;
        }
      });
    },
    handleCancel() {
      this.previewVisible = false;
    },
    async handlePreview(file: any) {
      if (!file.url && !file.preview) {
        file.preview = await getBase64(file.originFileObj);
      }
      this.previewImage = file.url || file.preview;
      this.previewVisible = true;
    },
    handleChange({ fileList }: any) {
      const app: any = this;
      app.dataForm.files = fileList;
    },
  },
  computed: {
    preColor() {
      const { max, min, dataForm }: any = this;
      const mid = ((max - min) / 2.5).toFixed(5);
      return dataForm.rating >= mid ? "" : "rgba(255, 0, 0, .45)";
    },
    nextColor() {
      const { max, min, dataForm }: any = this;
      const mid = ((max - min) / 2.5).toFixed(5);
      return dataForm.rating >= mid ? "rgba(0, 255, 0, .45)" : "";
    },
    uploadURL() {
      const app: any = this;
      return `http://lk.leadz.monster/company/${app.$route.params.id}/upload`;
    },
  },
  head() {
    const app: any = this;
    return {
      title: app.headTitle,
    };
  },
  created() {
    const app: any = this;
    app.$axios
      .get("/directory")
      .then(({ data }: any) => {
        app.regionsEach = data.regions;
        app.directionsEach = data.directions;
      })
      .catch((_err: any) => {
        console.error(_err);
      });
  },
  mounted() {
    const app: any = this;
    app.$axios
      .get(`/company/${app.$route.params.id}`)
      .then(({ data }: any) => {
        app.dataForm = {
          address: data.address,
          description: data.description,
          files: JSON.parse(data.files),
          id: data.id,
          name: data.name,
          rating: data.rating,
          region_id: data.region_id,
        };
        app.dataIssues = data.issues;
      })
      .catch((err: any) => {
        app.$message.error(err.response.error);
      });
  },
});
</script>