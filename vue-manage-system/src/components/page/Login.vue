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
            <!-- 用户名 -->
            <el-form-item prop="username1">
              <el-input v-model="ruleForm.username" placeholder="admin">
                <el-button slot="prepend" icon="el-icon-lx-people"></el-button>
              </el-input>
            </el-form-item>
            <!-- 密码 -->
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
              <!-- <el-button type="primary" @click="aaa()">登录1</el-button> -->
              <el-button class="blue-login" type="primary" @click="submitForm('ruleForm')">登录</el-button>
                <router-link to="/reg">
                <p class="admin blue">建立管理员账号</p>
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
              <el-button class="blue-login" type="primary" @click="submitForm1('ruleForm')">登录</el-button>
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
                 localStorage.setItem(
                  "loginDomainId",
                  res.data.message[0].id
                );
                 localStorage.setItem(
                  "loginDomainName",
                  res.data.message[0].domain_name
                );
                localStorage.setItem(
                  "last_time",
                  res.data.message[0].last_time
                );
                this.$notify.success({
                  title: "登录成功",
                  message: "欢迎进入EdgeAccess系统"
                  // position: 'bottom-left'
                });
                this.$router.push({ path: "/" });
              } else if (res.data.code == 201) {
                this.$message({
                  message: "用户名或密码输入错误   !",
                  type: "warning"
                });
              } else if (res.data.code == 202) {
                this.$message({
                  message: "请前往邮箱进行确认   !",
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
  font-size: 15px;
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
  margin: auto;
  top: 0;
  left: 0;
  right: 0;
  bottom: 0;
  width: 350px;
  height: 300px;
  border-radius: 5px;
  background: #ffffff;
  /* overflow: hidden; */
  padding: 5px 30px;
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
.blue {
  color: #409eff;
}
.blue-login {
  margin-top: 10px;
  background-color: #6296ea;
}
.el-tabs__item {
  color: #c9c9c9;
}
/* 清除自带小眼睛 */
input::-ms-reveal,
input::-ms-clear {
  display: none;
}
/* input边框颜色 */
.el-input__inner{
  border: 1px solid #DCDFE6;
}
</style>