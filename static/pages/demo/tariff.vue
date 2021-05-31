<template>
  <div class="demo-form">
    <div class="demo-header-1">Выберите подходящий тариф</div>
    <div class="demo-main-row">
      <a-row :gutter="[16, 16]">
        <a-col :xs="24" :md="8" :lg="8">
          <a-card title="Попробовать" hoverable>
            <div class="demo-tariff-description">
              +1-2 клиента уже через неделю БОНУС пропадает
            </div>
            <div class="demo-tariff-price">
              <span class="demo-tariff-icon">₽</span>5 000
            </div>
            <div class="demo-tariff-action">
              <a-button
                type="primary"
                shape="round"
                size="large"
                @click="onTariff('1001')"
                >Подключится</a-button
              >
            </div>
          </a-card>
        </a-col>
        <a-col :xs="24" :md="8" :lg="8">
          <a-card title="Начать" hoverable>
            <div class="demo-tariff-description">
              +3-4 клиента уже через неделю БОНУС 1.000 р. на баланс
            </div>
            <div class="demo-tariff-price">
              <span class="demo-tariff-icon">₽</span>10 000
            </div>
            <div class="demo-tariff-action">
              <a-button
                type="danger"
                shape="round"
                size="large"
                @click="onTariff('1002')"
                >Подключится</a-button
              >
            </div>
          </a-card>
        </a-col>
        <a-col :xs="24" :md="8" :lg="8">
          <a-card title="Максимум" hoverable>
            <div class="demo-tariff-description">
              +5-7 клиентов уже через неделю БОНУС 5.000 р. на баланс
            </div>
            <div class="demo-tariff-price">
              <span class="demo-tariff-icon">₽</span>25 000
            </div>
            <div class="demo-tariff-action">
              <a-button
                type="primary"
                shape="round"
                size="large"
                @click="onTariff('1003')"
                >Подключится</a-button
              >
            </div>
          </a-card>
        </a-col>
      </a-row>
    </div>
  </div>
</template>
<style scoped>
.demo-form {
  margin-top: 30px;
}
.demo-header-1 {
  font-size: 40px;
}

.demo-main-row {
  margin-top: 30px;
}

.demo-tariff-description {
  font-size: 16px;
  text-align: center;
}

.demo-tariff-price {
  font-size: 40px;
  text-align: center;
  padding-top: 15px;
}

.demo-tariff-icon {
  font-size: 20px;
}

.demo-tariff-action {
  display: flex;
  justify-content: center;
  padding-top: 15px;
}

@media screen and (max-width: 764px) {
  .demo-header-1 {
    font-size: 20px;
    padding: 0 20px;
  }
  .demo-main-row {
    padding: 0 20px;
  }
}
</style>
<script lang="ts">
import Vue from "vue";
export default Vue.extend({
  name: "demo-tariff",
  layout: "demo-layout",
  head() {
    return {
      title: "Тариф",
    };
  },
  created() {
    const { $store }: any = this;
    $store.state.localStorage.demoSiderHeader = 'Выберите тариф';
    $store.state.localStorage.demoSiderDescription =
      "<p>Выберите подходящий Вам тариф</p><p>“Попробовать” - пополните счет на 5.000 р. и привлеките первого клиента!</p><p>“Начать” - пополните счет на 10.000 р. и получите БОНУС +1.000 р. на баланс!</p><p>“Максимум” - пополните счет сразу на 25.000 р. и получите БОНУС +5.000 р. на баланс!</p><p>Только при пополнении баланса СЕЙЧАС Вы получаете бонус! </p>";
  },
  mounted() {},
  methods: {
    onTariff(id: string) {
      const { $store, $router }: any = this;
      $store.state.localStorage.tariffId = id;
      $store.state.localStorage.demoStepId = 3;
      $router.push("/demo/finish");
    },
  },
});
</script>