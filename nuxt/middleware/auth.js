export default ({ store, redirect }) => {
    console.log(store.state.auth);
    if (!store.state.auth.loggedIn) {
        return redirect('/sign')
    }
}
