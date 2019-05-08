import Vue from 'vue'
import Vuex from 'vuex'
Vue.use(Vuex);

var state = {
    count: 6,
    domainId: '',
    // trArray: []
}
//放 方法(主要用于改变state里面的数据)
var mutations = {
    incCount() {
        state.count++;
    },
    saveDomainId(state, data) {
        state.domainId = data
    },
    // savetrArray(state, data) {
    //     state.trArray = data
    // }
}
//vuex
const store = new Vuex.Store({
    state,
    mutations
})
//暴露出去
export default store;
