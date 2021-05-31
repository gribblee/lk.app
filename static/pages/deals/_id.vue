<template>
  <a-layout-content style="margin: 20px 16px 0 16px">
    <a-page-header
      title="Клиент"
      sub-title="пояснение"
      @back="() => $router.go(-1)"
    >
      <template slot="extra">
        <div :style="{ display: 'flex' }">
          <div>
            <a-radio-group
              v-model="DEAL_DATA.status"
              :default-value="DEAL_DATA.status"
              @change="onChangeStatus"
            >
              <a-radio-button value="1">Начальный</a-radio-button>
              <a-radio-button value="2">Документ подписан</a-radio-button>
              <a-radio-button value="3">Спорная</a-radio-button>
              <a-radio-button value="4">Закрыто</a-radio-button>
            </a-radio-group>
          </div>
        </div>
      </template>
    </a-page-header>
    <div
      :style="{
        padding: '24px',
        background: '#fff',
        minHeight: '360px',
      }"
    >
      <a-descriptions size="small" :column="3">
        <a-descriptions-item label="ФИО">
          {{ DEAL_DATA.name }}
        </a-descriptions-item>
        <a-descriptions-item label="Телефон">
          {{ DEAL_DATA.phone }}
        </a-descriptions-item>
        <a-descriptions-item label="E-mail">
          {{ DEAL_DATA.email }}
        </a-descriptions-item>
        <a-descriptions-item label="Регион">
          {{ DEAL_DATA.region.name }}
        </a-descriptions-item>
        <a-descriptions-item label="Направление">
          {{ DEAL_DATA.direction.name }}
        </a-descriptions-item>
        <a-descriptions-item label="Стоимость">
          {{ DEAL_DATA.amount }} ₽
        </a-descriptions-item>
      </a-descriptions>
      <div>
        <a-upload-dragger
          name="file"
          :multiple="true"
          action="https://www.mocky.io/v2/5cc8019d300000980a055e76"
          @change="handleChange"
        >
          <p class="ant-upload-drag-icon">
            <a-icon type="inbox" />
          </p>
          <p class="ant-upload-text">Нажмите или перекиньте файлы звонков</p>
          <p class="ant-upload-hint">Загрузить</p>
        </a-upload-dragger>
      </div>
    </div>
    <a-modal v-model="visible" title="Укажите причину статуса" on-ok="handleOk">
      <template slot="footer">
        <a-button key="back" @click="handleCancel">Отмена</a-button>
        <a-button key="submit" type="primary" @click="handleOk">
          Отправить
        </a-button>
      </template>
      <a-textarea
        placeholder="Описание"
        allow-clear
        @change="onChangeComment"
      />
    </a-modal>
  </a-layout-content>
</template>
<script>
export default {
  validate({ params }) {
    // Должен быть числом
    return /^\d+$/.test(params.id)
  },
  data() {
    return {
      visible: false,
      DEAL_DATA: {
        amount: 0,
        name: '',
        direction: {
          name: '',
        },
        region: {
          name: '',
        },
      },
    }
  },
  mounted() {
    this.$axios
      .post(`/deal/${this.$route.params.id}`)
      .then(({ data }) => {
        this.DEAL_DATA = data
      })
      .catch((_err) => {
        console.error(_err)
      })
    console.log(this.DEAL_DATA)
  },
  methods: {
    onChangeStatus(_e) {
      if (_e.target.value === '3') {
        this.buffStatus = this.DEAL_DATA.status
        this.visible = true
      }
    },
    handleMenuClick(_e) {},
    handleOk() {
      this.visible = false
      this.$router.push('/deals')
    },
    handleCancel() {
      this.visible = false
      this.DEAL_DATA.status = this.buffStatus
    },
    onChangeComment() {},
  },
}
</script>
