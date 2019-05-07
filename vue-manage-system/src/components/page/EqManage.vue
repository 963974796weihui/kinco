<template>
  <div class="table">
    <div class="crumbs">
      <el-button
        class="add-user b-red"
        icon="el-icon-plus"
        type="primary"
        @click="addHmiForm()"
      >添加设备</el-button>
      <!-- dialogFormVisible = true -->
      <el-dialog title="添加设备" :visible.sync="dialogFormVisible" width="25%">
        <el-form :model="form" :rules="ruleValidate" ref="ruleForm">
          <el-form-item label="序列号" :label-width="formLabelWidth" prop="sncode">
            <el-input v-model="form.sncode" autocomplete="off"></el-input>
          </el-form-item>
          <el-form-item label="授权码" :label-width="formLabelWidth" prop="auth_code">
            <el-input v-model="form.auth_code" autocomplete="off"></el-input>
          </el-form-item>
          <el-form-item label="设备名" :label-width="formLabelWidth" prop="hmi_name">
            <el-input v-model="form.hmi_name" autocomplete="off" ></el-input>
          </el-form-item>
        </el-form>
        <div slot="footer" class="dialog-footer">
          <el-button @click="dialogFormVisible = false">取 消</el-button>
          <el-button type="primary" @click="addSerial('ruleForm')">确 定</el-button>
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
        :row-style="rowClass"
        :header-cell-style="tableHeaderColor"
        :data="data1"
        class="table"
        :stripe="test"
        ref="multipleTable"
        @selection-change="handleSelectionChange"
        @row-click="clickRow"
      >
        <el-table-column type="selection" :selectable="checkboxT" min-width="1%" align="center"></el-table-column>
        <el-table-column prop="hmi_name" label="设备名" min-width="8%"></el-table-column>
        <el-table-column prop="hmi_status" label="在线状态" min-width="9%"></el-table-column>
        <el-table-column prop="type1" label="设备型号" min-width="11%"></el-table-column>
        <el-table-column prop="virtual_address" label="虚拟ip" min-width="10%"></el-table-column>
        <el-table-column prop="real_address" label="真实ip" min-width="10%"></el-table-column>
        <el-table-column prop="auth_code" label="授权码绑定" min-width="11%"></el-table-column>
        <el-table-column prop="end_time" label="授权码截止日期" min-width="13%"></el-table-column>
        <el-table-column prop="time" label="添加日期" min-width="14%"></el-table-column>
        <el-table-column label="相关操作" min-width="9%" align="center">
          <template slot-scope="scope">
            <el-button
              :disabled="scope.row.cut_off==2"
              type="text"
              icon="el-icon-delete"
              @click="handleDelete(scope.$index, scope.row)"
            >删除</el-button>
            <el-button
              type="text"
              icon="el-icon-close"
              class="red"
              @click="ban(scope.$index, scope.row)"
            >{{scope.row.cut_off==2? "解禁":"禁用"}}</el-button>
            <!-- <el-button
              type="text"
              icon="el-icon-close"
              class="green"
              @click="reban(scope.$index, scope.row)"
            >解禁</el-button>-->
          </template>
        </el-table-column>
      </el-table>
    </div>
      <div class="pagination">
        <el-pagination
          background
          @current-change="handleCurrentChange"
          layout="prev, pager, next"
          :total="total"
        ></el-pagination>
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
import store from "../../store/store.js";
import bus from "../common/bus";
export default {
  name: "basetable",
  data() {
    //只能输入英文或者数字
    const enOrnunText = (rule, value, callback) => {
      if (value && !/^[A-Za-z0-9]+$/.test(value)) {
        callback(new Error("只能填写英文或者数字"));
      } else {
        callback();
      }
    };
      const pure = (rule, value, callback) => {
    };
    return {
      flag: 0,
      selectRow: [],
      selectData: [],
      test: true,
      ruleValidate: {
        sncode: [
          { required: true, message: "请输入序列号", trigger: "blur" },
          { validator: enOrnunText, trigger: "blur" }
        ],
        hmi_name:[
 { required: true, message: "请输入设备名", trigger: "blur" },
 { validator: pure, trigger: "blur" }
        ],
        auth_code: [
          // { required: true, message: '请输入授权码', trigger: "blur" },
          { validator: enOrnunText, trigger: "blur" }
        ]
      },
      total: "",
      dialogTableVisible: false,
      dialogFormVisible: false,
      dialogEdit: false,
      formLabelWidth: "120px",
      // url: './static/vuetable.json',
      tableData: [],
      cur_page: 1,
      multipleSelection: [],
      select_cate: "",
      trHmi: [],
      shuzu: [],
      select_word: "",
      del_list: [],
      is_search: false,
      editVisible: false,
      delVisible: false,
      form: {
        auth_code: "",
        sncode: "",
        //计算属性
        domain_id: localStorage.getItem("loginDomainId"),
        // domain_id:1,
        hmi_name: ""
      },
      idx: -1
    };
  },
  store,
  // created() {
  //   // alert(this.$store.state.domainId)
  //     this.getData();
  // },

  created() {
      this.getData();

    // this.$http({
    //   method: "post",
    //   url: "/api/admin/login",
    //   data: {
    //     user_name: localStorage.getItem("ms_username"),
    //     password: localStorage.getItem("ms_password")
    //   }
    // }).then(res => {
    //   const domain_id = res.data.message[0].id;
    //   const domain_name = res.data.message[0].domain_name;
    //   //如果此用户从没建过域
    //   if (!domain_id) {
    //     return;
    //   }
    //   //存入vuex中
    //   this.$store.commit("saveDomainId", domain_id);
    //   this.getData();
    //   //                        this.$http({
    //   //   method: 'post',
    //   //   url: '/api/supply/supplyInfo',
    //   //     params: {
    //   //       domain_id: this.domain_id,
    //   //       limit: 10,
    //   //       page: this.cur_page
    //   //   },
    //   // }).then((res) => {
    //   //                   // console.log(res.data.message[0].user_name)   输入h
    //   //                   // console.log(111);
    //   //                   // console.log(res);
    //   //                     this.tableData = res.data.message.data;
    //   //                     //  console.log(this.tableData );

    //   // // for(var i=0;i<res.data.message.data.length;i++){
    //   // //   this.trHmi.push({key:i+1,label:res.data.message.data[i].hmi_name});
    //   // // }
    //   // //  bus.$emit('trhmi', this.trHmi);

    //   //                 });
    // });
  },

  mounted() {},
  computed: {
    domain_id() {
      return this.$store.state.domainId;
    },
    data1() {
      return this.tableData.filter(d => {
        let is_del = false;
        for (let i = 0; i < this.del_list.length; i++) {
          if (d.hmi_name === this.del_list[i].hmi_name) {
            is_del = true;
            break;
          }
        }
        if (!is_del) {
          if (
            d.hmi_name.indexOf(this.select_cate) > -1 &&
            (d.hmi_name.indexOf(this.select_word) > -1 ||
              d.hmi_name.indexOf(this.select_word) > -1)
          ) {
            return d;
          }
        }
      });
    }
  },
  watch: {
    selectData(data) {
      this.selectRow = [];
      if (data.length > 0) {
        data.forEach((item, index) => {
          this.selectRow.push(this.tableData.indexOf(item));
        });
      }
    }
  },
  methods: {
      clickRow(row){
                this.$refs.multipleTable.toggleRowSelection(row);
            },
    // 多选高亮选中行
    rowClass({ row, rowIndex }) {
      if (this.selectRow.includes(rowIndex)) {
        return { "background-color": "rgba(185, 221, 249, 0.75)" };
      }
    },
    // tableRowClassName({row, rowIndex}) {
    //     if (rowIndex === 1) {
    //       return 'warning-row';
    //     } else if (rowIndex === 3) {
    //       return 'success-row';
    //     }
    //     return '';
    //   },
    addHmiForm() {
      this.form = [];
      this.dialogFormVisible = true;
    },
    //表头样式
    tableHeaderColor({ row, column, rowIndex, columnIndex }) {
      if (rowIndex === 0) {
         return "background-color: #7dc1ff;color: #ffffff;font-weight:10;";
      }
    },
    //禁用按钮
    ban(index, row) {
      this.idx = index;
      const item = this.tableData[index];
      this.form = {
        //设备id
        id: item.id
      };
      if (this.flag == 0) {
      this.$http({
        method: "post",
        url: "/api/supply/forbid",
        params: {
          id: this.form.id
        }
      }).then(res => {
        this.getData();
      });
      this.flag = this.flag + 1;
      }else if(this.flag == 1){
         this.$http({
        method: "post",
        url: "/api/supply/unforbid",
        params: {
          id: this.form.id
        }
      }).then(res => {
        this.getData();
      });
      this.flag = this.flag - 1;
      }
    },
    //解禁按钮
    reban(index, row) {
      this.idx = index;
      const item = this.tableData[index];
      this.form = {
        //设备id
        id: item.id
      };
      this.$http({
        method: "post",
        url: "/api/supply/unforbid",
        params: {
          id: this.form.id
        }
      }).then(res => {
        this.getData();
      });
    },
    checkboxT(row) {
      return row.cut_off != 2;
    },
    //添加序列号
    addSerial(formName) {
       this.$refs[formName].validate(valid => {
          if (valid) {
  this.$http({
        method: "post",
        url: "/api/supply/addSupply",
        data: {
          auth_code: this.form.auth_code,
          sncode: this.form.sncode,
          domain_id: localStorage.getItem("loginDomainId"),
          hmi_name: this.form.hmi_name
        }
      }).then(res => {
        if (res.data.status == "S") {
          this.$message({
            message: "新增设备成功",
            type: "success"
          });
          this.getData();
          this.dialogFormVisible = false;
        } else if (res.data.message == "该序列号已存在") {
          this.$message({
            message: "该序列号已存在",
            type: "warning"
          });
        }else if(res.data.message == "授权码不存在"){
  this.$message({
            message: "授权码不存在",
            type: "warning"
          });
        }
      });
          }
        });
    },
    // 分页导航
    handleCurrentChange(val) {
      this.cur_page = val;
      this.getData();
    },
    // 获取 easy-mock 的模拟数据
    getData() {
      //  alert(this.$store.state.domainId)
      this.$http({
        method: "post",
        url: "/api/supply/supplyInfo",
        params: {
          domain_id: localStorage.getItem("loginDomainId"),
          limit: 10,
          page: this.cur_page
        }
      }).then(res => {
         if(res.data.code == 302){
this.$router.push("/login");
      }
        this.total = res.data.message.total;
        this.tableData = res.data.message.data;
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
      const item = this.tableData[index];
      this.form = {
        //设备id
        id: item.id
      };
      this.delVisible = true;
    },
    delAll() {
       this.shuzu = [];
     const length = this.multipleSelection.length;
      // let str = "";
      for (let i = 0; i < length; i++) {
        this.shuzu.push(this.multipleSelection[i].id);
        // str += this.multipleSelection[i].user_name + " ";
      }
      // this.$message.error("删除了" + str);
      
      this.$http({
        method: "post",
        url: "/api/supply/deleteSupply",
        data: {
          id: this.shuzu
        }
      }).then(res => {
        if (res.data.status == "S") {
          if(this.shuzu.length>0){
            
             this.del_list = this.del_list.concat(this.multipleSelection);
       this.multipleSelection = [];
        // this.$message({
        //     message: "设备删除成功   !",
        //     type: "error"
        //   });
          }
       } else if (res.data.status == "F") {
          this.$message({
            message: "已绑定授权码，不能删除   !",
            type: "warning"
          });
        }
      });
    },

    handleSelectionChange(val) {
      this.multipleSelection = val;
      if (val.length > 0) {
        this.test = false;
      } else {
        this.test = true;
      }
      this.selectData = val;
    },
    // 保存编辑
    saveEdit() {
      this.$set(this.tableData, this.idx, this.form);
      this.editVisible = false;
      this.$message.success(`修改第 ${this.idx + 1} 行成功`);
    },
    // 确定删除
    deleteRow() {
      this.delVisible = false;
      //删除设备接口
      this.$http({
        method: "post",
        url: "/api/supply/deleteSupply",
        data: {
          id: [this.form.id]
        }
      }).then(res => {
        if (res.data.status == "S") {
          this.tableData.splice(this.idx, 1);
          this.$message.success("删除成功");
        } else if (res.data.status == "F") {
          this.$message({
            message: "已绑定授权码，不能删除   !",
            type: "warning"
          });
        }
      });
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
  font-size: 18px;
}
.red {
  color: #ff3333;
}
.b-red {
  background-color: #ff3333;
}
.white {
  color: #ffffff;
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
.add-user{
  margin-left: 30px;
}

/* .el-table .warning-row {
    background: oldlace;
  }

  .el-table .success-row {
    background: #f0f9eb;
  } */
</style>
