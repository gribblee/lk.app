export const getters = {
    authenticated(state, getters) {
        return state.auth.loggedIn
    },
    user(state, getters) {
        return state.auth.user
    },
    jwtToken(state, getters) {
        return state.jwt
    }
}

export const state = () => ({
    busy: false,
    loggedIn: false,
    strategy: 'local',
    user: false,
    jwtToken: '',
})
