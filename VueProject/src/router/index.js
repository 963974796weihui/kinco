import Vue from 'vue';
import Router from 'vue-router';
const _import = require('./_import_' + process.env.NODE_ENV);
import Full from '@/containers/Full'
import Full2 from '@/containers/Full2'
 //import Register from '@/views/register'
 import wz from '@/views/wz'
import Buttons from '@/views/components/Buttons'

// Views - Pages
import Page404 from '@/views/errorPages/Page404'
import Page500 from '@/views/errorPages/Page500'


/* login */
const Login = _import('login/index');
const Register = _import('Register');
const NewDomain = _import('NewDomain');
Vue.use(Router);

export const constantRouterMap = [
    { path: '/login', component: Login, hidden: true },
    { path: '/register', component: Register, hidden: true},
    { path: '/newdomain', component: NewDomain, hidden: true},
    // { path: '/wz', name:'wz',comment:wz},
    {path: '/pages',redirect: '/pages/p404', name: 'Pages',
          component: {
            render (c) { return c('router-view') }
              // Full,
          },
          children: [{path: '404',  name: 'Page404', component: _import('errorPages/Page404') },
                     {path: '500',name: 'Page500',component: _import('errorPages/Page404')},
                    ]
    }


]

export default new Router({
  // routes:[
  //   {
  //     path: '/register',
  //     component: Register
  //   }
  // ],
  mode: 'history',
//  mode: 'hash', 
  linkActiveClass: 'open active',
  scrollBehavior: () => ({ y: 0 }),
  routes: constantRouterMap
});

export const asyncRouterMap = [
  // {path: '/register',name: 'Register',component: _import('Register')},
 {
    path: '/',
    redirect: '/dashboard',
    name: '首页',
    component: Full,
    hidden:false,
    children: [
     {path: '/dashboard',name: '新建域',icon:'speedometer',component: _import('Dashboard')},
     {path: '/components',name: '组件',redirect: '/components/buttons',icon:'bookmark',
        component: {render (c) { return c('router-view') }},
        children: [ {path: 'buttons',name: 'Buttons按钮',icon:'social-youtube',component: _import('components/Buttons'), hidden:false, },
                    {path: 'hoverbuttons',name: '悬停特效按钮',icon:'wand',component: _import('components/HoverButtons')},
                    {path: 'alert',name: 'Alert警告提示',icon:'alert',component: _import('components/Alert')},
                    {path: 'card',name: 'Card卡片',icon:'ios-browsers-outline',component: _import('components/Card')},
                    {path: 'datepicker',name: 'DatePicker',icon:'ios-calendar-outline',component: _import('components/DatePicker')},
                    {path: 'form',name: 'Form表单',icon:'ios-list-outline',component: _import('components/Form')},
                    {path: 'modal',name: 'Modal对话框',icon:'ios-chatbubble-outline',component: _import('components/Modal')},
                    {path: 'select',name: 'Select选择器',icon:'ios-arrow-down',component: _import('components/Select')},
                    {path: 'spin',name: 'Spin加载中',icon:'load-d ',component: _import('components/Spin')},
                    {path: 'steps',name: 'Steps步骤条',icon:'ios-checkmark-outline',component: _import('components/Steps')},
                    {path: 'timeline',name: 'Timeline时间轴',icon:'android-more-vertical',component: _import('components/Timeline')},
                    {path: 'transfer',name: 'Transfer穿梭框',icon:'ios-pause-outline',component: _import('components/Transfer')},
                    {path: 'timepicker',name: 'Timepicker',icon:'ios-clock-outline',component: _import('components/Timepicker')},
                    {path: 'upload',name: 'Upload上传',icon:'ios-cloud-upload-outline',component: _import('components/Upload')},
                  
                  ]
      },
       {path: '/wz',name: '用户',icon:"social-html5",component: _import('wz')},

       {path: '/charts',name: '人机',redirect: '/charts/shopchart',icon:'pie-graph',
        children: [ {path: 'shopchart',name: '人机管理',icon:'stats-bars',component: _import('charts/ShopChart'), hidden:false,
      },
                    {path: 'radarchart',name: '人机群组',icon:'arrow-graph-up-right',component: _import('charts/RadarChart')},
                  ]
      },
     
    ]
  },

   {
    path: '/home1',
    redirect: '/home1/introduction',
    name: '首页2',
    component: Full2,
    hidden:false,
    children: [
     {path: '/home1/dashboard',name: 'Dashboard2',icon:'speedometer',component: _import('Dashboard2')},
     {path: '/home1/introduction',name: '介绍2',icon:'thumbsup',component: _import('Introduction')},
    
    ]
  },


  { path: '*', redirect: '/pages/404', hidden: true }
  
];
