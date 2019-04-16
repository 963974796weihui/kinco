import Vue from 'vue';
import Router from 'vue-router';

Vue.use(Router);

export default new Router({
    routes: [
        {
            path: '/',
            redirect: '/dashboard'
        },
        {
            path: '/',
            component: resolve => require(['../components/common/Home.vue'], resolve),
            meta: { title: '自述文件' },
            children:[
                {
                    // 网址路径
                    path: '/dashboard',
                    // 关联vue组件
                    component: resolve => require(['../components/page/Dashboard.vue'], resolve),
                    // 面包屑的标题
                    meta: { title: '系统首页' }
                    // meta: { title: '授权码管理' }
                },
                {
                    path: '/icon',
                    component: resolve => require(['../components/page/Icon.vue'], resolve),
                    meta: { title: '自定义图标' }
                },
                {
                    path: '/codemanage',
                    component: resolve => require(['../components/page/CodeManage.vue'], resolve),
                    meta: { title: '授权码管理' }
                },
                {
                    path: '/tabs',
                    component: resolve => require(['../components/page/Tabs.vue'], resolve),
                    meta: { title: 'tab选项卡' }
                },
                {
                    path: '/usermanage',
                    component: resolve => require(['../components/page/UserManage.vue'], resolve),
                    meta: { title: '用户' }
                },
                {
                    // 富文本编辑器组件
                    path: '/eqmanage',
                    component: resolve => require(['../components/page/EqManage'], resolve),
                    meta: { title: '设备管理' }
                },
                {
                    // markdown组件
                    path: '/eqgroup',
                    component: resolve => require(['../components/page/EqGroup.vue'], resolve),
                    meta: { title: '设备群组' }    
                },
                {
                    // 图片上传组件
                    path: '/upload',
                    component: resolve => require(['../components/page/Upload.vue'], resolve),
                    meta: { title: '文件上传' }   
                },
                {
                    // vue-schart组件
                    path: '/charts',
                    component: resolve => require(['../components/page/BaseCharts.vue'], resolve),
                    meta: { title: 'schart图表' }
                },
                {
                    // 拖拽列表组件
                    path: '/drag',
                    component: resolve => require(['../components/page/DragList.vue'], resolve),
                    meta: { title: '拖拽列表' }
                },
                {
                    // 拖拽Dialog组件
                    path: '/dialog',
                    component: resolve => require(['../components/page/DragDialog.vue'], resolve),
                    meta: { title: '拖拽弹框' }
                },
                {
                    // 权限页面
                    path: '/permission',
                    component: resolve => require(['../components/page/Permission.vue'], resolve),
                    meta: { title: '权限测试', permission: true }
                },
                {
                    path: '/404',
                    component: resolve => require(['../components/page/404.vue'], resolve),
                    meta: { title: '404' }
                },
                {
                    path: '/403',
                    component: resolve => require(['../components/page/403.vue'], resolve),
                    meta: { title: '403' }
                }
            ]
        },
        {
            path: '/login',
            component: resolve => require(['../components/page/Login.vue'], resolve)
        },
         {
            path: '/reg',
            component: resolve => require(['../components/page/reg.vue'], resolve)
        },
          {
            path: '/new1',
            component: resolve => require(['../components/page/new1.vue'], resolve)
        },
        {
            path: '*',
            redirect: '/404'
        }
    ]
})
