import axios from 'axios';
axios.interceptors.request.use(
        config => {
                this.shuzu3=this.getArrDifference(this.value2,this.shuzu2);
            return this.shuzu3;
        },
        err => {
            return Promise.reject(err);//请求错误时，直接结束
    
            //return Promise.resolve(err);//请求错误时，不会直接结束，将会继续传到then里面,无论请求成功还是失败，在成功的回调中都能收到通知
    
        });
    export default axios;