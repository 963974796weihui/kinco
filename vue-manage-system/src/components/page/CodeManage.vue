<template>
  <div class="table">
    <div class="crumbs">
      <a  target="_blank" href="http://39.104.56.173:8091/alipay" > <el-button
        class="add-user"
        icon="el-icon-plus"
        type="primary"
        round
        @click="buyCode()"
      >购买授权码</el-button></a>
     
      <!-- <el-dialog title="购买授权码" :visible.sync="dialogFormVisible" width="30%">
        <h3>支付宝付款</h3>
          <div class="two-div"><img class="two" src="static/img/two.jpg"></div>
        <div slot="footer" class="dialog-footer">
          <el-button @click="dialogFormVisible = false">取 消</el-button>
          <el-button type="primary" @click="addUser()">确 定</el-button>
        </div>
      </el-dialog> -->
    </div>
    <div class="container">
      <div class="handle-box">
        <el-button type="primary" icon="delete" class="handle-del mr10" @click="delAll">批量删除</el-button>
        <div class="search">
          <el-input v-model="select_word" placeholder="筛选关键词" class="handle-input mr10"></el-input>
          <el-button type="primary" icon="search" @click="search">搜索</el-button>
        </div>
      </div>
      <el-table
        :header-cell-style="tableHeaderColor"
        :data="tableData"
        border
        class="table"
        ref="multipleTable"
        @selection-change="handleSelectionChange"
      >
        <el-table-column type="selection" width="55" align="center"></el-table-column>
         <el-table-column prop="sncode" label="授权码" width="170"></el-table-column>
         <el-table-column prop="long" label="有效期" width="170"></el-table-column>
         <el-table-column prop="activate_time" label="激活时间" width="220"></el-table-column>
         <el-table-column prop="bind" label="绑定情况" width="220"></el-table-column>
          <el-table-column label="相关操作" width="350" align="center">
              <template slot-scope="scope">
            <el-button
              type="text"
              icon="el-icon-date"
              @click="bindHmi(scope.$index, scope.row)"
            >绑定设备</el-button>
            </template>
            </el-table-column>
      </el-table>
      <div class="pagination">
        <el-pagination
          background
          @current-change="handleCurrentChange"
          layout="prev, pager, next"
          :total="total"
        ></el-pagination>
      </div>
    </div>

    <!-- 编辑弹出框 -->
    <el-dialog title="编辑" :visible.sync="editVisible" width="30%">
      <el-form ref="form" :model="form" label-width="50px">
        <el-form-item label="日期">
          <el-date-picker
            type="date"
            placeholder="选择日期"
            v-model="form.date"
            value-format="yyyy-MM-dd"
            style="width: 100%;"
          ></el-date-picker>
        </el-form-item>
        <el-form-item label="姓名">
          <el-input v-model="form.name"></el-input>
        </el-form-item>
        <!-- <el-form-item label="地址">
          <el-input v-model="form.address"></el-input>
        </el-form-item> -->
      </el-form>
      <span slot="footer" class="dialog-footer">
        <el-button @click="editVisible = false">取 消</el-button>
        <el-button type="primary" @click="saveEdit">确 定</el-button>
      </span>
    </el-dialog>

    <!-- 删除提示框 -->
    <el-dialog title="提示" :visible.sync="delVisible" width="300px" center>
      <div class="del-dialog-cnt">删除不可恢复，是否确定删除？</div>
      <span slot="footer" class="dialog-footer">
        <el-button @click="delVisible = false">取 消</el-button>
        <el-button type="primary" @click="deleteRow">确 定</el-button>
      </span>
    </el-dialog>
  </div>
</template>

<script>
    export default {
        name: 'codemanage',
        data() {
//   //邮箱校验
//     const validatemail=(rule, value, callback)=>{
//         let temp = /^[\w.\-]+@(?:[a-z0-9]+(?:-[a-z0-9]+)*\.)+[a-z]{2,3}$/
//     let tempOne = /^[A-Za-zd]+([-_.][A-Za-zd]+)*@([A-Za-zd]+[-.])+[A-Za-zd]{2,5}$/
//     if (value && (!(temp).test(value))) {
//       callback(new Error('邮箱格式不符合规范'))
//     } else {
//       callback()
//     }
//     };
//     //账户名校验
//      const validatename=(rule, value, callback)=>{
//        let acount = /^(?!_)(?!.*?_$)[a-zA-Z0-9_\u4e00-\u9fa5]+$/
//     if (value && (!(acount).test(value))) {
//       callback(new Error('账号不符合规范'))
//     } else {
//       callback()
//     }
//      };
            return {
              total:'',
         dialogTableVisible: false,
        // dialogFormVisible: false,
        form1: {
             name: '',
          email:""
        },
        //匹配校验器
//       ruleValidate: {
//    name: [{ required: true, message: "账号名不能为空", trigger: "blur" },
//           { validator: validatename, trigger: "blur" }],
//            email: [
//            {required: true, message: '请输入电子邮箱', trigger: 'blur'},
//           { validator: validatemail, trigger: "blur" }],

//       },
        formLabelWidth: '120px',
                // url: './static/vuetable.json',
                // tableData1:[],
                tableData: [],
                cur_page: 1,
                multipleSelection: [],
                select_cate: '',
                select_word: '',
                del_list: [],
                is_search: false,
                editVisible: false,
                delVisible: false,
                form: {
                   sncode: '',
                    long:'',
                    activate_time:'',
                    bind:''
                },
                idx: -1
            }
        },
        created() {
            this.getData();
        },
        computed: {
            data1() {
                return this.tableData.filter((d) => {
                    let is_del = false;
                    for (let i = 0; i < this.del_list.length; i++) {
                        if (d.name === this.del_list[i].name) {
                            is_del = true;
                            break;
                        }
                    }
                    if (!is_del) {
                        if (d.address.indexOf(this.select_cate) > -1 &&
                            (d.name.indexOf(this.select_word) > -1 ||
                                d.address.indexOf(this.select_word) > -1)
                        ) {
                            return d;
                        }
                    }
                })
            }
        },
        methods: {
            //表头样式
     tableHeaderColor({ row, column, rowIndex, columnIndex }) {
      if (rowIndex === 0) {
        return 'background-color: #9cba64;color: #f0f0f0;font-weight:10;'
      }
    },
          buyCode(){

          },
            addUser(){
                 this.$http.post('/api/admin/register',
   {
name:this.form1.user_name,
   email:this.form1.email,
     }
     )
     .then(function (response) {
     console.log(response);
      })
      .catch(function (error) {
          console.log(error);
     }); 
            },
            // 分页导航
            handleCurrentChange(val) {
                this.cur_page = val;
                this.getData();
            },
            // 获取 easy-mock 的模拟数据
            getData() {
               // 开发环境使用 easy-mock 数据，正式环境使用 json 文件
          //       if (process.env.NODE_ENV === 'development') {
          //           this.url = '/ms/table/list';
          //       };
          //       this.$axios.post(this.url, {
          //           page: this.cur_page
          //       }).then((res) => {
          //           this.tableData = res.data.list;
          //       })
          // alert(this.$store.state.domainId)
                       this.$http({
  method: 'post',
  url: '/api/AuthCode/codeInfo',
    data: {
      limit: 10,
      page: this.cur_page,
      // user_id:164
  },
}).then((res) => {
    // console.log(5555555555);
    //               console.log(res);
    //               console.log(res.data.message)
    this.total=res.data.total;
                   this.tableData = res.data.data;
                });

            },
            search() {
                this.is_search = true;
            },
            // formatter(row, column) {
            //     return row.address;
            // },
            filterTag(value, row) {
                return row.tag === value;
            },
            handleEdit(index, row) {
                this.idx = index;
                const item = this.tableData[index];
                this.form = {
                    name: item.name,
                    date: item.date,
                    // address: item.address
                }
                this.editVisible = true;
            },
            handleDelete(index, row) {
                this.idx = index;
                this.delVisible = true;
            },
            delAll() {
                const length = this.multipleSelection.length;
                let str = '';
                this.del_list = this.del_list.concat(this.multipleSelection);
                for (let i = 0; i < length; i++) {
                    str += this.multipleSelection[i].name + ' ';
                }
                this.$message.error('删除了' + str);
                this.multipleSelection = [];
            },
            handleSelectionChange(val) {
                this.multipleSelection = val;
            },
            // 保存编辑
            saveEdit() {
                this.$set(this.tableData, this.idx, this.form);
                this.editVisible = false;
                this.$message.success(`修改第 ${this.idx+1} 行成功`);
            },
            // 确定删除
            deleteRow(){
                this.tableData.splice(this.idx, 1);
                this.$message.success('删除成功');
                this.delVisible = false;
            }
        }
    }

</script>

<style scoped>
.search {
  float: right;
}
.handle-box {
  margin-bottom: 20px;
}

.handle-select {
  width: 120px;
}

.handle-input {
  width: 300px;
  display: inline-block;
}
.del-dialog-cnt {
  font-size: 16px;
  text-align: center;
}
.table {
  width: 100%;
  font-size: 18px;
}
.red {
  color: #ff0000;
}
.two{
  width: 200px;
  height: 200px;
  margin-left: 200px;
    }
</style>
