<template>
  <div class="header b-white">
    <!-- 折叠按钮 -->
    <el-tooltip class="item" effect="dark" content="进行缩放" placement="top-start">
      <div class="collapse-btn" @click="collapseChage">
        <i class="el-icon-menu blue"></i>
      </div>
    </el-tooltip>
    <p class="logo red">EdgeAccess</p>
    <!-- <h3>{{this.$store.state.domainId}}</h3> -->

    <el-dialog title="新建域" :visible.sync="dialogFormVisible" width="20%">
      <el-form :model="formRegion">
        <el-form-item label="域名" :label-width="formLabelWidth">
          <el-input v-model="formRegion.name" autocomplete="off" prop="name"></el-input>
        </el-form-item>
        <el-form-item label="域简介" :label-width="formLabelWidth">
          <el-input
            type="textarea"
            placeholder="80字以内"
            v-model="formRegion.regionInfo"
            autocomplete="off"
            prop="regionInfo"
            @change="SomeJavaScriptCode"
          ></el-input>
        </el-form-item>
      </el-form>
      <div slot="footer" class="dialog-footer">
        <el-button @click="dialogFormVisible = false">取 消</el-button>
        <el-button type="primary" :plain="true" @click="addRegion()">确 定</el-button>
      </div>
    </el-dialog>

    <div class="header-right">
      <div class="header-user-con">
        <el-button
          class="create-region b-red"
          icon="el-icon-plus"
          size="medium"
          @click="dialogFormVisible = true"
        >新建域</el-button>
        <span class="time blue">上次登录:{{this.time}}</span>
        <!-- 用户头像 -->
        <div class="user-avator">
          <img src="static/img/img.jpg">
        </div>
        <!-- 用户名下拉菜单 -->
        <el-dropdown class="user-name" trigger="click" @command="handleCommand">
          <span class="el-dropdown-link blue">
            {{username}}
            <i class="el-icon-caret-bottom"></i>
          </span>
          <el-dropdown-menu slot="dropdown">
            <el-dropdown-item divided command="loginout">退出登录</el-dropdown-item>
          </el-dropdown-menu>
        </el-dropdown>
        <div class="btn-fullscreen" @click="handleFullScreen">
          <el-tooltip effect="dark" :content="fullscreen?`取消全屏`:`全屏`" placement="bottom">
            <i class="el-icon-rank blue"></i>
          </el-tooltip>
        </div>
      </div>
    </div>
  </div>
</template>
<script>
import store from "../../store/store.js";
import bus from "../common/bus";
export default {
  data() {
    return {
      formRegion: {
        name: "",
        regionInfo: ""
      },
      dialogTableVisible: false,
      dialogFormVisible: false,
      collapse: false,
      fullscreen: false,
      name: "无名氏",
      message: 2,
      time: "",
      num1: 0,
      num2: 1,
      itemRegion: []
    };
  },
  store,
  created() {
    this.time = localStorage.getItem("last_time");
    // this.$store.commit('saveMenuList',this.itemRegion)
    //   alert(111111111)
  },
  computed: {
    username() {
      let username = localStorage.getItem("ms_username");
      return username ? username : this.name;
    }
  },
  methods: {
    SomeJavaScriptCode() {
      this.$http
        .post("/api/admin/registerDomain", {
          domain_name: this.formRegion.name,
          remark: this.formRegion.regionInfo
        })
        .then(res => {
          console.log(res);
          if (res.data.status == "S") {
            this.$message({
              message: "新建域成功",
              type: "success"
            });

            //     this.itemRegion=
            //    {
            //                     icon: 'el-icon-lx-calendar',
            //                     title: this.formRegion.name,
            //                     index: this.num1++,
            //                     subs: [
            //                         {
            //                             index: 'usermanage',
            //                             title: '用户'
            //                         },
            //                         {
            //                             index: this.num2++,
            //                             title: '设备',
            //                             subs: [
            //                                 {
            //                                     index: 'eqmanage',
            //                                     title: '设备管理'
            //                                 },
            //                                 {
            //                                     index: 'eqgroup',
            //                                     title: '设备群组'
            //                                 },
            //                             ]
            //                         }
            //                     ]
            //                 },
            //bus发送侧边栏
            // bus.$emit('items', this.itemRegion)
          } else if (res.data.status == "F") {
            this.$message({
              message: "该域已存在",
              type: "warning"
            });
          }
        });
    },
    //新建域
    addRegion() {
         const domain_id = localStorage.getItem("loginDomainId");
          const domain_name = localStorage.getItem("loginDomainName");
          this.itemRegion = [
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
          bus.$emit("items", this.itemRegion);
      this.dialogFormVisible = false;
    },
    // 用户名下拉菜单选择事件
    handleCommand(command) {
      if (command == "loginout") {
        localStorage.removeItem("ms_username");
        this.$http({
        method: "get",
        url: "/api/admin/logout",
        params: {
          id: localStorage.getItem("loginDomainId")
        }
      }).then(res => {
      });
        this.$router.push("/login");
      }
    },
    // 侧边栏折叠
    collapseChage() {
      this.collapse = !this.collapse;
      bus.$emit("collapse", this.collapse);
    },
    // 全屏事件
    handleFullScreen() {
      let element = document.documentElement;
      if (this.fullscreen) {
        if (document.exitFullscreen) {
          document.exitFullscreen();
        } else if (document.webkitCancelFullScreen) {
          document.webkitCancelFullScreen();
        } else if (document.mozCancelFullScreen) {
          document.mozCancelFullScreen();
        } else if (document.msExitFullscreen) {
          document.msExitFullscreen();
        }
      } else {
        if (element.requestFullscreen) {
          element.requestFullscreen();
        } else if (element.webkitRequestFullScreen) {
          element.webkitRequestFullScreen();
        } else if (element.mozRequestFullScreen) {
          element.mozRequestFullScreen();
        } else if (element.msRequestFullscreen) {
          // IE11
          element.msRequestFullscreen();
        }
      }
      this.fullscreen = !this.fullscreen;
    }
  },
  mounted() {
    if (document.body.clientWidth < 1500) {
      this.collapseChage();
    }
  }
};
</script>
<style scoped>
.create-region {
  margin-right: 20px;
  color: #fff;
  border: 0px;
}
.header {
  position: relative;
  box-sizing: border-box;
  width: 100%;
  height: 60px;
  font-size: 22px;
  color: #fff;
}
.collapse-btn {
  float: left;
  padding: 0 21px;
  cursor: pointer;
  line-height: 60px;
  background-color: #fff;
}
.header .logo {
  font-size: 30px;
  font-weight: bold;
  float: left;
  width: 250px;
  height: 60px;
  line-height: 60px;
}
.header-right {
  float: right;
  padding-right: 45px;
}
.header-user-con {
  display: flex;
  height: 60px;
  align-items: center;
}
.btn-fullscreen {
  transform: rotate(45deg);
  margin-right: 5px;
  font-size: 40px;
}
.btn-bell,
.btn-fullscreen {
  position: relative;
  width: 45px;
  height: 40px;
  text-align: center;
  border-radius: 15px;
  cursor: pointer;
}
.btn-bell-badge {
  position: absolute;
  right: 0;
  top: -2px;
  width: 8px;
  height: 8px;
  border-radius: 4px;
  background: #f56c6c;
  color: #fff;
}
.btn-bell .el-icon-bell {
  color: #fff;
}
.user-name {
  margin-left: 10px;
}
.user-avator {
  margin-left: 3px;
}
.user-avator img {
  display: block;
  width: 40px;
  height: 40px;
  border-radius: 50%;
}
.el-dropdown-link {
  cursor: pointer;
}
.el-dropdown-menu__item {
  text-align: center;
}
.time {
  font-size: 15px;
  margin-right: 20px;
}
.blue {
  color: #2798ff;
}
.b-white {
  background-color: #ffffff;
}
.b-red {
  background-color: #ff3333;
}
.red {
  color: #ff3333;
}
</style>
