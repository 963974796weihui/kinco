import Vue from 'vue';
import App from './App';
import router from './router';
import store from './store';
import './mock/index.js';  // 该项目所有请求使用mockjs模拟
import './login.js' 
import './ui.js' 
//引入阿里图标
import 'static/fonts/iconfont.css'
import iView from 'iview'
import 'iview/dist/styles/iview.css'  
import VDistpicker from 'v-distpicker'
import * as fundebug from "fundebug-javascript";
import fundebugVue from "fundebug-vue";
fundebug.apikey = "71ef3b915a41cc1331130f179339982998d9885efd1f34d9602c4bf30aff1fb3"
fundebugVue(fundebug, Vue);
Vue.component('v-distpicker', VDistpicker)
Vue.use(iView);
import axios from 'axios'
Vue.prototype.$http=axios
// import ElementUI from 'element-ui'
// import 'element-ui/lib/theme-chalk/index.css'
// Vue.use(ElementUI)
Vue.config.productionTip = false;
var vm=new Vue({
  el: '#app',
  router,
  store,
  template: '<App/>',
  components: { App }
})


