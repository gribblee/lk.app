<template>
  <a-layout-content>
    <a-page-header
      title="Создать новость"
      sub-title="Последние новости"
      @back="() => $router.go(-1)"
    ></a-page-header>

    <a-layout class="setting-layout-sider">
      <a-layout-content class="setting-layout-content">
        <a-row :gutter="[16, 16]" type="flex" justify="space-around">
          <a-col :xs="22" :md="10" :lg="10">
            <a-form-model ref="newsForm" :model="newsForm" :rules="newsRule">
              <a-form-model-item has-feedback label="Название" prop="title">
                <a-input
                  v-model="newsForm.title"
                  type="text"
                  autocomplete="off"
                />
              </a-form-model-item>
              <a-form-model-item
                has-feedback
                label="Краткое описание"
                prop="short_description"
              >
                <a-input
                  v-model="newsForm.short_description"
                  type="textarea"
                  autocomplete="off"
                />
              </a-form-model-item>
              <a-form-model-item
                has-feedback
                label="Полное описание"
                prop="description"
              >
                <a-input
                  v-model="newsForm.description"
                  type="textarea"
                  autocomplete="off"
                />
              </a-form-model-item>
              <a-form-model-item
                has-feedback
                label="Теги через запятую"
                prop="tags"
              >
                <a-input
                  v-model="newsForm.tags"
                  type="text"
                  autocomplete="off"
                />
              </a-form-model-item>
              <a-form-model-item label="Картинки">
                <a-upload
                  action="http://lk.leadz.monster/api/news/upload"
                  list-type="picture-card"
                  :multiple="true"
                  :file-list="newsForm.images"
                  :headers="{ Authorization: $auth.getToken('local') }"
                  @preview="handlePreview"
                  @change="handleChange"
                >
                  <div v-if="newsForm.images.length < 8">
                    <a-icon type="plus" />
                    <div class="ant-upload-text">Загрузить</div>
                  </div>
                </a-upload>
                <a-modal
                  :visible="previewVisible"
                  :footer="null"
                  @cancel="handleCancel"
                >
                  <img alt="example" style="width: 100%" :src="previewImage" />
                </a-modal>
              </a-form-model-item>
              <a-form-model-item>
                <a-button type="primary" @click="onSubmit">
                  Создать
                </a-button></a-form-model-item
              >
            </a-form-model>
          </a-col>
        </a-row>
      </a-layout-content>
    </a-layout>
  </a-layout-content>
</template>
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
  name: "news-create",

  data() {
    return {
      previewVisible: false,
      previewImage: "",
      newsRule: {
        title: [
          {
            required: true,
            message: "Поле название обязательно",
            trigger: "change",
          },
        ],
        short_description: [
          {
            required: true,
            message: "Краткое описание должно быть заполнено",
            trigger: "change",
          },
        ],
        description: [
          {
            required: true,
            message: "Описание нужно обязательно заполнить",
            trigger: "change",
          },
        ],
      },
      newsForm: {
        title: "",
        short_description: "",
        images: [],
        description: "",
        tags: "",
      },
    };
  },

  created() {
    const { $axios }: any = this;
  },
  methods: {
    onSubmit(e: any) {
      const { $refs, $router, $message, $axios, newsForm }: any = this;
      $refs.newsForm.validate((valid: any) => {
        if (valid) {
          newsForm.images = newsForm.images.map((image: any) => image.response);
          $axios
            .post(`/news/create`, newsForm)
            .then(({ data }: any) => {
              $message.success("Готово");
              $router.push(`/news/${data.id}`);
            })
            .catch((err: any) => {
              $message.error("Ошибка !");
            });
        } else {
          $message.error("Ошибка !");
          return false;
        }
      });
    },
    handleCancel() {
      const app: any = this;
      app.previewVisible = false;
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
      app.newsForm.images = fileList;
    },
  },
});
</script>