<template>
  <a-layout-content style="margin: 20px 16px 0 16px">
    <a-page-header
      title="Спорные заявки"
      sub-title=""
      @back="() => $router.go(-1)"
    />
    <div
      :style="{
        padding: '24px',
        background: '#fff',
        minHeight: '360px',
      }"
    >
      <div :style="{ marginTop: '20px' }">
        <a-config-provider>
          <template #renderEmpty>
            <div style="text-align: center; padding: 20px">
              <a-icon type="container" style="font-size: 32px" />
              <p>Нет данных</p>
            </div>
          </template>
          <a-list
            class="demo-loadmore-list"
            :loading="loading"
            item-layout="horizontal"
            :data-source="data"
          >
            <div
              v-if="showLoadingMore"
              slot="loadMore"
              :style="{
                textAlign: 'center',
                marginTop: '12px',
                height: '32px',
                lineHeight: '32px',
              }"
            >
              <a-spin v-if="loadingMore" />
              <a-button v-else @click="onLoadMore">Показать ещё</a-button>
            </div>
            <a-list-item slot="renderItem" slot-scope="item">
              <a-button
                slot="actions"
                type="danger"
                @click="handleCloseUser($event, item.id)"
                v-if="item.status !== 2010"
                >В пользу клиента</a-button
              >
              <a-button
                slot="actions"
                type="primary"
                @click="handleCloseService($event, item.id)"
                v-if="item.status !== 2006"
                >В пользу сервиса</a-button
              >
              <a-list-item-meta :description="item.description">
                <a
                  slot="title"
                  href="javascript:void(0)"
                  @click.prevent="showDrawer(item.deal_id, $event)"
                  >{{ item.name }}</a
                >
              </a-list-item-meta>
              <!-- <div>
                <span :style="{ display: 'flex', alignItems: 'center'}">
                    <a-icon type="clock-circle" :style="{ marginRight: '5px'}" />
                    {{ $dateFns.format(item.created_at, 'dd-MM-yyyy H:I') }}
                </span>
                </div>-->
            </a-list-item>
          </a-list>
        </a-config-provider>
      </div>
    </div>
    <a-drawer
      width="640"
      placement="right"
      :closable="false"
      :visible="visible"
      @close="onClose"
    >
      <p :style="[pStyle, pStyle2]">Клиент #{{ dealData.deal_id }}</p>
      <p :style="pStyle">Информация</p>
      <a-row :style="{ marginTop: '20px' }">
        <a-col :span="12">
          <b-description-item title="ФИО" :content="dealData.name" />
        </a-col>
        <a-col :span="12">
          <b-description-item title="E-mail" :content="dealData.email" />
        </a-col>
      </a-row>
      <a-row>
        <a-col :span="12">
          <b-description-item
            title="Регион"
            :content="dealData.region.name_with_type"
          />
        </a-col>
        <a-col :span="12">
          <b-description-item
            title="Направление"
            :content="dealData.bids.direction.name"
          />
        </a-col>
      </a-row>
      <a-row>
        <a-col :span="12">
          <b-description-item title="Телефон" :content="dealData.phone" />
        </a-col>
        <a-col :span="12">
          <b-description-item title="Стоимость" :content="`${dealData.amount} ₽`" />
        </a-col>
      </a-row>
      <tempalte
        v-if="user.role === 'ROLE_ADMIN' || user.role === 'ROLE_WEBMASTER'"
      >
        <div :style="{ marginTop: '20px' }">
          <p :style="pStyle">UTM метки</p>
          <a-row :style="{ marginTop: '20px' }">
            <a-col :span="12" v-for="(item, index) in utms" :key="index">
              <b-description-item :title="index" :content="item" />
            </a-col>
          </a-row>
        </div>
        <div :style="{ marginTop: '20px' }">
          <p :style="pStyle">Источник/запросы</p>
          <a-row :style="{ marginTop: '20px' }">
            <a-col :span="12">
              <b-description-item title="Источник" :content="refererDeal" />
            </a-col>
          </a-row>
          <a-row>
            <a-col :span="12" v-for="(item, index) in requestDeal" :key="index">
              <p :style="{ fontSize: '14px', fontWeight: 'bold' }">
                {{ index }}
              </p>
              <b-description-item
                v-for="(itm, idx) in item"
                :key="idx"
                :title="idx"
                :content="itm"
              />
            </a-col>
          </a-row>
        </div>
      </tempalte>
      <a-divider />
      <p :style="pStyle">Запись звонков</p>
      <a-row>
        <a-col
          v-for="(audio_item, index) in fileList"
          :key="index"
          :style="{ padding: '15px 0' }"
        >
          <template v-if="audio_item.url">
            <audio-player :src="audio_item.url" />
          </template>
        </a-col>
        <a-col :span="24">
          <a-upload-dragger
            name="file"
            :multiple="true"
            :action="uploadURL"
            :file-list="fileList"
            :remove="handleRemoveFile"
            :headers="{ Authorization: $auth.getToken('local') }"
            @change="handleUpload"
          >
            <p class="ant-upload-drag-icon">
              <a-icon type="inbox" />
            </p>
            <p class="ant-upload-text">Нажмите или перекиньте файлы звонков</p>
            <p class="ant-upload-hint">Загрузить</p>
          </a-upload-dragger>
        </a-col>
      </a-row>
    </a-drawer>
  </a-layout-content>
</template>
<script>
export default {
  data() {
    return {
      loading: true,
      loadingMore: false,
      showLoadingMore: false,
      pageTo: 1,
      pageTotal: 1,
      data: [],
      visible: false,
      uploadURL: "",
      fileList: [],
      pStyle: {
        fontSize: "16px",
        color: "rgba(0,0,0,0.85)",
        lineHeight: "24px",
        display: "block",
        marginBottom: "16px",
      },
      pStyle2: {
        marginBottom: "24px",
      },
      utms: {},
      requestDeal: {},
      refererDeal: "",
      dealData: {
        deal_id: 1,
        bids: {
          direction: {
            name: "",
          },
        },
        status: {
          name: "",
          type: 0,
        },
        email: "",
        phone: "",
        region: {
          name: "",
        },
        direction: {
          name: "",
        },
      },
    };
  },
  mounted() {
    this.loadData((result) => {
      this.loading = false;
      this.data = result.data;
      this.pageTo = result.to;
      this.pageTotal = result.total;
      if (this.pageTotal == 0) {
        this.showLoadingMore = false;
      } else {
        this.showLoadingMore = !(this.pageTo == this.pageTotal);
      }
    }, "/disput");
  },
  methods: {
    loadData(callback, apiUrl) {
      this.$axios
        .post(apiUrl)
        .then(({ data }) => {
          callback(data);
        })
        .catch((_err) => {
          console.error(_err);
        });
    },
    onLoadMore() {
      this.loadingMore = true;
      this.pageTo++;
      if (this.pageTo >= this.pageTotal) {
        this.pageTo = this.pageTotal;
        this.showLoadingMore = false;
      }
      this.loadData((res) => {
        this.data = this.data.concat(res.data);
        this.loadingMore = false;
        this.$nextTick(() => {
          window.dispatchEvent(new Event("resize"));
        });
      }, `/disput?page=${this.pageTo}`);
    },
    showDrawer(_id, _e) {
      this.utms = {};
      this.requestDeal = {};
      this.referer = "";
      this.$axios
        .post(`/deal/${_id}`)
        .then(({ data }) => {
          this.dealData = data;
          this.uploadURL = `http://lk.leadz.monster/api/deal/${this.dealData.deal_id}/upload`;
          this.fileList = data.deal_files.map((current) => {
            return {
              url: `http://lk.leadz.monster/api/deal/${data.deal_id}/storage/${current.id}`,
              ext: ["mp3"],
            };
          });
          if (
            this.user.role === "ROLE_WEBMASTER" ||
            this.user.role === "ROLE_ADMIN"
          ) {
            this.utms = JSON.parse(data.utm);
            this.requestDeal = JSON.parse(data.request);
            this.refererDeal = data.referer;
          }
          this.visible = true;
        })
        .catch((_err) => {
          console.log(_err);
        });
    },
    onClose() {
      this.visible = false;
    },
    handleCloseUser(_e, disput_id) {
      this.$axios
        .post(`/disput/${disput_id}/close`, {
          status: 2010,
        })
        .then(({ data }) => {
          this.loadData((result) => {
            this.loading = false;
            this.data = result.data;
            this.pageTo = result.to;
            this.pageTotal = result.total;
            this.showLoadingMore = !(this.pageTo == this.pageTotal);
          }, "/disput");
        })
        .catch((_err) => {
          console.error(_err);
        });
    },
    handleCloseService(_e, disput_id) {
      this.$axios
        .post(`/disput/${disput_id}/close`, {
          status: 2006,
        })
        .then(({ data }) => {
          this.loadData((result) => {
            this.loading = false;
            this.data = result.data;
            this.pageTo = result.to;
            this.pageTotal = result.total;
            this.showLoadingMore = !(this.pageTo == this.pageTotal);
          }, "/disput");
        })
        .catch((_err) => {
          console.error(_err);
        });
    },
    handleUpload({ fileList }) {
      this.fileList = fileList;
    },
    handleRemoveFile(file) {
      const { response, uid } = this.fileList[this.fileList.indexOf(file)];
      this.deleteURL = "";
      if (response !== undefined) {
        this.deleteURL = `/deal/${this.dealData.deal_id}/storage/${response.uid}/delete`;
      } else {
        this.deleteURL = `/deal/${this.dealData.deal_id}/storage/${uid}/delete`;
      }

      this.$axios
        .post(this.deleteURL)
        .then(({ data }) => {
          console.log(data);
        })
        .catch((_err) => {
          console.error(_err);
        });
    },
  },
};
</script>
