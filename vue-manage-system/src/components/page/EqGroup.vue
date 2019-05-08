<template>
  <div class="table">
    <div class="crumbs">
      <el-button
        class="add-user b-red"
        icon="el-icon-plus"
        type="primary"
        @click="addGroupStart()"
      >添加设备组</el-button>
      <el-dialog title="添加设备群组" :visible.sync="dialogFormVisible" width="20%">
        <el-form :model="form" :rules="ruleValidate" ref="ruleForm">
          <el-form-item label="设备组名" :label-width="formLabelWidth" prop="group_name">
            <el-input v-model="form.group_name" autocomplete="off"></el-input>
          </el-form-item>
        </el-form>
        <div slot="footer" class="dialog-footer">
          <el-button @click="dialogFormVisible = false">取 消</el-button>
          <el-button type="primary" @click="addGroup('ruleForm')">确 定</el-button>
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
        :header-cell-style="tableHeaderColor"
        :data="data1"
        :row-style="rowClass"
        :stripe="test"
        class="table"
        ref="multipleTable"
        @selection-change="handleSelectionChange"
        @row-click="clickRow"
      >
        <el-table-column type="selection" min-width="4%" align="center"></el-table-column>
        <el-table-column prop="group_name" label="设备组名" min-width="32%"></el-table-column>
        <el-table-column prop="hmi_num" label="组成员" min-width="32%"></el-table-column>
        <el-table-column label="相关操作" min-width="32%" align="center">
          <template slot-scope="scope">
            <el-button
              type="text"
              icon="el-icon-date"
              @click="handleGroupHmi(scope.$index, scope.row)"
            >管理组成员</el-button>

            <el-button
              type="text"
              icon="el-icon-lx-people"
              class="red"
              @click="handleGroupUser(scope.$index, scope.row)"
            >绑定用户</el-button>

            <el-button
              type="text"
              icon="el-icon-document"
              @click="handleEdit(scope.$index, scope.row)"
            >更改组名</el-button>
            <!-- <el-dialog title="更改组名" :visible.sync="dialogGroupName" width="30%">
              <el-form :model="form" :rules="ruleValidate" ref="ruleForm">
                <el-form-item label="组名" :label-width="formLabelWidth">
                  <el-input v-model="form.GroupName" autocomplete="off" prop="remark"></el-input>
                </el-form-item>
              </el-form>

              <div slot="footer" class="dialog-footer">
                <el-button @click="dialogGroupName = false">取 消</el-button>
                <el-button type="primary">确 定</el-button>
              </div>
            </el-dialog>-->
            <el-button
              type="text"
              icon="el-icon-delete"
              class="red"
              @click="handleDelete(scope.$index, scope.row)"
            >删除</el-button>
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
    <!-- 管理组成员弹出框 -->
    <el-dialog title="管理组成员" :visible.sync="dialogManagerMember" width="40%">
      <div class="tr">
        <el-transfer
          :button-texts="['移出', '加入']"
          :titles="['未绑定设备', '已绑定设备']"
          v-model="value1"
          :data="dataGroupHmi"
          @left-check-change="aaa1"
          @right-check-change="bbb1"
          filterable
          @change="handleChange"
        ></el-transfer>
      </div>

      <div slot="footer" class="dialog-footer">
        <el-button @click="dialogManagerMember = false">返 回</el-button>
        <el-button type="primary" @click="saveGroupHmi()">完 成</el-button>
      </div>
    </el-dialog>
    <!-- 绑定用户弹出框 -->
    <el-dialog title="绑定用户" :visible.sync="dialogBindUser" width="40%">
      <div class="tr">
        <el-transfer
          :button-texts="['进行解绑', '进行绑定']"
          :titles="['未绑定用户', '已绑定用户']"
          v-model="value2"
          :data="dataUser"
          filterable
          @left-check-change="aaa"
          @right-check-change="bbb"
          @change="handleChange2"
        ></el-transfer>
      </div>
      <div slot="footer" class="dialog-footer">
        <el-button @click="dialogBindUser = false">返 回</el-button>
        <el-button type="primary" @click="saveGroupUser()">完 成</el-button>
      </div>
    </el-dialog>
    <!-- 更改组名弹出框 -->
    <el-dialog title="更改组名" :visible.sync="dialogGroupName" width="30%">
      <el-form ref="form" :model="form" label-width="50px">
        <el-form-item label="组名">
          <el-input v-model="form.group_name"></el-input>
        </el-form-item>
      </el-form>
      <span slot="footer" class="dialog-footer">
        <el-button @click="dialogGroupName = false">取 消</el-button>
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
import store from "../../store/store.js";
//  import bus from '../common/bus';
export default {
  name: "basetable",
  data() {
    //组名校验
    const pure = (rule, value, callback) => {
      callback();
    };
    const enOrnunText = (rule, value, callback) => {
      if (value && !/^[A-Za-z0-9]+$/.test(value)) {
        callback(new Error("只能填写英文或者数字"));
      } else {
        callback();
      }
    };
    return {
      ff: "",
      ff1: "",
      shuzu3: [],
      shuzu31: [],
      selectRow: [],
      selectData: [],
      test: true,
      total: "",
      //穿梭框
      value1: [],
      value2: [],
      dialogFormVisible: false,
      dialogEdit1: false,
      dialogManagerMember: false,
      dialogBindUser: false,
      dialogGroupName: false,
      formLabelWidth: "120px",
      tableData: [],
      dataGroupHmi: [],
      dataUser: [],
      shuzu: [],
      shuzu2: [],
      shuzu21: [],
      trGroup: [],
      cur_page: 1,
      multipleSelection: [],
      select_cate: "",
      select_word: "",
      del_list: [],
      is_search: false,
      editVisible: false,
      delVisible: false,
      //匹配校验器
      ruleValidate: {
        group_name:[
          { required: true, message: "请输入序列号", trigger: "blur" },
          { validator: pure, trigger: "blur" }
        ],
        // group_name: [
        //   { required: true, message: "请输入组名", trigger: "blur" },
        //   { validator: pure, trigger: "blur" }
        // ]
      },
      form: {
        group_name: "",
        hmi_num: "",
        domain_id: localStorage.getItem("loginDomainId"),
        id: ""
      },
      idx: -1
    };
  },
  store,
  // created() {
  //   this.getData();
  // },

  created() {
    //获取所有组信息
    this.$http({
      method: "post",
      url: "/api/group/supplyInfo",
      data: {
        id: localStorage.getItem("loginDomainId"), //域id
        limit: 10,
        page: this.cur_page
      }
    }).then(res => {
      // console.log(res.data.message[0].user_name)   输入h
      console.log(11111111111111);
      // console.log(res.data.message.length);
      console.log(res);
      if (res.data.code == 302) {
        this.$router.push("/login");
      }
      this.total = res.data.total;
      this.tableData = res.data.message;
      console.log(this.tableData);
    });

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

    // });
  },

  mounted() {
    //     //调用 管理组成员 接口
    //               this.$http({
    //   method: 'post',
    //   url: '/api/group/hmiInfo',
    //     data: {
    //       domain_id: this.domain_id,
    //   },
    // }).then((res) => {
    //   			for(var i=0;i<res.data.message.length;i++){
    //   this.dataGroupHmi.push({key:res.data.message[i].id,label:res.data.message[i].hmi_name});
    // }
    //                 })
    //调用 绑定用户 接口
    //    this.$http({
    //   method: 'post',
    //   url: '/api/group/addUser',
    //     data: {
    //       domain_id: this.domain_id,
    //   },
    // }).then((res) => {
    //   			for(var i=0;i<res.data.message.length;i++){
    //   this.dataUser.push({key:res.data.message[i].id,label:res.data.message[i].user_name});
    // }
    //                 })
  },
  computed: {
    domain_id() {
      return this.$store.state.domainId;
    },
    data1() {
      return this.tableData.filter(d => {
        let is_del = false;
        for (let i = 0; i < this.del_list.length; i++) {
          if (d.group_name === this.del_list[i].group_name) {
            is_del = true;
            break;
          }
        }
        if (!is_del) {
          if (
            d.group_name.indexOf(this.select_cate) > -1 &&
            (d.group_name.indexOf(this.select_word) > -1 ||
              d.group_name.indexOf(this.select_word) > -1)
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
    clickRow(row) {
      this.$refs.multipleTable.toggleRowSelection(row);
    },
    addGroupStart() {
      this.form = [];
      this.dialogFormVisible = true;
    },
    aaa() {
      this.ff = 1;
      this.$http({
        method: "post",
        url: "/api/group/addUser",
        data: {
          domain_id: localStorage.getItem("loginDomainId"), //域id
          id: this.form.id
        }
      }).then(res => {
        console.log(6666666);
        console.log(res);
        this.value2 = [];
        // this.shuzu2=[];
        for (var i = 0; i < res.data.message.length; i++) {
          if (res.data.message[i].status == 1) {
            this.value2.push(res.data.message[i].id);
          }
        }
        this.shuzu2 = this.value2;
      });
    },
    aaa1() {
      this.ff1 = 1;
      this.$http({
        method: "post",
        url: "/api/group/hmiInfo",
        data: {
          domain_id: localStorage.getItem("loginDomainId"), //域id
          id: this.form.id
        }
      }).then(res => {
        this.value1 = [];
        for (var i = 0; i < res.data.message.length; i++) {
          if (res.data.message[i].status == 1) {
            this.value1.push(res.data.message[i].id);
          }
        }
        this.shuzu21 = this.value1;
      });
    },
    bbb() {
      this.ff = 0;
      this.$http({
        method: "post",
        url: "/api/group/addUser",
        data: {
          domain_id: localStorage.getItem("loginDomainId"), //域id
          id: this.form.id
        }
      }).then(res => {
        console.log(6666666);
        console.log(res);
        this.value2 = [];
        // this.shuzu2=[];
        for (var i = 0; i < res.data.message.length; i++) {
          if (res.data.message[i].status == 1) {
            this.value2.push(res.data.message[i].id);
          }
        }
        this.shuzu2 = this.value2;
      });
    },
    bbb1() {
      this.ff1 = 0;
      this.$http({
        method: "post",
        url: "/api/group/hmiInfo",
        data: {
          domain_id: localStorage.getItem("loginDomainId"), //域id
          id: this.form.id
        }
      }).then(res => {
        this.value1 = [];
        for (var i = 0; i < res.data.message.length; i++) {
          if (res.data.message[i].status == 1) {
            this.value1.push(res.data.message[i].id);
          }
        }
        this.shuzu21 = this.value1;
      });
    },
    //数组去重
    getArrDifference(arr1, arr2) {
      return arr1.concat(arr2).filter(function(v, i, arr) {
        return arr.indexOf(v) === arr.lastIndexOf(v);
      });
    },
    // 多选高亮选中行
    rowClass({ row, rowIndex }) {
      if (this.selectRow.includes(rowIndex)) {
        return { "background-color": "rgba(185, 221, 249, 0.75)" };
      }
    },
    //表头样式
    tableHeaderColor({ row, column, rowIndex, columnIndex }) {
      if (rowIndex === 0) {
        return "background-color: #7dc1ff;color: #ffffff;font-weight:10;";
      }
    },
    //获取所有设备
    getHmi() {
      this.$http({
        method: "post",
        url: "/api/supply/supplyInfo",
        params: {
          domain_id: localStorage.getItem("loginDomainId"),
          limit: 10000,
          page: 1
        }
      }).then(res => {
        this.dataGroupHmi = [];
        for (var i = 0; i < res.data.message.data.length; i++) {
          this.dataGroupHmi.push({
            key: res.data.message.data[i].id,
            label: res.data.message.data[i].hmi_name
          });
        }
      });
    },
    //获取所有用户
    getUser() {
      this.$http({
        method: "get",
        url: "/api/user/userInfo",
        params: {
          domain_id: localStorage.getItem("loginDomainId"),
          limit: 10000,
          page: 1
        }
      }).then(res => {
        this.dataUser = [];
        for (var i = 0; i < res.data.message.length; i++) {
          this.dataUser.push({
            key: res.data.message[i].id,
            label: res.data.message[i].user_name
          });
        }
      });
    },
    //管理组成员按钮事件
    handleGroupHmi(index, row) {
      this.getHmi();

      this.idx = index;
      // const item = this.tableData[index];
      const item = this.tableData[index];
      this.form = {
        group_name: item.group_name,
        hmi_num: item.hmi_num,
        domain_id: localStorage.getItem("loginDomainId"),
        //设备id
        id: item.id
      };
      //获取已绑定组成员
      this.$http({
        method: "post",
        url: "/api/group/hmiInfo",
        data: {
          domain_id: localStorage.getItem("loginDomainId"), //域id
          id: this.form.id
        }
      }).then(res => {
        this.value1 = [];
        for (var i = 0; i < res.data.message.length; i++) {
          if (res.data.message[i].status == 1) {
            this.value1.push(res.data.message[i].id);
          }
        }
        this.shuzu21 = this.value1;
      });
      this.dialogManagerMember = true;
    },
    //绑定用户按钮事件
    handleGroupUser(index, row) {
      this.$http({
        method: "get",
        url: "/api/user/userInfo",
        params: {
          domain_id: localStorage.getItem("loginDomainId"),
          limit: 10000,
          page: 1
        }
      }).then(res => {
        this.dataUser = [];
        for (var i = 0; i < res.data.message.length; i++) {
          this.dataUser.push({
            key: res.data.message[i].id,
            label: res.data.message[i].user_name
          });
        }
      });
      this.idx = index;
      // const item = this.tableData[index];
      const item = this.tableData[index];
      this.form = {
        group_name: item.group_name,
        hmi_num: item.hmi_num,
        domain_id: localStorage.getItem("loginDomainId"),
        //设备id
        id: item.id
      };
      //获取已绑定用户
      this.$http({
        method: "post",
        url: "/api/group/addUser",
        data: {
          domain_id: localStorage.getItem("loginDomainId"), //域id
          id: this.form.id
        }
      }).then(res => {
        console.log(6666666);
        console.log(res);
        this.value2 = [];
        // this.shuzu2=[];
        for (var i = 0; i < res.data.message.length; i++) {
          if (res.data.message[i].status == 1) {
            this.value2.push(res.data.message[i].id);
          }
        }
        this.shuzu2 = this.value2;
      });
      this.dialogBindUser = true;
    },
    //穿梭框的change事件
    handleChange() {
      //数组去重
      this.shuzu31 = this.getArrDifference(this.value1, this.shuzu21);
      // this.$http({
      //   method: "post",
      //   url: "/api/group/hmiInfoBind",
      //   data: {
      //     domain_id: this.domain_id,
      //     group_id: this.form.id,
      //     id: this.shuzu31
      //   }
      // }).then(res => {});
      if (this.ff1 == 1) {
        this.$http({
          method: "post",
          url: "/api/group/hmiInfoBind",
          data: {
            domain_id: localStorage.getItem("loginDomainId"),
            group_id: this.form.id,
            id: this.shuzu31
          }
        }).then(res => {
          if (res.data.status == "S") {
            this.$message.success("加入设备组成功");
          }
        });
      } else if (this.ff1 == 0) {
        this.$http({
          method: "post",
          url: "/api/group/unhmiInfoBind",
          data: {
            domain_id: localStorage.getItem("loginDomainId"),
            group_id: this.form.id,
            id: this.shuzu31
          }
        }).then(res => {
          if (res.data.status == "S") {
            this.$message.success("移出设备组成功！");
          }
        });
      }
    },
    handleChange2() {
      //数组去重
      this.shuzu3 = this.getArrDifference(this.value2, this.shuzu2);
      if (this.ff == 1) {
        this.$http({
          method: "post",
          url: "/api/group/addUserBind",
          data: {
            domain_id: localStorage.getItem("loginDomainId"),
            group_id: this.form.id,
            id: this.shuzu3
          }
        }).then(res => {
          if (res.data.status == "S") {
            this.$message.success("组绑定用户成功!");
          }
        });
      } else if (this.ff == 0) {
        this.$http({
          method: "post",
          url: "/api/group/unaddUserBind",
          data: {
            domain_id: localStorage.getItem("loginDomainId"),
            group_id: this.form.id,
            id: this.shuzu3
          }
        }).then(res => {
          if (res.data.status == "S") {
            this.$message.success("组解绑用户成功!");
          }
        });
      }
    },

    //添加设备群组
    addGroup(formName) {
      this.$refs[formName].validate(valid => {
        if (valid) {
           this.$http({
        method: "post",
        url: "/api/group/addGroup",
        data: {
          domain_id: localStorage.getItem("loginDomainId"), //域id
          group_name: this.form.group_name
        }
      }).then(res => {
        console.log(res);
        if (res.data.status == "S") {
          this.$message({
            message: "新增设备组成功",
            type: "success"
          });
          this.getData();
          this.dialogFormVisible = false;
        } else if (res.data.status == "F") {
          this.$message({
            message: "该设备组已存在",
            type: "warning"
          });
        }
      });
        }
      });
    },
    //保存管理组成员按钮
    saveGroupHmi() {
      this.$set(this.tableData, this.idx, this.form);
      this.dialogManagerMember = false;
      // this.$message.success(`修改第 ${this.idx + 1} 行成功`);
      this.getData();
    },
    //保存绑定用户按钮
    saveGroupUser() {
      this.$set(this.tableData, this.idx, this.form);
      this.dialogBindUser = false;
      // this.$message.success(`修改第 ${this.idx + 1} 行成功`);
      this.getData();
    },
    // 分页导航
    handleCurrentChange(val) {
      this.cur_page = val;
      this.getData();
    },
    getData() {
      this.$http({
        method: "post",
        url: "/api/group/supplyInfo",
        data: {
          id: localStorage.getItem("loginDomainId"), //域id
          limit: 10,
          page: this.cur_page
        }
      }).then(res => {
        // console.log(res.data.message[0].user_name)   输入h
        // console.log(111);
        // console.log(res.data.message.length);
        // console.log(res);
        this.total = res.data.total;
        this.tableData = res.data.message;
        console.log(this.tableData);
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
    //更改组名点击事件
    handleEdit(index, row) {
      this.idx = index;
      const item = this.tableData[index];
      this.form = {
        group_name: item.group_name,
        hmi_num: item.hmi_num,
        domain_id: item.domain_id,
        id: item.id
      };
      this.dialogGroupName = true;
    },
    handleDelete(index, row) {
      this.idx = index;

      const item = this.tableData[index];
      this.form = {
        // remark: item.remark,
        // user_name: item.user_name,
        // remark: item.remark,
        // phone: item.phone,
        // email: item.email,
        // hmi: item.hmi,
        // group:item.group,
        // domain_id:this.domain_id,
        // //用户id
        // id:item.id

        //    group_name: "",
        // hmi_num: "",
        // domain_id: this.domain_id,
        id: item.id
      };

      this.delVisible = true;
    },
    //批量删除
    delAll() {
      this.shuzu = [];
      const length = this.multipleSelection.length;

      for (let i = 0; i < length; i++) {
        this.shuzu.push(this.multipleSelection[i].id);
      }

      this.$http({
        method: "post",
        url: "/api/group/deleteGroup",
        data: {
          id: this.shuzu
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
      this.$http({
        method: "post",
        url: "/api/group/updateGroup",
        data: {
          id: this.form.id,
          group_name: this.form.group_name
        }
      }).then(res => {});
      this.$set(this.tableData, this.idx, this.form);
      this.dialogGroupName = false;
      // this.$message.success(`修改第 ${this.idx + 1} 行成功`);
    },
    // 确定删除
    deleteRow() {
      // this.$set(this.tableData, this.idx, this.form);
      // this.tableData.splice(this.idx, 1);
      // alert(this.form.id)
      this.$http({
        method: "post",
        url: "/api/group/deleteGroup",
        data: {
          id: [this.form.id]
        }
      }).then(res => {
        if (res.data.status == "S") {
          this.$message.success("删除成功");
          this.$set(this.tableData, this.idx, this.form);
          this.tableData.splice(this.idx, 1);
        } else if (res.data.code == 201) {
          this.$message.success("请先进行设备解绑");
        } else if (res.data.code == 202) {
          this.$message.success("请先进行用户解绑");
        }
      });
      // this.$message.success("删除成功");
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
  font-size: 18px;
}
.tr {
  text-align: left;
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
.add-user {
  margin-left: 30px;
}
</style>
