<template>
  <div class="b-select" @click="handleClick">
    <div class="b-select__label">{{ label }}</div>
    <div class="b-select__output">
      <template v-if="isNewLabel">{{ newLabel }}</template>
      <template v-if="selectedItems.length == 0 && isNewLabel == false"
        >Ничего не выбрано</template
      >
      <template v-if="selectedItems.length > 1 && isNewLabel == false">
        {{ multiLabel }}
      </template>
      <template v-if="selectedItems.length == 1">{{
        options[selectedItems[0]].title
      }}</template>
      <svg width="9px" height="13px" stroke="#222222" class="b-select__icon">
        <use xlink:href="/icons-pack.svg#svg-arrow-right"></use>
      </svg>
    </div>
    <div
      class="b-select__popup"
      :class="{ 'b-select__popup--active': isActive }"
    >
      <ul class="b-select__items">
        <li
          class="b-select__item"
          v-for="(option, index) in options"
          :key="index"
          @click.stop="handleSelected($event, index)"
        >
          {{ option.title }}
          <span
            class="b-select__btn"
            :class="{
              'b-select__btn--active': selectedItems.indexOf(index) != -1,
            }"
          >
            <svg>
              <use xlink:href="/icons-pack.svg#svg-check"></use>
            </svg>
          </span>
        </li>
      </ul>
    </div>
  </div>
</template>
<script>
export default {
  name: "b-select",
  props: {
    options: {
      type: Array,
    },
    selected: {
      type: Array,
    },
    label: {
      type: String,
      default: "",
    },
    multiLabel: {
      type: String,
      default: "",
    },
    isNewLabel: {
      type: Boolean,
      default: false,
    },
    newLabel: {
      type: String,
      default: "",
    },
    mode: {
      type: String,
      default: "multiple",
    },
  },
  data() {
    return {
      isActive: false,
      selectedItems: [],
    };
  },
  created() {
    this.selectedItems = this.selected;
  },
  methods: {
    handleClick(e) {
      this.isActive = !this.isActive;
    },
    handleSelected(e, index) {
      var indexSelected = this.selectedItems.indexOf(index);
      if (indexSelected > -1) {
        this.selectedItems.splice(indexSelected, 1);
      } else {
        this.selectedItems.push(index);
      }
      this.$emit("onSelected", this.selectedItems);
    },
  },
};
</script>