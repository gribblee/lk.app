<template>
  <div :style="{ height: '100%', display: 'flex', alignItems: 'center', justifyContent: 'center' }">
    <a-result status="error" title="Возникла ошибка при оплате повторите попытку чуть позже" sub-title="">
      <template #extra>
        <nuxt-link to="/">Перейти в управление</nuxt-link>
      </template>
    </a-result>
  </div>
</template>
<script>
export default {}
</script>
