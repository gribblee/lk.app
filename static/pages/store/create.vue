<template>
  <a-layout-content>
    <a-page-header
      title="Добавить позицию"
      sub-title=""
      @back="() => $router.go(-1)"
    ></a-page-header>

    <a-layout class="setting-layout-sider">
      <a-layout-content class="setting-layout-content">
        <a-row :gutter="[16, 16]" type="flex" justify="space-around">
          <a-col :xs="22" :md="10" :lg="10">
            <a-form-model ref="orderForm" :model="orderForm" :rules="orderRule">
              <a-form-model-item has-feedback label="Название" prop="title">
                <a-input
                  v-model="orderForm.title"
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
                  v-model="orderForm.short_description"
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
                  v-model="orderForm.description"
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
                  v-model="orderForm.tags"
                  type="text"
                  autocomplete="off"
                />
              </a-form-model-item>
              <a-form-model-item has-feedback label="Цена в руб." prop="price">
                <a-input
                  v-model="orderForm.price"
                  type="number"
                  autocomplete="off"
                  suffix="₽"
                />
              </a-form-model-item>
              <a-form-model-item label="Картинки">
                <a-upload
                  action="http://lk.leadz.monster/api/store/upload"
                  list-type="picture-card"
                  :multiple="true"
                  :file-list="orderForm.images"
                  :headers="{ Authorization: $auth.getToken('local') }"
                  @preview="handlePreview"
                  @change="handleChange"
                >
                  <div v-if="orderForm.images.length < 8">
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
  name: "store-create",

  data() {
    return {
      previewVisible: false,
      previewImage: "",
      orderRule: {
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
        price: [
          {
            required: true,
            message: "Поле цена обязательно и должно быть цифрами",
            trigger: "change",
          },
        ],
      },
      orderForm: {
        title: "",
        short_description: "",
        description: "",
        tags: "",
        price: 0,
        images: [],
      },
    };
  },

  created() {
    const { $axios }: any = this;
  },
  methods: {
    onSubmit() {
      const { $refs, $router, $message, $axios, orderForm }: any = this;
      $refs.orderForm.validate((valid: any) => {
        if (valid) {
          orderForm.images = orderForm.images.map((image: any) => image.response);
          $axios
            .post(`/store/create`, orderForm)
            .then(({ data }: any) => {
              $message.success("Готово");
              $router.push(`/store/${data.id}`);
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
      app.orderForm.images = fileList;
    },
  },
});
</script>