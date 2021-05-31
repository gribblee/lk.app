export default ({ store, redirect }) => {
    if (!store.state.auth.user.is_demo) {
        return redirect('/demo')
    }
}
