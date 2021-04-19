<template>
  <a-layout id="components-layout-responsive">
    <a-layout-sider
      breakpoint="lg"
      collapsed-width="0"
      @collapse="onCollapse"
      @breakpoint="onBreakpoint"
    >
      <div class="logo">
        <img src="/logo.png" :style="{ width: 'auto', height: '32px' }" />
      </div>
      <a-menu
        theme="dark"
        mode="inline"
        :default-selected-keys="menuSelectedKeys"
      >
        <template v-for="(item, index) in navMenu">
          <a-menu-item :key="index" v-if="isRoleHidden(item.rolesHidden)">
            <nuxt-link :to="item.url" v-if="item.type == 'Route'">
              <a-icon :type="item.icon" />
              <span class="nav-text">
                {{ item.name }}
              </span>
            </nuxt-link>
            <a
              :href="item.url"
              :target="item.target"
              v-if="item.type == 'Link'"
            >
              <a-icon :type="item.icon" />
              <span class="nav-text">{{ item.name }}</span>
            </a>
          </a-menu-item>
        </template>
      </a-menu>
    </a-layout-sider>
    <a-layout>
      <a-layout-header :style="{ background: '#fff', padding: 0 }">
        <div
          :style="{
            display: 'flex',
            height: '100%',
            justifyContent: 'space-between',
            padding: '0 24px',
          }"
        >
          <div>
            <a-button
              type="danger"
              @click="supportVisibleShow"
              class="btn-hover-error"
              >Сообщить о проблеме</a-button
            >
          </div>
          <div class="layout-top">
            <div class="layout-top__region">
              <a class="ant-dropdown-link" @click.prevent="openRegionModal">
                Ваш регион {{ regionName }} <a-icon type="down" />
              </a>
            </div>
            <span :style="{ padding: '0 10px' }">
              <a-tag color="green"
                >Вы зашли как {{ $userUpdated.typeName }}</a-tag
              >
            </span>
            <span
              :class="{ helpTooltip_end: helpTooltip == 10 }"
              @click="handleBTooltip"
            >
              <a-tooltip
                :visible="helpTooltip == 10"
                placement="bottom"
                title='Отлично! Теперь нажмите кнопку "Пополнить", выберите "с расчётного счёта" выберите созданные реквизиты и нажмите "Пополнить"'
              >
                <span>Бонусы {{ $userUpdated.bonus }} ₽</span>
                <span :style="{ marginLeft: '10px' }">
                  <a-dropdown-button
                    @click="handleBalanceClick"
                    :trigger="['click']"
                    type="primary"
                  >
                    <span>
                      <a-icon type="wallet" />
                    </span>
                    <span :style="{ marginLeft: '5px' }"
                      >{{ $userUpdated.balance }} ₽</span
                    >
                    <a-menu slot="overlay" @click="handlePaymentDropdown">
                      <a-menu-item :key="1">С карты</a-menu-item>
                      <a-menu-item :key="2">С расчётного счёта</a-menu-item>
                      <a-menu-item
                        :key="4"
                        :style="{
                          backgroundColor: 'rgba(232, 215, 63, 0.3)',
                        }"
                        >Плати потом</a-menu-item
                      >
                      <a-menu-item :key="3">
                        <a-checkbox
                          @change="onPayBonusChange"
                          v-model="payBonus"
                        >
                          Оплачивать с боунсов
                        </a-checkbox></a-menu-item
                      >
                    </a-menu>
                    <template slot="icon">Пополнить</template>
                  </a-dropdown-button>
                </span>
              </a-tooltip>
            </span>
            <div
              v-if="helpTooltip == 10"
              :style="{
                position: 'fixed',
                left: '0',
                top: '0',
                width: '100%',
                height: '100%',
                zIndex: '98',
                backgroundColor: 'rgba(34, 34, 34, 0.4)',
              }"
            ></div>
            <a-dropdown :trigger="['click']" :style="{ marginLeft: '30px' }">
              <a class="ant-dropdown-link">
                {{ user.name }}
                <a-icon type="down" />
              </a>
              <a-menu slot="overlay" @click="handleUserTypeUpdate">
                <a-menu-item
                  v-for="(category, index) in $directory.categories"
                  :key="index"
                  :style="{ position: 'relative', paddingRight: '27px' }"
                  :value="category.id"
                  >Я {{ category.name }}
                  <i
                    :style="{
                      position: 'absolute',
                      top: '50%',
                      transform: 'translateY(-50%)',
                      right: '10px',
                      borderRadius: '50%',
                      width: '10px',
                      height: '10px',
                      backgroundColor: '#40a9ff',
                    }"
                    v-if="userType === category.id"
                  ></i
                ></a-menu-item>
                <a-menu-divider />
                <a-menu-item key="3" @click="signOut">Выйти</a-menu-item>
              </a-menu>
            </a-dropdown>
          </div>
        </div>
      </a-layout-header>
      <div :style="{ margin: '20px 0' }">
        <b-tinkoff-offer />
      </div>
      <a-layout-content>
        <nuxt />
      </a-layout-content>
      <a-layout-footer style="textalign: center">
        Leadz.Monster 2020
        <!-- Карта пополнение -->
        <a-modal
          :visible="$visiblePayCard"
          title="Пополнение с карты"
          centered
          ok-text="Продолжить"
          cancel-text="Отмена"
          @cancel="(e) => $store.dispatch('payment/setVisiblePayCard', false)"
          @ok="payCDOk"
        >
          <div
            :style="{
              display: 'flex',
              flexDirection: 'column',
            }"
          >
            <span>Сумма: </span>
            <a-input
              v-model="FormCD.sumPay"
              placeholder="Сумма пополнения"
              :style="{ width: '100%', marginTop: '10px' }"
            />
          </div>
        </a-modal>
        <!-- Рассрочка -->
        <a-modal
          :visible="$tinkoffInstallmentVisible"
          title="Оформление рассрочки"
          centered
          ok-text="Продолжить"
          cancel-text="Отмена"
          @cancel="
            (e) => $store.dispatch('tinkoff/setInstallmentVisible', false)
          "
          @ok="payTinkoffOk"
        >
          <div
            :style="{
              display: 'flex',
              flexDirection: 'column',
            }"
          >
            <span>Сумма: </span>
            <a-input
              v-model="FormCredit.sumPay"
              placeholder="Сумма рассрочки"
              :style="{ width: '100%', marginTop: '10px' }"
            />
          </div>
        </a-modal>
        <!-- Реквизиты пополнение -->
        <a-modal
          :visible="$visiblePayRequisite"
          title="Пополнение по реквизитам"
          centered
          ok-text="Пополнить"
          cancel-text="Закрыть"
          @ok="payReqOk"
          @cancel="
            (e) => $store.dispatch('payment/setVisiblePayRequisite', false)
          "
          width="440px"
        >
          <template v-if="requesitesData.length > 0">
            <div>
              <div :style="{ display: 'flex', flexDirection: 'column' }">
                <span>Реквизиты: </span>
                <a-select
                  label-in-value
                  style="width: 120px"
                  option-label-prop="label"
                  v-model="FormReq.payReqID"
                  :style="{ width: '100%', marginTop: '10px' }"
                >
                  <a-select-option
                    v-for="(reqItem, index) in requesitesData"
                    :key="index"
                    :value="reqItem.id"
                    :label="reqItem.name"
                  >
                    {{ reqItem.name }}
                  </a-select-option>
                </a-select>
              </div>
              <div
                :style="{
                  display: 'flex',
                  flexDirection: 'column',
                  marginTop: '20px',
                }"
              >
                <span>Сумма: </span>
                <a-input
                  v-model="FormReq.sumPay"
                  placeholder="Сумма пополнения"
                  :style="{ width: '100%', marginTop: '10px' }"
                />
              </div>
            </div>
          </template>
          <template v-else>
            <div>
              <a-button
                type="link"
                @click="
                  (e) => {
                    $router.push({ name: 'setting-requisite' });
                    $store.dispatch('payment/setVisiblePayRequisite', false);
                  }
                "
                >Добавьте реквизиты</a-button
              >
              <span
                :style="{
                  display: 'block',
                  padding: ' 10px',
                  textAlign: 'center',
                }"
                >Реквизиты обязательно должны быть заполнены</span
              >
            </div>
          </template>
        </a-modal>
        <!-- Модальное окно об успешном пополнении -->
        <a-modal
          v-model="visibleSuccessReq"
          centered
          ok-text="Готово"
          cancel-text="Закрыть"
          @ok="visibleReqOk"
          width="440px"
        >
          <template slot="footer">
            <div :style="{ padding: '15px' }">
              <a :href="paymentData.url" target="_blank">
                <a-icon type="download" /> Сохранить документ
              </a>
            </div>
          </template>
          <template slot="title">
            <div>
              <b>Вам выставлен счёт на сумму {{ paymentData.paysum }} ₽</b>
            </div>
            <div>№ {{ paymentData.bill }}</div>
          </template>
          <div>
            <p>Он отправлен на {{ user.email }}</p>
            <p>Оплатить его можно с расчётного счёта ИП или Юр. Лица</p>
          </div>
          <div :style="{ textAlign: 'right', marginTop: '35px' }">
            <a-button
              key="back"
              type="link"
              @click="
                (e) => {
                  (visibleSuccessReq = false),
                    $store.dispatch('payment/setVisiblePayReq', true);
                }
              "
            >
              Пополнить ещё раз
            </a-button>
            <a-button
              key="submit"
              type="primary"
              @click="(e) => (visibleSuccessReq = false)"
            >
              Готово
            </a-button>
          </div>
        </a-modal>
        <!-- Модальное окно об ошибке -->
        <a-modal
          title="Сообщить о проблеме"
          :visible="supportVisible"
          centerd
          okText="Отправить"
          cancelText="Отмена"
          @ok="supportVisibleOk"
          @cancel="supportVisibleCancel"
        >
          <div>
            <a-textarea
              placeholder="Опишите проблему и причину возникновения"
              v-model="supportForm.description"
              auto-size
              :style="{ height: '70px' }"
            ></a-textarea>
          </div>
          <div :style="{ margin: '20px 0' }">
            <a-upload
              name="file"
              :multiple="true"
              :headers="{ Authorization: $auth.getToken('local') }"
              action="http://lk.leadz.monster/api/support/upload"
            >
              <a-button> <a-icon type="upload" /> Загрузить картинки </a-button>
            </a-upload>
          </div>
          <div
            :style="{
              marginTop: '20px',
              display: 'flex',
              justifyContent: 'center',
            }"
          >
            <div>
              <span :style="{ padding: '5px 0' }"
                >Выберите категории ошибки:</span
              >
              <a-checkbox-group
                v-model="supportForm.categories"
                button-style="solid"
                :style="{
                  display: 'flex',
                  justifyContent: 'space-between',
                  flexWrap: 'wrap',
                  marginTop: '15px',
                }"
              >
                <a-checkbox value="UI/UX">Элементы интерфейса</a-checkbox>
                <a-checkbox value="UX.Page">Страница с ошибкой</a-checkbox>
                <a-checkbox value="UI.Balance">Баланс</a-checkbox>
                <a-checkbox value="UX.Payment">Оплаты</a-checkbox>
                <a-checkbox value="UI.Bonus">Бонусы</a-checkbox>
                <a-checkbox value="UI.Setting">Настройки</a-checkbox>
                <a-checkbox value="UI.Deals">Клиенты</a-checkbox>
                <a-checkbox value="UI.Bids">Заявки</a-checkbox>
                <a-checkbox value="UI.Regions">Регионы</a-checkbox>
                <a-checkbox value="UI.Direction">Направления</a-checkbox>
                <a-checkbox value="UI.Rate">Ставка</a-checkbox>
                <a-checkbox value="UX.Logout">Выход</a-checkbox>
                <a-checkbox value="UX.Insurance">Страховка</a-checkbox>
                <a-checkbox value="API.Error">Странные ошибки</a-checkbox>
              </a-checkbox-group>
            </div>
          </div>
        </a-modal>
      </a-layout-footer>
    </a-layout>
    <a-modal
      v-model="isRegionModal"
      title="Выберите регион"
      cancelText="Отмена"
      okText="Выбрать"
      @ok="handleRegionOk"
    >
      <div class="layout-region-modal">
        <a-radio-group v-model="regionId">
          <a-radio
            v-for="(region, index) in regions"
            :value="region.id"
            :key="index"
            :style="radioStyle"
            >{{ region.name_with_type }}</a-radio
          >
        </a-radio-group>
      </div>
    </a-modal>
  </a-layout>
</template>
<script>
export default {
  middleware: ["auth", "demoMiddleware"],
  data() {
    return {
      menuSelectedKeys: [1],
      regionName: "",
      regions: [],
      regionId: 1,
      isRegionModal: false,
      radioStyle: {
        display: "block",
        height: "30px",
        lineHeight: "30px",
      },
      /**
       * START OF Ver1.0
       */
      helpTooltip: 100,
      isLoading: true,
      offerVisible: true,
      supportVisible: false,
      requisiteCount: 323,
      userType: 101,
      visibleSuccessReq: false,
      payBonus: false,
      supportForm: {
        description: "",
        referer: this.$route.fullPath,
        categories: [],
      },
      paymentData: {
        created_at: "",
        updated_at: "",
        bill: "",
        paysum: 0,
      },
      FormReq: {
        payReqID: 0,
        sumPay: "",
      },
      FormCredit: {
        sumPay: "",
      },
      FormCD: {
        sumPay: "",
      },
      requesitesData: [],
      /**
       * END OF Ver1.0
       */
      collapsed: false,
      navMenu: [
        {
          name: "Управление",
          icon: "appstore",
          url: "/",
          rolesHidden: [],
          type: "Route",
        },
        {
          name: "Заявки",
          icon: "bulb",
          url: "/deals",
          rolesHidden: [],
          type: "Route",
        },
        {
          name: "Мои компании",
          icon: "home",
          url: "/company",
          rolesHidden: [],
          type: "Route",
        },
        {
          name: "Рейтинг компаний",
          icon: "trophy",
          url: "/companies",
          rolesHidden: [],
          type: "Route",
        },
        {
          name: "Модерация",
          icon: "trophy",
          url: "/companies/moderato",
          rolesHidden: ["ROLE_USER", "ROLE_MANAGER", "ROLE_WEBMASTER"],
          type: "Route",
        },
        {
          name: "Магазин",
          icon: "shopping",
          url: "/store",
          rolesHidden: [],
          type: "Route",
        },
        {
          name: "Новости",
          icon: "tag",
          url: "/news",
          rolesHidden: [],
          type: "Route",
        },
        {
          name: "Не распределено",
          icon: "container",
          url: "/distributed",
          rolesHidden: [
            "ROLE_USER",
            "ROLE_MANAGER",
            "ROLE_WEBMASTER",
            "ROLE_ACCOUNAT",
          ],
          type: "Route",
        },
        {
          name: "Брак",
          icon: "container",
          url: "/distributed/break",
          rolesHidden: [
            "ROLE_USER",
            "ROLE_MANAGER",
            "ROLE_WEBMASTER",
            "ROLE_ACCOUNAT",
          ],
          type: "Route",
        },
        {
          name: "Спорные заявки",
          icon: "warning",
          url: "/disput",
          rolesHidden: [
            "ROLE_USER",
            "ROLE_MANAGER",
            "ROLE_WEBMASTER",
            "ROLE_ACCOUNAT",
          ],
          type: "Route",
        },
        {
          name: "Счета",
          icon: "pay-circle",
          url: "/payment/requisites",
          rolesHidden: [
            "ROLE_USER",
            "ROLE_MANAGER",
            "ROLE_WEBMASTER",
            "ROLE_ACCOUNAT",
          ],
          type: "Route",
        },
        {
          name: "Оплаты",
          icon: "history",
          url: "/payment/history",
          rolesHidden: [
            "ROLE_USER",
            "ROLE_MANAGER",
            "ROLE_WEBMASTER",
            "ROLE_ACCOUNAT",
          ],
          type: "Route",
        },
        {
          name: "История баланса",
          icon: "history",
          url: "/user/history",
          rolesHidden: [],
          type: "Route",
        },
        {
          name: "Страховка",
          icon: "file",
          url: "/insurance",
          rolesHidden: [
            "ROLE_USER",
            "ROLE_MANAGER",
            "ROLE_WEBMASTER",
            "ROLE_ACCOUNAT",
          ],
          type: "Route",
        },
        {
          name: "Новые пользователи",
          icon: "team",
          url: "/manager/leads",
          rolesHidden: ["ROLE_USER", "ROLE_WEBMASTER", "ROLE_ACCOUNAT"],
          type: "Route",
        },
        {
          name: "Мои клиенты",
          icon: "user",
          url: "/manager/users",
          rolesHidden: ["ROLE_USER", "ROLE_WEBMASTER", "ROLE_ACCOUNAT"],
          type: "Route",
        },
        {
          name: "Настройки",
          icon: "setting",
          url: "/setting",
          rolesHidden: [],
          type: "Route",
        },
        {
          name: "API приложения",
          icon: "api",
          url: "/webmaster",
          rolesHidden: ["ROLE_USER", "ROLE_MANAGER", "ROLE_ACCOUNAT"],
          type: "Route",
        },
        {
          name: "Статистика",
          icon: "pie-chart",
          url: "/statistics",
          rolesHidden: ["ROLE_USER", "ROLE_MANAGER", "ROLE_ACCOUNAT"],
          type: "Route",
        },
        {
          name: "Сгенерировано",
          icon: "pie-chart",
          url: "/statistics/generated",
          rolesHidden: ["ROLE_USER", "ROLE_MANAGER", "ROLE_ACCOUNAT"],
          type: "Route",
        },
        {
          name: "Пользователи",
          icon: "team",
          url: "/users",
          rolesHidden: [
            "ROLE_USER",
            "ROLE_WEBMASTER",
            "ROLE_MANAGER",
            "ROLE_ACCOUNAT",
          ],
          type: "Route",
        },
        {
          name: "База знаний",
          icon: "book",
          url: "https://leadz.monster/knowledge-base",
          rolesHidden: [],
          target: "_blank",
          type: "Link",
        },
      ],
    };
  },
  created() {
    this.payBonus = this.user.with_bonus;
    this.menuSelectedKeys = [];
    this.navMenu.forEach((each, index, array) => {
      if (each.url == this.$route.path) {
        this.menuSelectedKeys.push(index);
      }
    });
  },
  mounted() {
    this.isLoading = true;
    this.userType = this.user.category_id;
    this.$axios
      .get("/directory")
      .then(({ data }) => {
        this.regions = data.regions;
      })
      .catch((_err) => {
        console.error(_err);
      });
    this.regionId = this.user.region.id;
    this.regionName = this.user.region.name_with_type;
  },
  methods: {
    openRegionModal(e) {
      this.isRegionModal = true;
    },
    handleRegionOk(e) {
      this.$axios
        .post("/user/update/region", {
          region_id: this.regionId,
        })
        .then(({ data }) => {
          this.$message.success(data.message);
          this.regionName = data.region.name_with_type;
          this.$auth.fetchUser();
          this.isRegionModal = false;
        })
        .catch((err) => {
          console.error(err);
        });
    },
    /**
     * START Ver 1.0
     */
    handleBTooltip() {},
    supportVisibleShow() {
      this.supportVisible = true;
    },
    visibleReqOk(e) {
      this.visibleSuccessReq = false;
    },
    onPayBonusChange(e) {
      this.$axios
        .post("/user/pay_bonus", {
          is: this.payBonus,
        })
        .then(({ data }) => {
          this.payBonus = data.with_bonus;
        })
        .catch((err) => {
          console.error(err);
        });
    },
    handleUserTypeUpdate(e) {
      this.$axios
        .post("/user/update_type", {
          type: e.item.value,
        })
        .then(({ data }) => {
          if (data.success) {
            this.userType = e.item.value;
            this.$message.success(data.message);
          } else {
            this.$message.error(data.error);
          }
        })
        .catch((err) => {
          if (typeof response.message != "undefined") {
            this.$message.error(response.message);
          } else {
            this.$message.error(response);
          }
        });
    },
    loadRequesites() {
      this.$axios
        .get("/requisites")
        .then(({ data }) => {
          this.requesitesData = [];
          data.forEach((reqItem) => {
            if (
              reqItem.ogrn.length > 0 &&
              reqItem.inn.length > 0 &&
              reqItem.kpp.length > 0 &&
              reqItem.rsch.length > 0 &&
              reqItem.ksch.length > 0 &&
              reqItem.bik.length > 0
            ) {
              this.requesitesData.push(reqItem);
            }
          });
          console.log(this.requesitesData);
        })
        .catch(({ response }) => {
          if (typeof response.message != "undefined") {
            this.$message.error(response.message);
          } else {
            this.$message.error(response);
          }
        });
    },
    successReqOk(e) {},
    payReqOk(e) {
      this.$axios
        .post("/payment/requisite/create", {
          requisite_id: this.FormReq.payReqID.key,
          paysum: this.FormReq.sumPay,
        })
        .then(({ data }) => {
          if (data.success) {
            this.$message.success(data.message);
            this.$store.dispatch("payment/setVisiblePayRequisite", false);
            this.paymentData = data.data;
            this.visibleSuccessReq = true;
            this.$metrika.reachGoal("67456357", "client_bill", {
              order_price: this.FormReq.sumPay,
              currency: "RUB",
            });
          } else {
            this.$message.error(data.message);
          }
        })
        .catch((err) => {
          if (typeof err.response.data.message != "undefined") {
            this.$message.error(err.response.data.message);
          } else {
            this.$message.error(err.response.data);
          }
        });
    },
    payCDOk(e) {
      this.$axios
        .post("/payment/card/create", {
          paysum: this.FormCD.sumPay,
        })
        .then(({ data }) => {
          if (data.success) {
            window.location.href = data.payment_url;
            this.$store.dispatch("payment/setVisiblePayRequiqiste", false);
            this.$metrika.hit("67456357", "success", "client_payment", {
              order_price: this.FormCD.sumPay * 100,
              currency: "RUB",
            });
          } else {
            this.$message.error(data.message);
          }
        })
        .catch((err) => {
          console.error(err);
        });
    },
    payTinkoffOk(e) {
      this.$axios
        .post("/payment/credit/create", {
          sum: this.FormCredit.sumPay,
        })
        .then(({ data }) => {
          if (data.success) {
            window.location.href = data.payment_url;
            this.$store.dispatch("tinkoff/setInstallmentVisible", false);
            this.$metrika.hit("67456357", "success", "client_payment", {
              order_price: this.FormCredit.sumPay * 100,
              currency: "RUB",
            });
          } else {
            this.$message.error(data.message);
          }
        })
        .catch((err) => {
          console.error(err);
        });
    },

    supportVisibleOk() {
      this.$axios
        .post("/support", this.supportForm)
        .then(({ data }) => {
          this.$message.success(data.message);
        })
        .catch((err) => {
          console.error(err);
        });
      this.supportVisible = false;
    },
    supportVisibleCancel() {
      this.supportVisible = false;
    },
    handleBalanceClick(e) {},
    handlePaymentDropdown(e) {
      switch (e.key) {
        case 1:
          this.$store.dispatch("payment/setVisiblePayCard", true);
          break;
        case 2:
          this.loadRequesites();
          this.$store.dispatch("payment/setVisiblePayRequisite", true);
          break;
        case 4:
          this.$store.dispatch("tinkoff/setInstallmentVisible", true);
          break;
      }
    },
    /**
     * END Ver 1.0
     */

    signOut(_e) {
      this.$auth.logout().then(() => {
        this.$auth.redirect("/sign");
      });
    },

    onCollapse(collapsed, type) {
      console.log(collapsed, type);
    },
    onBreakpoint(broken) {
      console.log(broken);
    },
    isRoleHidden(rolesHidden) {
      if (rolesHidden.indexOf(this.user.role) == -1) {
        return true;
      } else {
        return false;
      }
    },
  },
};
</script>
<style>
#components-layout-responsive .logo {
  text-align: center;
  height: 32px;
  margin: 16px;
}

#components-layout-responsive .logo img {
  max-width: 100%;
  height: auto;
}

.btn-hover-error {
  opacity: 0.3;
}

.btn-hover-error:hover {
  opacity: 1;
}

.phone-title {
  font-size: 16px;
  color: #ffffff;
  text-align: center;
}
#components-layout-side .logo {
  margin: 30px;
}
#components-layout-side .logo img {
  max-width: 100%;
  height: auto;
}
.loader-container {
  position: fixed;
  left: 0;
  top: 0;
  width: 100%;
  height: 100%;
  background-color: #fff;
  z-index: 1000;
}
.loader-container.close {
  animation: close8 2s 1 forwards;
}
.loader:empty {
  position: absolute;
  top: calc(50% - 4em);
  left: calc(50% - 4em);
  width: 2em;
  height: 2em;
  border: 0.05em solid rgba(0, 0, 0, 0.2);
  border-left: 0.05em solid #1890ff;
  border-radius: 50%;
  animation: load8 1.1s infinite linear;
}

@keyframes load8 {
  0% {
    transform: rotate(0deg);
  }
  100% {
    transform: rotate(360deg);
  }
}

@keyframes close8 {
  0% {
    opacity: 1;
    visibility: visible;
  }
  99% {
    opacity: 0;
  }
  100% {
    visibility: hidden;
  }
}

.offer-tinkoff-about {
  position: absolute;
  left: 70%;
  bottom: 20px;
  font-size: 12px;
  transform: translateX(-50%);
  color: rgba(34, 34, 34, 0.4);
}

.offer-tinkoff-container {
  display: flex;
  position: relative;
  margin: 20px 24px;
  width: 1920px;
  max-width: 100%;
  padding-top: 15.625%;
  overflow: hidden;
  /* background-image: url("/offers/tinkoff_bg.png"); */
  background-position: center;
  background-size: cover;
  min-height: 200px;
}

.offer-tinkoff-container.close {
  display: none;
}

.offer-tinkoff {
  width: 61%;
  position: absolute;
  left: 35px;
  top: 50%;
  transform: translateY(-50%);
}

.offer-tinkoff__title {
  font-weight: 300;
  font-size: 30px;
  line-height: 30px;
  color: #222222;
}

.offer-tinkoff__description {
  font-weight: 300;
  font-size: 18px;
  line-height: 23px;
  color: #222222;
  margin-top: 15px;
  max-width: 850px;
}

.ant-checkbox-wrapper {
  margin-left: 0;
  padding: 5px 0;
}
.helpTooltip_end {
  position: relative;
  z-index: 99;
}

.offer-tinkoff__action {
  font-size: 15px;
  margin-top: 15px;
}

@media screen and (min-width: 1201px) and (max-width: 1450px) {
  .offer-tinkoff {
    width: 60%;
    left: 20px;
  }

  .offer-tinkoff__title {
    font-size: 30px;
    line-height: 30px;
  }
  .offer-tinkoff__description {
    margin-top: 10px;
    font-size: 16px;
  }
}

@media screen and (max-width: 1200px) {
  .offer-tinkoff__title {
    font-size: 20px;
    line-height: 20px;
  }
  .offer-tinkoff__description {
    font-size: 16px;
    margin-top: 10px;
  }
}
</style>
