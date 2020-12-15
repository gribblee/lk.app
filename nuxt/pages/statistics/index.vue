<template>
  <a-layout-content style="margin: 20px 16px 0 16px">
    <a-page-header
      title="Статистика"
      sub-title=""
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
      <div :style="{ margin: '20px 0px 0 0px' }">
        <div :style="{ display: 'flex', alignItems: 'center', height: '33px' }">
          <a-space :size="20">
            <span>Сортировать по:</span>
            <a-switch v-model="models.isTypeSort" @change="handleUpdateColumn" /><span :style="{ marginRight: '20px' }">{{ typeSortTitle }}</span>
          </a-space>
          <a-space :size="20">
            <div v-if="models.isTypeSort">
              <a-select
                v-model="models.regionSort"
                style="width: 300px"
                @change="handleRegionChange"
                placeholder="Выберите регион"
                option-label-prop="label"
              >
                <a-select-option
                  :key="0"
                  label="По всем регионам"
                >
                  Все регионы
                </a-select-option>
                <a-select-option
                  v-for="item in $directory.regions"
                  :key="item.id"
                  :label="item.name"
                >
                  {{ item.name }}
                </a-select-option>
              </a-select>
            </div>
            <div v-if="!models.isTypeSort">
              <a-select
                v-model="models.directionSort"
                style="width: 300px"
                @change="handleDirectionChange"
                placeholder="Выберите направление"
                option-label-prop="label"
              >
                <a-select-option
                  v-for="item in $directory.directions"
                  :key="item.id"
                  :label="item.name"
                >
                  {{ item.name }}
                </a-select-option>
              </a-select>
            </div>
          </a-space>
        </div>
        <div :style="{ marginTop: '30px' }">
          <a-row :gutter="[24, 32]">
            <a-col :span="wrapperCol.span">
              <div :style="{ border: '1px solid #ccc', padding: '15px' }">
                <a-statistic
                  title="Заявок поступило"
                  :value="generalStatistic.DEALS_COUNT"
                  class="demo-class"
                />
              </div>
            </a-col>
            <a-col :span="wrapperCol.span">
              <div :style="{ border: '1px solid #ccc', padding: '15px' }">
                <a-statistic
                  title="Источников"
                  :value="generalStatistic.API_APP_COUNT"
                  class="demo-class"
                />
              </div>
            </a-col>
            <a-col :span="wrapperCol.span">
              <div :style="{ border: '1px solid #ccc', padding: '15px' }">
                <a-tooltip title="(нужно / запущенных / на паузе)">
                    <a-statistic
                    title="Необходимо заявок"
                    :value="generalStatistic.LEAD_COUNT"
                    style="margin-right: 50px"
                    >
                        <template #suffix>
                            / {{ generalStatistic.BIDS_NO_PAUSE }} /
                            {{ generalStatistic.BIDS_ON_PAUSE }}
                        </template>
                    </a-statistic>
                </a-tooltip>
              </div>
            </a-col>
            <a-col :span="wrapperCol.span">
              <div :style="{ border: '1px solid #ccc', padding: '15px' }">
                <a-statistic
                  title="Требуется бюджета"
                  :value="
                    generalStatistic.AVG_COST_PRICE *
                    generalStatistic.LEAD_COUNT
                  "
                  :precision="2"
                  class="demo-class"
                >
                  <template #suffix>
                    <span>
                      / {{ generalStatistic.BUDGET_MIN_LEAD_GENERATE }} (сред. /
                      мин.)</span
                    >
                  </template>
                </a-statistic>
              </div>
            </a-col>
            <a-col :span="wrapperCol.span">
              <div :style="{ border: '1px solid #ccc', padding: '15px' }">
                <a-statistic
                  title="Необходимо заявок в день"
                  :value="generalStatistic.DAY_LEAD_GENERATE"
                  class="demo-class"
                >
                </a-statistic>
              </div>
            </a-col>
            <a-col :span="wrapperCol.span">
              <div :style="{ border: '1px solid #ccc', padding: '15px' }">
                <a-statistic
                  title="Скольким клиентам требуются заявки"
                  :value="generalStatistic.BIDS_USER_COUNT"
                  class="demo-class"
                />
              </div>
            </a-col>
            <a-col :span="wrapperCol.span">
              <div :style="{ border: '1px solid #ccc', padding: '15px' }">
                <a-statistic
                  title="Максимальная стоимость заявки"
                  :value="generalStatistic.MAX_BID_RATE"
                  class="demo-class"
                />
              </div>
            </a-col>
            <a-col :span="wrapperCol.span">
              <div :style="{ border: '1px solid #ccc', padding: '15px' }">
                <a-statistic
                  title="Средняя стоимость"
                  :value="generalStatistic.AVG_RATE"
                  class="demo-class"
                />
              </div>
            </a-col>
            <a-col :span="wrapperCol.span">
              <div :style="{ border: '1px solid #ccc', padding: '15px' }">
                <a-statistic
                  title="Время последней заявки(поступление)"
                  :value="generalStatistic.LAST_DEAL_CREATE"
                  class="demo-class"
                />
              </div>
            </a-col>
            <a-col :span="wrapperCol.span">
              <div :style="{ border: '1px solid #ccc', padding: '15px' }">
                <a-statistic
                  title="Время последней заявки(распределение)"
                  :value="generalStatistic.LAST_DEAL_DISTRIBUTION"
                  class="demo-class"
                />
              </div>
            </a-col>
            <a-col :span="wrapperCol.span">
              <div :style="{ border: '1px solid #ccc', padding: '15px' }">
                <a-statistic
                  title="Не определено"
                  :value="generalStatistic.DEALS_NO_DISTRIBUTION"
                  class="demo-class"
                />
              </div>
            </a-col>
          </a-row>
        </div>
        <div :style="{ marginTop: '20px' }">
          <a-config-provider>
            <template #renderEmpty>
              <div style="text-align: center; padding: 20px">
                <a-icon type="container" style="font-size: 20px" />
                <p>Ничего не найдено</p>
              </div>
            </template>
            <a-table
              :columns="columns"
              :data-source="dataSource"
              :loading="isLoading"
            >
            <template slot="budget" slot-scope="text, record">
              {{ record.AVG_COST_PRICE * record.LEAD_COUNT}}
            </template>
            </a-table>
          </a-config-provider>
        </div>
      </div>
    </div>
  </a-layout-content>
</template>
<script>
const columns = []

export default {
  middleware: 'roleWebmaster',
  head() {
    return {
      title: 'Статистика',
    }
  },
  data() {
    return {
      sortByStatistic_type: [],
      models: {
        directionSort: [],
        regionSort: [],
        isDirectionSort: false,
        isRegionSort: false,
        isTypeSort: false,
      },
      wrapperCol: {
        span: 6,
      },
      typeSortTitle: 'Регионам',
      dataSource: [],
      generalStatistic: [],
      columns,
      statisticTitles: [
        'Статистика за неделю',
        'Статистика за месяц',
        'Статистика за год',
      ],
      statisticDropdownTitle: 'Статистика за неделю',
      isLoading: false,
    }
  },
  created() {
    this.updateStatistic()
    this.handleUpdateColumn()
  },
  methods: {
    handleUpdateColumn(e) {
      this.typeSortTitle = this.models.isTypeSort ? 'Регионам' : 'Направлениям'
      if(this.models.isTypeSort) {
        this.columns = [
            {
              title: 'Направление',
              dataIndex: 'DIRECTION_NAME',
              key: 'DIRECTION_NAME',
              scopedSlots: { customRender: 'DIRECTION_NAME' },
            },
            {
              title: 'Сгенерировать',
              dataIndex: 'LEAD_COUNT',
              key: 'LEAD_COUNT',
            },
            {
              title: 'Бюджет',
              dataIndex: 'budget',
              key: 'budget',
              scopedSlots: { customRender: 'budget' },
            },
            {
              title: 'Кол-во клиентов',
              dataIndex: 'USERS_COUNT',
              key: 'USERS_COUNT',
            },
            {
              title: 'Макс. стоимость',
              dataIndex: 'MAX_RATE',
              key: 'MAX_RATE',
            },
            {
              title: 'Сред. стоимость',
              dataIndex: 'AVG_RATE',
              key: 'AVG_RATE',
            },
            {
              title: 'Поступление заявки',
              dataIndex: 'LAST_DEAL_CREATE',
              key: 'LAST_DEAL_CREATE',
            },
            {
              title: 'Распределение заявки',
              dataIndex: 'LAST_DEAL_DISTRIBUTION',
              key: 'LAST_DEAL_DISTRIBUTION',
            },
        ]
      } else {
        this.columns = [
            {
              title: 'Регион',
              dataIndex: 'REGION_NAME',
              key: 'REGION_NAME',
              scopedSlots: { customRender: 'REGION_NAME' },
            },

            {
              title: 'Сгенерировать',
              dataIndex: 'LEAD_COUNT',
              key: 'LEAD_COUNT',
            },
            {
              title: 'Бюджет',
              dataIndex: 'budget',
              key: 'budget',
              scopedSlots: { customRender: 'budget' },
            },
            {
              title: 'Кол-во клиентов',
              dataIndex: 'USERS_COUNT',
              key: 'USERS_COUNT',
            },
            {
              title: 'Макс. стоимость',
              dataIndex: 'MAX_RATE',
              key: 'MAX_RATE',
            },
            {
              title: 'Сред. стоимость',
              dataIndex: 'AVG_RATE',
              key: 'AVG_RATE',
            },
            {
              title: 'Поступление заявки',
              dataIndex: 'LAST_DEAL_CREATE',
              key: 'LAST_DEAL_CREATE',
            },
            {
              title: 'Распределение заявки',
              dataIndex: 'LAST_DEAL_DISTRIBUTION',
              key: 'LAST_DEAL_DISTRIBUTION',
            },
        ]
      }
    }, 
    handleRegionChange(e) {
      this.updateStatistic()
    },
    handleDirectionChange(e) {
      this.updateStatistic()
    },
    updateStatistic() {
      this.isLoading = true
      let postData = {}
      if (!this.models.isTypeSort) {
        postData.directionSort = this.models.directionSort
      }
      if (this.models.isTypeSort) {
        postData.regionSort = this.models.regionSort
      }
      if(Object.keys(postData).length > 0) {
	      this.$axios
		.post('/admin/statistic', postData)
		.then(({ data }) => {
		  if (data.success === true) {
		    const { statistic } = data
		    this.dataSource = statistic.source
		    this.generalStatistic = statistic.general
		    this.generalStatistic.BUDGET_MIN_LEAD_GENERATE  = statistic.general.BUDGET_MIN_LEAD_GENERATE.toFixed(2)
		    this.isLoading = false
		  } else {
		    this.$message.error(data.msg)
		  }
		})
		.catch(({ response }) => {
		  this.$message.error(response.data)
		})
        }
    },
  },
}
</script>
