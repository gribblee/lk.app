export const state = () => ({
    title: '',
    description: '',
    stateId: 0,
    step: 1,
});

export const getters = {
    title(state) {
        return state.title;
    },
    description(state) {
        return state.description;
    },
    stateId(state) {
        return state.stateId;
    },
    step(state) {
        return state.step;
    }
};

export const mutations = {
    SET_TITLE(state, title) {
        state.title = title;
    },
    SET_DESCRIPTION(state, description) {
        state.description = description;
    },

    NEXT_STATE_ID(state) {
        state.stateId = state.stateId + 1;
    },
    BACK_STATE_ID(state) {
        state.stateId = state.stateId - 1;
        if (state.stateId < 0) {
            state.stateId = 0;
        }
    },
    SET_STATE_ID(state, id) {
        state.stateId = id;
    },
    GET_STATE_ID(state) {
        return state.stateId;
    },

    NEXT_STEP(state) {
        state.step = state.step + 1;
    },

    BACK_STEP(state) {
        state.step = state.step - 1;
        if (state.step < 1) {
            state.step = 1;
        }
    },

    SET_STEP(state, step) {
        state.step = step;
    },
    GET_STEP(state) {
        return state.step;
    },

    GET_TITLE(state) {
        return state.title;
    },
    GET_DESCRIPTION(state) {
        return state.description;
    }
};

export const actions = {
    setTitle({ commit }, title) {
        return commit('SET_TITLE', title);
    },
    setDescription({ commit }, description) {
        return commit('SET_DESCRIPTION', description);
    },

    getTitle({ commit }) {
        return commit('GET_TITLE');
    },
    getDescription({ commit }) {
        return commit('GET_DESCRIPTION');
    },

    nextStateId({ commit }) {
        return commit('NEXT_STATE_ID');
    },
    backStateId({ commit }) {
        return commit('BACK_STATE_ID');
    },

    setStateId({ commit }, id) {
        return commit('SET_STATE_ID', id);
    },
    getStateId({ commit }) {
        return commit('GET_STATE_ID');
    },

    nextStep({ commit }) {
        return commit('NEXT_STEP');
    },
    backStep({ commit }) {
        return commit('BACK_STEP');
    },

    setStep({ commit }, step) {
        return commit('SET_STEP', step);
    },
    getStep({ commit }) {
        return commit('GET_STEP');
    }
};