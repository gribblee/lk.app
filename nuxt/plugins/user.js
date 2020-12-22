import { notification } from 'ant-design-vue'
import Timer from './mixins/timerMixin';

export default ({ $axios, store, $auth }) => {
    if (process.server) {
        return false;
    }

    if (!store.state.auth.loggedIn) {
        return false
    }

    const userPing = () => {
        $axios
            .get('/user/updated', {
                headers: {
                    Authorization: $auth.getToken('local')
                },
                progress: false
            })
            .then(({ data }) => {
                store.dispatch('userData/setUpdated', {
                    dealsCount: data.deals_count,
                    msgCount: data.msg_count,
                    disputCount: data.disput_count,
                    distributedCount: data.distributed_count,
                    balance: data.balance,
                    bonus: data.bonus,
                    typeName: data.type_name
                })
                if (data.notifications.length > 0) {
                    data.notifications.forEach((n) => {
                        notification.open({
                            message: 'Сообщение',
                            description:
                                n.description,
                            duration: 0,
                            onClose: () => {
                                console.log('Notification Clicked!');
                            },
                            style: {
                                backgroundColor: "#CCC"
                            }
                        })
                    })
                }
            })
            .catch((_err) => {
                console.error(_err)
            })
    }

    userPing();

    Timer.Interval(userPing, 10000);
};