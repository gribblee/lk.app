<template>
  <a-layout-content style="margin: 20px 16px 0 16px">
    <a-page-header title="История бюджета" @back="() => $router.go(-1)" />
    <div :style="{ background: '#fff' }">
      <div :style="{ padding: '20px 0 0 0' }">
        <a-config-provider>
            <template #renderEmpty>
              <div style="text-align: center; padding: 20px">
                <a-icon type="container" style="font-size: 32px" />
                <p>Нет данных</p>
              </div>
            </template>
            <a-table :columns="columns" :data-source="data" :loading="isLoading">
            <template slot="type_transaction" slot-scope="text, record">
                <a-tag
                :color="typeTransaction_title[record.type_transaction].color"
                >
                {{ typeTransaction_title[record.type_transaction].title }}
                </a-tag>
            </template>
            </a-table>
        </a-config-provider>
      </div>
    </div>
  </a-layout-content>
</template>
<script>
const columns = [
  {
    title: 'Тип транзакции',
    dataIndex: 'type_transaction',
    key: 'type_transaction',
    scopedSlots: { customRender: 'type_transaction' },
  },
  {
    title: 'Сумма',
    dataIndex: 'paysum',
    key: 'paysum',
  },
  {
    title: 'Сумма бонуса',
    dataIndex: 'paybonus',
    key: 'paybonus',
  },
  {
    title: 'Баланс до',
    dataIndex: 'before_balance',
    key: 'before_balance',
  },
  {
    title: 'Баланс после',
    dataIndex: 'after_balance',
    key: 'after_balance',
  },
  {
    title: 'Бонусы до',
    dataIndex: 'before_bonus',
    key: 'before_bonus',
  },
  {
    title: 'Бонусы после',
    dataIndex: 'after_bonus',
    key: 'after_bonus',
  },
  {
    title: 'Дата создания',
    dataIndex: 'date_at',
    key: 'date_at',
    scopedSlots: { customRender: 'date_at' },
  },
]
export default {
  data() {
    return {
      columns,
      data: [],
      isLoading: true,
      typeTransaction_title: {
        0: {
          title: 'Общее',
          color: 'volcano',
        },
        10: {
          title: 'Пополение с карты',
          color: 'green',
        },
        11: {
          title: 'Пополение с расчётного счёта',
          color: 'green',
        },
        12: {
          title: 'Покупка заявки',
          color: 'red',
        },
        13: {
          title: 'Покупка страховки',
          color: 'blue',
        },
        14: {
          title: 'Изменено администратором',
          color: 'geekblue',
        },
        15: {
          title: 'Рассрочка',
          color: 'yellow',
        },
      },
    }
  },
  created() {
    this.loadStory()
  },
  methods: {
    loadStory() {
      this.isLoading = true
      this.$axios
        .post('/user/balance/history')
        .then(({ data }) => {
          this.data = data.data
          this.isLoading = false
        })
        .catch((err) => {
          if (typeof err.response.data.message != 'undefined') {
            this.$message.error(err.response.data.message)
          } else {
            this.$message.error(err.response.data)
          }
        })
    },
  },
}
</script>