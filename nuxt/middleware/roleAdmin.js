/**
 * Middleware Ver 1.0
 */

export default ({ store, redirect }) => {
  if (store.state.auth.user.role !== 'ROLE_ADMIN') {
    return redirect('/')
  }
}