<template>
  <div class="table">
    <div class="crumbs">
      <el-button
        class="add-user"
        icon="el-icon-plus"
        type="primary"
        round
        @click="dialogFormVisible = true"
      >添加设备群组</el-button>
      <el-dialog title="添加设备群组" :visible.sync="dialogFormVisible" width="30%">
        <el-form :model="form" :rules="ruleValidate" ref="ruleForm">
          <el-form-item label="设备组名" :label-width="formLabelWidth">
            <el-input v-model="form.group_name" autocomplete="off" prop="group_name"></el-input>
          </el-form-item>
        </el-form>
        <div slot="footer" class="dialog-footer">
          <el-button @click="dialogFormVisible = false">取 消</el-button>
          <el-button type="primary" @click="addGroup()">确 定</el-button>
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
      <el-table
        :data="data1"
        border
        class="table"
        ref="multipleTable"
        @selection-change="handleSelectionChange"
      >
        <el-table-column type="selection" width="55" align="center"></el-table-column>
        <el-table-column prop="group_name" label="设备组名" width="170"></el-table-column>
        <el-table-column prop="hmi_num" label="组成员" width="280"></el-table-column>
        <el-table-column label="相关操作" width="500" align="center">
          <template slot-scope="scope">
            <el-button type="text" icon="el-icon-date" @click="handleGroupHmi(scope.$index, scope.row)">管理组成员</el-button>

            <el-dialog title="管理组成员" :visible.sync="dialogManagerMember" width="30%">
              <div class="tr">
                <el-transfer v-model="value1" :data="dataGroupHmi" filterable
                @change="handleChange"
                ></el-transfer>
              </div>

              <div slot="footer" class="dialog-footer">
                <el-button @click="dialogManagerMember = false">取 消</el-button>
                <el-button type="primary" @click="saveGroupHmi()">确 定</el-button>
              </div>
            </el-dialog>

            <el-button type="text" icon="el-icon-lx-people" class="red" @click="handleGroupUser(scope.$index, scope.row)">绑定用户</el-button>
    <el-dialog title="绑定用户" :visible.sync="dialogBindUser" width="30%">
              <div class="tr">
                <el-transfer v-model="value2" :data="dataUser" filterable
                @change="handleChange2"
                ></el-transfer>
              </div>
              <div slot="footer" class="dialog-footer">
                <el-button @click="dialogBindUser = false">取 消</el-button>
                <el-button type="primary" @click="saveGroupUser()">确 定</el-button>
              </div>
            </el-dialog>
            <el-button type="text" icon="el-icon-document" @click="dialogGroupName = true">更改组名</el-button>
 <el-dialog title="更改组名" :visible.sync="dialogGroupName" width="30%">
   
 <el-form :model="form" :rules="ruleValidate" ref="ruleForm">
          <el-form-item label="组名" :label-width="formLabelWidth">
            <el-input v-model="form.GroupName" autocomplete="off" prop="remark"></el-input>
          </el-form-item>
        </el-form>
        
        <div slot="footer" class="dialog-footer">
          <el-button @click="dialogGroupName = false">取 消</el-button>
          <el-button type="primary">确 定</el-button>
        </div>
      </el-dialog>
            <el-button
              type="text"
              icon="el-icon-delete"
              class="red"
              @click="handleDelete(scope.$index, scope.row)"
            >删除</el-button>
          </template>
        </el-table-column>
      </el-table>
      <div class="pagination">
        <el-pagination
          background
          @current-change="handleCurrentChange"
          layout="prev, pager, next"
          :total=dot
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
        <el-form-item label="地址">
          <el-input v-model="form.address"></el-input>
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
import store from '../../store/store.js'
//  import bus from '../common/bus';
export default {
  name: "basetable",
  data() {
    return {
      dot:100,
      //穿梭框
      value1:[],
      value2:[],
      dialogFormVisible: false,
      dialogEdit1: false,
      dialogManagerMember: false,
      dialogBindUser: false,
      dialogGroupName: false,
      formLabelWidth: "120px",
      tableData: [],
      dataGroupHmi:[],
      dataUser:[],
      trGroup:[],
      cur_page: 1,
      multipleSelection: [],
      select_cate: "",
      select_word: "",
      del_list: [],
      is_search: false,
      editVisible: false,
      delVisible: false,
      // form: {
      //   domain_id: 1,
      //   group_name:'',
      //   name: "",
      //   type1: "",
      //   serial: "",
      //   online: "",
      //   virtual: "",
      //   real: "",
      //   bind: "",
      //   date: ""
      // },
      form: {
        group_name: "",
        hmi_num: "",
        domain_id: 1,
        id:""
      },
      idx: -1
    };
  },
  created() {
    this.getData();
  },
  mounted() {
  
    //调用 管理组成员 接口
              this.$http({
  method: 'post',
  url: '/api/group/hmiInfo',
    data: {
      domain_id: this.form.domain_id,
  },
}).then((res) => {
  			for(var i=0;i<res.data.message.length;i++){
  this.dataGroupHmi.push({key:res.data.message[i].id,label:res.data.message[i].hmi_name});
}
                })

//调用 绑定用户 接口
   this.$http({
  method: 'post',
  url: '/api/group/addUser',
    data: {
      domain_id: this.form.domain_id,
  },
}).then((res) => {
  			for(var i=0;i<res.data.message.length;i++){
  this.dataUser.push({key:res.data.message[i].id,label:res.data.message[i].user_name});
}
                })
  },
  computed: {
            data1() {
                return this.tableData.filter((d) => {
                    let is_del = false;
                    for (let i = 0; i < this.del_list.length; i++) {
                        if (d.group_name === this.del_list[i].group_name) {
                            is_del = true;
                            break;
                        }
                    }
                    if (!is_del) {
                        if (d.group_name.indexOf(this.select_cate) > -1 &&
                            (d.group_name.indexOf(this.select_word) > -1 ||
                                d.group_name.indexOf(this.select_word) > -1)
                        ) {
                            return d;
                        }
                    }
                })
            }
        },
  methods: {

  //管理组成员按钮事件
     handleGroupHmi(index, row) {
        this.idx = index;
      // const item = this.tableData[index];
       const item = this.tableData[index];
      this.form = {
        group_name: item.group_name,
        hmi_num: item.hmi_num,
        domain_id:this.$store.state.domainId,
        //设备id
        id:item.id
      };
    this.dialogManagerMember = true;
    },
      //绑定用户按钮事件
    handleGroupUser(index, row) {
        this.idx = index;
      // const item = this.tableData[index];
       const item = this.tableData[index];
      this.form = {
        group_name: item.group_name,
        hmi_num: item.hmi_num,
        domain_id:this.$store.state.domainId,
        //设备id
        id:item.id
      };
    this.dialogBindUser = true;
    },

  //穿梭框的change事件
    handleChange(){
      this.$http({
        method: "post",
        url: "/api/group/hmiInfoBind",
        data: {
           domain_id: this.form.domain_id,
          group_id: this.form.id,
          id:this.value1
        }
      }).then(res => {
       
      })
    },
    handleChange2(){
      this.$http({
        method: "post",
        url: "/api/group/addUserBind",
        data: {
           domain_id: this.form.domain_id,
          group_id: this.form.id,
          id:this.value2
        }
      }).then(res => {
       
      })
    },

    //添加设备群组
    addGroup() {
      this.$http({
  method: 'post',
  url: '/api/group/addGroup',
    data: {
      domain_id: this.form.domain_id,
      group_name: this.form.group_name
  },
}).then(res => {
            console.log(res)
            if(res.data.status=="S"){
                   this.$message({
          message: '新增设备组成功',
          type: 'success'
        });
        this.getData();
        this.dialogFormVisible=false;
            }else if(res.data.status=="F"){
 this.$message({
          message: '该设备组已存在',
          type: 'warning'
        });
            }
        })
    },
    //保存管理组成员按钮
    saveGroupHmi(){
this.$set(this.tableData, this.idx, this.form);
      this.dialogManagerMember = false;
      this.$message.success(`修改第 ${this.idx + 1} 行成功`);
      this.getData();
    },
    //保存绑定用户按钮
    saveGroupUser(){
this.$set(this.tableData, this.idx, this.form);
      this.dialogBindUser = false;
      this.$message.success(`修改第 ${this.idx + 1} 行成功`);
      this.getData();
    },
    // 分页导航
    handleCurrentChange(val) {
      this.cur_page = val;
      this.getData();
    },
    getData() {
           this.$http({
  method: 'post',
  url: '/api/group/supplyInfo',
    data: {
      id: this.form.domain_id,
      limit: 10,
      page: this.cur_page
  },
}).then((res) => {
                  // console.log(res.data.message[0].user_name)   输入h  
                  console.log(111);
                  console.log(res.data.message.length);
                  console.log(res);
                    this.tableData = res.data.message;
                     console.log(this.tableData );
// for(var i=0;i<res.data.message.length;i++){
//   this.trGroup.push({key:i+1,label:res.data.message[i].group_name});
// }
//  bus.$emit('trgroup', this.trGroup);
 //------------------------------
  // //  bus接收
  //                 bus.$on('trhmi', msg => {
  //               this.dataGroupHmi = msg;
                
  //           })
            //        bus.$on('truser', msg => {
            //     this.dataUser = msg;
                
            // })
			
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
        name: item.name,
        date: item.date,
        address: item.address
      };
      this.editVisible = true;
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
        str += this.multipleSelection[i].group_name + " ";
      }
      this.$message.error("删除了" + str);
      this.multipleSelection = [];
    },
    handleSelectionChange(val) {
      this.multipleSelection = val;
    },
    // 保存编辑
    saveEdit() {
      this.$set(this.tableData, this.idx, this.form);
      this.editVisible = false;
      this.$message.success(`修改第 ${this.idx + 1} 行成功`);
    },
    // 确定删除
    deleteRow() {
      this.tableData.splice(this.idx, 1);
      this.$message.success("删除成功");
      this.delVisible = false;
    }
  }
};
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
  font-size: 14px;
}
.red {
  color: #ff0000;
}
.tr {
  text-align: left;
}
</style>
