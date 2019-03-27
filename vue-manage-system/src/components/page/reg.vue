<template>

<el-form 
	class="card-box login-form"
	 ref="formValidate"
    :model="formValidate"
    :rules="ruleValidate">
    <router-link to="/login">
    <i class="el-icon-back"></i>
       </router-link>
	   <h1 class="reg-adm">注册管理员</h1>
    <br>
    <el-form-item label="账号名" prop="user_name">
      <el-input type="text" v-model="formValidate.user_name" placeholder="包括中文字、英文字母、数字和下划线"></el-input>
    </el-form-item>

    <el-form-item label="密码" prop="password">
      <el-input type="password" v-model="formValidate.password" placeholder="数字,小写字母,大写字母,特殊符号至少包含三种,长度为 8 - 30位"></el-input>
    </el-form-item>
    <el-form-item label="确认密码" prop="passwdCheck">
      <el-input type="password" v-model="formValidate.passwdCheck"></el-input>
    </el-form-item>
    <el-form-item label="姓名" prop="first_name">
      <el-input type="text" v-model="formValidate.first_name"></el-input>
    </el-form-item>
    <el-form-item label="电子邮箱" prop="email">
      <el-input v-model="formValidate.email" placeholder="我们将发送确认注册邮件至您的邮箱"></el-input>
    </el-form-item>
    <el-form-item label="手机号">
      <el-input v-model="formValidate.phone" placeholder="Enter your phone"></el-input>
    </el-form-item>
    
    <el-form-item label="所在地区">
     <v-distpicker  @province="onChangeProvince" @city="onChangeCity" @area="onChangeArea"></v-distpicker>
    </el-form-item>
    
      <!-- <FormItem label="省" prop="province">
      <Input v-model="formValidate.province"></Input>
    </FormItem>
      <FormItem label="市" prop="city">
      <Input v-model="formValidate.city"></Input>
    </FormItem>
      <FormItem label="区" prop="area">
      <Input v-model="formValidate.area"></Input>
    </FormItem> -->
    <el-form-item label="详细地址" prop="address">
      <el-input v-model="formValidate.address"></el-input>
    </el-form-item>
    <el-form-item label="公司名称" prop="company_name">
      <el-input v-model="formValidate.company_name"></el-input>
    </el-form-item>
    <el-form-item prop="interest">
      <el-checkbox-group v-model="formValidate.interest">
        <a>隐私权政策</a>
        <el-checkbox>同意</el-checkbox>
      </el-checkbox-group>
    </el-form-item>
    <el-form-item>
      <el-button
        type="primary"
        @click="zhuce()"
        style="margin-left: 350px;width:100px"
      >注册</el-button>
      <!-- <Button @click="handleReset('formValidate')" style="margin-left: 8px">重置</Button> -->
    </el-form-item>
</el-form>
</template>
<script>
	import VDistpicker from "v-distpicker";
	export default {
		 data() {
		 	   //手机号校验
    const validatephone=(rule, value, callback)=>{
       if (value && (!(/^[1][34578]\d{9}$/).test(value) || !(/^[1-9]\d*$/).test(value) || value.length !== 11)) {
      callback(new Error('手机号码不符合规范'))
    } else {
      callback()
    }
    };
     //邮箱校验
    const validatemail=(rule, value, callback)=>{
        let temp = /^[\w.\-]+@(?:[a-z0-9]+(?:-[a-z0-9]+)*\.)+[a-z]{2,3}$/
    let tempOne = /^[A-Za-zd]+([-_.][A-Za-zd]+)*@([A-Za-zd]+[-.])+[A-Za-zd]{2,5}$/
    if (value && (!(temp).test(value))) {
      callback(new Error('邮箱格式不符合规范'))
    } else {
      callback()
    }
    };
    //账户名校验
     const validatename=(rule, value, callback)=>{
       let acount = /^(?!_)(?!.*?_$)[a-zA-Z0-9_\u4e00-\u9fa5]+$/
    if (value && (!(acount).test(value))) {
      callback(new Error('账号不符合规范'))
    } else {
      callback()
    }
     }
    //密码校验
    const validatePass = (rule, value, callback) => {
      if (value === "") {
        callback(new Error("密码不能为空"));
      } else {
        if (this.formValidate.passwdCheck !== "") {
          // 对第二个密码框单独验证
          this.$refs.formValidate.validateField("passwdCheck");
        }
        callback();
      }
    };
    //二次输入密码校验
    const validatePassCheck = (rule, value, callback) => {
      if (value === "") {
        callback(new Error("请再一次确认您的密码"));
      } else if (value !== this.formValidate.password) {
        callback(new Error("两次输入密码不一致"));
      } else {
        callback();
      }
    };
		 	
		 	
		 	  return {
		 	  	 formValidate: {
        user_name: "",
        password: "",
        passwdCheck: "",
        first_name: "",
        email: "",
        phone: "",
        province:"",
        city:"",
        area:"",
        address:"",
        // area: "",
        // detail_area: "",
        company_name: "",
        interest: []
      },
       //匹配校验器
      ruleValidate: {
        password: [{ validator: validatePass, trigger: "blur" },
        { pattern: /^(?![a-zA-Z]+$)(?![A-Z0-9]+$)(?![A-Z\W_]+$)(?![a-z0-9]+$)(?![a-z\W_]+$)(?![0-9\W_]+$)[a-zA-Z0-9\W_]{8,30}$/, message: '密码为数字，小写字母，大写字母，特殊符号 至少包含三种，长度为 8 - 30位' }
        ],
        passwdCheck: [{ validator: validatePassCheck, trigger: "blur" }],
        phone: [
           {required: true, message: '请输入电话号码', trigger: 'blur'},
          { validator: validatephone, trigger: "blur" }],
           email: [
           {required: true, message: '请输入电子邮箱', trigger: 'blur'},
          { validator: validatemail, trigger: "blur" }],
        user_name: [{ required: true, message: "账号名不能为空", trigger: "blur" },
          { validator: validatename, trigger: "blur" }],
        city: [
          {
            required: true,
            message: "Please select the city",
            trigger: "change"
          }
        ],
        interest: [],
        date: [
          {
            required: true,
            type: "date",
            message: "Please select the date",
            trigger: "change"
          }
        ]
      }
		 	  }
		 },
		 methods: {
		 	onChangeProvince(a){
               this.formValidate.province=a.value;             
            },
            onChangeCity(a){
               this.formValidate.city=a.value;         
            },
            onChangeArea(a){
                  this.formValidate.area=a.value;         
            }, 
		 	a(){
		 		this.$router.push('/login');
		 	},
		 	zhuce(){
		 		 this.$http.post('/api/admin/register',
   {
   user_name:this.formValidate.user_name,
   email:this.formValidate.email,
   password:this.formValidate.password,
   first_name:this.formValidate.first_name,
   address:this.formValidate.address,
   company_name:this.formValidate.company_name,
   province:this.formValidate.province,
   city:this.formValidate.city,
   area:this.formValidate.area,
   phone:this.formValidate.phone
     }
     )
     .then(function (response) {
       alert(12);
     console.log(response);
      })
      .catch(function (error) {
        alert(34);
          console.log(error);
     }); 
		 	}
		 },
		   components: { VDistpicker },
	}
	
</script>

<style>
	.login-form {
  position: absolute;
  left: 0;
  right: 0;
  /* border: 1px red solid; */
  width: 700px;
  padding: 0px 35px 15px 35px;
  margin: 50px auto;
}
.el-icon-back{
  font-size: 38px;
  color: #409eff;
  position: relative;
float: left;
margin-left: -70px;
}
.reg-adm{
}
/* tab导航字体颜色 */
.el-tabs__item{
  color: #fff
}
</style>