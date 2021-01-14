<template>
  <a-layout-sider>
    <a-menu
      mode="inline"
      :default-selected-keys="[menuKey]"
      style="height: 100%"
    >
      <template v-for="item in menuItems">
        <a-menu-item
          :key="item.name"
          v-if="item.modeHidden.indexOf(user.role) < 0"
        >
          <nuxt-link :to="item.url">{{ item.title }}</nuxt-link>
        </a-menu-item>
      </template>
    </a-menu>
  </a-layout-sider>
</template>
<script lang="ts">
import Vue from "vue";
export default Vue.extend({
  props: {
    menuKey: String,
  },
  data() {
    return {
      menuItems: [
        {
          name: "general",
          title: "Общие настройки",
          url: "/setting",
          modeHidden: [],
        },
        {
          name: "setting-requisite",
          title: "Реквизиты",
          url: "/setting/requisite",
          modeHidden: [],
        },
        {
          name: "setting-admin-categories",
          title: "Категории",
          url: "/setting/admin/categories",
          modeHidden: ["ROLE_MANAGER", "ROLE_WEBMASTER", "ROLE_USER"],
        },
        {
          name: "setting-admin-directory",
          title: "Справочники",
          url: "/setting/admin/directory",
          modeHidden: ["ROLE_MANAGER", "ROLE_WEBMASTER", "ROLE_USER"],
        },
        {
          name: "setting-admin-disput",
          title: "Статусы спора",
          url: "/setting/admin/disput",
          modeHidden: ["ROLE_MANAGER", "ROLE_WEBMASTER", "ROLE_USER"],
        },
        {
          name: "setting-admin-options",
          title: "Опции",
          url: "/setting/admin/options",
          modeHidden: ["ROLE_MANAGER", "ROLE_WEBMASTER", "ROLE_USER"],
        },
      ],
    };
  },
  name: "b-layout-sider",
  methods: {},
  mounted() {},
});
</script>