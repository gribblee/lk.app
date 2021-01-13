<template>
  <a-layout-content style="margin: 20px 16px 0 16px">
    <a-page-header
      title="Настройки"
      sub-title="пояснение"
      @back="() => $router.go(-1)"
    ></a-page-header>
    <a-layout style="padding: 24px 0; background: #fff">
      <a-layout-sider width="200" style="background: #fff">
        <a-menu
          :default-selected-keys="defaultSelected"
          mode="inline"
          style="height: 100%"
        >
          <a-menu-item key="1" @click="handleSelected(1, $event)"
            >Общие настройки</a-menu-item
          >
          <a-menu-item key="2" @click="handleSelected(2, $event)"
            >Реквизиты</a-menu-item
          >
          <a-menu-item
            key="3"
            @click="handleSelected(3, $event)"
            v-if="user.role === 'ROLE_ADMIN'"
            >Справочники</a-menu-item
          >
          <a-menu-item
            key="4"
            @click="handleSelected(4, $event)"
            v-if="user.role === 'ROLE_ADMIN'"
            >Опции</a-menu-item
          >
          <a-menu-item
            key="5"
            @click="handleSelected(5, $event)"
            v-if="user.role === 'ROLE_ADMIN'"
            >Категории</a-menu-item
          >
          <a-menu-item
            key="6"
            @click="handleSelected(6, $event)"
            v-if="user.role === 'ROLE_ADMIN'"
            >Статусы спора</a-menu-item
          >
        </a-menu>
      </a-layout-sider>
      <a-layout-content :style="{ padding: '0 24px', minHeight: '280px' }">
        <div v-show="settingForm === 1">
          <a-row>
            <a-col :span="24">
              <a-form
                :label-col="labelCol"
                :form="form"
                :wrapper-col="wrapperCol"
                @submit.prevent="handleSettingSubmit"
              >
                <a-form-item label="Email">
                  <a-input
                    v-decorator="[
                      'email',
                      {
                        rules: [
                          {
                            required: true,
                            message: 'Пожалуйста введите Email',
                          },
                        ],
                      },
                    ]"
                  />
                </a-form-item>
                <a-form-item label="Email для уведомлений">
                  <a-input
                    v-decorator="[
                      'email_notification',
                      {
                        rules: [
                          {
                            required: false,
                            message: '',
                          },
                        ],
                      },
                    ]"
                  />
                </a-form-item>
                <a-form-item label="Телефон">
                  <a-input
                    v-decorator="[
                      'phone',
                      {
                        rules: [
                          {
                            required: false,
                          },
                        ],
                      },
                    ]"
                  />
                </a-form-item>
                <a-form-item label="Имя">
                  <a-input
                    v-decorator="[
                      'name',
                      {
                        rules: [
                          { required: true, message: 'Пожалуйста введите Имя' },
                        ],
                      },
                    ]"
                  />
                </a-form-item>
                <a-form-item label="Новый пароль" has-feedback>
                  <a-input
                    v-decorator="[
                      'password',
                      {
                        rules: [
                          {
                            required: false,
                            message: '',
                          },
                          {
                            validator: validateToNextPassword,
                          },
                        ],
                      },
                    ]"
                    type="password"
                  />
                  <span>Не менее 8 символов</span>
                </a-form-item>
                <a-form-item
                  label="Подтвердите пароль"
                  has-feedback
                  v-show="form.getFieldValue('password')"
                >
                  <a-input
                    v-decorator="[
                      'confirm',
                      {
                        rules: [
                          {
                            required: false,
                            message: '',
                          },
                          {
                            validator: compareToFirstPassword,
                          },
                        ],
                      },
                    ]"
                    type="password"
                    @blur="handleConfirmBlur"
                  />
                </a-form-item>
                <a-form-item :wrapper-col="{ span: 12, offset: 5 }">
                  <a-button type="primary" html-type="submit"
                    >Сохранить</a-button
                  >
                </a-form-item>
              </a-form>
            </a-col>
          </a-row>
        </div>
        <div v-show="settingForm === 2">
          <div>
            <a-space>
              <a-button type="primary" @click="handleCreate">
                Добавить Реквизиты
                <a-icon type="plus" />
              </a-button>
            </a-space>
          </div>
          <div :style="{ marginTop: '24px' }">
            <a-row :gutter="[16, 16]">
              <a-col
                :span="6"
                v-for="(requisite, index) in requisites"
                :key="requisite.id"
              >
                <a-alert
                  message="Обязательно заполните все поля. Реквизиты с не заполнеными полями будут не доступны"
                  v-if="
                    requisite.ogrn.length <= 0 ||
                    requisite.inn.length <= 0 ||
                    requisite.ogrn.kpp <= 0 ||
                    requisite.rsch.length <= 0 ||
                    requisite.ksch.length <= 0 ||
                    requisite.bik.length <= 0
                  "
                  type="error"
                />
                <a-card hoverable style="width: 100%">
                  <template slot="actions" class="ant-card-actions">
                    <a-popconfirm
                      title="Действительно хотите удалить реквизиты"
                      ok-text="Да"
                      cancel-text="Нет"
                      @confirm="handleDelete(index, $event)"
                    >
                      <a key="delete">
                        <a-icon type="delete" :style="{ color: 'red' }" />
                      </a>
                    </a-popconfirm>
                    <a key="edit" @click="handleEdit(requisite.id, $event)">
                      <a-icon type="edit" />
                    </a>
                  </template>
                  <a-card-meta
                    :title="requisite.name"
                    :description="['ИНН: ', requisite.inn]"
                  />
                  <template v-if="requisite.editable">
                    <div
                      :style="{
                        marginTop: '12px',
                        borderTop: '1px solid #e8e8e8',
                      }"
                    >
                      <a-form
                        :label-col="labelCol"
                        :wrapper-col="wrapperCol"
                        @submit.prevent="handleSubmit(requisite.id, $event)"
                      >
                        <a-input
                          type="hidden"
                          name="id"
                          :value="requisite.id"
                        />
                        <div :style="{ paddingTop: '12px' }">
                          <a-input
                            placeholder="Полное название"
                            name="name"
                            v-model="requisite.name"
                          />
                        </div>
                        <div :style="{ paddingTop: '12px' }">
                          <a-input
                            placeholder="ОГРН"
                            name="ogrn"
                            v-model="requisite.ogrn"
                            @change="
                              (e) =>
                                (requisite.ogrn = requisite.ogrn.replace(
                                  /[^-0-9]/gim,
                                  ''
                                ))
                            "
                          />
                        </div>
                        <div :style="{ paddingTop: '12px' }">
                          <a-input
                            placeholder="ИНН"
                            name="inn"
                            v-model="requisite.inn"
                            @change="
                              (e) =>
                                (requisite.inn = requisite.inn.replace(
                                  /[^-0-9]/gim,
                                  ''
                                ))
                            "
                          />
                        </div>
                        <div :style="{ paddingTop: '12px' }">
                          <a-input
                            placeholder="КПП"
                            name="kpp"
                            v-model="requisite.kpp"
                            @change="
                              (e) =>
                                (requisite.kpp = requisite.kpp.replace(
                                  /[^-0-9]/gim,
                                  ''
                                ))
                            "
                          />
                        </div>
                        <div :style="{ paddingTop: '12px' }">
                          <a-input
                            placeholder="БИК"
                            name="bik"
                            v-model="requisite.bik"
                            @change="
                              (e) =>
                                (requisite.bik = requisite.bik.replace(
                                  /[^-0-9]/gim,
                                  ''
                                ))
                            "
                          />
                        </div>
                        <div :style="{ paddingTop: '12px' }">
                          <a-input
                            placeholder="БАНК"
                            name="bank"
                            v-model="requisite.bank"
                          />
                        </div>
                        <div :style="{ paddingTop: '12px' }">
                          <a-input
                            placeholder="К/СЧ"
                            name="ksch"
                            v-model="requisite.ksch"
                            @change="
                              (e) =>
                                (requisite.ksch = requisite.ksch.replace(
                                  /[^-0-9]/gim,
                                  ''
                                ))
                            "
                          />
                        </div>
                        <div :style="{ paddingTop: '12px' }">
                          <a-input
                            placeholder="Р/СЧ"
                            name="rsch"
                            v-model="requisite.rsch"
                            @change="
                              (e) =>
                                (requisite.rsch = requisite.rsch.replace(
                                  /[^-0-9]/gim,
                                  ''
                                ))
                            "
                          />
                        </div>
                        <div :style="{ paddingTop: '12px' }">
                          <a-input
                            placeholder="Юр. адрес"
                            name="jour_address"
                            v-model="requisite.jour_address"
                          />
                        </div>
                        <div :style="{ paddingTop: '12px' }">
                          <a-input
                            placeholder="Почтовый адрес"
                            name="poste_address"
                            v-model="requisite.poste_address"
                          />
                        </div>
                        <div :style="{ paddingTop: '12px' }">
                          <a-input
                            placeholder="ФИО директора / ИП"
                            name="director"
                            v-model="requisite.director"
                          />
                        </div>
                        <div
                          :style="{
                            paddingTop: '20px',
                            display: 'flex',
                            justifyContent: 'center',
                          }"
                        >
                          <a-button type="primary" html-type="submit"
                            >Сохранить</a-button
                          >
                        </div>
                      </a-form>
                    </div>
                  </template>
                </a-card>
              </a-col>
            </a-row>
          </div>
        </div>
        <div v-if="user.role === 'ROLE_ADMIN'" v-show="settingForm === 3">
          <div :style="{ marginTop: '24px' }">
            <a-row :gutter="[24, 24]">
              <a-col :span="8">
                <a-form-item
                  v-for="(k, index) in formDirectionModel"
                  :key="index"
                >
                  <a-space>
                    <a-input-group :style="{ marginRight: '8px' }" compact>
                      <div :style="{ padding: '10px 0', width: '100%' }">
                        ID направления: {{ k.id }}
                      </div>
                      <a-input
                        placeholder="Название направления"
                        style="width: 50%"
                        v-model="k.name"
                      />
                      <a-input
                        placeholder="Ставка"
                        style="width: 50%"
                        v-model="k.extra"
                      />
                      <a-input
                        :style="{ marginTop: '10px', width: '20%' }"
                        placeholder="Себе стоимость"
                        v-model="k.cost_price"
                      />
                      <a-input
                        :style="{ marginTop: '10px', width: '80%' }"
                        placeholder="Описание"
                        v-model="k.description"
                      />
                      <a-input
                        :style="{ marginTop: '10px', width: '33.33333%' }"
                        placeholder="Конверсия договоров"
                        v-model="k.conversion_contract"
                      />
                      <a-input
                        :style="{ marginTop: '10px', width: '33.33333%' }"
                        placeholder="Конверсия встреч"
                        v-model="k.conversion_meetings"
                      />
                      <a-input
                        :style="{ marginTop: '10px', width: '33.33333%' }"
                        placeholder="Средний чек"
                        v-model="k.average_check"
                      />
                      <a-input
                        :style="{ marginTop: '10px', width: '80%' }"
                        placeholder="Ссылка на видео"
                        v-model="k.iframe_url"
                      />
                      <a-select
                        mode="multiple"
                        placeholder="Категория"
                        option-label-prop="label"
                        v-model="k.categories"
                        :style="{ marginTop: '20px', width: '100%' }"
                      >
                        <a-select-option
                          v-for="category in categories"
                          :key="category.id"
                          :value="category.id"
                          :label="category.name"
                          >{{ category.name }}</a-select-option
                        >
                      </a-select>
                    </a-input-group>
                    <a-icon
                      class="dynamic-delete-button"
                      type="minus-circle-o"
                      @click="() => removeDirection(index)"
                    />
                  </a-space>
                </a-form-item>
                <a-form-item>
                  <a-button
                    type="dashed"
                    style="width: calc(100% - 24px)"
                    @click="addDirection"
                  >
                    <a-icon type="plus" /> Добавить направление
                  </a-button>
                </a-form-item>
                <a-form-item>
                  <a-button
                    @click="handleDirectionSave"
                    type="primary"
                    style="width: calc(100% - 24px)"
                  >
                    Сохранить
                  </a-button>
                </a-form-item>
              </a-col>
              <a-col :span="8">
                <a-form-item
                  v-for="(rg, index) in formRegionModel"
                  :key="index"
                >
                  <a-space>
                    <a-input-group :style="{ marginRight: '8px' }" compact>
                      <a-input
                        placeholder="Имя региона"
                        style="width: 50%"
                        v-model="rg.name"
                      />
                      <a-input
                        placeholder="Код региона"
                        style="width: 50%"
                        v-model="rg.kladr_id"
                      />
                      <a-input
                        placeholder="Тип"
                        style="width: 50%"
                        v-model="rg.type"
                      />
                      <a-input
                        placeholder="Имя региона"
                        style="width: 50%"
                        v-model="rg.name_with_type"
                      />
                      <a-input
                        placeholder="Федеральный округ"
                        style="width: 50%"
                        v-model="rg.federal_district"
                      />
                      <a-input
                        placeholder="Фиас ИД"
                        style="width: 50%"
                        v-model="rg.fias_id"
                      />
                    </a-input-group>
                    <a-icon
                      class="dynamic-delete-button"
                      type="minus-circle-o"
                      @click="() => removeRegion(index)"
                    />
                  </a-space>
                </a-form-item>
                <a-form-item>
                  <a-button
                    type="dashed"
                    style="width: calc(100% - 24px)"
                    @click="addRegions"
                  >
                    <a-icon type="plus" /> Добавить регион
                  </a-button>
                </a-form-item>
                <a-form-item>
                  <a-button
                    @click="handleRegionSave"
                    type="primary"
                    style="width: calc(100% - 24px)"
                  >
                    Сохранить
                  </a-button>
                </a-form-item>
              </a-col>
              <a-col :span="8">
                <a-form-item
                  v-for="(st, index) in formStatusModel"
                  :key="index"
                >
                  <div
                    :style="{
                      display: 'flex',
                      alignItems: 'center',
                      width: '100%',
                    }"
                  >
                    <a-input-group
                      :style="{ marginRight: '8px', width: '100%' }"
                      compact
                    >
                      <a-input
                        placeholder="Название статуса"
                        style="width: 50%"
                        v-model="st.name"
                      />
                      <a-select
                        style="width: 50%"
                        v-model="st.type"
                        option-label-prop="label"
                      >
                        <a-select-option :value="1000" label="Общий"
                          >Общий</a-select-option
                        >
                        <a-select-option :value="1001" label="Важные"
                          >Важные</a-select-option
                        >
                        <a-select-option :value="1002" label="Принятые"
                          >Принятыйе</a-select-option
                        >
                        <a-select-option :value="1003" label="Спорные"
                          >Спорные</a-select-option
                        >
                        <a-select-option :value="1004" label="Не известно"
                          >Не известно(не распределено)</a-select-option
                        >
                      </a-select>
                    </a-input-group>
                    <a-icon
                      class="dynamic-delete-button"
                      type="minus-circle-o"
                      @click="() => removeStatus(index)"
                    />
                  </div>
                  <a-slider v-model="st.weight" :min="0" :max="99" :step="1" />
                </a-form-item>
                <a-form-item>
                  <a-button
                    type="dashed"
                    style="width: calc(100% - 24px)"
                    @click="addStatus"
                  >
                    <a-icon type="plus" /> Добавить статус
                  </a-button>
                </a-form-item>
                <a-form-item>
                  <a-button
                    @click="handleStatusSave"
                    type="primary"
                    style="width: calc(100% - 24px)"
                  >
                    Сохранить
                  </a-button>
                </a-form-item>
              </a-col>
            </a-row>
          </div>
        </div>
        <div v-if="user.role === 'ROLE_ADMIN'" v-show="settingForm === 4">
          <a-row :gitter="[24, 24]">
            <a-col :span="6">
              <div :style="{ display: 'flex', flexDirection: 'column' }">
                <div>
                  <a-form-item label="Конверсия договоров %">
                    <a-input v-model="formOptions.conversion_contract" />
                  </a-form-item>
                </div>
                <div>
                  <a-form-item label="Конверсия встреч %">
                    <a-input v-model="formOptions.conversion_meetings" />
                  </a-form-item>
                </div>
                <div>
                  <a-form-item label="Средний чек руб.">
                    <a-input v-model="formOptions.average_check" />
                  </a-form-item>
                </div>
                <div>
                  <a-form-item
                    label="Сколько списывать с бонусов(% от стоимости заявки)"
                  >
                    <a-input v-model="formOptions.bill_bonus" />
                  </a-form-item>
                </div>
                <div>
                  <a-form-item
                    label="Баланс при котором показывается сообщение"
                  >
                    <a-input v-model="formOptions.threshold_balance" />
                  </a-form-item>
                </div>
                <div>
                  <a-form-item label="Текст сообщения при балансе">
                    <a-input v-model="formOptions.message_balance" />
                  </a-form-item>
                </div>
                <div :style="{ marginTop: '25px' }">
                  <a-button type="primary" @click="handleOptionsSave">
                    Сохранить
                  </a-button>
                </div>
              </div>
            </a-col>
          </a-row>
        </div>
        <div v-if="user.role === 'ROLE_ADMIN'" v-show="settingForm === 5">
          <div :style="{ display: 'flex' }">
            <div class="action-button">
              <a-button
                type="primary"
                size="large"
                @click="handleCategoryCreate"
              >
                <a-icon type="plus" />
                Добавить категорию
              </a-button>
            </div>
          </div>
          <div :style="{ marginTop: '20px' }">
            <a-row :gutter="[16, 16]">
              <a-col
                :span="6"
                v-for="(category, index) in categories"
                :key="index"
              >
                <a-card hoverable style="width: 100%">
                  <template slot="actions" class="ant-card-actions">
                    <a-popconfirm
                      title="Действительно хотите удалить категорию"
                      ok-text="Да"
                      cancel-text="Нет"
                      @confirm="handleCategoryDelete(index, $event)"
                    >
                      <a key="delete">
                        <a-icon type="delete" :style="{ color: 'red' }" />
                      </a>
                    </a-popconfirm>
                    <a key="save" @click="handleCategorySave(index, $event)">
                      <a-icon type="save" />
                    </a>
                  </template>
                  <a-card-meta :title="category.name" />
                  <template>
                    <a-form :label-col="labelCol" :wrapper-col="wrapperCol">
                      <a-input type="hidden" name="id" :value="category.id" />
                      <div :style="{ paddingTop: '12px' }">
                        <a-input
                          placeholder="Название"
                          name="name"
                          v-model="category.name"
                        />
                      </div>
                      <div :style="{ paddingTop: '12px' }">
                        <a-input
                          placeholder="Описание"
                          name="name"
                          v-model="category.description"
                        />
                      </div>
                    </a-form>
                  </template>
                </a-card>
              </a-col>
            </a-row>
          </div>
        </div>
        <div v-if="user.role === 'ROLE_ADMIN'" v-show="settingForm === 6">
          <div :style="{ display: 'flex' }">
            <div class="action-button">
              <a-button
                type="primary"
                size="large"
                @click="handleDisputStatusCreate"
              >
                <a-icon type="plus" />
                Добавить статус
              </a-button>
            </div>
          </div>
          <div :style="{ marginTop: '20px' }">
            <a-row :gutter="[16, 16]">
              <a-col
                :span="6"
                v-for="(disput_status, index) in disput_statuses"
                :key="index"
              >
                <a-card hoverable style="width: 100%">
                  <template slot="actions" class="ant-card-actions">
                    <a-popconfirm
                      title="Действительно хотите удалить категорию"
                      ok-text="Да"
                      cancel-text="Нет"
                      @confirm="handleDisputStatusDelete(index, $event)"
                    >
                      <a key="delete">
                        <a-icon type="delete" :style="{ color: 'red' }" />
                      </a>
                    </a-popconfirm>
                    <a
                      key="save"
                      @click="handleDisputStatusSave(index, $event)"
                    >
                      <a-icon type="save" />
                    </a>
                  </template>
                  <a-card-meta :title="disput_status.name" />
                  <template>
                    <a-form :label-col="labelCol" :wrapper-col="wrapperCol">
                      <a-input
                        type="hidden"
                        name="id"
                        :value="disput_status.id"
                      />
                      <div :style="{ paddingTop: '12px' }">
                        <a-input
                          placeholder="Название"
                          name="name"
                          v-model="disput_status.name"
                        />
                      </div>
                    </a-form>
                  </template>
                </a-card>
              </a-col>
            </a-row>
          </div>
        </div>
      </a-layout-content>
    </a-layout>
  </a-layout-content>
</template>
<style scoped>
.ant-input.error {
  border-color: #ff0000;
}
</style>
<script>
let idDirection = 0;
export default {
  head() {
    return {
      title: 'Настройки'
    }
  },
  data() {
    return {
      userSetting: {},
      defaultSelected: ["1"],
      settingForm: 1,
      requisites: [],
      categories: [],
      confirmDirty: false,
      categories: [],
      disput_statuses: [],
      formOptions: {
        conversion_contract: 0,
        conversion_meetings: 0,
        average_check: 0,
        threshold_balance: 0,
        bill_bonus: 0,
      },
      labelCol: {
        xs: { span: 24 },
        sm: { span: 5 },
      },
      wrapperCol: {
        xs: { span: 24 },
        sm: { span: 12 },
      },
      formItemLayout: {
        labelCol: {
          xs: { span: 24 },
          sm: { span: 4 },
        },
        wrapperCol: {
          xs: { span: 24 },
          sm: { span: 20 },
        },
      },
      formItemLayoutWithOutLabel: {
        wrapperCol: {
          xs: { span: 24, offset: 0 },
          sm: { span: 20, offset: 0 },
        },
      },
      formDirectionModel: [],
      formRegionModel: [],
      formStatusModel: [],
    };
  },
  beforeCreate() {
    this.$axios
      .get("/requisites")
      .then(({ data }) => {
        this.requisites = data;
      })
      .catch((_err) => {
        console.error(_err);
      });
    this.$axios
      .get("/directory")
      .then(({ data }) => {
        this.formDirectionModel = data.directions.map((cur) => {
          return {
            average_check: cur.average_check,
            categories: JSON.parse(cur.categories),
            conversion_contract: cur.conversion_contract,
            conversion_meetings: cur.conversion_meetings,
            cost_price: cur.cost_price,
            created_at: cur.created_at,
            description: cur.description,
            extra: cur.extra,
            id: cur.id,
            name: cur.name,
            updated_at: cur.updated_at,
            iframe_url: cur.iframe_url
          };
        });
        this.formRegionModel = data.regions;
        this.formStatusModel = data.status;
        if (Object.keys(data.options).length > 0) {
          this.formOptions = data.options;
        }
      })
      .catch((_err) => {
        console.error(_err);
      });
    this.$axios
      .get("/categories")
      .then(({ data }) => {
        this.categories = data;
      })
      .catch((err) => {
        console.error(err);
      });
    this.form = this.$form.createForm(this, { name: "setting" });
  },
  created() {
    this.loadCategories();
    this.loadDisputStatus();
    if (this.$settingForm == 2) {
      this.settingForm = 2;
      this.defaultSelected = ["2"];
      //this.$store.dispatch("statuses/setSettingForm", 0);
    }
    this.$axios
      .get('/user/me')
      .then(({ data }) => {
          this.form.setFieldsValue({
            email: data.data.email,
            name: data.data.name,
            phone: data.data.phone,
            email_notification: data.data.email_notification,
        });
      })
      .catch((err) => {
        console.error(err)
      })
  },
  mounted() {
  },
  methods: {
    handleCategoryCreate(e) {
      this.$axios
        .post("/category/create")
        .then(({ data }) => {
          if (data.success == true) {
            this.loadCategories();
          }
        })
        .catch((err) => {
          console.error(err);
        });
    },
    handleCategorySave(idx, e) {
      this.$axios
        .post("/category/update", {
          id: this.categories[idx].id,
          name: this.categories[idx].name,
          description: this.categories[idx].description,
        })
        .then(({ data }) => {
          if (data.success == true) {
            this.loadCategories();
          }
        })
        .catch((err) => {
          console.error(err);
        });
    },
    handleCategoryDelete(idx, e) {
      this.$axios
        .post("/category/delete", {
          id: this.categories[idx].id,
        })
        .then(({ data }) => {
          if (data.success == true) {
            this.loadCategories();
          }
        })
        .catch((err) => {
          console.error(err);
        });
    },

    handleDisputStatusCreate(e) {
      this.$axios
        .post("/disput_status/create")
        .then(({ data }) => {
          if (data.success == true) {
            this.loadDisputStatus();
          }
        })
        .catch((err) => {
          console.error(err);
        });
    },
    handleDisputStatusSave(idx, e) {
        if(this.user.role == 'ROLE_ADMIN') {
            this.$axios
             .post("/disput_status/update", {
                id: this.disput_statuses[idx].id,
                name: this.disput_statuses[idx].name,
              })
              .then(({ data }) => {
                if (data.success == true) {
                  this.loadDisputStatus();
                }
              })
              .catch((err) => {
                console.error(err);
              });
        }
    },
    handleDisputStatusDelete(idx, e) {
      this.$axios
        .post("/disput_status/delete", {
          id: this.disput_statuses[idx].id,
        })
        .then(({ data }) => {
          this.loadDisputStatus();
        })
        .catch((err) => {
          console.error(err);
        });
    },

    loadDisputStatus() {
      if (this.user.role == 'ROLE_ADMIN') {
        this.$axios
            .get("/disput_status")
            .then(({ data }) => {
            this.disput_statuses = data;
            })
            .catch((err) => {
            console.error(err);
            });
        }
    },

    loadCategories() {
      this.$axios
        .get("/categories")
        .then(({ data }) => {
          this.categories = data;
        })
        .catch((err) => {
          console.error(err);
        });
    },
    handleOptionsSave(e) {
      if (this.user.role == "ROLE_ADMIN") {
        this.$axios
          .post("/directory/options_save", {
            formOptions: this.formOptions,
          })
          .then(({ data }) => {
            if (data.success == true) {
              this.$message.success(data.message);
            } else {
              this.$message.error(data.message);
            }
          })
          .catch((err) => {
            if (typeof err.response.message === "undefined") {
              this.$message.error(err.response);
            } else {
              this.$message.error(err.response.meessage);
            }
          });
      }
    },
    handleSettingSubmit(_e) {
      this.form.validateFields((err, values) => {
        if (!err) {
          var sendPost = {};
          for (var key in values) {
            if (
              typeof values[key] !== "undefined" &&
              values[key] != null
            ) {
              if (values[key].toString().length > 0) {
                sendPost[key] = values[key];
              }
            }
          }
          this.$axios
            .post("/user/update", sendPost)
            .then(({ data }) => {
              if (data.success === true) {
                this.$message.success(data.msg);
              } else {
                this.$message.error(data.error);
              }
            })
            .catch(({ response }) => {
              this.$message.error(response.data);
            });
        }
      });
    },
    validateToNextPassword(rule, value, callback) {
      const form = this.form;
      if (value && this.confirmDirty) {
        form.validateFields(["confirm"], { force: true });
      }
      callback();
    },
    compareToFirstPassword(rule, value, callback) {
      const form = this.form;
      if (
        form.getFieldValue("password") &&
        value !== form.getFieldValue("password")
      ) {
        callback("Пароли не совпадают");
      } else {
        callback();
      }
    },
    handleConfirmBlur(e) {
      const value = e.target.value;
      this.confirmDirty = this.confirmDirty || !!value;
    },
    handleSelected(key, _e) {
      this.settingForm = key;
    },
    handleCreate(_e) {
      this.$axios
        .get("/requisite/create")
        .then(({ data }) => {
          this.requisites.unshift(data);
        })
        .catch((_err) => {
          console.error(_err);
        });
    },
    handleDelete(_id, _e) {
      this.$axios
        .get(`/requisite/${this.requisites[_id].id}/delete`)
        .then(({ data }) => {
          if (data.success === true) {
            this.requisites.splice(_id, 1);
          }
        })
        .catch((_err) => {
          console.error(_err);
        });
    },
    handleEdit(_id, _e) {
      const newData = [...this.requisites];
      const target = newData.filter((item) => _id === item.id)[0];
      if (target) {
        if (typeof target.editable == "udnefined") {
          target.editable = true;
        } else {
          target.editable = !target.editable;
        }
        this.requisites = newData;
      }
    },
    handleEditField(idx, _req, _e) {
      console.log(idx, _req, _e);
    },
    handleSubmit(id, _e) {
      const elements = Array.prototype.slice.call(_e.target.elements);
      const formData = new Object();
      let ErrorElement = [];
      elements.map((curr) => {
        if (curr.tagName === "INPUT") {
          curr.classList.remove("error");
          if (curr.name != "kpp" && /([^\s])/.test(curr.value) == false) {
            curr.classList.add("error");
            ErrorElement.push(
              Object({
                bank: "Банк",
                bik: "Бик",
                director: "ФИО директора / ИП",
                inn: "ИНН",
                jour_address: "Юр. адрес",
                ksch: "К/СЧ",
                rsch: "Р/СЧ",
                name: "Название",
                ogrn: "ОГРН",
                poste_address: "Почтовый адрес",
              })[curr.name]
            );
          }
          formData[curr.name] = curr.value;
        }
      });
      if (ErrorElement.length == 0) {
        this.$axios
          .post(`requisite/${formData.id}/update`, {
            bank: formData.bank,
            bik: formData.bik,
            director: formData.director,
            inn: formData.inn,
            jour_address: formData.jour_address,
            kpp: formData.kpp,
            ksch: formData.ksch,
            rsch: formData.rsch,
            name: formData.name,
            ogrn: formData.ogrn,
            poste_address: formData.poste_address,
          })
          .then(({ data }) => {
            if (data.success === true) {
              this.$message.success(data.msg);
              this.handleEdit(id, _e);
            }
          })
          .catch((_err) => {
            this.$message.error(error.error);
          });
      } else {
        this.$message.error(`Поля ${ErrorElement.join()} не заполнены`);
        console.log(ErrorElement);
      }
    },
    removeDirection(k) {
      if (this.formDirectionModel.length === 0) {
        return;
      }
      this.formDirectionModel = this.formDirectionModel.filter(
        (el, key) => key !== k
      );
    },

    removeRegion(k) {
      if (this.formRegionModel.length === 0) {
        return;
      }
      this.formRegionModel = this.formRegionModel.filter(
        (el, key) => key !== k
      );
    },

    removeStatus(k) {
      if (this.formStatusModel.length === 0) {
        return;
      }
      this.formStatusModel = this.formStatusModel.filter(
        (el, key) => key !== k
      );
    },

    addDirection(e) {
      this.formDirectionModel.push({
        name: "",
        extra: "",
      });
    },
    addRegions(e) {
      this.formRegionModel.push({
        name: "",
        type: "",
        kladr_id: "",
        name_with_type: "",
        federal_district: "",
        fias_id: "",
      });
    },
    addStatus(e) {
      this.formStatusModel.push({
        name: "",
        type: 1000,
        weight: 0,
      });
    },
    handleDirectionSave(e) {
      this.$axios
        .post("/directory/direction/save", {
          directions: this.formDirectionModel,
        })
        .then(({ data }) => {
          if (data.success === true) {
            this.$message.success(data.msg);
          } else {
            this.$message.error(data.error);
          }
        })
        .catch(({ response }) => {
          if (response.data.message) {
            this.$message.error(response.data.message);
          } else {
            this.$message.error(response.data);
          }
        });
    },
    handleRegionSave(e) {
      this.$axios
        .post("/directory/regions/save", {
          regions: this.formRegionModel,
        })
        .then(({ data }) => {
          if (data.success === true) {
            this.$message.success(data.msg);
          } else {
            this.$message.error(data.error);
          }
        })
        .catch(({ response }) => {
          if (response.data.message) {
            this.$message.error(response.data.message);
          } else {
            this.$message.error(response.data);
          }
        });
    },
    handleStatusSave(e) {
      this.$axios
        .post("/directory/status/save", {
          status: this.formStatusModel,
        })
        .then(({ data }) => {
          if (data.success === true) {
            this.$message.success(data.msg);
          } else {
            this.$message.error(data.error);
          }
        })
        .catch(({ response }) => {
          if (response.data.message) {
            this.$message.error(response.data.message);
          } else {
            this.$message.error(response.data);
          }
        });
    },
  },
};
</script>
