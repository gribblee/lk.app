<template>
  <a-layout-content>
    <a-page-header
      title="Новость"
      sub-title=""
      @back="() => $router.go(-1)"
    ></a-page-header>

    <a-layout class="setting-layout-sider-2">
      <a-layout-content class="setting-layout-content">
        <a-row :gutter="[24, 24]">
          <a-col :xs="24" :md="12" :lg="12">
            <a-carousel arrows dots-class="slick-dots slick-thumb">
              <a slot="customPaging">
                <img :src="`/${news.images[0]}`" />
              </a>
              <div v-for="(image, index) in news.images" :key="index">
                <img :src="`/${image}`" />
              </div>
              <div
                slot="prevArrow"
                class="custom-slick-arrow news-custom-slick-arrow-left"
              >
                <a-icon type="left-circle" />
              </div>
              <div
                slot="nextArrow"
                class="custom-slick-arrow news-custom-slick-arrow-right"
              >
                <a-icon type="right-circle" />
              </div>
            </a-carousel>
          </a-col>
          <a-col :xs="24" :md="12" :lg="12">
            <h1 class="news-title">{{ news.title }}</h1>
            <div class="news-timestamp">
              <a-icon type="clock-circle" /> {{ getDate(news.created_at) }}
            </div>
            <div class="news-tags">
              <div
                v-for="(tag, index) in news.tags
                  .split(/(?:#|,| )+/)
                  .filter((el) => {
                    return (
                      String(el).length !== 0 &&
                      String(el).indexOf('#') === -1 &&
                      String(el).indexOf(',') === -1
                    );
                  })"
                :key="index"
                class="news-tag-inner"
              >
                <span class="news-tag">#{{ tag }}</span>
              </div>
            </div>
            <div
              class="news-description"
              v-linkified
              v-html="news.description"
            />
          </a-col>
          <a-col :xs="24" :md="24" :lg="24" v-if="news.recomended.length > 0">
            <div class="news-recomended">
              <div class="news-recomended-title">Рекомендуем почитать</div>
              <a-row :gutter="[24, 24]">
                <a-col
                  :xs="24"
                  :md="6"
                  :lg="6"
                  v-for="(item, index) in news.recomended"
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
                        <nuxt-link :to="`/news/${item.id}`">{{
                          item.title
                        }}</nuxt-link>
                      </template>
                      <template slot="description">
                        <div class="news-description">{{ item.short_description }}</div>
                        <div class="news-tags">
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
                            class="news-tag-inner"
                          >
                            <span class="news-tag">#{{ tag }}</span>
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
  name: "news-id",

  data() {
    return {
      news: {
        title: "",
        short_description: "",
        description: "",
        tags: "",
        images: [],
        recomended: [],
      },
    };
  },

  created() {
    const { $route, $axios }: any = this;
    const app: any = this;
    const { id }: any = $route.params;
    $axios
      .get(`/news/${id}`)
      .then(({ data }: any) => {
        app.news = data;
      })
      .catch((err: any) => {
        console.error(err);
      });
  },
  methods: {
    getDate(date: any) {
      return moment(date).format("DD-MM-YYYY HH:mm");
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