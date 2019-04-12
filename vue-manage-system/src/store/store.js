import Vue from 'vue'
import Vuex from 'vuex'
Vue.use(Vuex);

var state={
    count:5,
    domainId:'',
    aaaa:100
}
//放 方法(主要用于改变state里面的数据)
var mutations={
    incCount(){
        state.count++;
    },
    saveDomainId(state,data){
state.domainId=data
    }
}
//vuex
const store=new Vuex.Store({
    state,
    mutations
})
//暴露出去
export default store;
