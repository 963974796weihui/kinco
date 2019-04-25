<template>
  <div class="login-wrap">
    <h1 class="white ea">EdgeAccess</h1>
    <div class="ms-login">
      <el-tabs v-model="activeName" @tab-click="handleClick">
        <el-tab-pane label="管理员" name="admin">
          <el-form
            :model="ruleForm"
            :rules="rules"
            ref="ruleForm"
            label-width="0px"
            class="ms-content"
          >
            <el-form-item prop="username1">
              <el-input v-model="ruleForm.username" placeholder="admin">
                <el-button slot="prepend" icon="el-icon-lx-people"></el-button>
              </el-input>
            </el-form-item>
            <el-form-item prop="password1">
              <el-input
                type="password"
                placeholder="password"
                v-model="ruleForm.password"
                @keyup.enter.native="submitForm('ruleForm')"
              >
                <el-button slot="prepend" icon="el-icon-lx-lock"></el-button>
              </el-input>
            </el-form-item>
            <div class="login-btn">
              <el-button type="primary" @click="submitForm('ruleForm')">登录</el-button>
              <!-- <el-button type="primary" @click="aaa()">登录1</el-button> -->
              <router-link to="/reg">
                <p class="white admin">建立管理员账号</p>
              </router-link>
            </div>
          </el-form>
        </el-tab-pane>
        <el-tab-pane label="普通用户" name="user">
          <el-form
            :model="ruleForm"
            :rules="rules"
            ref="ruleForm"
            label-width="0px"
            class="ms-content"
          >
            <el-form-item prop="username">
              <el-input v-model="ruleForm.username1" placeholder="username">
                <el-button slot="prepend" icon="el-icon-lx-people"></el-button>
              </el-input>
            </el-form-item>
            <el-form-item prop="password">
              <el-input
                type="password"
                placeholder="password"
                v-model="ruleForm.password1"
                @keyup.enter.native="submitForm1('ruleForm')"
              >
                <el-button slot="prepend" icon="el-icon-lx-lock"></el-button>
              </el-input>
            </el-form-item>
            <div class="login-btn">
              <el-button type="primary" @click="submitForm1('ruleForm')">登录</el-button>
            </div>
          </el-form>
        </el-tab-pane>
      </el-tabs>
    </div>
  </div>
</template>

<script>
import bus from "../common/bus";
export default {
  data: function() {
    return {
      activeName: "admin",
      ruleForm: {
        username: "",
        password: ""
      },
      rules: {
        username: [
          { required: true, message: "请输入用户名", trigger: "blur" }
        ],
        password: [{ required: true, message: "请输入密码", trigger: "blur" }]
      }
      // firstItem:[
      //        {
      //         icon: 'el-icon-lx-home',
      //         //index关联路由数组对象中的路径path
      //         index: 'dashboard',
      //         title: '系统首页'
      //     },
      //     {
      //         icon: 'el-icon-lx-cascades',
      //         index: 'codemanage',
      //         title: '授权码管理'
      //     }
      // ]
    };
  },
  methods: {
    // aaa(){
    //         this.$router.push({ path: "/",query: { num:1} });
    // },
    reg() {
      this.$router.push("/reg");
    },
    submitForm(formName) {
      this.$refs[formName].validate(valid => {
        if (valid) {
          localStorage.setItem("ms_username", this.ruleForm.username);
          localStorage.setItem("ms_password", this.ruleForm.password);
          this.$http
            .post("/api/admin/login", {
              user_name: this.ruleForm.username,
              password: this.ruleForm.password
            })
            .then(res => {
              if (res.data.status == "S") {
                localStorage.setItem("last_time", res.data.message[0].last_time);
   this.$notify.success({
          title: '登录成功',
          message: '欢迎进入EdgeAccess系统',
          // position: 'bottom-left'
        });

                //                 console.log(res)
                //                  var total=res.data.message.length
                //              for(var i=0;i<total;i++){
                // //                 this.firstItem.push(
                // //   {
                // //                         icon: 'el-icon-lx-calendar',
                // //                         index: res.data.message[i].id,
                // //                         title: res.data.message[i].domain_name,
                // //                         subs: [
                // //                             {
                // //                                 index: 'usermanage',
                // //                                 title: '用户'
                // //                             },
                // //                             {
                // //                                 index: res.data.message[i].id,
                // //                                 title: '设备',
                // //                                 subs: [
                // //                                     {
                // //                                         index: 'eqmanage',
                // //                                         title: '设备管理'
                // //                                     },
                // //                                     {
                // //                                         index: 'eqgroup',
                // //                                         title: '设备群组'
                // //                                     },
                // //                                 ]
                // //                             },
                // //                         ]
                // //                     }
                // //                 )

                //              }
                //  console.log( this.firstItem)
                //bus.$emit('firstitem', this.firstItem);
                //    localStorage.setItem('hou', JSON.stringify(this.firstItem));
                this.$router.push({ path: "/" });
              }else if(res.data.status == "F"){
                 this.$message({
              message: "用户名或密码输入错误   !",
              type: "warning"
            });
              }
            })
            .catch(function(error) {
              alert(axios错误回调);
              console.log(error);
            });
        } else {
            this.$message({
              message: "请输入用户名或密码   !",
              type: "warning"
            });
          console.log("error submit!!");
          return false;
        }
      });
    }
  }
};
</script>

<style scoped>
.white {
  color: azure;
}
.admin {
  float: right;
  font-size: 10px;
}
.login-wrap {
  position: relative;
  width: 100%;
  height: 100%;
  /* background: #da2020; */
  background-image: url(../../assets/login.jpg);
  background-size: 100%;
}
.ms-title {
  width: 100%;
  line-height: 50px;
  text-align: center;
  font-size: 20px;
  color: #fff;
  border-bottom: 1px solid #ddd;
}
.ms-login {
  position: absolute;
  left: 50%;
  top: 50%;
  width: 350px;
  height: 300px;
  margin: -190px 0 0 -175px;
  border-radius: 5px;
  background: rgba(255,255,255, 0.3);
  overflow: hidden;
}
.ms-content {
  padding: 30px 30px;
}
.login-btn {
  text-align: center;
}
.login-btn button {
  width: 100%;
  height: 36px;
  margin-bottom: 10px;
}
.login-tips {
  font-size: 12px;
  line-height: 30px;
  color: #fff;
}
.ea {
  font-size: 50px;
  position: absolute;
  top: 20px;
  left: 20px;
}
/* tab导航栏样式 */
/* .el-tabs__item{
  color: #fff
} */
</style>