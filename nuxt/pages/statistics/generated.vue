<template>
  <a-layout-content style="margin: 20px 16px 0 16px">
    <a-page-header title="Статистика" sub-title="" @back="() => $router.go(-1)">
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
            <div>
              <a-select
                v-model="models.regionSort"
                style="width: 300px"
                @change="handleRegionChange"
                placeholder="Выберите регион"
                option-label-prop="label"
              >
                <a-select-option :key="0" label="По всем регионам">
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
            <div>
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
            <div>
              <a-select
                v-model="models.managerSort"
                style="width: 300px"
                @change="handleManagerChange"
                placeholder="Выберите менеджера"
                option-label-prop="label"
              >
                <a-select-option
                  v-for="item in managers"
                  :key="item.id"
                  :label="item.name"
                >
                  {{ item.name }}
                </a-select-option>
              </a-select>
            </div>
            <div>
              <a-input
                type="text"
                v-model="models.clientSort"
                placeholder="Имя клиента"
              />
            </div>
            <div>
              <a-range-picker
                @change="onDateChange"
                :placeholder="['Дата от', 'Дата до']"
              />
            </div>
          </a-space>
        </div>
        <div :style="{ marginTop: '30px' }">
          <b-statistic-generated :statistic="dataStatistic" />
        </div>
      </div>
    </div>
  </a-layout-content>
</template>
<script>
const columns = [];

export default {
  middleware: "roleWebmaster",
  head() {
    return {
      title: "Статистика",
    };
  },
  data() {
    return {
      models: {
        directionSort: [],
        regionSort: [],
        managerSort: [],
        clientSort: "",
        dateSort: [],
      },
      dataStatistic: {
        received: 0,
        distributed: 0,
        distributedInsurance: 0,
        noDistributed: 0,
        returnedInsurance: 0,
        sumDistributed: 0,
        sumDistributedBonus: 0,
        sumReturned: 0,
      },
      managers: [],
      isLoading: false,
    };
  },
  created() {
    this.loadStatisitic();
  },
  methods: {
    handleRegionChange(e) {},
    handleDirectionChange(e) {},
    handleManagerChange(e) {},
    handleClientChange(e) {},
    onDateChange(date, dateString) {
      this.models.dateSort = dateString;
    },
    loadStatisitic() {
      this.$axios
        .post("/statistic/generated", this.models)
        .then(({ data }) => {
          this.dataStatistic = data;
        })
        .catch((err) => console.log(err));
    },
  },
};
</script>
