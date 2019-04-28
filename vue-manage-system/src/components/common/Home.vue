<template>
    <div class="wrapper">
        <v-head></v-head>
        <v-sidebar></v-sidebar>
        <div class="content-box" :class="{'content-collapse':collapse}">
            <div class="content">
                <transition name="move" mode="out-in">
                        <router-view></router-view>
                </transition>
            </div>
        </div>
        <!-- <Organ dataList:"dataArray"></Organ> -->
    </div>
</template>

<script>
// import Organ from "@/components/OrganizationTree"
    import vHead from './Header.vue';
    import vSidebar from './Sidebar.vue';
    import vTags from './Tags.vue';
    import bus from './bus';
    export default {
        data(){
            return {
//                 dataArray:[
//   {
//     'name': '总经办',
//     'type': 'department',
//     'checked': false,
//     'children': [
//       {
//         'name': '总经理',
//         'type': 'human',
//         'checked': false,
//         'children': []
//       },
//       {
//         'name': '财务总监',
//         'type': 'human',
//         'checked': false,
//         'children': []
//       },
//       {
//         'name': '秘书处',
//         'type': 'department',
//         'checked': false,
//         'children': [
//           {
//             'name': '秘书1',
//             'type': 'human',
//             'checked': false,
//             'children': []
//           },
//           {
//             'name': '秘书2',
//             'type': 'human',
//             'checked': false,
//             'children': []
//           }
//         ]
//       }
//     ]
//   },
//   {
//     'name': '营销中心',
//     'type': 'department',
//     'checked': false,
//     'children': [
//       {
//         'name': '营销总监',
//         'type': 'human',
//         'checked': false,
//         'children': []
//       },
//       {
//         'name': '营销一部',
//         'type': 'department',
//         'checked': false,
//         'children': [
//           {
//             'name': '营销一部经理',
//             'type': 'human',
//             'checked': false,
//             'children': []
//           },
//           {
//             'name': '营销员A',
//             'type': 'human',
//             'checked': false,
//             'children': []
//           }
//         ]
//       }
//     ]
//   },
//   {
//     'name': '未分组人员',
//     'type': 'human',
//     'checked': false,
//     'children': []
//   }
// ],
                tagsList: [],
                collapse: false,
            }
        },
         components:{
            vHead, vSidebar, vTags
        },
        created(){
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
/* 右侧显示的样式 */
.content {
    box-shadow: 0 20px 35px -5px #bac2ce inset;
    background-color: #e5effe;
    margin-top: -10px;
}
</style>