<template>
  <div class="demo-form">
    <a-card title="Мастер настройки">
      <a-form-model :rules="rulesForm" :model="form" ref="demoForm">
        <a-form-model-item label="Выберите регионы" prop="regions">
          <a-select
            mode="multiple"
            option-label-prop="label"
            v-model="form.regions"
            :disabled="form.allRegion"
          >
            <a-select-option
              v-for="(region, index) in regions"
              :key="index"
              :label="region.name_with_type"
              :value="region.id"
              >{{ region.name_with_type }}</a-select-option
            >
          </a-select>
          <a
            href="#"
            @click.prevent="(e) => (form.allRegion = true)"
            v-show="!form.allRegion"
            >Хочу получать по всей России</a
          >
          <a
            href="#"
            @click.prevent="(e) => (form.allRegion = false)"
            v-show="form.allRegion"
            >Хочу выбрать регионы</a
          >
        </a-form-model-item>
        <a-form-model-item label="Чем вы занимаетесь?" prop="directions">
          <a-checkbox-group
            v-model="form.directions"
            name="checkboxgroup"
            :options="directionOptions"
          />
        </a-form-model-item>
        <a-form-model-item label="Что для вас важнее?" prop="strategyId">
          <a-radio-group v-model="form.strategyId" button-style="solid">
            <a-radio-button :value="1">Много клиентов</a-radio-button>
            <a-radio-button :value="2">Мало затрат</a-radio-button>
          </a-radio-group>
        </a-form-model-item>
      </a-form-model>
      <a-form-model-item v-if="isNext">
        <a-button type="danger" size="large" @click="submitForm('demoForm')"
          >Перейти к Выбору тарифа</a-button
        >
      </a-form-model-item>
    </a-card>
  </div>
</template>
<style scoped>
.demo-form {
  margin-top: 30px;
}
</style>
<script lang="ts">
import Vue from "vue";
export default Vue.extend({
  name: "demo",
  layout: "demo-layout",
  head() {
    return {
      title: "Мастер настроек",
    };
  },
  data() {
    return {
      regions: [],
      form: {
        allRegion: false,
        regions: [],
        directions: [],
        strategyId: 1,
      },
      rulesForm: {
        directions: [
          {
            type: "array",
            required: true,
            message: "Пожалуйста укажите хотя бы одно направление",
            trigger: "change",
          },
        ],
      },
      directionOptions: [],
    };
  },
  computed: {
    isNext() {
      const { form }: any = this;
      return (
        form.directions.length > 0 &&
        (form.allRegion == true || form.regions.length > 0)
      );
    },
  },
  methods: {
    submitForm(formName: string) {
      const { $router, $refs, $axios, $store, form }: any = this;
      $refs[formName].validate((valid: any) => {
        if (valid) {
          $store.state.localStorage.demoStepId = 2;
          $store.state.localStorage.demoForm = form;
          $router.push("/demo/tariff");
        } else {
          console.error("DEMO FORM NOT VALID");
        }
      });
    },
  },
  beforeCreate() {
    const app: any = this;
    const { $store }: any = app;
    $store.state.localStorage.demoSiderHeader = "Мастер настроек";
    $store.state.localStorage.demoSiderDescription =
      "<p>Вам нужно выбрать только по каким направлениям вы работаете. Регион и мастер настроек в автоматическом режиме подберёт нужные для вас параметры</p>";

    app.$axios.get("/directory").then(({ data }: any) => {
      app.regions = data.regions;
      app.directionOptions = data.directions.map((direction: any) => {
        return { label: direction.name, value: direction.id };
      });
    });
  },
  mounted() {},
});
</script>