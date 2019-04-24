<template>
    <div class="header">
        <!-- 折叠按钮 -->
          <el-tooltip class="item" effect="dark" content="进行缩放" placement="top-start">
        <div class="collapse-btn" @click="collapseChage">
            <i class="el-icon-menu"></i>
        </div>
    </el-tooltip>
      
        <p class="logo">EdgeAccess</p>
        <!-- <h3>{{this.$store.state.domainId}}</h3> -->
        <el-button class="create-region" icon="el-icon-plus" size="medium" type="danger" @click="dialogFormVisible = true">新建域</el-button>
        <el-dialog title="新建域" :visible.sync="dialogFormVisible" width="30%">
        <el-form :model="formRegion">
          <el-form-item label="域名" :label-width="formLabelWidth">
            <el-input v-model="formRegion.name"  autocomplete="off" prop="name"></el-input>
          </el-form-item>
           <el-form-item label="域简介" :label-width="formLabelWidth">
            <el-input type="textarea" placeholder="80字以内" v-model="formRegion.regionInfo"  autocomplete="off" prop="regionInfo"
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
                <!-- 全屏显示 -->
                    <h6>全屏</h6>
                <div class="btn-fullscreen" @click="handleFullScreen">
                    <el-tooltip effect="dark" :content="fullscreen?`取消全屏`:`全屏`" placement="bottom">
                        <i class="el-icon-rank"></i>
                    </el-tooltip>
                </div>
                <!-- 消息中心 -->
                <!-- <div class="btn-bell">
                    <el-tooltip effect="dark" :content="message?`有${message}条未读消息`:`消息中心`" placement="bottom">
                        <router-link to="/tabs">
                            <i class="el-icon-bell"></i>
                        </router-link>
                    </el-tooltip>
                    <span class="btn-bell-badge" v-if="message"></span>
                </div> -->
                <!-- 用户头像 -->
                <div class="user-avator"><img src="static/img/img.jpg"></div>
                <!-- 用户名下拉菜单 -->
                <el-dropdown class="user-name" trigger="click" @command="handleCommand">
                    <span class="el-dropdown-link">
                        {{username}} <i class="el-icon-caret-bottom"></i>
                    </span>
                    <el-dropdown-menu slot="dropdown">
                        <!-- <a href="http://blog.gdfengshuo.com/about/" target="_blank">
                            <el-dropdown-item>关于作者</el-dropdown-item>
                        </a>
                        <a href="https://github.com/lin-xin/vue-manage-system" target="_blank">
                            <el-dropdown-item>项目仓库</el-dropdown-item>
                        </a> -->
                        <el-dropdown-item divided command="loginout">退出登录</el-dropdown-item>
                    </el-dropdown-menu>
                </el-dropdown>
            </div>
        </div>
    </div>
</template>
<script>
import store from '../../store/store.js'
    import bus from '../common/bus';
    export default {
        data() {
            return {
                formRegion:{
                    name:'',
                    regionInfo:''
                },
                 dialogTableVisible: false,
                 dialogFormVisible: false,
                collapse: false,
                fullscreen: false,
                name: '无名氏',
                message: 2,
                num1:0,
                num2:1,
                itemRegion:[],
            }
        },
        store,
    created(){
// this.$store.commit('saveMenuList',this.itemRegion)
        //   alert(111111111)
        },
computed:{
            username(){
                let username = localStorage.getItem('ms_username');
                return username ? username : this.name;
            }
        },
        methods:{
            SomeJavaScriptCode(){
                 this.$http.post('/api/admin/registerDomain',
                {
                     domain_name: this.formRegion.name,
                     remark: this.formRegion.regionInfo
                }).then(res => {
            console.log(res)
            if(res.data.status=="S"){
                   this.$message({
          message: '新建域成功',
          type: 'success'
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
            }else if(res.data.status=="F"){
 this.$message({
          message: '该域已存在',
          type: 'warning'
        });
            }
        })
            },
            //新建域
            addRegion(){
this.$http({
      method: "post",
      url: "/api/admin/login",
      data: {
        user_name:localStorage.getItem('ms_username'),
        password:localStorage.getItem('ms_password')
      }
    }).then(res => {
        if (res.data.status == "S") {
                  this.$message({
              message: "新建域成功   !",
              type: "success"
            });
        }
        const domain_id=res.data.message[0].id;
         const domain_name=res.data.message[0].domain_name;
// console.log(res.data.message[0].domain_name)
 this.itemRegion=
[
                    // {
                    //     icon: 'el-icon-lx-home',
                    //     //index关联路由数组对象中的路径path
                    //     index: 'dashboard',
                    //     title: '系统首页'
                    // },
                    {
                        icon: 'el-icon-lx-cascades',
                        index: 'codemanage',
                        title: '授权码管理'
                    },
                      {
                        icon: 'el-icon-lx-calendar',
                        title: domain_name,
                        index: domain_id,
                        subs: [
                            {
                                index: 'usermanage',
                                title: '用户'
                            },
                            {
                                index: this.num2++,
                                title: '设备',
                                subs: [
                                    {
                                        index: 'eqmanage',
                                        title: '设备管理'
                                    },
                                    {
                                        index: 'eqgroup',
                                        title: '设备群组'
                                    },
                                ]
                            }
                        ]
                    }
]
     
//                     var objStr=JSON.stringify(this.itemRegion);
// localStorage.setItem('aa',objStr);
                     bus.$emit('items', this.itemRegion)
    });
 this.dialogFormVisible=false;

            },
            // 用户名下拉菜单选择事件
            handleCommand(command) {
                if(command == 'loginout'){
                    localStorage.removeItem('ms_username')
                    this.$router.push('/login');
                }
            },
            // 侧边栏折叠
            collapseChage(){
                this.collapse = !this.collapse;
                bus.$emit('collapse', this.collapse);
            },
            // 全屏事件
            handleFullScreen(){
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
        mounted(){
          
            if(document.body.clientWidth < 1500){
                this.collapseChage();
            }
        },
        
    }
</script>
<style scoped>
.create-region{
    margin-top: 15px;
}
    .header {
        background-color: #5e99e9;
        position: relative;
        box-sizing: border-box;
        width: 100%;
        height: 70px;
        font-size: 22px;
        color: #fff;
    }
    .collapse-btn{
        float: left;
        padding: 0 21px;
        cursor: pointer;
        line-height: 70px;
    }
    .header .logo{
        font-size:30px;
        float: left;
        width:250px;
        line-height: 70px;
    }
    .header-right{
        float: right;
        padding-right: 50px;
    }
    .header-user-con{
        display: flex;
        height: 70px;
        align-items: center;
    }
    .btn-fullscreen{
        transform: rotate(45deg);
        margin-right: 5px;
        font-size: 24px;
    }
    .btn-bell, .btn-fullscreen{
        position: relative;
        width: 30px;
        height: 30px;
        text-align: center;
        border-radius: 15px;
        cursor: pointer;
    }
    .btn-bell-badge{
        position: absolute;
        right: 0;
        top: -2px;
        width: 8px;
        height: 8px;
        border-radius: 4px;
        background: #f56c6c;
        color: #fff;
    }
    .btn-bell .el-icon-bell{
        color: #fff;
    }
    .user-name{
        margin-left: 10px;
    }
    .user-avator{
        margin-left: 20px;
    }
    .user-avator img{
        display: block;
        width:40px;
        height:40px;
        border-radius: 50%;
    }
    .el-dropdown-link{
        color: #fff;
        cursor: pointer;
    }
    .el-dropdown-menu__item{
        text-align: center;
    }
</style>
