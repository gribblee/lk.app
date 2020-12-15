<template>
  <a-layout-content style="margin: 20px 16px 0 16px">
    <a-page-header
      title="Сообщения"
      sub-title="пояснение"
      @back="() => $router.go(-1)"
    >
      <template slot="extra">
        <div :style="{ display: 'flex' }">
          <div>
            <a-dropdown :trigger="['click']">
              <a-menu slot="overlay" @click="handleMenuClick">
                <a-menu-item key="1">
                  <a-icon type="user" />
                  Удалить
                </a-menu-item>
                <a-menu-item key="2">
                  <a-icon type="user" />
                  Отключить
                </a-menu-item>
                <a-menu-item key="3">
                  <a-icon type="user" />
                  Включить
                </a-menu-item>
              </a-menu>
              <a-button type="default">
                Не прочитанные
                <a-icon type="down" />
              </a-button>
            </a-dropdown>
          </div>
        </div>
      </template>
    </a-page-header>
    <a-layout style="padding: 24px 0; background: #fff">
      <a-layout-sider width="200" style="background: #fff">
        <a-menu mode="inline" style="height: 100%">
          <a-menu-item key="1" @click="handleSelected(1)">
            <a-badge dot>Сообщение #123</a-badge>
          </a-menu-item>
        </a-menu>
      </a-layout-sider>
      <a-layout-content :style="{ padding: '0 24px', minHeight: '280px' }">
        <template v-if="messageId === 0">
          <span>Нет диалогов</span>
        </template>
        <div v-else>Сообщение</div>
      </a-layout-content>
    </a-layout>
  </a-layout-content>
</template>
<script>
import moment from 'moment'

export default {
  data() {
    return {
      comments: [],
      messageId: 0,
      submitting: false,
      value: '',
      moment,
    }
  },
  methods: {
    handleSelected(_id) {
      this.messageId = _id
    },
    handleMenuClick(_e) {},
    handleSubmit(_e) {},
    handleChange(_e) {},
  },
}
</script>
