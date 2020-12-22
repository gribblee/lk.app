<template>
  <div class="offer-tinkoff-container" :class="{ close: !offerVisible }">
    <div class="offer-tinkoff">
      <div class="offer-tinkoff__title">
        Получай заявки сейчас, а плати потом
      </div>
      <div class="offer-tinkoff__description">
        Хотите
        <b :style="{ fontWeight: '900' }"
          >получить клиентов уже сегодня, а заплатить только через месяц</b
        >? Наш сервис предоставляет специальное предложение рассрочки на заявки.
        <b :style="{ fontWeight: '900' }"
          >Сначала получайте клиентов и зарабатывайте, а платите потом</b
        >. Оформите уже сейчас - это не займёт больше пяти минут!
      </div>
      <div class="offer-tinkoff__action">
        <a-button type="danger" @click="openOffer">Попробовать</a-button>
        <a-button type="link" @click="closeOffer">Нет спасибо</a-button>
      </div>
    </div>
    <div class="offer-tinkoff-about">
      Рассрочка предоставляется нашим Партнёром - Банком Тинькофф
    </div>
  </div>
</template>
<script>
export default {
  name: "b-tinkoff-offer",

  data() {
    return {
      offerVisible: false,
    };
  },
  created() {},
  mounted() {
    if (!localStorage.getItem("offerVisible")) {
      localStorage.setItem("offerVisible", this.offerVisible);
    } else {
      this.offerVisible =
        localStorage.getItem("offerVisible") == "true" ? true : false;
    }
  },
  methods: {
    openOffer(e) {
      this.$store.dispatch("tinkoff/setInstallmentVisible", true);
      this.$axios.post("/counter/open_tinkoff_offer");
    },
    closeOffer(e) {
      this.offerVisible = false;
      localStorage.setItem("offerVisible", this.offerVisible);
      this.$axios.post("/counter/close_tinkoff_offer");
    },
  },
};
</script>