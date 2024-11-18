import VueCookies from 'vue-cookies'
import { createStore } from 'vuex';

const Auth =  {
    state: {
      token: null,
      role: null,
      userName: null,
      userId: null,
    },
    mutations: {
      SET_TOKEN(state, payload) {
        if (payload === null)
          VueCookies.remove('token')
        else
          VueCookies.set('token', payload);

        state.token = payload;
      },
      SET_ROLE(state, payload) {
        if (payload === null)
          VueCookies.remove('role')
        else
          VueCookies.set('role', payload);

        state.role = payload;
      },
      SET_USERNAME(state, payload) {
        if (payload === null)
          VueCookies.remove('userName')
        else
          VueCookies.set('userName', payload);

        state.userName = payload;
      },
      SET_USERID(state, payload) {
        if (payload === null)
          VueCookies.remove('userId')
        else
          VueCookies.set('userId', payload);

        state.userId = payload;
      }
    },
    actions: {
      setToken(context, payload) {
        context.commit('SET_TOKEN', payload)
      },
      removeToken(context) {
        context.commit('SET_TOKEN', null)
      },
      setRole(context, payload) {
        context.commit('SET_ROLE', payload)
      },
      removeRole(context) {
        context.commit('SET_ROLE', null)
      },
      setUserName(context, payload) {
        context.commit('SET_USERNAME', payload)
      },
      removeUserName(context) {
        context.commit('SET_USERNAME', null)
      },
      setUserId(context, payload) {
        context.commit('SET_USERID', payload)
      },
      removeUserId(context) {
        context.commit('SET_USERID', null)
      }
    },
    getters: {
      getToken: state => state.token || VueCookies.get('token'),
      getRole: state => state.role || VueCookies.get('role'),
      getUserName: state => state.userName || VueCookies.get('userName'),
      getUserId: state => state.userId || VueCookies.get('userId'),
    },
  };

const Store = createStore({
    modules: {
    Auth : Auth
  }
})

export default Store;
