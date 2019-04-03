<template>
  <div class="table">
    <div class="crumbs">
      <el-button
        class="add-user"
        icon="el-icon-plus"
        type="primary"
        round
        @click="dialogFormVisible = true"
      >新增用户</el-button>
      <el-dialog title="新增用户" :visible.sync="dialogFormVisible" width="30%">
        <el-form :model="form" :rules="ruleValidate" ref="ruleForm">
          <el-form-item label="用户名" :label-width="formLabelWidth">
            <el-input v-model="form.user_name" autocomplete="off" prop="user_name"></el-input>
          </el-form-item>
          <el-form-item label="用户个人Email" :label-width="formLabelWidth">
            <el-input v-model="form.email" autocomplete="off" prop="email"></el-input>
          </el-form-item>
        </el-form>
        <div slot="footer" class="dialog-footer">
          <el-button @click="dialogFormVisible = false">取 消</el-button>
          <el-button type="primary" @click="addUser()">确 定</el-button>
        </div>
      </el-dialog>
    </div>
    <div class="container">
      <div class="handle-box">
        <el-button type="primary" icon="delete" class="handle-del mr10" @click="delAll">批量删除</el-button>
        <div class="search">
          <el-input v-model="select_word" placeholder="筛选关键词" class="handle-input mr10"></el-input>
          <el-button type="primary" icon="search" @click="search">搜索</el-button>
        </div>
      </div>
      <!-- :data="data1" -->
      <!-- :header-cell-style="{background:'#20a0ff',color:'#92ff00'}" -->
      <el-table
        :data="tableData"
        border
        class="table"
        ref="multipleTable"
        @selection-change="handleSelectionChange"
      >
        <el-table-column type="selection" :selectable="checkboxT" width="55" align="center"></el-table-column>
         <el-table-column prop="user_name" label="用户名" width="100"></el-table-column>
         <el-table-column prop="remark" label="备注" width="100"></el-table-column>
         <el-table-column prop="phone" label="手机号" width="150"></el-table-column>
         <el-table-column prop="email" label="邮箱号" width="200"></el-table-column>
         <el-table-column prop="hmi" label="匹配设备" width="280"></el-table-column>
        <el-table-column label="相关操作" width="350" align="center">
          <template slot-scope="scope">
 <el-button
             type="text"
              icon="el-icon-date"
              @click="dialogFormVisible1 = true"
            >设备管理</el-button>
      <el-dialog title="设备管理" :visible.sync="dialogFormVisible1" width="30%">
  <el-tabs v-model="activeName" @tab-click="handleClick">
    <el-tab-pane label="设备组" name="first">
  <div class="tr">
           <el-transfer v-model="value1" :data="data"></el-transfer>
           </div>
    </el-tab-pane>
    <el-tab-pane label="设备" name="second">
  <div class="tr">
           <el-transfer v-model="value2" :data="data2"></el-transfer>
           </div>
    </el-tab-pane>
  </el-tabs>
        <div slot="footer" class="dialog-footer">
          <el-button @click="dialogFormVisible1 = false">取 消</el-button>
          <el-button type="primary" @click="addUser()">确 定</el-button>
        </div>
      </el-dialog>

            <el-button
              type="text"
              icon="el-icon-edit"
              @click="handleEdit(scope.$index, scope.row)"
            >编辑</el-button>
            <!-- <el-dialog title="编辑" :visible.sync="dialogEdit" width="30%">

 <el-form :model="form" :rules="ruleValidate" ref="ruleForm">
          <el-form-item label="备注名" :label-width="formLabelWidth">
            <el-input v-model="form.remark" autocomplete="off" prop="remark"></el-input>
          </el-form-item>
          <el-form-item label="个人email" :label-width="formLabelWidth" >
            <el-input v-model="form.email" autocomplete="off" prop="email">
              <el-button slot="prepend" icon="el-icon-edit"></el-button>
            </el-input>
             <el-checkbox style="float:left" v-model="checked">寄送新密码</el-checkbox>
          </el-form-item>
        </el-form>
        <div slot="footer" class="dialog-footer">
          <el-button @click="dialogEdit = false">取 消</el-button>
          <el-button type="primary" @click="addUser()">确 定</el-button>
        </div>
      </el-dialog> -->

            <el-button
              type="text"
              icon="el-icon-delete"
              class="red"
              @click="handleDelete(scope.$index, scope.row)"
            >删除</el-button>
             <el-button
              type="text"
              icon="el-icon-close"
              class="red"
              @click="aa= true"
            >禁用</el-button>


          </template>
        </el-table-column>
      </el-table>
      <div class="pagination">
        <el-pagination
          background
          @current-change="handleCurrentChange"
          layout="prev, pager, next"
          :total="1000"
        ></el-pagination>
      </div>
    </div>

    <!-- 编辑弹出框 -->
        <el-dialog title="编辑" :visible.sync="editVisible" width="30%">
            <el-form ref="form" :model="form" label-width="50px">
                <el-form-item label="备注">
                    <el-input v-model="form.remark"></el-input>
                </el-form-item>
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
        name: 'basetable',
        data() {
          const generateData = _ => {
        const data = [];
        for (let i = 1; i <= 15; i++) {
          data.push({
            key: i,
            label: `设备组 ${ i }`,
            disabled: i % 4 === 0
          });
        }
        return data;
      };
         const generateData2 = _ => {
        const data2 = [];
        for (let i = 1; i <= 15; i++) {
          data2.push({
            key: i,
            label: `设备 ${ i }`,
            disabled: i % 4 === 0
          });
        }
        return data2;
      };
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
                checked: true,//寄送新用户密码
               activeName: 'first',
               data: generateData(),
                data2: generateData2(),
        value1: [1, 4],
        value2: [1, 4],
        dialogFormVisible: false,
        dialogFormVisible1: false,
        // dialogEdit:false,
        aa:false,
        //曾经form1
        // form: {
        //      name: '',
        //   email:"",
        //   remark:''
        // },
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
                    user_name: '',
                    email:'',
                    remark:'',
                    phone:'',
                    hmi:'',
                    domain_id:1
                },
                idx: -1
            }
        },
        created() {
            this.getData();
        },
        computed: {
            // data1() {
            //     return this.tableData.filter((d) => {
            //         let is_del = false;
            //         for (let i = 0; i < this.del_list.length; i++) {
            //           //res.data.message[0].user_name
            //             if (d.name === this.del_list[i].user_name) {
            //                 is_del = true;
            //                 break;
            //             }
            //         }
            //         if (!is_del) {
            //             if (d.address.indexOf(this.select_cate) > -1 &&
            //                 (d.name.indexOf(this.select_word) > -1 ||
            //                     d.address.indexOf(this.select_word) > -1)
            //             ) {
            //                 return d;
            //             }
            //         }
            //     })
            // }
        },
        methods: {
          //禁用
// checkboxT(row,index){
//     		if(row.status==0){
//     			return 1;
//     		}else{
//     			return 0;
//     		}
//     	},

            addUser(){
 this.$http.post('/api/user/addUser',
                {
                     user_name: this.form.user_name,
                     email: this.form.email,
                     domain_id: this.form.domain_id//域id
                }).then(res => {
            console.log(res)
            if(res.data.status=="S"){
                   this.$message({
          message: '新增用户成功',
          type: 'success'
        });
         this.getData();
        this.dialogFormVisible=false;
            }else if(res.data.status=="F"){
 this.$message({
          message: '该用户已存在',
          type: 'warning'
        });
            }

        }).catch(function(error) {
          alert(axios错误回调);
          console.log(error);
        });

            },
            // 分页导航
            handleCurrentChange(val) {
                this.cur_page = val;
                this.getData();
            },
            getData() {
              this.$http({
  method: 'get',
  url: '/api/user/userInfo',
    params: {
      domain_id: this.form.domain_id,
      limit: 10
  },
}).then((res) => {
                  // console.log(res.data.message[0].user_name)   输入h  
                    this.tableData = res.data.message;
                })
            },
            
            search() {
                this.is_search = true;
            },
            formatter(row, column) {
                return row.address;
            },
            filterTag(value, row) {
                return row.tag === value;
            },
            handleEdit(index, row) {
                this.idx = index;
                const item = this.tableData[index];
                this.form = {
                   // remark: item.remark,
                     user_name: item.user_name,
                    remark: item.remark,
                    phone: item.phone,
                    email: item.email,
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
                           this.$http({
  method: 'get',
  url: '/api/user/delete/1',
}).then((res) => {
                  // console.log(res.data.message[0].user_name)   输入h  
                  console.log(res);
                    //  this.tableData = res.data.message;
                })
                this.$message.success('删除成功');
                this.delVisible = false;
            }
        }
    }

</script>

<style >
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
  font-size: 14px;
}
.red {
  color: #ff0000;
}
.tr{
text-align: left;
}

.el-table td, .el-table th.is-leaf {
    border-bottom: 1px solid #dcdee2;
}

.el-table--border td {
    border-right: 1px solid #dcdee2;
}

</style>
