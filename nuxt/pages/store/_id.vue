<template>
  <a-layout-content>
    <a-page-header
      :title="`Товар ${order.title}`"
      sub-title="Последние новости"
      @back="() => $router.go(-1)"
    >
    </a-page-header>
    <a-layout class="setting-layout-sider">
      <a-layout-content class="setting-layout-content">
        <a-row :gutter="[24, 24]">
          <a-col :xs="24" :md="12" :lg="12">
            <a-carousel arrows dots-class="slick-dots slick-thumb">
              <a slot="customPaging">
                <img :src="`/${order.images[0]}`" />
              </a>
              <div v-for="(image, index) in order.images" :key="index">
                <img :src="`/${image}`" />
              </div>
              <div
                slot="prevArrow"
                class="custom-slick-arrow order-custom-slick-arrow-left"
              >
                <a-icon type="left-circle" />
              </div>
              <div
                slot="nextArrow"
                class="custom-slick-arrow order-custom-slick-arrow-right"
              >
                <a-icon type="right-circle" />
              </div>
            </a-carousel>
          </a-col>
          <a-col :xs="24" :md="12" :lg="12">
            <h1 class="order-title">{{ order.title }}</h1>
            <div class="order-timestamp">
              <a-icon type="clock-circle" /> {{ getDate(order.created_at) }}
            </div>
            <div class="order-prices">
              <div class="order-price-inner">
                <span class="order-price">{{ order.price }} ₽</span>
              </div>
              <div class="order-action">
                <a-popconfirm
                  title="Вы действительно хотите купить этот товар?"
                  ok-text="Да"
                  cancel-text="Нет"
                  @confirm="orderBuy"
                >
                  <a-button type="danger">Купить</a-button>
                </a-popconfirm>
              </div>
              <div class="order-action" v-if="user.role === 'ROLE_ADMIN'">
                <a-popconfirm
                  title="Вы действительно хотите удалить этот товар?"
                  ok-text="Да"
                  cancel-text="Нет"
                  @confirm="orderDelete"
                >
                  <a-button type="primary">Удалить</a-button>
                </a-popconfirm>
              </div>
            </div>
            <div
              class="order-description"
              v-linkified
              v-html="order.description"
            />
          </a-col>
          <a-col :xs="24" :md="24" :lg="24" v-if="order.recomended.length > 0">
            <div class="order-recomended">
              <div class="order-recomended-title">Рекомендуем почитать</div>
              <a-row :gutter="[24, 24]">
                <a-col
                  :xs="24"
                  :md="6"
                  :lg="6"
                  v-for="(item, index) in order.recomended"
                  :key="index"
                >
                  <a-card hoverable>
                    <img
                      slot="cover"
                      alt="example"
                      :src="`/${item.images[0]}`"
                      v-if="item.images.length > 0"
                    />
                    <a-card-meta>
                      <template slot="title">
                        <nuxt-link :to="`/order/${item.id}`">{{
                          item.title
                        }}</nuxt-link>
                      </template>
                      <template slot="description">
                        <div class="order-description">
                          {{ item.short_description }}
                        </div>
                        <div class="order-tags">
                          <div
                            v-for="(tag, index) in item.tags
                              .split(/(?:#|,| )+/)
                              .filter((el) => {
                                return (
                                  String(el).length !== 0 &&
                                  String(el).indexOf('#') === -1 &&
                                  String(el).indexOf(',') === -1
                                );
                              })"
                            :key="index"
                            class="order-tag-inner"
                          >
                            <span class="order-tag">#{{ tag }}</span>
                          </div>
                        </div>
                      </template>
                    </a-card-meta>
                  </a-card>
                </a-col>
              </a-row>
            </div>
          </a-col>
        </a-row>
      </a-layout-content>
    </a-layout>
  </a-layout-content>
</template>
<script lang="ts">
import Vue from "vue";
import moment from "moment";
export default Vue.extend({
  name: "store-id",
  head() {
    return {
      title: "Просмотреть товар",
    };
  },
  validate({ params }) {
    return /^\d+$/.test(params.id);
  },
  data() {
    return {
      order: {
        title: "",
        short_description: "",
        description: "",
        tags: "",
        price: 0,
        images: [],
        recomended: [],
      },
    };
  },

  created() {
    const app: any = this;

    app.$axios
      .get(`/store/${app.$route.params.id}`)
      .then(({ data }: any) => {
        app.order = data;
      })
      .catch((err: any) => {
        console.error(err);
      });
  },
  methods: {
    getDate(date: any) {
      return moment(date).format("DD-MM-YYYY HH:mm");
    },
    orderBuy() {
      const app: any = this;
      app.$axios
        .post(`/store/${app.$route.params.id}/buy`)
        .then(({ data }: any) => {
          if (data.success) {
            app.$message.success(data.message);
          } else {
            app.$message.error(data.message);
          }
        })
        .catch((err: any) => {
          console.error(err);
        });
    },
    orderDelete() {
      const app: any = this;
      app.$axios
        .post(`/store/${app.$route.params.id}/delete`)
        .then(({ data }: any) => {
          if (data.success) {
            app.$message.success(data.message);
            app.$router.push('/store');
          } else {
            app.$message.error(data.message);
          }
        })
        .catch((err: any) => {
          console.error(err);
        });
    },
  },
});
</script>
<style scoped>
/* For demo */
.ant-carousel >>> .slick-dots {
  height: auto;
}
.ant-carousel >>> .slick-slide img {
  border: 5px solid #fff;
  display: block;
  margin: auto;
  max-width: 80%;
}
.ant-carousel >>> .slick-thumb {
  bottom: -45px;
}
.ant-carousel >>> .slick-thumb li {
  width: 60px;
  height: 45px;
}
.ant-carousel >>> .slick-thumb li img {
  width: 100%;
  height: 100%;
  filter: grayscale(100%);
}
.ant-carousel >>> .slick-thumb li.slick-active img {
  filter: grayscale(0%);
}
.ant-carousel >>> .custom-slick-arrow {
  width: 25px;
  height: 25px;
  font-size: 25px;
  color: #333333;
  background-color: rgba(31, 45, 61, 0.11);
  opacity: 0.3;
}

.ant-carousel >>> .custom-slick-arrow::before {
  display: none;
}
.ant-carousel >>> .custom-slick-arrow::hover {
  opacity: 0.5;
}

.ant-carousel >>> .news-custom-slick-arrow-left {
  left: 3px;
  z-index: 1;
}
.ant-carousel >>> .news-custom-slick-arrow-right {
  right: 3px;
}
</style>