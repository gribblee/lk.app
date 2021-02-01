<template>
  <a-layout class="b-demo-layout">
    <b-demo-sider v-if="$store.state.localStorage.demoStepId !== 3">
      <div class="b-demo-sider-header">
        {{ $store.state.localStorage.demoSiderHeader }}
      </div>
      <div
        class="b-demo-sider-description"
        v-html="$store.state.localStorage.demoSiderDescription"
      ></div>
    </b-demo-sider>
    <a-layout>
      <a-layout-header class="b-demo-layout-header"> Logo </a-layout-header>
      <a-layout-content class="demo-content">
        <div class="demo-content__step">
          <a-steps :current="$store.state.localStorage.demoStepId">
            <a-step title="Начать" description="Пройти регистрацию">
              <a-icon slot="icon" type="user" />
            </a-step>
            <a-step title="Настроить" description="что -то сделать">
              <a-icon slot="icon" type="setting" />
            </a-step>
            <a-step title="Выбрать тариф" description="что -то сделать">
              <a-icon slot="icon" type="credit-card" />
            </a-step>
            <a-step title="Завершение" description="описание">
              <a-icon slot="icon" type="smile-o" />
            </a-step>
          </a-steps>
        </div>
        <nuxt />
      </a-layout-content>
    </a-layout>
  </a-layout>
</template>
<style scoped>
.b-demo-layout {
  display: flex;
  flex-direction: row;
}

.b-demo-layout-header {
  display: flex;
  justify-content: center;
  background-color: transparent;
}

.demo-content {
  max-width: 100%;
  width: 940px;
  margin: 0 auto;
}

.b-demo-sider-header {
  font-size: 24px;
  font-weight: 800;
  margin-top: 40px;
}

.b-demo-sider-description {
  font-size: 15px;
  margin-top: 30px;
}

@media screen and (max-width: 764px) {
  .b-demo-layout {
    flex-direction: column;
  }
  .demo-content__step {
    display: none;
    padding: 0 20px;
  }
}
</style>
<script lang="ts">
import Vue from "vue";
export default Vue.extend({
  name: "demo-layout",
  beforeCreate() {},
  mounted() {
    const { $store, $router, user }: any = this;
    if (user.is_demo) {
      $router.push("/");
    }
    if ($store.state.localStorage.demoStepId == 2) {
      $router.push("/demo/tariff");
    }
  },
});
</script>