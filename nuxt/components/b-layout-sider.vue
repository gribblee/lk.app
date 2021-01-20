<template>
  <a-layout-sider>
    <a-menu
      mode="inline"
      :default-selected-keys="[menuKey]"
      :default-open-keys="openKeys"
      style="height: 100%"
    >
      <template v-for="item in menuItems">
        <template
          v-if="
            (typeof item.children == 'undefined' ? '' : item.children).length >
              0 && item.modeHidden.indexOf(user.role) < 0
          "
        >
          <a-sub-menu :key="item.name">
            <span slot="title"><a-icon type="book" />{{ item.title }}</span>
            <a-menu-item v-for="child in item.children" :key="child.name"
              ><nuxt-link :to="child.url">{{
                child.title
              }}</nuxt-link></a-menu-item
            >
          </a-sub-menu>
        </template>
        <template v-else>
          <a-menu-item
            :key="item.name"
            v-if="item.modeHidden.indexOf(user.role) < 0"
          >
            <nuxt-link :to="item.url">{{ item.title }}</nuxt-link>
          </a-menu-item>
        </template>
      </template>
    </a-menu>
  </a-layout-sider>
</template>
<script lang="ts">
import Vue from "vue";
export default Vue.extend({
  props: {
    menuKey: String,
    openKeys: Array,
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
          children: [
            {
              name: "setting-admin-directions",
              title: "Направления",
              url: "/setting/admin/directions",
            },
            {
              name: "setting-admin-regions",
              title: "Регионы",
              url: "/setting/admin/regions",
            },
            {
              name: "setting-admin-sendpulse",
              title: "SendPulse",
              url: "/setting/admin/sendpulse",
            },
          ],
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