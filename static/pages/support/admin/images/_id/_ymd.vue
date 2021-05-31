<template>
  <div :style="{display: 'flex', flexWrap: 'wrap'}">
    <div v-for="(image, index) in images" :key="index" :style="{ padding: '0 20px', width: '50%'}">
      <img :src="`data:${image.mime_type};base64,${image.b64}`" :style="{ maxWidth: '100%', height: 'auto'}" />
    </div>
  </div>
</template>
<script lang="ts">
import Vue from "vue";
export default Vue.extend({
  data() {
    return {
      images: [],
    };
  },
  created() {
    const { id, ymd } = this.$route.params;
    const { $axios } : any = this;
    $axios.get(`support/images/${id}/${ymd}`).then(({ data }: any) => {
      this.images = data;
    });
  },
});
</script>