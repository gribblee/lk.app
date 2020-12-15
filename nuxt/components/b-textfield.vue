<template>
  <label class="textfield" :class="{ 'textfield--focus': isFocus }">
    <span class="textfield__label" v-text="label"></span>
    <input
      type="text"
      class="textfield__input"
      :value="value"
      @focus="onFocus"
      @blur="onBlur"
      @input="updateValue($event.target.value)"
      autocomplete="off"
      v-mask="mask"
    />
  </label>
</template>
<script>
export default {
  name: "b-textfield",
  props: {
    label: {
      type: String,
      default: "",
    },
    value: {
      type: String,
      default: "",
    },
    type: {
      type: String,
      default: "text",
    },
  },
  model: {
    prop: "value",
    event: "input",
  },
  data() {
    return {
      isFocus: false,
      mask: '',
    };
  },
  created() {
    if (this.type == 'phone') {
      this.mask = '+7 (###) ###-##-##';
    }
    if (this.type == 'code') {
      this.mask = '###-###';
    }
    if (this.value.length != 0) {
      this.isFocus = true;
    }
  },
  methods: {
    onFocus(e) {
      this.isFocus = true;
    },
    onBlur(e) {
      if (this.value.length == 0) {
        this.isFocus = false;
      }
    },
    updateValue(value) {
      this.$emit("input", value);
    },
  },
};
</script>