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
        <!-- <p>{{this.domain_id}}</p> -->
        <el-button type="primary" icon="delete" class="handle-del mr10" @click="delAll">批量删除</el-button>
        <div class="search">
          <el-input v-model="select_word" placeholder="筛选关键词" class="handle-input mr10"></el-input>
          <el-button type="primary" icon="search" @click="search">搜索</el-button>
        </div>
      </div>
      <!-- :data="data1" -->
      <!-- :header-cell-style="{background:'#20a0ff',color:'#92ff00'}" -->
      <el-table
        :data="data1"
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
        <el-table-column prop="group" label="匹配设备组" width="280"></el-table-column>
        <el-table-column prop="hmi" label="匹配设备" width="280"></el-table-column>
        <el-table-column label="相关操作" width="350" align="center">
          <template slot-scope="scope">
            <el-button type="text" icon="el-icon-date" @click="handleHmi(scope.$index, scope.row)">设备管理</el-button>
          
            <el-dialog title="设备管理" :visible.sync="dialogFormVisible1" width="30%">
              <el-tabs v-model="activeName" @tab-click="handleClick">
                <el-tab-pane label="设备组" name="first">
                  <div class="tr">
                    <el-transfer v-model="value1" :data="dataGroup" filterable
                    @change="hmiGroupChange"
                    ></el-transfer>
                     <div slot="footer" class="dialog-footer">
                <el-button @click="dialogFormVisible1 = false">取 消</el-button>
                <el-button type="primary" @click="saveHmiGroup()">确 定</el-button>
              </div>
                  </div>
                </el-tab-pane>
                <el-tab-pane label="设备" name="second">
                  <div class="tr">
                    <el-transfer v-model="value2" :data="dataHmi" filterable
                    @change="hmiChange"
                    ></el-transfer>
                        <div slot="footer" class="dialog-footer">
                <el-button @click="dialogFormVisible1 = false">取 消</el-button>
                <el-button type="primary" @click="saveHmi()">确 定</el-button>
              </div>
                  </div>
                </el-tab-pane>
              </el-tabs>
            </el-dialog>

            <el-button
              type="text"
              icon="el-icon-edit"
              @click="handleEdit(scope.$index, scope.row)"
            >编辑</el-button>
            <el-button
              type="text"
              icon="el-icon-delete"
              class="red"
              @click="handleDelete()"
            >删除</el-button>
            <el-button type="text" icon="el-icon-close" class="red" @click="aa= true">禁用</el-button>
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
    <el-dialog title="编辑" :visible.sync="editVisible" width="30%" >
      <el-form ref="form" :model="form" label-width="50px" >
        <el-form-item label="备注">
          <el-input v-model="form.remark" @change="SomeJavaScriptCode"></el-input>
        </el-form-item>
      </el-form>
      <span slot="footer" class="dialog-footer" >
        <el-button @click="editVisible = false">取 消</el-button>
        <el-button type="primary" @click="saveEdit()">确 定</el-button>
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
import store from '../../store/store.js'
// import bus from "../common/bus";
export default {
  name: "basetable",
  data() {
    return {
        form: {
        user_name: "",
        email: "",
        remark: "",
        phone: "",
        hmi: "",
        group:"",
        // domain_id:this.$store.state.domainId,
        //计算属性
        domain_id:this.domain_id,
        id:""
      },
      // ccc:this.$store.state.domainId,
      // ccc:"小明",
      checked: true, //寄送新用户密码
      activeName: "first",
      dataGroup: [],
      dataHmi: [],
      //穿梭框
      value1:[],
      value2:[],
      // item:"",
      receiveGroup: [],
      dialogFormVisible: false,
      dialogFormVisible1: false,
      aa: false,
      formLabelWidth: "120px",
      // url: './static/vuetable.json',
      tableData: [],
      cur_page: 1,
      multipleSelection: [],
      trUser: [],
      select_cate: "",
      select_word: "",
      del_list: [],
      is_search: false,
      editVisible: false,
      delVisible: false,
      idx: -1
    };
  },
  store,
  created() {
   
    this.getData();
  },
  mounted() {
      // alert( this.$store.state.domainId)
    this.$http({
      method: "post",
      url: "/api/user/supplyGroup",
      data: {
        id: this.form.domain_id
      }
    }).then(res => {
      for (var i = 0; i < res.data.message.length; i++) {
        this.dataGroup.push({
          // key: i + 1,
          key: res.data.message[i].id,
          label: res.data.message[i].group_name,
        });
      }
    });

    //调用获取所有设备 接口
    this.$http({
      method: "post",
      url: "/api/user/hmiGroup",
      data: {
        id: this.form.domain_id
      }
    }).then(res => {
      for (var i = 0; i < res.data.message.length; i++) {
        this.dataHmi.push({ key: res.data.message[i].id, label: res.data.message[i].hmi_name });
      }
    });
  },
       computed: {
domain_id(){
  return this.$store.state.domainId
  },
         
            data1() {
                return this.tableData.filter((d) => {
                    let is_del = false;
                    for (let i = 0; i < this.del_list.length; i++) {
                        if (d.user_name === this.del_list[i].user_name) {
                            is_del = true;
                            break;
                        }
                    }
                    if (!is_del) {
                        if (d.user_name.indexOf(this.select_cate) > -1 &&
                            (d.user_name.indexOf(this.select_word) > -1 ||
                                d.user_name.indexOf(this.select_word) > -1)
                        ) {
                            return d;
                        }
                    }
                })
            }
        },
  methods: {
    //穿梭框的hmigroupchange事件
    hmiGroupChange(){
      this.$http({
        method: "post",
        url: "/api/user/supplyGroupBind",
        data: {
           domain_id: this.form.domain_id,
          user_id: this.form.id,
          id:this.value1
        }
      }).then(res => {
       
      })
    },
    //穿梭框的hmichange事件
    hmiChange(){
      console.log("777");
      console.log(this.form.domain_id);
      console.log(this.form.id);
      this.$http({
        method: "post",
        url: "/api/user/hmiGroupBind",
        data: {
           domain_id: this.form.domain_id,
          user_id: this.form.id,
          id:this.value2
        }
      }).then(res => {
       
      })
    },
    // 编辑的change事件
    SomeJavaScriptCode(){
        this.$http({
        method: "post",
        url: "/api/user/updateInfo",
        data: {
          id:this.form.id,
          email: this.form.email,
          remark: this.form.remark
        }
      }).then(res => {
       
      })
    },
    addUser() {
      this.$http
        .post("/api/user/addUser", {
          user_name: this.form.user_name,
          email: this.form.email,
          domain_id: this.domain_id //域id
        })
        .then(res => {
          console.log(res);
          if (res.data.status == "S") {
            this.$message({
              message: "新增用户成功",
              type: "success"
            });
            this.getData();
            this.dialogFormVisible = false;
          } else if (res.data.status == "F") {
            this.$message({
              message: "该用户已存在",
              type: "warning"
            });
          }
        })
        .catch(function(error) {
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
        method: "get",
        url: "/api/user/userInfo",
        params: {
          domain_id: this.form.domain_id,
          limit: 10,
          page: this.cur_page
        }
      }).then(res => {
        // console.log(res.data.message[0].user_name)   输入h
        this.tableData = res.data.message;
      });
      
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
    //设备管理按钮
     handleHmi(index, row) {
        this.idx = index;
      // const item = this.tableData[index];
       const item = this.tableData[index];
      this.form = {
        // remark: item.remark,
        user_name: item.user_name,
        remark: item.remark,
        phone: item.phone,
        email: item.email,
        hmi: item.hmi,
        group:item.group,
        domain_id:this.$store.state.domainId,
        //用户id
        id:item.id
      };
    this.dialogFormVisible1 = true;
    // this.$http({
    //     method: "post",
    //     url: "/api/user/supplyGroupBind",
    //     data: {
    //        domain_id: this.form.domain_id,
    //       user_id: item.id,
    //       id:this.value1
    //     }
    //   }).then(res => {
       
    //   })
    },

    //11111111111111111  编辑按钮
    handleEdit(index, row) {
        this.idx = index;
      // const item = this.tableData[index];
       const item = this.tableData[index];
      this.form = {
        // remark: item.remark,
        user_name: item.user_name,
        remark: item.remark,
        phone: item.phone,
        email: item.email,
        hmi: item.hmi,
        group:item.group,
        domain_id:this.$store.state.domainId,
        //用户id
        id:item.id
      };
    this.editVisible = true;
    // this.$http({
    //     method: "post",
    //     url: "/api/user/updateInfo",
    //     data: {
    //       id: item.id,
    //       email: item.email,
    //       remark: item.remark
    //     }
    //   }).then(res => {
       
    //   })
    },



    handleDelete(index, row) {
      this.idx = index;
      this.delVisible = true;
    },
    delAll() {
      const length = this.multipleSelection.length;
      let str = "";
      this.del_list = this.del_list.concat(this.multipleSelection);
      for (let i = 0; i < length; i++) {
        str += this.multipleSelection[i].user_name + " ";
      }
      this.$message.error("删除了" + str);
      this.multipleSelection = [];
    },
    handleSelectionChange(val) {
      this.multipleSelection = val;
    },
    //保存设备组管理
    saveHmiGroup(){
     this.$set(this.tableData, this.idx, this.form);
      this.dialogFormVisible1 = false;
      this.$message.success(`修改第 ${this.idx + 1} 行成功`);
      this.getData();
    },
    //保存设备管理
    saveHmi(){
     this.$set(this.tableData, this.idx, this.form);
      this.dialogFormVisible1 = false;
      this.$message.success(`修改第 ${this.idx + 1} 行成功`);
      this.getData();
    },
    // 保存编辑 222222222
    saveEdit() {
      this.$set(this.tableData, this.idx, this.form);
      this.editVisible = false;
      this.$message.success(`修改第 ${this.idx + 1} 行成功`);
    },
    // 确定删除
    deleteRow() {
      this.tableData.splice(this.idx, 1);
      this.$http({
        method: "get",
        url: "/api/user/delete/1"
      }).then(res => {
        // console.log(res.data.message[0].user_name)   输入h
        console.log(res);
        //  this.tableData = res.data.message;
      });
      this.$message.success("删除成功");
      this.delVisible = false;
    }
  }
};
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
.tr {
  text-align: left;
}

.el-table td,
.el-table th.is-leaf {
  border-bottom: 1px solid #dcdee2;
}

.el-table--border td {
  border-right: 1px solid #dcdee2;
}
</style>
