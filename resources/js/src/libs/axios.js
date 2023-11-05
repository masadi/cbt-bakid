import Vue from 'vue'

// axios
import axios from 'axios'

const axiosIns = axios.create({
  // You can add your headers here
  // ================================
  baseURL: app_url+'/api/',
  // timeout: 1000,
  // headers: {'X-Custom-Header': 'foobar'}
})
axiosIns.interceptors.request.use(function (config) {
  // Do something before request is sent
  //console.log(config);
  return config;
}, function (error) {
  //console.log(error);
  // Do something with request error
  return Promise.reject(error);
});

axiosIns.interceptors.response.use(
  response => response,
  error => {
    //console.log(error.response);
    //return Promise.reject(error);
    //console.log(error);
    return error.response
  });
Vue.prototype.$http = axiosIns

export default axiosIns
