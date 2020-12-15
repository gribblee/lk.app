<template>
  <a-layout-content style="margin: 20px 16px 0 16px">
    <a-page-header
      title="Редактирование направления"
      sub-title=""
      @back="() => $router.go(-1)"
    >
      <template slot="tags">
        <a-tag :color="PPTagColor">{{ PPTagText }}</a-tag>
      </template>
      <template slot="extra">
        <div :style="{ display: 'flex' }">
          <div :style="{ marginLeft: '20px' }">
            <a-popconfirm
              title="Вы действительно хотите удалить"
              ok-text="Да"
              cancel-text="Нет"
              @confirm="handleDelete"
            >
              <a-button type="default" key="2">
                <a-icon type="delete" />
                Удалить
              </a-button>
            </a-popconfirm>
          </div>
        </div>
      </template>
    </a-page-header>
    <div :style="{ padding: '24px', background: '#fff', minHeight: '360px' }">
      <a-row type="flex" justify="space-around">
        <a-col :span="6">
          <a-form
            id="components-form-demo-validate-other"
            @submit.prevent="handleSubmit"
          >
            <a-form-item label="Направление" has-feedback>
              <a-tooltip
                :visible="helpTooltip == 2"
                title="Выберите направление, по которому хотите получать клиентов. Справа, в синей рамке Вы увидите подробное описание выбранного направления - что за люди обращаются, что они хотят, и т.д."
              >
                <a-select
                  size="large"
                  placeholder="Выберите направление"
                  v-model="formBid.direction_id"
                  @change="handleDirectionChange"
                  option-label-prop="label"
                  :style="{ zIndex: '99' }"
                  :disabled="BID_DATA.is_update == 1"
                >
                  <a-select-option
                    v-for="direction in directory.directions"
                    :key="direction.id"
                    :value="direction.id"
                    :label="direction.name"
                  >
                    {{ direction.name }}
                  </a-select-option>
                </a-select>
              </a-tooltip>
              <span
                v-if="helpTooltip == 2"
                :style="{
                  position: 'relative',
                  zIndex: '99',
                  display: 'block',
                  textAlign: 'center',
                }"
              >
                <a-button
                  type="primary"
                  @click.prevent="nextTooltip(3)"
                  :style="{ color: '#FFFFFF' }"
                  >Пропустить</a-button
                >
              </span>
              <div
                v-if="helpTooltip == 2"
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
            </a-form-item>
            <a-form-item label="Регионы" has-feedback>
              <a-tooltip
                title="Выберите регионы, по которым Вы хотите получать клиентов. Вы можете выбрать один либо несколько регионов. Обратите внимание - если Вы оставите поле пустым, Вы будете получать клиентов со всей регионов"
                :visible="helpTooltip == 3"
              >
                <a-select
                  size="large"
                  placeholder="Выберите регионы"
                  mode="multiple"
                  v-model="formBid.regions"
                  @change="onRegionsSelected"
                  option-label-prop="label"
                  :style="{ zIndex: '97' }"
                >
                  <a-select-option
                    v-for="region in directory.regions"
                    :key="region.id"
                    :value="JSON.stringify(region)"
                    :label="region.name_with_type"
                  >
                    {{ region.name_with_type }}
                  </a-select-option>
                </a-select>
              </a-tooltip>
              <span
                v-if="helpTooltip == 3"
                :style="{
                  position: 'relative',
                  zIndex: '97',
                  display: 'block',
                  textAlign: 'center',
                }"
              >
                <a-button
                  type="primary"
                  @click.prevent="nextTooltip(4)"
                  :style="{ color: '#FFFFFF' }"
                  >Пропустить</a-button
                >
              </span>
              <div
                v-if="formBid.regions.length === 0"
                :style="{
                  marginTop: '10px',
                  position: 'relative',
                  zIndex: '97',
                }"
              >
                <a-alert
                  message="Вы будете получать заявки по всем регионам"
                  type="info"
                />
              </div>
              <div
                v-if="helpTooltip == 3"
                :style="{
                  position: 'fixed',
                  left: '0',
                  top: '0',
                  width: '100%',
                  height: '100%',
                  zIndex: '96',
                  backgroundColor: 'rgba(34, 34, 34, 0.4)',
                }"
              ></div>
            </a-form-item>
            <a-form-item label="Количество заявок в день">
              <a-tooltip
                title="Укажите максимальное количество клиентов, которое Вы хотите получать. Минимум 5 заявок в день. Если Вы оставите поле пустым, будете получать заявки постоянно"
                :visible="helpTooltip == 4"
              >
                <a-input-number
                  :min="0"
                  :max="1000"
                  size="large"
                  v-model="formBid.dailyLimit"
                  @blur="handleDayLimit"
                  :style="{ width: '100%', zIndex: '95' }"
                />
              </a-tooltip>
              <span
                v-if="helpTooltip == 4"
                :style="{
                  position: 'relative',
                  zIndex: '95',
                  display: 'block',
                  textAlign: 'center',
                }"
              >
                <a-button
                  type="primary"
                  @click.prevent="nextTooltip(5)"
                  :style="{ color: '#FFFFFF' }"
                  >Пропустить</a-button
                >
              </span>
              <div
                v-if="formBid.dailyLimit <= 0 || formBid.dailyLimit == ''"
                :style="{
                  marginTop: '10px',
                  position: 'relative',
                  zIndex: '95',
                }"
              >
                <a-alert
                  message="Вы будете получать неограниченное количество заявок"
                  type="info"
                />
              </div>
              <div
                v-if="formBid.dailyLimit > 0 && formBid.dailyLimit < 5"
                :style="{
                  marginTop: '10px',
                  position: 'relative',
                  zIndex: '95',
                }"
              >
                <a-alert
                  message="Укажите от 5 лидов в день, либо оставьте поле пустым"
                  type="error"
                />
              </div>
              <div
                v-if="helpTooltip == 4"
                :style="{
                  position: 'fixed',
                  left: '0',
                  top: '0',
                  width: '100%',
                  height: '100%',
                  zIndex: '94',
                  backgroundColor: 'rgba(34, 34, 34, 0.4)',
                }"
              ></div>
            </a-form-item>
            <a-form-item
              label="Ставка за заявку"
              v-if="formBid.rateFix === false"
            >
              <a-tooltip
                title="Введите Вашу ставку за получение клиента. У нас аукционная система, и получает клиента тот, кто больше за него заплатит. Если Вы не знаете, какую ставку указать - нажмите “Рекомендуемая ставка”, тогда Вы с наибольшей вероятностью получите клиента”"
                :visible="helpTooltip == 5"
              >
                <a-input
                  v-model="formBid.bidPerRate"
                  size="large"
                  @blur="handlePerRate"
                  :style="{ position: 'relative', zIndex: '93' }"
                />
              </a-tooltip>
              <a-slider
                v-model="formBid.bidPerRate"
                :min="0"
                :max="parseInt(user.balance) + 10000"
                :step="1"
                :tip-formatter="null"
                :style="{
                  margin: '6px 0 6px 0px',
                  position: 'relative',
                  zIndex: '93',
                }"
                @afterChange="handlePerRate"
              />
              <div
                :style="{
                  padding: '10px 10px',
                  position: 'relative',
                  zIndex: '93',
                  backgroundColor: '#FFFFFF',
                  margin: '0 -10px',
                  borderRadius: '5px 5px 0 0',
                }"
                v-if="formBid.bidPerRate < minPerRate"
              >
                <a-alert
                  v-if="formBid.bidPerRate < minPerRate"
                  message="При такой ставке Вы не будете получать заявки"
                  type="error"
                />
              </div>
              <div
                class="space-justify-between"
                :class="{
                  visibHidden: formBid.bidPerRate >= recomendRate,
                }"
                :style="{
                  position: 'relative',
                  zIndex: '93',
                  backgroundColor: '#FFFFFF',
                  padding: '0 10px',
                  margin: '0 -10px',
                  borderRadius: '0 0 5px 5px',
                }"
              >
                <div>
                  <a href="javascript:void(0)" @click.prevent="handleRecommend">
                    Рекомендуемая ставка {{ recomendRate }} ₽
                  </a>
                </div>
                <div>
                  <a-tooltip title="Выше ставка - больше заявок">
                    <a-icon
                      type="exclamation-circle"
                      :style="{ cursor: 'pointer' }"
                    />
                  </a-tooltip>
                </div>
              </div>
              <span
                v-if="helpTooltip == 5"
                :style="{
                  position: 'relative',
                  zIndex: '99',
                  display: 'block',
                  textAlign: 'center',
                }"
              >
                <a-button
                  type="primary"
                  @click.prevent="nextTooltip(7)"
                  :style="{ color: '#FFFFFF' }"
                  >Пропустить</a-button
                >
              </span>
              <div
                v-if="helpTooltip == 5"
                :style="{
                  position: 'fixed',
                  left: '0',
                  top: '0',
                  width: '100%',
                  height: '100%',
                  zIndex: '92',
                  backgroundColor: 'rgba(34, 34, 34, 0.4)',
                }"
              ></div>
            </a-form-item>
            <a-form-item v-else>
              <a-button
                @click="handleRegions"
                size="large"
                type="primary"
                :style="{ width: '100%' }"
                >Настроить ставки</a-button
              >
            </a-form-item>
            <a-form-item>
              <div
                :style="{
                  display: 'flex',
                  justifyContent: 'space-between',
                  alignItems: 'center',
                  position: 'relative',
                }"
              >
                <span>
                  <a-switch
                    @change="onBidRegionsRate"
                    v-model="formBid.rateFix"
                  />
                  <span :style="{ marginLeft: '20px' }">
                    Настроить ставку по регионам
                  </span>
                </span>
                <a-tooltip
                  title="Вы можете настроить отдельно ставку по каждому региону регион. ДЛЯ ОПЫТНЫХ!"
                >
                  <a-icon
                    type="exclamation-circle"
                    :style="{ cursor: 'pointer' }"
                  />
                </a-tooltip>
              </div>
            </a-form-item>
            <a-form-item v-if="insurances.length > 0">
              <p :style="{ fontSize: '18px', fontWeight: 'bold' }">
                Количество заявок по страховке: {{ insuranceCount }}
              </p>
            </a-form-item>
            <a-form-item v-if="insuranceCount == 0 && insurances.length > 0">
              <div
                :style="{
                  display: 'flex',
                  justifyContent: 'space-between',
                  width: '100%',
                }"
              >
                <div>
                  <a-switch v-model="onInsurance" @change="handleInsurance" />
                  <span :style="{ marginLeft: '20px' }">
                    Подключить страховку
                  </span>
                </div>
                <div>
                  <a-tooltip
                    title="Покупка страховки позволит Вам заменить ВСЕ нецелевые заявки БЕСПЛАТНО"
                  >
                    <a-icon
                      type="exclamation-circle"
                      :style="{ cursor: 'pointer' }"
                    />
                  </a-tooltip>
                </div>
              </div>
            </a-form-item>
            <a-form-item v-if="onInsurance || insuranceCount > 0">
              <a-list item-layout="horizontal" :data-source="insurances">
                <a-list-item slot="renderItem" slot-scope="item">
                  <a-list-item-meta>
                    <span slot="description">Стоимость: {{ item.price }}₽</span>
                    <a slot="title">{{ item.name }} {{ item.count }} заявок</a>
                  </a-list-item-meta>
                  <a-popconfirm
                    title="Вы уверены, что покупаете страховку"
                    ok-text="Да"
                    cancel-text="Нет"
                    @confirm="buyInsurance(item.id, $event)"
                    slot="actions"
                  >
                    <a>Подключить</a></a-popconfirm
                  >
                </a-list-item>
              </a-list>
            </a-form-item>
            <a-form-item>
              <a-tooltip
                :visible="helpTooltip == 8"
                title='Отлично! Осталось только запустить поток клиентов и пополнить бюджет. Нажмите кнопку "Запустить"'
              >
                <div
                  :style="{
                    position: 'relative',
                    margin: '-10px',
                    padding: '10px',
                  }"
                  :class="{ help_active_tool: helpTooltip == 8 }"
                >
                  <a-button
                    size="large"
                    :type="PPType"
                    key="1"
                    @click="handlePPAction"
                    :style="{ width: '100%' }"
                  >
                    <a-icon :type="PPIcon" />
                    {{ PPText }}
                  </a-button>
                </div>
              </a-tooltip>
              <div
                v-if="helpTooltip == 8"
                :style="{ position: 'fixed' }"
                :class="{ help_active_bg: helpTooltip == 8 }"
              ></div>
            </a-form-item>
          </a-form>
        </a-col>
        <a-col :span="8">
          <div :style="{ position: 'relative', zIndex: '99' }">
            <a-alert
              message="Информация"
              :description="direction_description"
              type="info"
              show-icon
            />
          </div>
          <div :style="{ marginTop: '20px' }">
            <div class="row-container">
              <iframe
                class="second-row"
                :src="direction_iframe"
                frameborder="0"
                allow="accelerometer; autoplay; clipboard-write; encrypted-media; gyroscope; picture-in-picture"
                allowfullscreen
              ></iframe>
            </div>
          </div>
          <a-tooltip
            :visible="helpTooltip == 7"
            placement="left"
            title="Здесь указывается примерное количество встреч и сделок, которое Вы получите в месяц. Также указывается ожидаемый уровень дохода. ВНИМАНИЕ! Чтобы получить такие результаты, надо делать всё строго по нашим правилам."
          >
            <div
              :style="{
                position: 'relative',
                margin: '0 -65px',
                padding: '10px 65px',
                zIndex: '89',
                backgroundColor: '#FFFFFF',
                borderRadius: '5px',
              }"
            >
              <a-row :gutter="[24, 24]">
                <a-col :span="24">
                  <a-card>
                    <a-statistic
                      title="Вы получите"
                      :precision="2"
                      :value="incomeTotal"
                      suffix="₽"
                      :value-style="{ color: '#3f8600' }"
                    />
                  </a-card>
                </a-col>
                <a-col :span="24">
                  <a-card>
                    <a-statistic
                      title="Заявок"
                      :value="bidsCount"
                      suffix=""
                      :value-style="{ color: '#3f8600' }"
                      style="margin-right: 50px"
                    >
                    </a-statistic>
                  </a-card>
                </a-col>
                <a-col :span="12">
                  <a-card>
                    <a-statistic
                      title="Встреч"
                      :value="mettingsCount"
                      suffix=""
                      :value-style="{ color: '#3f8600' }"
                      style="margin-right: 50px"
                    >
                    </a-statistic>
                  </a-card>
                </a-col>
                <a-col :span="12">
                  <a-card>
                    <a-statistic
                      title="Сделок"
                      :value="dealsCount"
                      suffix=""
                      :value-style="{ color: '#3f8600' }"
                      style="margin-right: 50px"
                    >
                    </a-statistic>
                  </a-card>
                </a-col>
              </a-row>
              <div
                v-if="helpTooltip == 7"
                :style="{
                  display: 'flex',
                  justifyContent: 'center',
                  marginTop: '20px',
                }"
              >
                <a-button type="primary" @click="handleFunnelNext"
                  >Продолжить</a-button
                >
              </div>
            </div>
          </a-tooltip>
          <div
            v-if="helpTooltip == 7"
            :style="{
              position: 'fixed',
              left: '0',
              top: '0',
              width: '100%',
              height: '100%',
              zIndex: '88',
              backgroundColor: 'rgba(34, 34, 34, 0.4)',
            }"
          ></div>
        </a-col>
      </a-row>
      <a-modal
        v-model="visibleRateRegions"
        title="Настройка ставки по регионам"
        @ok="handleOk"
        cancelText="Отмена"
        okText="Сохранить"
      >
        <a-form>
          <a-form-item
            v-for="_region in regionsRate"
            :key="_region.id"
            :label="_region.name_with_type"
          >
            <a-input suffix="₽" v-model="_region.rate" />
          </a-form-item>
        </a-form>
      </a-modal>
      <a-modal v-model="errorLaunch" title="Ошибка!" centred closable>
        <a-space align="start" :size="20">
          <a-icon
            type="close-circle"
            :style="{
              color: 'red',
              fontSize: '24px',
            }"
          />
          <span
            :style="{
              color: 'red',
            }"
            >Для получения клиентов надо пополнить бюджет. Вы можете это сделать
            по карте либо по расчётному счёту юрлица или ИП. Выберите способ
            пополнения</span
          >
        </a-space>
        <template slot="footer">
          <a-space align="center" :size="20">
            <a-button
              type="primary"
              @click="
                (e) => {
                  errorLaunch = false;
                  $store.dispatch('directory/setPayReq', true);
                }
              "
              >По Р/С</a-button
            >
            <a-button
              type="primary"
              @click="
                (e) => {
                  errorLaunch = false;
                  $store.dispatch('directory/setPaycard', true);
                }
              "
              >По карте</a-button
            >
          </a-space>
        </template>
      </a-modal>
    </div>
  </a-layout-content>
</template>
<style scoped>
.row-container {
  position: relative;
  display: flex;
  width: 100%;
  height: 100%;
  padding-top: 56%;
  flex-direction: column;
  background-color: blue;
  overflow: hidden;
}

.second-row {
  position: absolute;
  left: 0;
  top: 0;
  width: 100%;
  height: 100%;
  flex-grow: 1;
  border: none;
  margin: 0;
  padding: 0;
}

.help_active_bg {
  left: 0;
  top: 0;
  width: 100%;
  height: 100%;
  z-index: 100;
  background-color: rgba(34, 34, 34, 0.4);
}

.help_active_tool {
  z-index: 101;
  background-color: #ffffff;
  border-radius: 5px;
}

.space-justify-between {
  display: flex;
  justify-content: space-between;
}
.visibHidden {
  visibility: hidden;
}
.funnel-container {
  margin-top: 20px;
  display: flex;
  flex-direction: column;
  align-items: center;
}

.funnel {
  display: flex;
  justify-content: center;
  align-items: flex-start;
  width: 100%;
  mask-position: center;
  mask-repeat: no-repeat;
  mask-size: contain;
  padding-top: 10px;
}

@media screen and (max-width: 1024px) {
  .funnel {
    height: 60px;
  }
}

@media screen and (max-width: 1400px) {
  .funnel-1 {
    width: 150%;
  }
}

@media screen and (min-width: 1025px) {
  .funnel {
    height: 99px;
  }
}

@media screen and (min-width: 1700px) {
  .funnel {
    height: 130px;
  }
}

.funnel-1 {
  background-color: #e8523f;
  mask-image: url("/icons/funnel_1.svg");
}
.funnel-2 {
  margin-top: 5px;
  background-color: #e8d73f;
  mask-image: url("/icons/funnel_2.svg");
}

.funnel-3 {
  margin-top: 5px;
  background-color: #3fe86e;
  mask-image: url("/icons/funnel_3.svg");
}
</style>
<script>
export default {
  data() {
    return {
      minPerRate: 1080,
      is_bidsInfo: true,
      helpTooltip: 2,
      BID_DATA: {},
      directionDescription: "",
      formBid: {
        direction_id: [],
        regions: [],
        bidPerRate: 1080,
        dayily_limit: 0,
        dailyLimit: 0
      },
      direction_iframe: "",
      insuranceCount: 0,
      regionsRate: [],
      visibleRateRegions: false,
      PPType: "primary",
      PPIcon: "play-circle",
      PPText: "Запустить",
      PPTagColor: "red",
      PPTagText: "Остановлено",
      bidsCount: 32,
      mettingsCount: 12,
      dealsCount: 5,
      incomeTotal: 0,
      recomendRate: 0,
      directory: [],
      maxRate: 0,
      direction_description: "",
      insurances: [],
      onInsurance: false,
      options: {
        conversion_meetings: 0,
        average_check: 0,
        conversion_contract: 0,
      },
      errorLaunch: false,
      errorLaunchMsg: "",
    };
  },
  validate({ params }) {
    // Должен быть числом
    return /^\d+$/.test(params.id);
  },
  created() {
    this.$axios
      .get("/directory")
      .then(({ data }) => {
        this.directory = data;
      })
      .catch((_err) => {
        console.error(_err);
      });
    this.$axios
      .post(`/bid/${this.$route.params.id}`)
      .then(({ data }) => {
        this.BID_DATA = data;
        this.formBid = {
          direction_id: data.direction.id,
          regions: data.regions.map((current) => {
            return JSON.stringify(current);
          }),
          dailyLimit: parseInt(data.daily_limit),
          daily_limit: parseInt(data.daily_limit),
          bidPerRate: parseInt(data.consumption),
          rateFix: Boolean(data.rateFix),
        };
        this.options = data.options;

        this.regionsRate = data.regions;
        if (data.MAX_RATE) {
          this.recomendRate = data.MAX_RATE;
        } else {
          this.recomendRate = 1080;
        }
        this.direction_description = data.direction_description;
        this.direction_iframe = data.direction.iframe_url;
        this.insuranceCount = data.insurance;
        this.minPerRate = data.options.min_per_rate;
        this.updateLaunch(data.is_launch);
        this.updateFunel();
      })
      .catch((_err) => {
        console.error(_err);
      });
    this.form = this.$form.createForm(this, { name: "create_bid" });
    this.loadInsurance();
  },
  mounted() {
    if (!localStorage.bidsInfo != "undefined") {
      this.is_bidsInfo = !(localStorage.bidsInfo == "close");
    } else {
      this.bidsInfo = true;
    }
    if (localStorage.helpTooltip) {
      this.helpTooltip = localStorage.helpTooltip;
    }
  },
  methods: {
    closeInfo() {
      localStorage.bidsInfo = "close";
    },
    nextTooltip(n) {
      if (parseInt(localStorage.helpTooltip) < n) {
        this.helpTooltip = n;
        localStorage.helpTooltip = n;
      }
    },
    handleFunnelNext(e) {
      if (localStorage.helpTooltip.toString() == "7") {
        localStorage.helpTooltip = "8";
        window.scrollTo(0, window.innerHeight);
        this.helpTooltip = localStorage.helpTooltip;
      }
    },
    updateFunel() {
      this.bidsCount = Math.ceil(
        this.formBid.daily_limit <= 0 ? 100 : this.formBid.daily_limit * 30
      );
      this.mettingsCount = Math.ceil(
        this.bidsCount * (this.options.conversion_meetings / 100)
      );
      this.dealsCount = Math.ceil(
        this.mettingsCount * (this.options.conversion_contract / 100)
      );
      this.incomeTotal = Math.ceil(
        this.dealsCount * this.options.average_check
      );
    },
    buyInsurance(id, e) {
      this.$axios
        .post(`/bid/${this.BID_DATA.id}/buy_insurance`, {
          insurance_id: id,
        })
        .then(({ data }) => {
          if (data.success == true) {
            this.$message.success(data.message);
            this.BID_DATA = data.bid;
            this.insuranceCount = data.bid.insurance;
          } else {
            this.$message.error(data.error);
          }
        })
        .catch((err) => {
          if (typeof err.response.message != "undefined") {
            this.$message.error(err.response.message);
          } else {
            this.$message.error(err.response);
          }
        });
    },
    loadInsurance() {
      this.$axios
        .get("/insurance")
        .then(({ data }) => {
          if (data.success == true) {
            this.insurances = data.data;
          } else {
            console.log('Non Insurance');
          }
        })
        .catch((err) => {
          if (typeof err.response.message != "undefined") {
            this.$message.error(err.response.message);
          } else {
            this.$message.error(err.response);
          }
        });
    },
    updateLaunch(isLaunch) {
      if (isLaunch === 1 || isLaunch === true) {
        this.PPType = "danger";
        this.PPIcon = "pause-circle";
        this.PPText = "Остановить";
        this.PPTagColor = "green";
        this.PPTagText = "Запущено";
        this.$metrika.reachGoal("67456357", "client_order", {
          order_price: this.formBid.bidPerRate,
          currency: "RUB",
        });
      } else {
        this.PPType = "primary";
        this.PPIcon = "play-circle";
        this.PPText = "Запустить";
        this.PPTagColor = "red";
        this.PPTagText = "Остановлено";
      }
    },
    handlePPAction(_e) {
      if (localStorage.helpTooltip.toString() == "8") {
        localStorage.helpTooltip = "9";
        this.helpTooltip = localStorage.helpTooltip;
      }
      this.$axios
        .post(`/bid/${this.BID_DATA.id}/launch`)
        .then(({ data }) => {
          if (data.code === 1004) {
            this.errorLaunch = true;
            this.errorLaunchMsg = data.msg;
          }
          if (data.code == 1003) {
            this.$message.error(data.msg);
          }
          this.updateLaunch(data.is_launch);
        })
        .catch((_err) => {
          console.error(_err);
        });
    },
    handleSubmit(_e) {
      this.form.validateFieldsAndScroll((err, values) => {
        if (!err) {
          console.log("Received values of form: ", values);
        }
      });
    },
    handleOkTooltip() {
      if (localStorage.helpTooltip.toString() == "7") {
        localStorage.helpTooltip = "8";
        this.helpTooltip = localStorage.helpTooltip;
      }
    },
    handleRecommend(_e) {
      this.formBid.bidPerRate = this.recomendRate;
      this.$axios
        .post(`/bid/${this.BID_DATA.id}/update`, {
          perRate: this.formBid.bidPerRate,
        })
        .then(({ data }) => {
          if (localStorage.helpTooltip == "5") {
            localStorage.helpTooltip = "7"; //Здесь был переход на страховку т.е. 6
            this.helpTooltip = localStorage.helpTooltip;
          }
          this.$message.success("Обновлено");
          this.direction_description = data.direction_description;
          this.direction_iframe = data.direction.iframe_url;
          this.updateFunel();
        })
        .catch((_err) => {
          console.error(_err);
        });

      //   if (localStorage.helpTooltip == "5") {
      //     localStorage.helpTooltip = "6";
      //     this.helpTooltip = localStorage.helpTooltip;
      //   }
      this.updateFunel();
    },
    handleDelete(_e) {
      this.$axios
        .post(`/bid/${this.$route.params.id}/delete`)
        .then(({ data }) => {
          this.$message.success("Удалено");
          this.$router.push("/");
        })
        .catch((_err) => {
          console.error(_err);
        });
    },
    handleInsurance() {
      if (localStorage.helpTooltip == "6") {
        localStorage.helpTooltip = "7";
        this.helpTooltip = localStorage.helpTooltip;
      }
    },
    async onBidRegionsRate(_e) {
      await this.$axios
        .post(`/bid/${this.BID_DATA.id}/rate_fix_regions`, {
          rateFix: this.formBid.rateFix,
        })
        .then(({ data }) => {
          if (data.success === false) {
            this.$message.error(data.errors);
          }
        })
        .catch((_err) => {
          console.error(_err);
        });
    },
    handleOk(_e) {
      this.visibleRateRegions = false;
      this.saveRegions();
    },
    handleRegions(_e) {
      this.visibleRateRegions = true;
    },
    onRegionsSelected(_v) {
      this.formBid.regions = _v;
      this.regionsRate = _v.map((_mpc_) => {
        const current = this.json(_mpc_);
        const region = this.BID_DATA.regions.find(
          (element, index, array) => element.id === current.id
        );
        if (region === undefined) {
          return {
            rate: this.formBid.bidPerRate,
            bids_id: this.BID_DATA.id,
            region_id: current.id,
            region: current,
          };
        } else {
          return region;
        }
      });
      this.saveRegions();
    },
    saveRegions() {
      this.$axios
        .post(`/bid/${this.BID_DATA.id}/update_region`, {
          regions: this.regionsRate,
        })
        .then(({ data }) => {
          this.$message.success("Обновлено");
        })
        .catch((_err) => {
          console.error(_err);
        });
    },
    handlePerRate() {
      this.$axios
        .post(`/bid/${this.BID_DATA.id}/update`, {
          perRate: this.formBid.bidPerRate,
        })
        .then(({ data }) => {
          if (localStorage.helpTooltip == "5") {
            localStorage.helpTooltip = "7"; //Переход на пункт 7 6 это страхвока
            this.helpTooltip = localStorage.helpTooltip;
          }
          this.$message.success("Обновлено");
          this.direction_description = data.direction_description;
          this.direction_iframe = data.direction.iframe_url;
          this.updateFunel();
        })
        .catch((_err) => {
          console.error(_err);
        });
    },
    handleDayLimit() {
      this.$axios
        .post(`/bid/${this.BID_DATA.id}/update`, {
          dailyLimit: this.formBid.dailyLimit,
        })
        .then(({ data }) => {
          if (localStorage.helpTooltip.toString() == "4") {
            localStorage.helpTooltip = "5";
            this.helpTooltip = localStorage.helpTooltip;
          }
          this.$message.success("Обновлено");
          this.direction_description = data.direction_description;
          this.direction_iframe = data.direction.iframe_url;
          this.updateFunel();
        })
        .catch((_err) => {
          console.error(_err);
        });
    },
    handleDirectionChange(_v) {
      this.$axios
        .post(`/bid/${this.BID_DATA.id}/update`, {
          direction_id: _v,
        })
        .then(({ data }) => {
          if (localStorage.helpTooltip.toString() == "2") {
            localStorage.helpTooltip = "3";
            this.helpTooltip = localStorage.helpTooltip;
          }
          this.BID_DATA.is_update = true;
          this.$message.success("Обновлено");
          this.direction_description = data.direction_description;
          this.direction_iframe = data.direction.iframe_url;
          this.options.conversion_meetings = data.conversion_meetings;
          this.average_check = data.average_check;
          this.conversion_contract = data.conversion_contract;
          this.minPerRate = data.min_per_rate;
          this.updateFunel();
        })
        .catch((_err) => {
          console.error(_err);
        });
    },
    json(JString) {
      return JSON.parse(JString);
    },
  },
};
</script>
