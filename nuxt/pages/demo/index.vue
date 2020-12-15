<template>
  <div class="demo-content">
    <div class="demo-content__step">
      <b-step :items="stepItems" :step="$demoStep" />
    </div>
    <div
      class="demo-content__header"
      :class="{ textAlignLeft: $demoStateId == 1 }"
    >
      <template v-if="$demoStateId == 0"
        >Первое задание - Чего вы хотите?</template
      >
      <template v-if="$demoStateId == 1">Выберите подходящий тариф</template>
    </div>
    <div class="demo-content__body">
      <div class="b-form b-form--demo" v-if="$demoStateId == 0">
        <div class="b-form__body">
          <div class="b-form__group">
            <b-select
              mode="multiple"
              label="Вы хотите получать только по региону"
              multiLabel="Выбранно несколько регионов"
              newLabel="Хочу получать по всей России"
              :isNewLabel="form.allRegion"
              :options="regionOptions"
              :selected="regionSelected"
              @onSelected="handleRegions"
            />
            <button
              class="demo-button-link"
              @click="handleAllRegion"
              v-if="form.allRegion == false"
            >
              Хочу получать по всей России
            </button>
          </div>
          <div class="b-form__group">
            <div class="b-checkbox__group">
              <div class="b-checkbox__title">Чем вы занимаетесь?</div>
              <div class="b-checkbox__items">
                <b-checkbox
                  v-for="(dir, index) in form.direction"
                  :key="index"
                  :label="dir.name"
                  :id="index"
                  @change="handleCheck"
                />
              </div>
            </div>
          </div>
          <div class="b-form__group">
            <b-radio-group
              label="Что для вас важнее?"
              :value="form.target"
              :items="radioItems"
              @change="updateTarget"
            />
          </div>
          <div class="b-form__group" :class="{ isVisibility: !isMore }">
            <b-button title="Перейти к завершению" @click="handleCreate" />
          </div>
        </div>
      </div>
      <b-form-tariff v-if="$demoStateId == 1" @onpay="handlePayment" />
      <b-form-end :isShow="isPayment" @onEnd="handleEnd" />
    </div>
    <div class="b-form-end" v-if="isEnd">
      <span>Перейдите на обычный интерфейс и ожидайте своих клиентов <nuxt-link to="/">перейти</nuxt-link></span>
    </div>
  </div>
</template>
<script>
import bRadioGroup from "../../components/b-radio-group.vue";
export default {
  components: { bRadioGroup },
  name: "demo",
  layout: "demo-layout",
  head() {
    return {
      title: "Создание заявки",
    };
  },
  data() {
    return {
      isEnd: false,
      isMore: false,
      isPayment: false,
      formPaysum: 0,
      radioItems: [
        {
          id: 0,
          title: "Больше клиентов",
        },
        {
          id: 1,
          title: "Минимум затрат",
        },
      ],
      regionOptions: [],
      regionSelected: [],
      stepItems: [
        {
          title: "Начать",
          description: "This is a description",
        },
        {
          title: "Финиш",
          description: "This is a description",
        },
      ],
      form: {
        regions: [],
        allRegion: false,
        direction: [],
        target: 0,
      },
    };
  },
  created() {
    this.$store.dispatch("demoData/setTitle", "Значимость процесса");
    this.$store.dispatch(
      "demoData/setDescription",
      "Значимость этих проблем настолько очевидна, что консультация с широким активом влечет за собой процесс внедрения и модернизации системы обучения кадров, соответствует насущным потребностям. Товарищи! дальнейшее развитие различных форм деятельности позволяет выполнять важные задания по разработке позиций, занимаемых участниками в отношении поставленных задач. Идейные соображения высшего порядка, а также постоянный количественный рост и сфера нашей активности представляет собой интересный эксперимент проверки модели развития. Таким образом начало повседневной работы по формированию позиции позволяет выполнять важные задания по разработке новых предложений. С другой стороны новая модель организационной деятельности способствует подготовки и реализации систем массового участия."
    );

    this.$axios.get("/directory").then(({ data }) => {
      this.regionOptions = data.regions.map((c) => {
        return {
          id: c.id,
          title: c.name,
        };
      });
      this.form.direction = data.directions.map((d) => {
        d.checked = false;
        return d;
      });
    });
  },
  mounted() {
    if (localStorage.getItem("demo.isEnd")) {
      this.isEnd = localStorage.getItem("demo.isEnd") == 1;
    }
    if (
      localStorage.getItem("demo.step") &&
      localStorage.getItem("demo.state.id") &&
      localStorage.getItem("demo.payment")
    ) {
      this.$store.dispatch(
        "demoData/setStateId",
        parseInt(localStorage.getItem("demo.state.id"))
      );
      this.$store.dispatch(
        "demoData/setStep",
        parseInt(localStorage.getItem("demo.step"))
      );
      this.isPayment =
        parseInt(localStorage.getItem("demo.payment")) == 1 ? true : false;
    } else {
      localStorage.setItem("demo.step", this.$demoStep);
      localStorage.setItem("demo.state.id", this.$demoStateId);
      localStorage.setItem("demo.payment", this.isPayment ? 1 : 0);
    }
  },
  methods: {
    handlePayment(v) {
      this.isPayment = v.is;
      localStorage.setItem("demo.form.tariff_id", v.id);
      localStorage.setItem("demo.payment", this.isPayment ? 1 : 0);
    },
    checkMore() {
      if (
        (this.form.allRegion == true || this.form.regions.length > 0) &&
        this.form.direction.filter((c) => {
          return c.checked == true;
        }).length > 0
      ) {
        this.isMore = true;
      } else {
        this.isMore = false;
      }
    },
    handleAllRegion() {
      this.form.allRegion = true;

      this.checkMore();
    },
    handleRegions(value) {
      this.form.allRegion = false;
      this.form.regions = value;

      this.checkMore();
    },
    handleCheck(value) {
      this.form.direction[value.id].checked = value.checked;

      this.checkMore();
    },
    updateTarget(value) {
      this.form.target = value;
    },
    handleEnd(e) {
      localStorage.setItem("demo.isEnd", 1);
      this.isEnd = true;
    },
    handleCreate() {
      this.form.regions = this.form.regions.map((r) => {
        return this.regionOptions[r].id;
      });
      this.$store.dispatch("demoData/nextStateId");
      this.$store.dispatch("demoData/nextStep");
      localStorage.setItem("demo.form.bid", JSON.stringify(this.form));
      // this.$axios
      //   .post("/demo/create", this.form)
      //   .then(({ data }) => {
      //     if (data.success) {
      //     } else {
      //       this.$message.error(data.error);
      //     }
      //   })
      //   .catch((err) => {
      //     console.error(err);
      //   });
    },
  },
};
</script>