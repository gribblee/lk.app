export const state = () => ({
    updated: {},
    FormSettingItem: 0,
});

export const getters = {
    updated(state) {
        return state.updated;
    },
    FormSettingItem(state) {
        return state.FormSettingItem;
    }
};

export const mutations = {
    SET_FORM_SETTING_ITEM(state, id) {
        state.FormSettingItem = id;
    },
    GET_FORM_SETTING_ITEM(state) {
        return state.FormSettingItem;
    },
    SET_UPDATED(state, updated) {
        state.updated = updated;
    },
    GET_UPDATED(state) {
        return state.updated;
    }
};

export const actions = {
    setFormSettingItem({ commit }, id) {
        return commit("SET_FORM_SETTING_ITEM", id);
    },
    getFormSettingItem({ commit }) {
        return commit("GET_FORM_SETTING_ITEM");
    },
    setUpdated({ commit }, updated) {
        return commit("SET_UPDATED", updated);
    },
    getUpdated({ commit }) {
        return commit("GET_UPDATED");
    }
};
