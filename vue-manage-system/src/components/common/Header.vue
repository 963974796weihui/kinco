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
      <el-form :model="formRegion" :rules="ruleValidate" ref="ruleForm">
        <el-form-item label="域名" :label-width="formLabelWidth" prop="name">
          <el-input v-model="formRegion.name" autocomplete="off"></el-input>
        </el-form-item>
        <el-form-item label="域简介" :label-width="formLabelWidth" prop="regionInfo">
          <el-input
            type="textarea"
            placeholder="80字以内"
            maxlength="80"
            v-model="formRegion.regionInfo"
            autocomplete="off"
          ></el-input>
        </el-form-item>
      </el-form>
      <div slot="footer" class="dialog-footer">
        <el-button @click="dialogFormVisible = false">取 消</el-button>
        <el-button type="primary" :plain="true" @click="addRegion('ruleForm')">确 定</el-button>
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
    //域名和域简介校验
    const pure = (rule, value, callback) => {
      callback();
    };
    return {
      formRegion: {
        name: "",
        regionInfo: ""
      },
      dialogTableVisible: false,
      dialogFormVisible: false,
      collapse: false,
      fullscreen: false,
      name: "你好，请登录",
      message: 2,
      time: "",
      num1: 0,
      num2: 1,
      itemRegion: [],
      ruleValidate: {
        name: [
          { required: true, message: "请输入域名", trigger: "blur" },
          { validator: pure, trigger: "blur" }
        ],
        regionInfo: [
          { required: true, message: "请输入域简介", trigger: "blur" },
          { validator: pure, trigger: "blur" }
        ]
      }
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
    //             SomeJavaScriptCode(){
    //                  this.$http.post('/api/admin/registerDomain',
    //                 {
    //                      domain_name: this.formRegion.name,
    //                      remark: this.formRegion.regionInfo
    //                 }).then(res => {
    //             console.log(res)
    //             if(res.data.status=="S"){
    //                    this.$message({
    //           message: '新建域成功',
    //           type: 'success'
    //         });
    //             }else if(res.data.status=="F"){
    //  this.$message({
    //           message: '该域已存在',
    //           type: 'warning'
    //         });
    //             }
    //         })
    //             },
    //新建域
    addRegion(formName) {
      this.$refs[formName].validate(valid => {
        if (valid) {
          this.$http
            .post("/api/admin/registerDomain", {
              domain_name: this.formRegion.name,
              remark: this.formRegion.regionInfo
            })
            .then(res => {
              this.dialogFormVisible = false;
              this.$http({
                method: "post",
                url: "/api/admin/login",
                data: {
                  user_name: localStorage.getItem("ms_username"),
                  password: localStorage.getItem("ms_password")
                }
              }).then(res => {
                if (res.data.status == "S") {
                  localStorage.setItem("loginDomainName", res.data.message[0].domain_name);
                  localStorage.setItem("loginDomainId", res.data.message[0].id);
                  // this.$router.push({ path: "/codemanage" });
                }
                const domain_id = res.data.message[0].id;
                const domain_name = res.data.message[0].domain_name;
                // console.log(res.data.message[0].domain_name)
                this.itemRegion = [
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

                //                     var objStr=JSON.stringify(this.itemRegion);
                // localStorage.setItem('aa',objStr);
                bus.$emit("items", this.itemRegion);
              });

              if (res.data.status == "S") {
                this.$message({
                  message: "新建域成功",
                  type: "success"
                });
              } else if (res.data.status == "F") {
                this.$message({
                  message: "该域已存在",
                  type: "warning"
                });
              }
            });
        }
      });
    },
    // 用户名下拉菜单选择事件
    handleCommand(command) {
      if (command == "loginout") {
        this.$http({
          method: "get",
          url: "/api/admin/logout",
          params: {
            id: localStorage.getItem("loginDomainId") //域id
          }
        }).then(res => {});

        localStorage.removeItem("ms_username");
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
