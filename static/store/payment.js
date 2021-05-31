export const state = () => ({
    visiblePayRequisite: false,
    visiblePayCard: false,
});

export const getters = {
    visiblePayRequisite(state) {
        return state.visiblePayRequisite;
    },
    visiblePayCard(state) {
        return state.visiblePayCard;
    }
};

export const mutations = {
    SET_VISIBLE_PAY_REQUISITE(state, visible) {
        return state.visiblePayRequisite = visible;
    },
    GET_VISIBLE_PAY_REQUISITE(state) {
        return state.visiblePayRequisite;
    },
    SET_VISIBLE_PAY_CARD(state, visible) {
        return state.visiblePayCard = visible;
    },
    GET_VISIBLE_PAY_CARD(state) {
        return state.visiblePayCard;
    }
};

export const actions = {
    setVisiblePayRequisite({ commit }, visible) {
        return commit("SET_VISIBLE_PAY_REQUISITE", visible);
    },
    getVisiblePayRequisite({ commit }) {
        return commit("GET_VISIBLE_PAY_REQUISITE");
    },
    setVisiblePayCard({ commit }, visible) {
        return commit("SET_VISIBLE_PAY_CARD", visible);
    },
    getVisiblePayCard({ commit }) {
        return commit("GET_VISIBLE_PAY_CARD");
    }
};