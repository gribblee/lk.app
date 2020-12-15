<template>
  <a-layout-content style="margin: 20px 16px 0 16px">
    <a-page-header
      :title="userData.name"
      sub-title="пользователь"
      @back="() => $router.go(-1)"
    >
    </a-page-header>
    <div :style="{ padding: '24px', background: '#fff', minHeight: '360px' }">
      <a-row type="flex" justify="space-around">
        <a-col :span="6">
          <div>
            <div>
              <span>Имя пользователя: </span>
              <a-input
                :style="{ marginTop: ' 10px' }"
                type="text"
                v-model="userData.name"
              />
            </div>
            <div :style="{ marginTop: '15px' }">
              <span>E-mail: </span>
              <a-input
                :style="{ marginTop: ' 10px' }"
                type="text"
                v-model="userData.email"
              />
            </div>
            <div :style="{ marginTop: '15px' }">
              <span>E-mail для уведомлений: </span>
              <a-input
                :style="{ marginTop: ' 10px' }"
                type="text"
                v-model="userData.email_notification"
              />
            </div>
            <div :style="{ marginTop: '15px' }">
              <span>Телефон: </span>
              <a-input
                :style="{ marginTop: ' 10px' }"
                type="text"
                v-model="userData.phone"
              />
            </div>
            <div :style="{ marginTop: '15px' }">
              <span>Пароль: </span>
              <a-input
                :style="{ marginTop: ' 10px' }"
                type="text"
                v-model="userData.password"
              />
            </div>
            <div :style="{ marginTop: '15px' }" v-if="user.role == 'ROLE_ADMIN'">
              <span>Баланс: </span>
              <a-input
                :style="{ marginTop: ' 10px' }"
                type="text"
                v-model="userData.balance"
              />
            </div>
            <div :style="{ marginTop: '15px' }" v-if="user.role == 'ROLE_ADMIN'">
              <span>Бонусы: </span>
              <a-input
                :style="{ marginTop: ' 10px' }"
                type="text"
                v-model="userData.bonus"
              />
            </div>
            <div :style="{ marginTop: '15px' }">
              <span>ID в Bitrix: </span>
              <a-input
                :style="{ marginTop: ' 10px' }"
                type="text"
                v-model="userData.contact_id"
              />
            </div>
            <div :style="{ marginTop: '15px' }" v-if="user.role == 'ROLE_ADMIN'">
              <span>Роль: </span>
              <a-select
                v-model="userData.role"
                opt-label-prop="label"
                :style="{ marginTop: ' 10px', width: '100%' }"
              >
                <a-select-option value="ROLE_ADMIN" label="Администратор"
                  >Администратор</a-select-option
                >
                <a-select-option value="ROLE_WEBMASTER" label="Вебмастер"
                  >Вебмастер</a-select-option
                >
                <a-select-option value="ROLE_MANAGER" label="Менеджер"
                  >Менеджер</a-select-option
                >
                <a-select-option value="ROLE_ACCOUNTANT" label="Бухгалтер"
                  >Бухгалтер</a-select-option
                >
                <a-select-option value="ROLE_USER" label="Пользователь"
                  >Пользователь</a-select-option
                >
              </a-select>
            </div>
            <div :style="{ marginTop: '15px' }" v-if="user.role == 'ROLE_ADMIN'">
              <span>Менеджер: </span>
              <a-select
                v-model="userData.manager_id"
                opt-label-prop="label"
                :style="{ marginTop: ' 10px', width: '100%' }"
              >
                <a-select-option :key="0">Без менеджера</a-select-option>
                <a-select-option
                  v-for="(manager, index) in managers"
                  :key="manager.id"
                  :value="manager.id"
                  :label="manager.name"
                  >{{ manager.name }} ({{ manager.email }})</a-select-option
                >
              </a-select>
            </div>
            <div :style="{ marginTop: '25px', textAlign: 'center' }">
              <a-button type="primary" @click="userSend">Сохранить</a-button>
            </div>
          </div>
        </a-col>
      </a-row>
    </div>
  </a-layout-content>
</template>
<script>
export default {
  middleware: 'roleManager',
  data() {
    return {
      userData: {
        password: '',
      },
      defUser: {
        password: '',
      },
      userId: 0,
      managers: [],
    }
  },
  created() {
    this.$axios
      .post('/user/show', {
        user_id: this.$route.params.id,
      })
      .then(({ data }) => {
        delete data.updated_at
        delete data.created_at
        this.userData = data
        this.defUser = Object.assign({}, data)
        this.userId = data.id
      })
      .catch((err) => {
        console.error(err)
      })
    this.$axios
      .get('/managers')
      .then(({ data }) => {
        this.managers = data
      })
      .catch((err) => {
        console.error(err)
      })
  },
  methods: {
    userSend(e) {
      let formData = {}
      for (let k in this.userData) {
        if (
          (this.userData[k] != this.defUser[k] &&
            (this.userData[k]).toString().length > 0) ||
          (this.userData[k] != null && this.defUser[k] == null)
        ) {
          formData[k] = this.userData[k]
        }
      }
      console.log(this.userData, this.defUser);
      this.$axios
        .post('/user/updates', {
          user_id: this.userId,
          update: formData,
        })
        .then(({ data }) => {
          if (data.success == true) {
            this.$message.success(data.message)
            this.defUser = data.user
            this.defUser.password = this.userData.password
            delete this.defUser.updated_at
            delete this.defUser.created_at
          }
        })
        .catch((err) => {
          this.$message.error(err)
        })
    },
  },
}
</script>
