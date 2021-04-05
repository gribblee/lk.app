<template>
  <a-layout-content style="margin: 20px 16px 0 16px">
    <a-page-header title="Не распределено" sub-title="пояснение" @back="() => $router.go(-1)">
      <div slot="extra">
        <div :style="{ display: 'flex' }">
          <div></div>
        </div>
      </div>
    </a-page-header>
    <div
      :style="{
        padding: '24px',
        background: '#fff',
        minHeight: '360px',
      }"
    >
      <div :style="{ margin: '20px -24px 0 -24px' }">
        <a-table :columns="columns" :data-source="dataDistributed" :loading="dataIsLoading">
          <a
            @click.prevent="showDrawer(record.deal_id, $event)"
            href="javascript:;"
            slot="name"
            slot-scope="text, record"
            :to="{ path: `/deals/${record.deal_id}` }"
          >{{ record.name }}</a>
          <span slot="status_name" slot-scope="text, record">
            <a-dropdown :trigger="['click']">
              <a
                @click.prevent="(_e) => {
                    onHandleStatus($event, record.deal_id)
                }"
              >Распределить</a>
            </a-dropdown>
          </span>
          <span slot="region" slot-scope="text, record">{{ record.region == null ? '' : record.region.name_with_type }}</span>
          <span slot="direction" slot-scope="text, record">{{ record.direction == null ? '' : record.direction.name }}</span>
        </a-table>
      </div>
    </div>
    <a-drawer width="640" placement="right" :closable="false" :visible="visible" @close="onClose">
      <p :style="[pStyle, pStyle2]">Клиент #{{ dealData.deal_id }}</p>
      <p :style="pStyle">Информация</p>
      <a-row :style="{ marginTop: '20px' }">
        <a-col :span="24">
          <a-space :size="20">
            <a-button type="primary" @click="onHandleStatus">Распределить</a-button>
            <a-button type="danger" @click="onDeleteDeal">Отправить в брак</a-button>
          </a-space>
        </a-col>
      </a-row>
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
          <div :style="{ marginRight: '20px', lineHeight: '22px', marginBottom: '7px' }">
            <a-select
              v-model="dealDataRegion_id"
              style="width: 100%"
              placeholder="Выберите регион"
              option-label-prop="label"
              @change="onHandleRegion"
            >
              <a-select-option
                v-for="region_ in $directory.regions"
                :value="region_.id"
                :key="region_.id"
                :label="region_.name"
              >{{ region_.name }}</a-select-option>
            </a-select>
          </div>
        </a-col>
        <a-col :span="12">
          <div
            :style="{ marginRight: '20px', lineHeight: '22px', marginBottom: 'calc(1em + 7px)' }"
          >
            <a-select
              v-model="dealDataDirection_id"
              style="width: 100%"
              placeholder="Выберите направление"
              option-label-prop="label"
              @change="onHandleDirection"
            >
              <a-select-option
                v-for="dir_ in $directory.directions"
                :value="dir_.id"
                :key="dir_.id"
                :label="dir_.name"
              >{{ dir_.name }}</a-select-option>
            </a-select>
          </div>
        </a-col>
      </a-row>
      <a-row>
        <a-col :span="12">
          <b-description-item title="Телефон" :content="dealData.phone" />
        </a-col>
      </a-row>
      <tempalte v-if="user.role === 'ROLE_ADMIN' || user.role === 'ROLE_WEBMASTER'">
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
              <p :style="{ fontSize: '14px', fontWeight: 'bold' }">{{ index }}</p>
              <b-description-item v-for="(itm, idx) in item" :key="idx" :title="idx" :content="itm" />
            </a-col>
          </a-row>
        </div>
      </tempalte>
    </a-drawer>
  </a-layout-content>
</template>
<script>
const columns = [
  {
    title: 'ID',
    dataIndex: 'deal_id',
    key: 'deal_id',
  },
  {
    title: 'ФИО',
    dataIndex: 'name',
    key: 'name',
    scopedSlots: { customRender: 'name' },
  },
  {
    title: 'Статус',
    dataIndex: 'status_name',
    key: 'status_name',
    scopedSlots: { customRender: 'status_name' },
  },
  {
    title: 'Регион',
    dataIndex: 'region',
    key: 'region',
    scopedSlots: { customRender: 'region' },
  },
  {
    title: 'Направление',
    dataIndex: 'direction',
    key: 'direction',
    scopedSlots: { customRender: 'direction' },
  },
  {
    title: 'Телефон',
    dataIndex: 'phone',
    key: 'phone',
  },
  {
    title: 'E-mail',
    dataIndex: 'email',
    key: 'email',
  },
]

export default {
  middleware: 'roleWebmaster',
  data() {
    return {
      columns,
      dataIsLoading: true,
      dataDistributed: [],
      utms: {},
      requestDeal: {},
      refererDeal: '',
      disputType: 1,
      disputTypeData: [],
      dealStatus: '',
      dealDataRegion_id: 0,
      dealDataDirection_id: 0,
      isUpd: false,
      pStyle: {
        fontSize: '16px',
        color: 'rgba(0,0,0,0.85)',
        lineHeight: '24px',
        display: 'block',
        marginBottom: '16px',
      },
      pStyle2: {
        marginBottom: '24px',
      },
      visible: false,
      dealData: {
        deal_id: 1,
        bids: {
          direction: {
            name: '',
          },
        },
        status: {
          name: '',
          type: 0,
        },
        email: '',
        phone: '',
        region: {
          name: '',
        },
        direction: {
          name: '',
        },
      },
    }
  },
  created() {
    this.loadTable()
  },
  methods: {
    loadTable() {
      this.dataIsLoading = true
      this.$axios
        .get('/deals/distributed?break=1')
        .then(({ data }) => {
          if (data.success === true) {
            this.dataDistributed = data.distributed.data
            this.dataIsLoading = false
          } else {
            this.$message.error(data.error)
          }
        })
        .catch(({ resposne }) => {
          this.$message.error(response.data.message)
        })
    },
    showDrawer(_id, _e) {
      this.loadDeal(_id)
      this.visible = true
    },
    loadDeal(_id) {
      this.utms = {}
      this.requestDeal = {}
      this.referer = ''
      this.$axios
        .post(`/deals/distributed/${_id}`)
        .then(({ data }) => {
          this.dealData = data
          this.dealStatus = data.status.id.toString()
          this.utms = JSON.parse(data.utm)
          this.requestDeal = JSON.parse(data.request)
          this.refererDeal = data.referer
          this.dealDataRegion_id = data.region.id
          this.dealDataDirection_id = data.direction.id
        })
        .catch(({ response }) => {
          this.$message.error(response.data.message)
        })
    },
    onClose() {
      this.visible = false
      if (this.isUpd) {
        this.loadTable()
        this.isUpd = false
      }
    },
    onHandleStatus(_e, _id = 0) {
      const deal_id = _id === 0 ? this.dealData.deal_id : _id
      this.$axios
        .post(`/deals/distributed/${deal_id}/status/`)
        .then(({ data }) => {
          if (data.success === true) {
            this.visible = false
            this.loadTable()
            this.$message.success(data.msg)
          } else {
            this.$message.error(data.error)
          }
        })
        .catch(({ response }) => {
          this.$message.error(response.data.message)
        })
    },
    onDeleteDeal(_e) {
      this.$axios
        .post(`/deals/distributed/${this.dealData.deal_id}/delete/`)
        .then(({ data }) => {
          if (data.success === true) {
            this.visible = false
            this.loadTable()
            this.$message.success(data.msg)
          } else {
            this.$message.error(data.error)
          }
        })
        .catch(({ response }) => {
          this.$message.error(response.data.message)
        })
    },
    udpateDeal(update_data) {
      this.$axios
        .post(
          `/deals/distributed/${this.dealData.deal_id}/update/`,
          update_data
        )
        .then(({ data }) => {
          if (data.success === true) {
            this.$message.success(data.msg)
          } else {
            this.$message.error(data.error)
          }
        })
        .catch(({ response }) => {
          this.$message.error(response.data.message)
        })
    },
    onHandleDirection(_e) {
      this.udpateDeal({
        direction_id: _e,
      })
      this.isUpd = true
    },
    onHandleRegion(_e) {
      this.udpateDeal({
        region_id: _e,
      })
      this.isUpd = true
    },
  },
}
</script>
