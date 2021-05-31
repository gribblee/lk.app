<template>
  <div :style="{ height: '100%', display: 'flex', alignItems: 'center', justifyContent: 'center' }">
    <a-result status="success" title="Оплата прошла успено" sub-title="">
      <template #extra>
        <nuxt-link to="/">Перейти в управление</nuxt-link>
      </template>
    </a-result>
  </div>
</template>
<script>
export default {
    mounted() {
        yaCounter67456357.reachGoal('client_payment', {
            order_price: this.$route.query.Amount * 100,
            currency: 'RUB'
        })
    //   this.$metrika.hit('67456357','success', 'client_payment', {
    //     order_price: this.$route.query.Amount * 100,
    //     currency: 'RUB'
    //   });
    }
}
</script>
