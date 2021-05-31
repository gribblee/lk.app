export default ({ $axios, store, $auth }) => {

    if (process.server) {
        return false;
    }

    if (!store.state.auth.loggedIn) {
        return false
    }
    $axios
        .get('/directory', {
            headers: {
                Authorization: $auth.getToken('local'),
            },
        })
        .then(({ data }) => {
            store.dispatch('directory/setDirectory', data)
        })
        .catch((_err) => {
            console.error(_err)
        });
}
