<template>
    <div id="app">
        <router-view></router-view>
        <el-tree :data="data" :props="defaultProps" @node-click="handleNodeClick"></el-tree>
    </div>
</template>
<style>
    @import "../static/css/main.css";
    /* 深色主题 */
    @import "../static/css/color-dark.css";     
    /* 浅绿色主题 */
    /* @import "../static/css/theme-green/color-green.css";    */
</style>