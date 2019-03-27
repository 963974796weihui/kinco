<template>
    <div class="login-wrap">
        <h1 class="white ea">EdgeAccess</h1>
        <div class="ms-login">
<el-tabs v-model="activeName" @tab-click="handleClick">
    <el-tab-pane label="管理员" name="admin">
 <el-form :model="ruleForm" :rules="rules" ref="ruleForm" label-width="0px" class="ms-content">
                <el-form-item prop="username">
                    <el-input v-model="ruleForm.username" placeholder="admin">
                        <el-button slot="prepend" icon="el-icon-lx-people"></el-button>
                    </el-input>
                </el-form-item>
                <el-form-item prop="password">
                    <el-input type="password" placeholder="password" v-model="ruleForm.password" @keyup.enter.native="submitForm('ruleForm')">
                        <el-button slot="prepend" icon="el-icon-lx-lock"></el-button>
                    </el-input>
                </el-form-item>
                <div class="login-btn">
                    <el-button type="primary" @click="submitForm('ruleForm')">登录</el-button>
                    <router-link to="/reg">
                     <p class="white admin">建立管理员账号</p>
                     </router-link>
                </div>
            </el-form>
    </el-tab-pane>
    <el-tab-pane label="普通用户" name="user">
<el-form :model="ruleForm" :rules="rules" ref="ruleForm" label-width="0px" class="ms-content">
                <el-form-item prop="username">
                    <el-input v-model="ruleForm.username" placeholder="username">
                        <el-button slot="prepend" icon="el-icon-lx-people"></el-button>
                    </el-input>
                </el-form-item>
                <el-form-item prop="password">
                    <el-input type="password" placeholder="password" v-model="ruleForm.password" @keyup.enter.native="submitForm('ruleForm')">
                        <el-button slot="prepend" icon="el-icon-lx-lock"></el-button>
                    </el-input>
                </el-form-item>
                <div class="login-btn">
                    <el-button type="primary" @click="submitForm('ruleForm')">登录</el-button>
                </div>
            </el-form>

    </el-tab-pane>
  </el-tabs>
        </div>
    </div>
</template>

<script>
    export default {
        data: function(){
            return {
                activeName: 'admin',
                ruleForm: {
                    username: '',
                    password: ''
                },
                rules: {
                    username: [
                        { required: true, message: '请输入用户名', trigger: 'blur' }
                    ],
                    password: [
                        { required: true, message: '请输入密码', trigger: 'blur' }
                    ]
                }
            }
        },
        methods: {
        	reg(){
        		this.$router.push('/reg');
        	},
            submitForm(formName) {
                this.$refs[formName].validate((valid) => {
                    if (valid) {
                        localStorage.setItem('ms_username',this.ruleForm.username);
         this.$http.post("/api/admin/login", {
          user_name: this.ruleForm.username,
          password: this.ruleForm.password
        })
        .then(response => {
            console.log(response)
            if(response.data.status=="S"){
    this.$router.push({ path: "/" });
          console.log(response);
            }
        })
        .catch(function(error) {
          alert(axios错误回调);
          console.log(error);
        });
                    } else {
                        alert(34)
                        console.log('error submit!!');
                        return false;
                    }
                });
            },
        }
    }
</script>

<style scoped>
	.white {
  color: azure;
}
.admin {
  float: right;
  font-size: 10px;
}
    .login-wrap{
        position: relative;
        width:100%;
        height:100%;
        /*background-image: url(../../assets/login-bg.jpg);*/
        background-size: 100%;
    }
    .ms-title{
        width:100%;
        line-height: 50px;
        text-align: center;
        font-size:20px;
        color: #fff;
        border-bottom: 1px solid #ddd;
    }
    .ms-login{
        position: absolute;
        left:50%;
        top:50%;
        width:350px;
        height:300px;
        margin:-190px 0 0 -175px;
        border-radius: 5px;
        background: rgba(255,255,255, 0.3);
        overflow: hidden;
    }
    .ms-content{
        padding: 30px 30px;
    }
    .login-btn{
        text-align: center;
    }
    .login-btn button{
        width:100%;
        height:36px;
        margin-bottom: 10px;
    }
    .login-tips{
        font-size:12px;
        line-height:30px;
        color:#fff;
    }
    .ea{
        font-size: 50px;
        position: absolute;
        top: 20px;
        left: 20px;
    }
    
</style>