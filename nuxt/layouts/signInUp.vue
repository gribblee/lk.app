<template>
  <main class="main-sign">
    <div class="view-1" id="scene">
      <div class="logo" data-depth="0.6">
        <svg>
          <use xlink:href="/icons-pack.svg#svg-logo"></use>
        </svg>
      </div>
      <div class="view-1__content" data-depth="0.6">
        <div>
          <h1 class="view-1__heading-1">КЛИЕНТЫ НА БУХГАЛТЕРСКИЕ УСЛУГИ</h1>
          <div class="view-1__subtitle">
            Оставь заявку и получи от 20 клиентов на бухгалтерские услуги уже
            завтра Это +150 000 руб. дополнительной прибыли ежемесячно
          </div>
        </div>
        <div>12</div>
        <div>
          <span class="view-1__foottitle"
            >Клиенты на бухгалтерские услуги без головной боли по всей России
            Готовое решение для бухгалтерских компаний: До 150 клиентов на
            бухуслуги в месяц без проблем</span
          >
        </div>
      </div>
    </div>
    <div class="view-2">
      <div class="view-2__content">
        <h1 class="view-2__heading-1">Пройти регистрацию</h1>
        <Nuxt />
        <div>
          <span class="view-2__foottitle"
            >Заполните ФИО, телефон и почту - и увидите, как просто получать
            клиентов Вы будете направлены на страницу с подробным описанием ВСЕЙ
            технологии привлечения</span
          >
        </div>
      </div>
    </div>
  </main>
</template>
<script>
export default {
  name: "signInUp",
  middleware: "guest",
  mounted() {},
};
</script>