<template>
  <a-layout-content style="margin: 20px 16px 0 16px">
    <a-page-header
      title="Страховка"
      sub-title="пояснение"
      @back="() => $router.go(-1)"
    >
      <div slot="extra">
        <div :style="{ display: 'flex' }"></div>
      </div>
    </a-page-header>
    <div
      :style="{
        padding: '24px',
        background: '#fff',
        minHeight: '360px',
      }"
    >
      <div :style="{ padding: '20px 0 0 0' }">
        <div :style="{ display: 'flex' }">
          <div class="action-button">
            <a-button type="primary" size="large" @click="handleCreate">
              <a-icon type="plus" />
              Добавить страховку
            </a-button>
          </div>
        </div>
        <div :style="{ marginTop: '20px' }">
          <a-row :gutter="[24, 24]">
            <a-col
              :span="6"
              v-for="(insurance, index) in insurances"
              :key="index"
            >
              <a-card hoverable style="width: 100%">
                <template slot="actions" class="ant-card-actions">
                  <a-popconfirm
                    title="Действительно хотите удалить страховку"
                    ok-text="Да"
                    cancel-text="Нет"
                    @confirm="handleDelete(index, $event)"
                  >
                    <a key="delete">
                      <a-icon type="delete" :style="{ color: 'red' }" />
                    </a>
                  </a-popconfirm>
                  <a key="save" @click="handleSave(index, $event)">
                    <a-icon type="save" />
                  </a>
                </template>
                <a-card-meta :title="insurance.name" />
                <template>
                  <a-form :label-col="labelCol" :wrapper-col="wrapperCol">
                    <a-input type="hidden" name="id" :value="insurance.id" />
                    <div :style="{ paddingTop: '12px' }">
                      <a-input
                        placeholder="Название"
                        name="name"
                        v-model="insurance.name"
                      />
                    </div>
                    <div :style="{ paddingTop: '12px' }">
                      <a-input
                        placeholder="Цена"
                        name="price"
                        v-model="insurance.price"
                      />
                    </div>
                    <div :style="{ paddingTop: '12px' }">
                      <a-input
                        placeholder="Колчиество заявок"
                        name="count"
                        v-model="insurance.count"
                      />
                    </div>
                    <div :style="{ paddingTop: '12px' }">
                      <a-select
                        placeholder="Статус"
                        name="status"
                        v-model="insurance.status"
                        option-label-prop="label"
                      >
                        <a-select-option value="Y" key="Y" label="Активен"
                          >Активен</a-select-option
                        >
                        <a-select-option value="N" key="N" label="Не активен"
                          >Не активен</a-select-option
                        >
                      </a-select>
                    </div>
                  </a-form>
                </template>
              </a-card>
            </a-col>
          </a-row>
        </div>
      </div>
    </div>
  </a-layout-content>
</template>
<script>
export default {
  data() {
    return {
      insurances: [],
      labelCol: {
        xs: { span: 24 },
        sm: { span: 5 },
      },
      wrapperCol: {
        xs: { span: 24 },
        sm: { span: 12 },
      },
    }
  },
  created() {
    this.loadInsurance()
  },
  methods: {
    loadInsurance() {
      this.$axios
        .get('/insurance')
        .then(({ data }) => {
          if (data.success == true) {
            this.insurances = data.data
          } else {
            this.$message.error(data.error)
          }
        })
        .catch((err) => {
          if (typeof err.response.message != 'undefined') {
            this.$message.error(err.response.message)
          } else {
            this.$message.error(err.response)
          }
        })
    },
    handleCreate(e) {
      this.$axios
        .post('/insurance/create')
        .then(({ data }) => {
          if (data.success == true) {
            this.loadInsurance()
          } else {
            this.$message.error(data.error)
          }
        })
        .catch((err) => {
          if (typeof err.response.message != 'undefined') {
            this.$message.error(err.response.message)
          } else {
            this.$message.error(err.response)
          }
        })
    },
    handleDelete(idx, e) {
      const postData = this.insurances[idx]
      this.$axios
        .post(`/insurance/${postData.id}/delete`)
        .then(({ data }) => {
          if (data.success == true) {
            this.loadInsurance()
            this.$message.success(data.message)
          } else {
            this.$message.error(data.error)
          }
        })
        .catch((err) => {
          if (typeof err.response.message != 'undefined') {
            this.$message.error(err.response.message)
          } else {
            this.$message.error(err.response)
          }
        })
    },
    handleSave(idx, e) {
      const postData = this.insurances[idx]
      this.$axios
        .post(`/insurance/update`, postData)
        .then(({ data }) => {
          if (data.success == true) {
            this.loadInsurance()
            this.$message.success(data.message)
          } else {
            this.$message.error(data.error)
          }
        })
        .catch((err) => {
          if (typeof err.response.message != 'undefined') {
            this.$message.error(err.response.message)
          } else {
            this.$message.error(err.response)
          }
        })
    },
  },
}
</script>