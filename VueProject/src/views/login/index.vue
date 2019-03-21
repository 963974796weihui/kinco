<template>
<div class="login-container" style="background-color: #243456;margin: 0px;overflow: hidden;">
    <div id="canvascontainer" ref='can'></div>
    <h1 class="white distance">EdgeAccess</h1>
    <Form ref="loginForm" autoComplete="on" :model="loginForm" :rules="loginRules"  class="card-box login-form">
<Tabs value="name1">
        <TabPane label="管理员" name="name1">
 <FormItem  prop="user_name">
            <Input type="text" v-model="loginForm.user_name" placeholder="Domain" autoComplete="on">
                <Icon type="person" slot="prepend" ></Icon>
            </Input>
        </FormItem>
        <FormItem prop="password">
            <Input type="password" v-model="loginForm.password" placeholder="Password" @keyup.enter.native="handleLogin">
                <Icon type="android-lock" slot="prepend"></Icon>
            </Input>
        </FormItem>
        <Checkbox label="twitter">
            <span>记住密码</span>
        </Checkbox>
            <Button type="primary"  @click="handleLogin('loginForm')" long>登录</Button>
            <br>
            <router-link to="/register">
         <p class="primary white admin">建立管理员账号</p></router-link>

        </TabPane>
        <TabPane label="普通用户" name="name2">
<FormItem prop="email">
            <Input type="text" v-model="loginForm.email" placeholder="Username" autoComplete="on">
                <Icon type="ios-person-outline" slot="prepend" ></Icon>
            </Input>
        </FormItem>
        <FormItem prop="password">
            <Input type="password" v-model="loginForm.password" placeholder="Password" @keyup.enter.native="handleLogin">
                <Icon type="ios-locked-outline" slot="prepend"></Icon>
            </Input>
        </FormItem>
            <Button type="primary" long>修改密码</Button>
            <br><br>
         <span class="white admin">未收到注册信/忘记密码</span>
        </TabPane>
    </Tabs>
           </Form>
  </div> 
</template>
<script>
    import { isWscnEmail } from 'utils/validate';

    export default {
      name: 'login',
      data() {
            history.pushState(null, null, document.URL);
window.addEventListener('popstate', function () {
    history.pushState(null, null, document.URL);
});
        const validateEmail = (rule, value, callback) => {
          if (!isWscnEmail(value)) {
            callback(new Error('请输入正确的合法邮箱'));
          } else {
            callback();
          }
        };
          const validatePass = (rule, value, callback) => {
          if (value.length < 6) {
            callback(new Error('密码不能小于6位'));
          } else {
            callback();
          }
        };
        
    //      //账户名校验
    //  const validatename=(rule, value, callback)=>{
    //    let acount = /^(?!_)(?!.*?_$)[a-zA-Z0-9_\u4e00-\u9fa5]+$/
    // if (value && (!(acount).test(value))) {
    //   callback(new Error('账号不符合规范'))
    // } else {
    //   callback()
    // }
    //  };

    // //密码校验
    // const validatePass = (rule, value, callback) => {
    //   if (value === "") {
    //     callback(new Error("密码不能为空"));
    //   } else {
    //   callback()
    // }
    // };
        return {
          loginForm: {
            user_name: '',
            password: ''
          },
          loginRules: {
          user_name:[{required: true, trigger: 'blur'}],
            // email: [
            //     { required: true, trigger: 'blur', validator: validateEmail }
            // ],
            password: [
                // { required: true, trigger: 'blur', validator: validatePass }
                 { required: true, trigger: 'blur'}
            ]
          },
          loading: false,
          showDialog: false
        }
      },
      //动画
       mounted () {
        container = document.createElement( 'div' );
   this.$refs.can.appendChild( container );  

  camera = new THREE.PerspectiveCamera( 75, window.innerWidth / window.innerHeight, 1, 10000 );
  camera.position.z = 1000;

  scene = new THREE.Scene();

  particles = new Array();

  var PI2 = Math.PI * 2;
  var material = new THREE.ParticleCanvasMaterial( {

    color: 0x0078de,
    program: function ( context ) {

      context.beginPath();
      context.arc( 0, 0, 1, 0, PI2, true );
      context.fill();
    }
  } );
  var i = 0;
  for ( var ix = 0; ix < AMOUNTX; ix ++ ) {

    for ( var iy = 0; iy < AMOUNTY; iy ++ ) {

      particle = particles[ i ++ ] = new THREE.Particle( material );
      particle.position.x = ix * SEPARATION - ( ( AMOUNTX * SEPARATION ) / 2 );
      particle.position.z = iy * SEPARATION - ( ( AMOUNTY * SEPARATION ) / 2 );
      scene.add( particle );
    }
  }

  renderer = new THREE.CanvasRenderer();
  renderer.setSize( window.innerWidth, window.innerHeight );
  container.appendChild( renderer.domElement );

  document.addEventListener( 'mousemove', onDocumentMouseMove, false );
  //

  window.addEventListener( 'resize', onWindowResize, false );

animate();
       },
      methods: {
        //登录按钮
        handleLogin() {
          this.$refs.loginForm.validate(valid => {
            //如果验证成功
            if (valid) {
              // this.loading = true;
              // //异步操作
              // this.$store.dispatch('LoginByEmail', this.loginForm).then(() => {
              //   this.$Message.success('登录成功');
                
              //   this.loading = false;
              //   this.$router.push({ path: '/' });
              // }).catch(err => {
              //   this.$message.error(err);
              //   this.loading = false;
              // });
 this.$http.post('/api/admin/login',
   {
   user_name:this.loginForm.user_name,
   password:this.loginForm.password,
     }
     )
     .then((response)=>{
        this.$router.push({path:'/newdomain'});
     console.log(response);
      }).catch(function (error) {
        alert(axios错误回调);
          console.log(error);
     }); 



            } else {
              console.log('error submit!!');
              return false;
            }
          });
        },
      },
    }

var SEPARATION = 100, AMOUNTX = 50, AMOUNTY = 50;

var container;
var camera, scene, renderer;

var particles, particle, count = 0;

var mouseX = 0, mouseY = 0;

var windowHalfX = window.innerWidth / 2;
var windowHalfY = window.innerHeight / 2;


// animate();

function init() {

  

}

function onWindowResize() {

  windowHalfX = window.innerWidth / 2;
  windowHalfY = window.innerHeight / 2;

  camera.aspect = window.innerWidth / window.innerHeight;
  camera.updateProjectionMatrix();

  renderer.setSize( window.innerWidth, window.innerHeight );

}


function onDocumentMouseMove( event ) {

  mouseX = event.clientX - windowHalfX;
  mouseY = event.clientY - windowHalfY;

}

function onDocumentTouchStart( event ) {

  if ( event.touches.length === 1 ) {

    event.preventDefault();

    mouseX = event.touches[ 0 ].pageX - windowHalfX;
    mouseY = event.touches[ 0 ].pageY - windowHalfY;

  }

}

function onDocumentTouchMove( event ) {

  if ( event.touches.length === 1 ) {

    event.preventDefault();

    mouseX = event.touches[ 0 ].pageX - windowHalfX;
    mouseY = event.touches[ 0 ].pageY - windowHalfY;

  }

}

//

function animate() {

  requestAnimationFrame( animate );

  render();


}

function render() {

  camera.position.x += ( mouseX - camera.position.x ) * .05;
  camera.position.y += ( - mouseY - camera.position.y ) * .05;
  camera.lookAt( scene.position );

  var i = 0;

  for ( var ix = 0; ix < AMOUNTX; ix ++ ) {

    for ( var iy = 0; iy < AMOUNTY; iy ++ ) {

      particle = particles[ i++ ];
      particle.position.y = ( Math.sin( ( ix + count ) * 0.3 ) * 50 ) + ( Math.sin( ( iy + count ) * 0.5 ) * 50 );
      particle.scale.x = particle.scale.y = ( Math.sin( ( ix + count ) * 0.3 ) + 1 ) * 2 + ( Math.sin( ( iy + count ) * 0.5 ) + 1 ) * 2;

    }

  }

  renderer.render( scene, camera );

  count += 0.1;

}
</script>
<style scope>
.distance{
  margin-top: 20px;
  margin-left: 20px;
}
.login{
 display:block;
 margin:0 auto;
 width: 100%
}
.white{
  color: azure
}
.ivu-tabs-nav,.ivu-checkbox+span {
   color: azure
}
.login-container a{color:#0078de;}
#canvascontainer{
  position: absolute;
  top: 0px;
}
.wz-input-group-prepend{
  background-color: #141a48;
   border: 1px solid #2d8cf0;
   border-right: none;
   color:  #2d8cf0;
}

</style>

<style rel="stylesheet/scss" lang="scss" scope>
.admin{
  float: right;
  font-size: 15px
}
     .tips{
      font-size: 14px;
      color: #fff;
      margin-bottom: 5px;
    } 
    .login-container {
        height: 100vh;
        background-color: #2d3a4b;

        input:-webkit-autofill {
            -webkit-box-shadow: 0 0 0px 1000px #293444 inset !important;
            -webkit-text-fill-color: #fff !important;
        }
        input {
            background: transparent;
            border: 1px solid #2d8cf0;
            -webkit-appearance: none;
            border-radius: 3px;
            padding: 12px 5px 12px 15px;
            color: #eeeeee;
            height: 47px;
        }
        .el-input {
            display: inline-block;
            height: 47px;
            width: 85%;
        }
        .svg-container {
            padding: 6px 5px 6px 15px;
            color: #889aa4;
        }

        .title {
            font-size: 26px;
            font-weight: 400;
            color: #eeeeee;
            margin: 0px auto 40px auto;
            text-align: center;
            font-weight: bold;
        }

        .login-form {
            position: absolute;
            left: 0;
            right: 0;
            width: 400px;
            padding: 35px 35px 15px 35px;
            margin: 120px auto;
        }

        .el-form-item {
            border: 1px solid rgba(255, 255, 255, 0.1);
            background: rgba(0, 0, 0, 0.1);
            border-radius: 5px;
            color: #454545;
        }

        .forget-pwd {
            color: #fff;
        }
    }

</style>
