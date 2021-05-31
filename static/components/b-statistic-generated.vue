<template>
  <a-row :gutter="[24, 32]">
    <a-col :xs="24" :md="8" :lg="8">
      <a-card>
        <a-statistic
          title="Заявок поступило"
          :value="statistic.received"
          :precision="2"
        >
        </a-statistic>
      </a-card>
    </a-col>
    <a-col :xs="24" :md="8" :lg="8">
      <a-card>
        <a-statistic
          title="Заявок распределено"
          :value="statistic.distributed"
          :precision="2"
        >
        </a-statistic>
      </a-card>
    </a-col>
    <a-col :xs="24" :md="8" :lg="8">
      <a-card>
        <a-statistic
          title="Заявок распределено (по страховке)"
          :value="statistic.distributedInsurance"
          :precision="2"
        >
        </a-statistic>
      </a-card>
    </a-col>
    <a-col :xs="24" :md="8" :lg="8">
      <a-card>
        <a-statistic
          title="Заявок не распределено"
          :value="statistic.noDistributed"
          :precision="2"
        >
        </a-statistic>
      </a-card>
    </a-col>
    <a-col :xs="24" :md="8" :lg="8">
      <a-card>
        <a-statistic
          title="Заявок возвращено по страховке"
          :value="statistic.returned"
          :precision="2"
        >
        </a-statistic>
      </a-card>
    </a-col>
    <a-col :xs="24" :md="8" :lg="8">
      <a-card>
        <a-statistic
          title="Сумма распределения заявок (рубли)"
          :value="statistic.sumDistributed"
          :precision="2"
        >
        </a-statistic>
      </a-card>
    </a-col>
    <a-col :xs="24" :md="8" :lg="8">
      <a-card>
        <a-statistic
          title="Сумма распределения заявок (бонусы)"
          :value="statistic.sumDistributedBonus"
          :precision="2"
        >
        </a-statistic>
      </a-card>
    </a-col>
    <a-col :xs="24" :md="8" :lg="8">
      <a-card>
        <a-statistic
          title="Сумма возврата заявок (бонусы)"
          :value="statistic.sumReturned"
          :precision="2"
        >
        </a-statistic>
      </a-card>
    </a-col>
  </a-row>
</template>
<script>
export default {
  name: "b-statisitc-generated",
  props: {
    statistic: {
      type: Object,
      default: {},
    },
  },
};
</script>