/**
 * setInstallmentVisible
 */

export const state = () => ({
    InstallmentVisible: false,
});

export const getters = {
    InstallmentVisible(state) {
        return state.InstallmentVisible;
    }
};

export const mutations = {
    SET_INSTALLMENT_VISIBLE(state, visible) {
        state.InstallmentVisible = visible;
    },
    GET_INSTALLMENT_VISIBLE(state) {
        return state.InstallmentVisible;
    }
};

export const actions = {
    setInstallmentVisible({ commit }, data) {
        return commit("SET_INSTALLMENT_VISIBLE", data);
    },
    getInstallmentVisible({ commit }) {
        return commit("GET_INSTALLMENT_VISIBLE");
    }
};