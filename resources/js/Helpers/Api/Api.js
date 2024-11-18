import axios from "axios";
import Store from '@/Store';
import SystemVars from '@/Helpers/General/SystemVars';

axios.defaults.baseURL = SystemVars.baseUrlAPI;
axios.defaults.headers.common['Authorization'] = "Bearer " + Store.getters.getToken;
axios.defaults.headers.common["Accept"] = "application/json"

axios.defaults.timeout = 20000;
// axios.defaults.headers.post['Access-Control-Allow-Origin'] = '*';
//axios.defaults.headers.options['Access-Control-Allow-Origin'] = '*';

let Api = {
  get(route, params) {
    axios.get(route, {
      data : JSON.stringify(params),
      withCredentials: false,
      headers: {
        'Accept': 'application/json',
        'Content-Type': 'application/json;charset=utf-8',
        'Access-Control-Allow-Origin': '*',
        'Access-Control-Allow-Headers': '*',
        'Access-Control-Allow-Credentials': 'true'
      }
    })
    .then(response => {
      // JSON responses are automatically parsed.
      return {
        code: response.status,
        response : response.data
      };
    })
    .catch(e => {
      return {
        code: e.status,
        response : e.message
      };
    })
  },

  post(route, params) {
    axios.post(route, params, {
      //data : JSON.stringify(params),
      //withCredentials: true,
      headers: {
        'Accept': 'application/json',
        'Content-Type': 'application/json',
        'Access-Control-Allow-Origin': '*',
        'Access-Control-Allow-Headers': '*',
        'Access-Control-Allow-Credentials': 'true'
      }
    })
    .then(response => {
      // JSON responses are automatically parsed.
      return {
        code: response.status,
        response : response.data
      };
    })
    .catch(e => {
      return {
        code: e.response.status,
        response : e.response.data
      };
    })
  },
  async postAsync(route, params) {
    return await axios.post(route, params, {
      //data : JSON.stringify(params),
      //withCredentials: true,
      headers: {
        'Accept': 'application/json',
        'Content-Type': 'application/json',
        'Access-Control-Allow-Origin': '*',
        'Access-Control-Allow-Headers': '*',
        'Access-Control-Allow-Credentials': 'true'
      }
    })
    .then(response => {
      // JSON responses are automatically parsed.

      return {
        code: response.status,
        response : response.data
      };
    })
    .catch(e => {
        console.log(e);

      return {
        code: e.response.status,
        response : e.response.data
      };
    })
  },
  async getAsync(route, params) {
    return await axios.get(route, params, {
      //data : JSON.stringify(params),
      //withCredentials: true,
      headers: {
        'Accept': 'application/json',
        'Content-Type': 'application/json',
        'Access-Control-Allow-Origin': '*',
        'Access-Control-Allow-Headers': '*',
        'Access-Control-Allow-Credentials': 'true'
      }
    })
    .then(response => {
      // JSON responses are automatically parsed.
      return {
        code: response.status,
        response : response.data
      };
    })
    .catch(e => {
      return {
        code: e.response.status,
        response : e.response.data
      };
    })
  },
  async deleteAsync(route, params) {
    return await axios.delete(route, params, {
      //data : JSON.stringify(params),
      //withCredentials: true,
      headers: {
        'Accept': 'application/json',
        'Content-Type': 'application/json',
        'Access-Control-Allow-Origin': '*',
        'Access-Control-Allow-Headers': '*',
        'Access-Control-Allow-Credentials': 'true'
      }
    })
    .then(response => {
      // JSON responses are automatically parsed.
      return {
        code: response.status,
        response : response.data
      };
    })
    .catch(e => {
      return {
        code: e.response.status,
        response : e.response.data
      };
    })
  },
  async patchAsync(route, params) {
    return await axios.patch(route, params, {
      //data : JSON.stringify(params),
      //withCredentials: true,
      headers: {
        'Accept': 'application/json',
        'Content-Type': 'application/json',
        'Access-Control-Allow-Origin': '*',
        'Access-Control-Allow-Headers': '*',
        'Access-Control-Allow-Credentials': 'true'
      }
    })
    .then(response => {
      // JSON responses are automatically parsed.
      return {
        code: response.status,
        response : response.data
      };
    })
    .catch(e => {
      return {
        code: e.response.status,
        response : e.response.data
      };
    })
  },
  put(route, params) {
    return axios.put(route, params, {
        //data : JSON.stringify(params),
        //withCredentials: true,
        headers: {
          'Accept': 'application/json',
          'Content-Type': 'application/json',
          'Access-Control-Allow-Origin': '*',
          'Access-Control-Allow-Headers': '*',
          'Access-Control-Allow-Credentials': 'true'
        }
      })
      .then(response => {
        // JSON responses are automatically parsed.
        return {
          code: response.status,
          response : response.data
        };
      })
      .catch(e => {
        return {
          code: e.response.status,
          response : e.response.data
        };
      })
  },

  async putAsync(route, params) {
    return await axios.put(route, params, {
      //data : JSON.stringify(params),
      //withCredentials: true,
      headers: {
        'Accept': 'application/json',
        'Content-Type': 'application/json',
        'Access-Control-Allow-Origin': '*',
        'Access-Control-Allow-Headers': '*',
        'Access-Control-Allow-Credentials': 'true'
      }
    })
    .then(response => {
      // JSON responses are automatically parsed.

      return {
        code: response.status,
        response : response.data
      };
    })
    .catch(e => {
        console.log(e);

      return {
        code: e.response.status,
        response : e.response.data
      };
    })
  },

};

export default Api;
