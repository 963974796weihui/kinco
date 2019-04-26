<template>
    <div class="wrapper">
        <v-head></v-head>
        <v-sidebar></v-sidebar>
        <div class="content-box" :class="{'content-collapse':collapse}">
            <!-- <v-tags></v-tags> -->
            <div class="content">
                <transition name="move" mode="out-in">
                    <!-- <keep-alive :include="tagsList"> -->
                        <router-view></router-view>
                    <!-- </keep-alive> -->
                </transition>
            </div>
        </div>
    </div>
</template>

<script>
    import vHead from './Header.vue';
    import vSidebar from './Sidebar.vue';
    import vTags from './Tags.vue';
    import bus from './bus';
    export default {
        data(){
            return {
                tagsList: [],
                collapse: false,
            }
        },
         components:{
            vHead, vSidebar, vTags
        },
        created(){
// this.$http({
//       method: "post",
//       url: "/api/admin/login",
//       data: {
//         user_name:localStorage.getItem('ms_username'),
//         password:localStorage.getItem('ms_password')
//       }
//     }).then(res => {
//         const domain_id=res.data.message[0].id;
//          const domain_name=res.data.message[0].domain_name;
// // console.log(res.data.message[0].domain_name)
//  this.parent1=
//        {
//                         icon: 'el-icon-lx-calendar',
//                         title: domain_name,
//                         index: domain_id,
//                         subs: [
//                             {
//                                 index: 'usermanage',
//                                 title: '用户'
//                             },
//                             {
//                                 index: this.num2++,
//                                 title: '设备',
//                                 subs: [
//                                     {
//                                         index: 'eqmanage',
//                                         title: '设备管理'
//                                     },
//                                     {
//                                         index: 'eqgroup',
//                                         title: '设备群组'
//                                     },
//                                 ]
//                             }
//                         ]
//                     }
//                     //使浏览器静止的代码
//                 //   bus.$emit('parent1', this.parent1)  

//     });
   

            bus.$on('collapse', msg => {
                this.collapse = msg;
            })

            // 只有在标签页列表里的页面才使用keep-alive，即关闭标签之后就不保存到内存中了。
            bus.$on('tags', msg => {
                let arr = [];
                for(let i = 0, len = msg.length; i < len; i ++){
                    msg[i].name && arr.push(msg[i].name);
                }
                this.tagsList = arr;
            })
        }
    }
</script>
<style>
.content {
    box-shadow: 0 20px 35px -5px #bac2ce inset;
    background-color: #e5effe;
}
</style>