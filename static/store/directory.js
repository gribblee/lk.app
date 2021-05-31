export const state = () => ({
    directory: {},
});

export const getters = {
    directory(state) {
        return state.directory;
    },
};

export const mutations = {
    SET_DIRECTORY(state, directory) {
        state.directory = directory;
    },
    GET_DIRECTORY(state) {
        return state.directory;
    },
};

export const actions = {
    setDirectory({ commit }, data) {
        return commit("SET_DIRECTORY", data);
    },
    getDirectory({ commit }) {
        return commit("GET_DIRECTORY");
    },
    clearDirectroy({ commit }) {
        return commit("SET_DIRECTROY", {});
    },
};
