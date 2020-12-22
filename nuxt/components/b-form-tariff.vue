<template>
  <div class="b-form-tariff">
    <div class="b-form-row">
      <b-form-col-tariff
        :id="1001"
        title="Попробовать"
        subtitle="+1-2 клиента уже через неделю БОНУС пропадает"
        :price="5000"
        titleButton="Подключиться"
        @change="handleChange"
      />
      <b-form-col-tariff
        :id="1002"
        title="Начать"
        subtitle="+3-4 клиента уже через неделю БОНУС 1.000 р. на баланс"
        :price="10000"
        titleButton="Подключиться"
        @change="handleChange"
      />
      <b-form-col-tariff
        :id="1003"
        title="Максимум"
        subtitle="+5-7 клиентов уже через неделю БОНУС 5.000 р. на баланс"
        :price="25000"
        titleButton="Подключиться"
        @change="handleChange"
      />
    </div>
  </div>
</template>
<script>
export default {
  name: "b-form-tariff",

  data() {
    return {};
  },

  created() {},
  mounted() {},

  methods: {
    handleChange(v) {
      this.$emit("onpay", {
        is: true,
        id: v.id,
      });
      // this.$axios
      //   .post("/demo/tariff", { id: v.id })
      //   .then(({ data }) => {
      //     if (data.success) {

      //     } else {
      //     }
      //   })
      //   .catch((err) => {
      //     console.error(err);
      //   });
    },
  },
};
</script>