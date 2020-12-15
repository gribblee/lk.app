/**
 * Middleware Ver 1.0
 */
export default ({ store, redirect }) => {
  if (
    store.state.auth.user.role !== 'ROLE_ADMIN' &&
    store.state.auth.user.role !== 'ROLE_WEBMASTER'
  ) {
    return redirect('/')
  }
}
