<template>
  <a-layout-content style="margin: 20px 16px 0 16px">
    <a-page-header
      title="Создание направления"
      sub-title=""
      @back="() => $router.go(-1)"
    />
    <div :style="{ padding: '24px', background: '#fff', minHeight: '360px' }">
      <a-row type="flex" justify="space-around">
      </a-row>
    </div>
    </a-layout-content>
</template>
<script>
export default {
    head() {
        return {
            title: 'Создание заявки'
        }
    }
}
</script>