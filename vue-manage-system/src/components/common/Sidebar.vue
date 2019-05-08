<template>
  <div class="sidebar">
    <!-- 侧边栏    头-->
    <el-menu
      class="sidebar-el-menu"
      :default-active="onRoutes"
      :collapse="collapse"
      background-color="#015db4"
      text-color="#ffffff"
      active-text-color="#ffffff"
      router
    >
      <template v-for="item in items">
        <template v-if="item.subs">
          <el-submenu :index="item.index" :key="item.index">
            <template slot="title">
              <i :class="item.icon"></i>
              <span slot="title">{{ item.title }}</span>
            </template>

            <template v-for="subItem in item.subs">
              <el-submenu v-if="subItem.subs" :index="subItem.index" :key="subItem.index">
                <template slot="title">{{ subItem.title }}</template>
                <el-menu-item
                  v-for="(threeItem,i) in subItem.subs"
                  :key="i"
                  :index="threeItem.index"
                >{{ threeItem.title }}</el-menu-item>
              </el-submenu>
              <el-menu-item v-else :index="subItem.index" :key="subItem.index">{{ subItem.title }}</el-menu-item>
            </template>
          </el-submenu>
        </template>
        <template v-else>
          <el-menu-item :index="item.index" :key="item.index">
            <i :class="item.icon"></i>
            <span slot="title">{{ item.title }}</span>
          </el-menu-item>
        </template>
      </template>
    </el-menu>
    <!-- 侧边栏   尾 -->
  </div>
</template>

<script>
import store from "../../store/store.js";
import bus from "../common/bus";
export default {
  data() {
    return {
      collapse: false,
      num2: "",
      items: [
        {
          icon: "el-icon-lx-cascades",
          index: "codemanage",
          title: "授权码管理"
        }
      ]
      // 左侧边栏数组
      // items: [
      //     {
      //         icon: 'el-icon-lx-home',
      //         //index关联路由数组对象中的路径path
      //         index: 'dashboard',
      //         title: '系统首页'
      //     },
      //     {
      //         icon: 'el-icon-lx-cascades',
      //         index: 'codemanage',
      //         title: '授权码管理'
      //     },
      //     {
      //         icon: 'el-icon-lx-calendar',
      //         index: '3',
      //         title: '域A',
      //         subs: [
      //             {
      //                 index: 'usermanage',
      //                 title: '用户'
      //             },
      //             {
      //                 index: '3-2',
      //                 title: '设备',
      //                 subs: [
      //                     {
      //                         index: 'eqmanage',
      //                         title: '设备管理'
      //                     },
      //                     {
      //                         index: 'eqgroup',
      //                         title: '设备群组'
      //                     },
      //                 ]
      //             },
      //         ]
      //     },
      // ]
    };
  },
  store,
  computed: {
    //记住下一次侧边栏刷新的位置
    onRoutes() {
      return this.$route.path.replace("/", "");
    }
  },
  mounted() {
    //  //  接收侧边栏
    bus.$on("items", msg => {
      this.items = msg;
    });
    //登录接口
  },

  created() {
    // alert("侧边栏刷新")
    this.$http({
      method: "post",
      url: "/api/admin/login",
      data: {
        user_name: localStorage.getItem("ms_username"),
        password: localStorage.getItem("ms_password")
      }
    }).then(res => {
      const domain_id = res.data.message[0].id;
      const domain_name = res.data.message[0].domain_name;

      //如果此用户从没建过域
      if (!domain_id) {
        return;
      }
      this.items = [
        // {
        //     icon: 'el-icon-lx-home',
        //     //index关联路由数组对象中的路径path
        //     index: 'dashboard',
        //     title: '系统首页'
        // },
        {
          icon: "el-icon-lx-cascades",
          index: "codemanage",
          title: "授权码管理"
        },
        {
          icon: "el-icon-lx-calendar",
          title: domain_name,
          index: domain_id,
          subs: [
            {
              index: "usermanage",
              title: "用户"
            },
            {
              index: this.num2++,
              title: "设备",
              subs: [
                {
                  index: "eqmanage",
                  title: "设备管理"
                },
                {
                  index: "eqgroup",
                  title: "设备群组"
                }
              ]
            }
          ]
        }
      ];
      //存入vuex中
      this.$store.commit("saveDomainId", domain_id);
      //  console.log(this.$store.state.domainId)   可以打印
      //                     var objStr=JSON.stringify(this.itemRegion);
      // localStorage.setItem('aa',objStr);
      //  bus.$emit('items', this.itemRegion)
    });
    bus.$on("collapse", msg => {
      this.collapse = msg;
    });
  }
};
</script>

<style scoped>
.sidebar {
  display: block;
  position: absolute;
  left: 0;
  top: 60px;
  bottom: 0;
  overflow-y: scroll;
}
.sidebar::-webkit-scrollbar {
  width: 0;
}
.sidebar-el-menu:not(.el-menu--collapse) {
  width: 250px;
}
.sidebar > ul {
  height: 100%;
}
/* 选中颜色 */
.el-menu-item.is-active {
  background-color: #2798ff !important;
}
</style>
