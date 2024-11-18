import Api from '@/Helpers/Api/Api.js';
import Store from '@/Store';
import SystemVars from '@/Helpers/General/SystemVars';

let Auth = {
    async checkAuthentication() {
        if ((route().current('pages.admin.*') || route().current('pages.user.*')) || route().current('pages.email.*') && Store.getters.getToken != null) {
            const result = await Api.postAsync(route('api.refresh'), { token: Store.getters.getToken });

            if (result.code == 200) {
                Store.dispatch('setToken', result.response.data.token);
                Store.dispatch('setRole', result.response.data.role);
                Store.dispatch('setUserName', result.response.data.user_name);
                Store.dispatch('setUserId', result.response.data.user_id);

                if (route().current('pages.login')) {
                    window.location.href = route(`pages.${result.response.data.role}.dashboard.index`);
                }
                return true;
            }
        }

        Store.dispatch('removeToken');
        Store.dispatch('removeRole');
        Store.dispatch('removeUserName');
        Store.dispatch('removeUserId');
        window.location.href = route('pages.login');
        return false;
    },
    async maintainOnLogin() {
        if (route().current('pages.login') && Store.getters.getToken != null && Store.getters.getRole != null) {
            window.location.href = route(`pages.${Store.getters.getRole}.dashboard.index`);
            return false;
        }

        return true;
    },
    async login(data) {
        const result = await Api.postAsync('/login', data);

        if (result.code == 200) {
            Store.dispatch('setToken', result.response.data.token);
            Store.dispatch('setRole', result.response.data.role);

            if (route().current('pages.login')) {
                window.location.href = route(`pages.${result.response.data.role}.dashboard.index`);
            }
            return true;
        }

        return false;
    },
    async logout() {
        const result = await Api.postAsync('/logout', { token: Store.getters.getToken });

        if (result.code == 200) {
            Store.dispatch('removeToken');
            Store.dispatch('removeRole');
            Store.dispatch('removeUserName');
            Store.dispatch('removeUserId');

            window.location.href = route('pages.login');
        } else {
            console.error (result.response);
        }
    }
}

export default Auth;
