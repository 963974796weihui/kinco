<template>
  <div class="table">
    <div class="crumbs">
      <a target="_blank" href="http://39.104.56.173:8091/alipay">
        <el-button
          class="add-user b-red"
          icon="el-icon-plus"
          type="primary"
          @click="buyCode()"
        >购买授权码</el-button>
      </a>

      <!-- <el-dialog title="购买授权码" :visible.sync="dialogFormVisible" width="30%">
        <h3>支付宝付款</h3>
          <div class="two-div"><img class="two" src="static/img/two.jpg"></div>
        <div slot="footer" class="dialog-footer">
          <el-button @click="dialogFormVisible = false">取 消</el-button>
          <el-button type="primary" @click="addUser()">确 定</el-button>
        </div>
      </el-dialog>-->
    </div>
    <div class="container">
      <div class="handle-box">
        <!-- <el-button type="primary" icon="delete" class="handle-del mr10" @click="delAll">批量删除</el-button> -->
        <div class="search">
          <el-input v-model="select_word" placeholder="筛选关键词" class="handle-input mr10"></el-input>
          <el-button type="primary" icon="search" @click="search">搜索</el-button>
        </div>
      </div>
      <el-table
        :header-cell-style="tableHeaderColor"
        :data="data1"
        class="table"
        :stripe="test"
        ref="multipleTable"
        :row-style="rowClass"
        @selection-change="handleSelectionChange"
        @row-click="clickRow"
      >
        <el-table-column type="selection" min-width="4%" align="center"></el-table-column>
        <el-table-column prop="sncode" label="授权码" min-width="16%"></el-table-column>
        <el-table-column prop="buy_time" :formatter="timestampToTime" label="购买时间" min-width="16%"></el-table-column>
        <el-table-column prop="long" label="有效期" min-width="16%"></el-table-column>
        <el-table-column prop="activate_time" label="激活时间" min-width="16%"></el-table-column>
        <el-table-column prop="bind" label="绑定情况" min-width="16%"></el-table-column>
        <el-table-column label="相关操作" min-width="16%" align="center">
          <template slot-scope="scope">
            <el-button
              type="text"
              icon="el-icon-date"
              @click="bindCode(scope.$index, scope.row)"
            >绑定设备</el-button>
            <el-button
              class="red"
              type="text"
              icon="el-icon-date"
              @click="bindRelieve(scope.$index, scope.row)"
            >解除绑定</el-button>
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
    <!-- 绑定设备弹出框 -->
    <el-dialog title="未绑定授权码设备" :visible.sync="dialogCode" width="20%">
      <!-- <input type="radio" name="test" v-for="item in dataCode" :key="item.id" :value="item.label" v-model="checkedValue"> -->
      <el-radio-group v-model="radio" @change="onRadioChange()">
        <el-radio
          :label="item.id+','+item.xlh"
          :key="item.id"
          v-for="item in dataCode"
        >{{item.label}}</el-radio>
      </el-radio-group>
      <div slot="footer" class="dialog-footer">
        <el-button @click="dialogCode = false">取 消</el-button>
        <el-button type="primary" @click="saveCode()">确 定</el-button>
      </div>
    </el-dialog>
    <!-- 解除绑定弹出框 -->
    <el-dialog title="提示" :visible.sync="dialogRelieve" width="300px" center>
      <div class="del-dialog-cnt">是否确认解除绑定？</div>
      <span slot="footer" class="dialog-footer">
        <el-button @click="dialogRelieve = false">取 消</el-button>
        <el-button type="primary" @click="saveRelieve()">确 定</el-button>
      </span>
    </el-dialog>
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
        </el-form-item>-->
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
export default {
  name: "codemanage",
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
      test: true,
      selectRow: [],
      selectData: [],
      radio: "",
      radioId: "",
      radioxlh: "",
      sttusCodes: [
        {
          label: "男",
          value: "0",
          selected: "1"
        },
        {
          label: "女",
          value: "1",
          selected: "0"
        }
      ],
      dialogCode: false,
      dialogRelieve: false,
      total: "",
      dialogTableVisible: false,
      //    items:[  { message:'Foo' },
      // { message: 'Bar' }],
      // dialogFormVisible: false,
      form1: {
        name: "",
        email: ""
      },
      //匹配校验器
      //       ruleValidate: {
      //    name: [{ required: true, message: "账号名不能为空", trigger: "blur" },
      //           { validator: validatename, trigger: "blur" }],
      //            email: [
      //            {required: true, message: '请输入电子邮箱', trigger: 'blur'},
      //           { validator: validatemail, trigger: "blur" }],

      //       },
      formLabelWidth: "120px",
      // url: './static/vuetable.json',
      // tableData1:[],

      tableData: [],
      dataCode: [],
      cur_page: 1,
      multipleSelection: [],
      select_cate: "",
      select_word: "",
      del_list: [],
      shuzu: [],
      is_search: false,
      editVisible: false,
      delVisible: false,
      form: {
        sncode: "",
        long: "",
        activate_time: "",
        bind: ""
      },
      idx: -1
    };
  },
  store,
  created() {
    this.getData();
  },
  computed: {
    domain_id() {
      return this.$store.state.domainId;
    },

    data1() {
      return this.tableData.filter(d => {
        let is_del = false;
        for (let i = 0; i < this.del_list.length; i++) {
          if (d.sncode === this.del_list[i].sncode) {
            is_del = true;
            break;
          }
        }
        if (!is_del) {
          if (
            d.sncode.indexOf(this.select_cate) > -1 &&
            (d.sncode.indexOf(this.select_word) > -1 ||
              d.sncode.indexOf(this.select_word) > -1)
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
    //购买时间
    timestampToTime(row, column) {
      var date = new Date(row.buy_time * 1000); //timestamp 为10位需*1000，timestamp 为13位的话不需乘1000
      var Y = date.getFullYear() + "-";
      var M =
        (date.getMonth() + 1 < 10
          ? "0" + (date.getMonth() + 1)
          : date.getMonth() + 1) + "-";
      var D =
        (date.getDate() < 10 ? "0" + date.getDate() : date.getDate()) + " ";
      var h =
        (date.getHours() < 10 ? "0" + date.getHours() : date.getHours()) + ":";
      var m =
        (date.getMinutes() < 10 ? "0" + date.getMinutes() : date.getMinutes()) +
        ":";
      var s =
        date.getSeconds() < 10 ? "0" + date.getSeconds() : date.getSeconds();
      return Y + M + D + h + m + s;
    },
    clickRow(row) {
      this.$refs.multipleTable.toggleRowSelection(row);
    },

    // 多选高亮选中行
    rowClass({ row, rowIndex }) {
      if (this.selectRow.includes(rowIndex)) {
        return { "background-color": "rgba(185, 221, 249, 0.75)" };
      }
    },
    //解除绑定
    saveRelieve() {
      this.dialogRelieve = false;
      //  this.$set(this.tableData, this.idx, this.form);

      this.$http({
        method: "post",
        url: "/api/AuthCode/unbind",
        data: {
          id: this.form.id
        }
      }).then(res => {
        console.log(res);
        this.getData();
        this.$message.success("解绑成功！");
      });
    },
    //绑定设备确定按钮
    saveCode() {
      this.$http({
        method: "post",
        url: "/api/AuthCode/bind",
        data: {
          auth_code: this.form.auth_code,
          sncode: this.radioxlh,
          //人机id
          id: this.radioId
        }
      }).then(res => {
        if (res.data.code == 202) {
          this.$message.success("授权码已被使用！");
        } else if (res.data.code == 203) {
          this.$message.success("授权码已过期 请重新购买!");
        } else if (res.data.code == 204) {
          this.$message.success("请选择要绑定的设备!");
        } else {
          this.getData();
          this.$message.success("绑定成功！");
        }
      });
      // this.$set(this.tableData, this.idx, this.form);
      this.dialogCode = false;
      // this.$message.success(`修改第 ${this.idx + 1} 行成功`);
    },
    //进行绑定
    getBind() {
      this.$http({
        method: "post",
        url: "/api/AuthCode/bind",
        data: {
          auth_code: this.form.auth_code,
          sncode: this.radioxlh,
          //人机id
          id: this.radioId
        }
      }).then(res => {});
    },
    //获取所有未绑定的设备
    getCode() {
      this.$http({
        method: "POST",
        url: "/api/AuthCode/allhmi",
        data: {
          domain_id: localStorage.getItem("loginDomainId")
        }
      }).then(res => {
        this.dataCode = [];
        for (var i = 0; i < res.data.data.length; i++) {
          this.dataCode.push({
            id: res.data.data[i].id,
            // key: res.data.data[i].id,
            label: res.data.data[i].hmi_name,
            xlh: res.data.data[i].sncode
          });
        }
      });
    },
    //绑定设备
    bindCode(index, row) {
      this.getCode();
      this.idx = index;
      // const item = this.tableData[index];
      const item = this.tableData[index];
      this.form = {
        auth_code: item.sncode
        // hmi_num: item.hmi_num,
        // domain_id: this.domain_id,
        // //设备id
        // id: item.id
      };
      this.dialogCode = true;
    },
    //解除绑定
    bindRelieve(index, row) {
      this.idx = index;
      // const item = this.tableData[index];
      const item = this.tableData[index];
      this.form = {
        // auth_code: item.sncode,
        // hmi_num: item.hmi_num,
        // domain_id: this.domain_id,
        // //设备id
        id: item.id
      };
      this.dialogRelieve = true;
    },
    onRadioChange() {
      console.log(this.radio); //要写的方法,
      this.radioId = this.radio.split(",")[0];
      this.radioxlh = this.radio.split(",")[1];
    },
    //表头样式
    tableHeaderColor({ row, column, rowIndex, columnIndex }) {
      if (rowIndex === 0) {
        return "background-color: #7dc1ff;color: #ffffff;font-weight:10;";
      }
    },
    buyCode() {},
    addUser() {
      this.$http
        .post("/api/admin/register", {
          name: this.form1.user_name,
          email: this.form1.email
        })
        .then(function(response) {
          console.log(response);
        })
        .catch(function(error) {
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
        method: "post",
        url: "/api/AuthCode/codeInfo",
        data: {
          limit: 10,
          page: this.cur_page
          // user_id:164
        }
      }).then(res => {
        if (res.data.code == 302) {
          this.$router.push("/login");
        }
        // console.log(5555555555);
        //               console.log(res);
        //               console.log(res.data.message)
        this.total = res.data.total;
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
        date: item.date
        // address: item.address
      };
      this.editVisible = true;
    },
    handleDelete(index, row) {
      this.idx = index;
      this.delVisible = true;
    },

    // delAll() {
    //   const length = this.multipleSelection.length;
    //   let str = "";
    //   this.del_list = this.del_list.concat(this.multipleSelection);
    //   for (let i = 0; i < length; i++) {
    //     str += this.multipleSelection[i].name + " ";
    //   }
    //   // this.$message.error("删除了" + str);
    //   this.multipleSelection = [];
    // },
    delAll() {
      this.shuzu = [];
      const length = this.multipleSelection.length;

      for (let i = 0; i < length; i++) {
        this.shuzu.push(this.multipleSelection[i].id);
      }

      this.$http({
        method: "get",
        url: "/api/user/delete",
        params: {
          user_id: this.shuzu
        }
      }).then(res => {
        if (res.data.status == "S") {
          this.del_list = this.del_list.concat(this.multipleSelection);
          this.multipleSelection = [];
          // this.$message.success("删除成功");
          // this.$set(this.tableData, this.idx, this.form);
          // this.tableData.splice(this.idx, 1);
        } else if (res.data.code == 201) {
          this.$message.success("请先进行设备解绑");
        } else if (res.data.code == 202) {
          this.$message.success("请先进行用户解绑");
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
      // this.$message.success(`修改第 ${this.idx + 1} 行成功`);
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
  margin-bottom: 15px;
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
.two {
  width: 200px;
  height: 200px;
  margin-left: 200px;
}
/* .el-table .warning-row {
    background: oldlace;
  }

  .el-table .success-row {
    background: #f0f9eb;
  } */
</style>
