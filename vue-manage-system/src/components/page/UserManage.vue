<template>
  <div class="table">
    <div class="crumbs">
      <el-button
        class="add-user b-red"
        type="danger"
        icon="el-icon-plus"
        @click="addUserStart()"
      >新增用户</el-button>
      <el-dialog title="新增用户" :visible.sync="dialogFormVisible" width="20%">
        <el-form :model="form" :rules="ruleValidate" ref="ruleForm">
          <el-form-item label="用户名" :label-width="formLabelWidth">
            <el-input v-model="form.user_name" autocomplete="off" prop="user_name"></el-input>
          </el-form-item>
          <el-form-item label="手机号" :label-width="formLabelWidth" prop="phone">
            <el-input v-model="form.phone" autocomplete="off"></el-input>
          </el-form-item>
          <el-form-item label="电子邮箱" :label-width="formLabelWidth" prop="email">
            <el-input v-model="form.email" autocomplete="off"></el-input>
          </el-form-item>
        </el-form>
        <div slot="footer" class="dialog-footer">
          <el-button @click="dialogFormVisible = false">取 消</el-button>
          <el-button type="primary" @click="addUser('ruleForm')">确 定</el-button>
        </div>
      </el-dialog>
    </div>
    <!-- 右侧白色 -->
    <div class="container">
      <!-- 表格 -->
      <div class="handle-box">
        <!-- 测试vuex -->
        <!-- <p>{{localStorage.getItem("loginDomainId")}}</p> -->
        <el-button type="primary" icon="delete" class="handle-del mr10" @click="delAll">批量删除</el-button>
        <div class="search">
          <el-input v-model="select_word" placeholder="筛选关键词" class="handle-input mr10"></el-input>
          <el-button type="primary" icon="search" @click="search">搜索</el-button>
        </div>
      </div>
      <!-- :data="data1" -->
      <!-- :header-cell-style="{background:'#20a0ff',color:'#92ff00'}" -->
      <!-- :row-class-name="tableRowClassName" -->

      <el-table
        :stripe="test"
        :header-cell-style="tableHeaderColor"
        :data="data1"
        :row-style="rowClass"
        class="table"
        ref="multipleTable"
        @selection-change="handleSelectionChange"
      >
        <el-table-column type="selection" :selectable="checkboxT" min-width="2%" align="center"></el-table-column>
        <el-table-column prop="user_name" label="用户名" min-width="14%">
          <!-- <template slot-scope="scope">
            <div :class="scope.row.cut_off==2? 'one' :''"> {{ scope.row.user_name }}</div>
          </template>-->
        </el-table-column>
        <el-table-column prop="remark" label="备注" min-width="14%"></el-table-column>
        <el-table-column prop="phone" label="手机号" min-width="11%"></el-table-column>
        <el-table-column prop="email" label="邮箱号" min-width="19%"></el-table-column>
        <el-table-column prop="group" label="匹配设备组" min-width="10%">
           <template slot-scope="scope">
              <!-- @mouseover.native="handleDetails(scope.$index, scope.row)" -->
 <el-button type="primary" @mouseleave.native='detailsOut()' @mouseenter.native="handleDetails(scope.$index, scope.row)">{{scope.row.group}}</el-button>
</template>
        </el-table-column>
        <el-table-column prop="hmi" label="匹配设备" min-width="10%"></el-table-column>
        <el-table-column label="相关操作" min-width="20%" align="center">
          <template slot-scope="scope">
            <el-button
              type="text"
              icon="el-icon-date"
              :disabled="scope.row.cut_off==2"
              @click="handleHmi(scope.$index, scope.row)"
            >设备管理</el-button>
            <el-dialog title="设备管理" :visible.sync="dialogFormVisible1" width="40%">
              <el-tabs v-model="activeName" @tab-click="handleClick">
                <el-tab-pane label="设备组" name="first">
                  <div class="tr">
                    <!-- 穿梭框 -->
                    <el-transfer
                      :button-texts="['进行解绑', '进行绑定']"
                      :titles="['未绑定设备组', '已绑定设备组']"
                      v-model="value1"
                      :data="dataGroup"
                      @left-check-change="aaa"
                      @right-check-change="bbb"
                      filterable
                      @change="hmiGroupChange"
                    ></el-transfer>
                    <div slot="footer" class="dialog-footer">
                      <el-button @click="dialogFormVisible1 = false">返 回</el-button>
                      <el-button type="primary" @click="saveHmiGroup()">确 定</el-button>
                    </div>
                  </div>
                </el-tab-pane>
                <el-tab-pane label="设备" name="second">
                  <div class="tr">
                    <el-transfer
                      :button-texts="['进行解绑', '进行绑定']"
                      :titles="['未绑定设备', '已绑定设备']"
                      v-model="value2"
                      :data="dataHmi"
                      filterable
                      @change="hmiChange"
                    ></el-transfer>
                    <div slot="footer" class="dialog-footer">
                      <el-button @click="dialogFormVisible1 = false">返 回</el-button>
                      <el-button type="primary" @click="saveHmi()">确 定</el-button>
                    </div>
                  </div>
                </el-tab-pane>
              </el-tabs>
            </el-dialog>

            <el-button
              :disabled="scope.row.cut_off==2"
              type="text"
              class="editbtn"
              icon="el-icon-edit"
              @click="handleEdit(scope.$index, scope.row)"
            >编辑</el-button>
             <!-- <el-button
              :disabled="scope.row.cut_off==2"
              type="text"
              class="editbtn"
              icon="el-icon-edit"
              @click="handleDetails(scope.$index, scope.row)"
            >组详情</el-button> -->
            <el-button
              :disabled="scope.row.cut_off==2"
              type="text"
              icon="el-icon-delete"
              class="red deletebtn"
              @click="handleDelete(scope.$index, scope.row)"
            >删除</el-button>
            <el-button
              type="text"
              icon="el-icon-close"
              class="red"
              @click="ban(scope.$index, scope.row)"
            >{{scope.row.cut_off==2? "解禁":"禁用"}}</el-button>
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
    <el-dialog title="编辑" :visible.sync="editVisible" width="20%">
      <el-form ref="form" :model="form" label-width="50px">
        <el-form-item label="备注">
          <el-input v-model="form.remark" maxlength="20" @change="SomeJavaScriptCode"></el-input>
        </el-form-item>
      </el-form>
      <span slot="footer" class="dialog-footer">
        <el-button @click="editVisible = false">取 消</el-button>
        <el-button type="primary" @click="saveEdit()">确 定</el-button>
      </span>
    </el-dialog>
    <!-- 组详情弹框 -->
 
    <el-dialog title="设备组详情" :visible.sync="detailsVisible" width="20%" :before-close="handleClose">
    <el-tree :data="dataTree" node-key="lable" :props="defaultProps"></el-tree>
      <!-- <span slot="footer" class="dialog-footer">
        <el-button @click="detailsVisible = false">取 消</el-button>
        <el-button type="primary" @click="saveDetails()">确 定</el-button>
      </span> -->
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
// import treeTransfer from "@/components/transfer-extend";
import store from "../../store/store.js";
// import bus from "../common/bus";
export default {
  name: "basetable",
  data() {
    //手机号校验
    const validatephone = (rule, value, callback) => {
      if (
        value &&
        (!/^[1][34578]\d{9}$/.test(value) ||
          !/^[1-9]\d*$/.test(value) ||
          value.length !== 11)
      ) {
        callback(new Error("手机号码不符合规范"));
      } else {
        callback();
      }
    };
    //邮箱校验
    const validatemail = (rule, value, callback) => {
      let temp = /^[\w.\-]+@(?:[a-z0-9]+(?:-[a-z0-9]+)*\.)+[a-z]{2,3}$/;
      let tempOne = /^[A-Za-zd]+([-_.][A-Za-zd]+)*@([A-Za-zd]+[-.])+[A-Za-zd]{2,5}$/;
      if (value && !temp.test(value)) {
        callback(new Error("邮箱格式不符合规范"));
      } else {
        callback();
      }
    };
    return {
      ff: "",
      shuzu2: [],
      shuzu3: [],
      valueTree:[],
      //树形图数据
      dataTree:[],
        defaultProps: {
          children: 'children',
          label: 'lable'
        },
      flag: 0,
      banText: "禁用",
      //匹配校验器
      ruleValidate: {
        phone: [
          { required: true, message: "请输入电话号码", trigger: "blur" },
          { validator: validatephone, trigger: "blur" }
        ],
        email: [
          { required: true, message: "请输入电子邮箱", trigger: "blur" },
          { validator: validatemail, trigger: "blur" }
        ]
      },
      total: "",
      form: {
        user_name: "",
        email: "",
        remark: "",
        phone: "",
        hmi: "",
        group: "",
        // domain_id:this.$store.state.domainId,
        //计算属性
        // domain_id:localStorage.getItem("loginDomainId"),
        id: ""
      },
      // ccc:this.$store.state.domainId,
      // ccc:"小明",
      // trArray:[],
      userId: "",
      checked: true, //寄送新用户密码
      activeName: "first",
      dataGroup: [],
      dataHmi: [],
      //穿梭框
      value1: [],
      // v1:[{name:'小明'}],
      //  value1:[{key:22,label:"222"}],
      value2: [],
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
      selectRow: [],
      selectData: [],
      test: true,
      shuzu: [],
      trUser: [],
      select_cate: "",
      select_word: "",
      del_list: [],
      is_search: false,
      editVisible: false,
      delVisible: false,
      detailsVisible:false,
      idx: -1
    };
  },
  // components: { treeTransfer },
  store,
  created() {
    this.$http({
        method: "get",
        url: "/api/user/userInfo",
        params: {
          domain_id: localStorage.getItem("loginDomainId"),
          limit: 10,
          page: this.cur_page
        }
      }).then(res => {
      //   if (!domain_id) {
      //   return;
      // }
      if(res.data.code == 302){
this.$router.push("/login");
      }
        this.total = res.data.total;
        // console.log(res.data.message[0].user_name)   输入h
        this.tableData = res.data.message;
      });
    // this.$store.commit('savetrArray',this.value1);
    // alert("刷新")
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
    // this.getData();
    // alert( this.$store.state.domainId)
    // this.$http({
    //   method: "post",
    //   url: "/api/user/supplyGroup",
    //   data: {
    //     id: localStorage.getItem("loginDomainId")
    //   }
    // }).then(res => {
    //   for (var i = 0; i < res.data.message.length; i++) {
    //     this.dataGroup.push({
    //       // key: i + 1,
    //       key: res.data.message[i].id,
    //       label: res.data.message[i].group_name,
    //     });
    //   }
    // });
    //调用获取所有设备 接口
    // this.$http({
    //   method: "post",
    //   url: "/api/user/hmiGroup",
    //   data: {
    //     id: localStorage.getItem("loginDomainId")
    //   }
    // }).then(res => {
    //   for (var i = 0; i < res.data.message.length; i++) {
    //     this.dataHmi.push({ key: res.data.message[i].id, label: res.data.message[i].hmi_name });
    //   }
    // });
  },
  computed: {
    domain_id() {
      return this.$store.state.domainId;
    },
    // trArray(){
    //   console.log(55555555);
    //   console.log(this.$store.state.trArray)
    //     return this.$store.state.trArray
    // },

    data1() {
      return this.tableData.filter(d => {
        let is_del = false;
        for (let i = 0; i < this.del_list.length; i++) {
          if (d.user_name === this.del_list[i].user_name) {
            is_del = true;
            break;
          }
        }
        if (!is_del) {
          if (
            d.user_name.indexOf(this.select_cate) > -1 &&
            (d.user_name.indexOf(this.select_word) > -1 ||
              d.user_name.indexOf(this.select_word) > -1)
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
   handleClose(done) {
            done();
      },
    aaa() {
      this.ff = 1;
      //获取已绑定设备组
      this.$http({
        method: "post",
        url: "/api/user/supplyGroup",
        data: {
          id: localStorage.getItem("loginDomainId"), //域id
          user_id: this.form.id
        }
      }).then(res => {
        this.value1 = [];
        for (var i = 0; i < res.data.message.length; i++) {
          if (res.data.message[i].status == 1) {
            this.value1.push(res.data.message[i].id);
          }
        }
        this.shuzu2 = this.value1;
      });
    },
    bbb() {
      this.ff = 0;
      //获取已绑定设备组
      this.$http({
        method: "post",
        url: "/api/user/supplyGroup",
        data: {
          id: localStorage.getItem("loginDomainId"), //域id
          user_id: this.form.id
        }
      }).then(res => {
        this.value1 = [];
        for (var i = 0; i < res.data.message.length; i++) {
          if (res.data.message[i].status == 1) {
            this.value1.push(res.data.message[i].id);
          }
        }
        this.shuzu2 = this.value1;
      });
    },
    //数组去重
    getArrDifference(arr1, arr2) {
      return arr1.concat(arr2).filter(function(v, i, arr) {
        return arr.indexOf(v) === arr.lastIndexOf(v);
      });
    },
    addUserStart() {
      this.form = [];
      this.dialogFormVisible = true;
    },
    // 表格选中行的颜色
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
    rowClass({ row, rowIndex }) {
      if (this.selectRow.includes(rowIndex)) {
        return { "background-color": "rgba(185, 221, 249, 0.75)" };
      }
    },
    // selectedHighlight(row) {
    //   if ( row.is==true  ) {
    //     return {
    //       "background-color": "black"
    //     };
    //     return {}
    //   }
    // },
    //表头样式
    tableHeaderColor({ row, column, rowIndex, columnIndex }) {
      //       background: -webkit-linear-gradient(top left, #007acc 0%, #563516 100%);
      // background: linear-gradient(to bottom right, #007acc 0%, #563516 100%);
      if (rowIndex === 0) {
        return "background-color: #7dc1ff;color: #ffffff;font-weight:10;";
      }
    },
    //  tableRowClassName({row, rowIndex}) {
    //         if (rowIndex === 1) {
    //           return 'warning-row';
    //         } else if (rowIndex === 3) {
    //           return 'success-row';
    //         }
    //         return '';
    //       },

    //获取所有设备组
    getGroup() {
      this.$http({
        method: "post",
        url: "/api/group/supplyInfo",
        data: {
          id: localStorage.getItem("loginDomainId"), //域id
          limit: 10000,
          page: 1
        }
      }).then(res => {
        this.dataGroup = [];
        for (var i = 0; i < res.data.message.length; i++) {
          this.dataGroup.push({
            // key: i + 1,
            key: res.data.message[i].id,
            label: res.data.message[i].group_name
          });
        }
      });
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
        this.dataHmi = [];
        for (var i = 0; i < res.data.message.data.length; i++) {
          this.dataHmi.push({
            key: res.data.message.data[i].id,
            label: res.data.message.data[i].hmi_name
          });
        }
      });
    },
    //禁用-解禁按钮
    ban(index, row) {
      this.idx = index;
      const item = this.tableData[index];
      this.form = {
        //用户id
        id: item.id
      };
      if (this.flag == 0) {
        // this.idx = index;
        this.$http({
          method: "post",
          url: "/api/user/forbid",
          params: {
            id: this.form.id
          }
        }).then(res => {
          this.getData();
        });
        this.flag = this.flag + 1;
        // this.banText = "解禁";
      } else if (this.flag == 1) {
        this.$http({
          method: "post",
          url: "/api/user/unforbid",
          params: {
            id: this.form.id
          }
        }).then(res => {
          this.getData();
        });
        this.flag = this.flag - 1;
        // this.banText = "禁用";
      }
    },
    //禁用多选框
    checkboxT(row, rows) {
      return row.cut_off != 2;
    },

    //穿梭框的hmigroupchange事件
    hmiGroupChange() {
      //数组去重
      this.shuzu3 = this.getArrDifference(this.value1, this.shuzu2);
      if (this.ff == 1) {
        this.$http({
          method: "post",
          url: "/api/user/supplyGroupBind",
          data: {
            domain_id: localStorage.getItem("loginDomainId"),
            user_id: this.form.id,
            id: this.shuzu3
          }
        }).then(res => {});
      } else if (this.ff == 0) {
        this.$http({
          method: "post",
          url: "/api/user/unsupplyGroupBind",
          data: {
            domain_id: localStorage.getItem("loginDomainId"),
            user_id: this.form.id,
            id: this.shuzu3
          }
        }).then(res => {});
      }
    },
    //穿梭框的hmichange事件
    hmiChange() {
      // console.log("777");
      // console.log(this.form.domain_id);
      // console.log(this.form.id);
      this.$http({
        method: "post",
        url: "/api/user/hmiGroupBind",
        data: {
          domain_id: localStorage.getItem("loginDomainId"),
          user_id: this.form.id,
          id: this.value2
        }
      }).then(res => {});
    },
    // 编辑的change事件
    SomeJavaScriptCode() {
      this.$http({
        method: "post",
        url: "/api/user/updateInfo",
        data: {
          id: this.form.id,
          email: this.form.email,
          remark: this.form.remark
        }
      }).then(res => {});
    },
    addUser(formName) {
      this.dialogFormVisible = false;
      this.$refs[formName].validate(valid => {
        if (valid) {
          this.$http
            .post("/api/user/addUser", {
              user_name: this.form.user_name,
              email: this.form.email,
              domain_id: localStorage.getItem("loginDomainId"), //域id
              phone: this.form.phone
            })
            .then(res => {
              if (res.data.status == "S") {
                this.$message({
                  message: "新增用户成功",
                  type: "success"
                });
                this.getData();
                this.dialogFormVisible = false;
              } else if (res.data.status == "F") {
                this.$message({
                  message: "用户名已存在   !",
                  type: "warning"
                });
              }
            });
        } else {
          this.$message.error("请正确输入用户信息   !");
        }
      });
    },
    // 分页导航
    handleCurrentChange(val) {
      this.cur_page = val;
      this.getData();
    },
    getData() {
      // alert("用户获取")
      this.$http({
        method: "get",
        url: "/api/user/userInfo",
        params: {
          domain_id: localStorage.getItem("loginDomainId"),
          limit: 10,
          page: this.cur_page
        }
      }).then(res => {
        // console.log(res.data.message[0].user_name)   输入h
        this.total = res.data.total;
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
      //     if (localStorage.getItem("tr1") == null) {
      //    this.value1=[];
      // }else{
      //  this.value1 = JSON.parse(localStorage.getItem('tr1'));
      // }
      this.getGroup();
      this.getHmi();
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
        group: item.group,
        domain_id: localStorage.getItem("loginDomainId"),
        //用户id
        id: item.id
      };
      //获取已绑定设备组
      this.$http({
        method: "post",
        url: "/api/user/supplyGroup",
        data: {
          id: localStorage.getItem("loginDomainId"), //域id
          user_id: this.form.id
        }
      }).then(res => {
        this.value1 = [];
        for (var i = 0; i < res.data.message.length; i++) {
          if (res.data.message[i].status == 1) {
            this.value1.push(res.data.message[i].id);
          }
        }
        this.shuzu2 = this.value1;
      });
      //获取已绑定设备
      this.$http({
        method: "post",
        url: "/api/user/hmiGroup",
        data: {
          id: localStorage.getItem("loginDomainId"), //域id
          user_id: this.form.id
        }
      }).then(res => {
        this.value2 = [];
        for (var i = 0; i < res.data.message.length; i++) {
          if (res.data.message[i].status == 1) {
            this.value2.push(res.data.message[i].id);
          }
        }
      });

      //从用户界面获取所有添加的设备组
      // this.$http({
      //     method: "post",
      //     url: "/api/user/supplyGroup",
      //     data: {
      //       id: localStorage.getItem("loginDomainId")
      //     }
      //   }).then(res => {
      //     for (var i = 0; i < res.data.message.length; i++) {
      //       this.dataGroup.push({
      //         // key: i + 1,
      //         key: res.data.message[i].id,
      //         label: res.data.message[i].group_name,
      //       });
      //     }
      //   });
      //从用户界面获取所有添加的设备
      //  this.$http({
      //       method: "post",
      //       url: "/api/user/hmiGroup",
      //       data: {
      //         id: localStorage.getItem("loginDomainId")
      //       }
      //     }).then(res => {
      //       for (var i = 0; i < res.data.message.length; i++) {
      //         this.dataHmi=[{ key: res.data.message[i].id, label: res.data.message[i].hmi_name }];
      //       }
      //     });

      this.dialogFormVisible1 = true;
    },

    //11111111111111111  编辑按钮
    handleEdit(index, row) {
      // alert(12);
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
        group: item.group,
        domain_id: localStorage.getItem("loginDomainId"),
        //用户id
        id: item.id
      };

      this.editVisible = true;
    },
    //组详情
    handleDetails(index, row){
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
        group: item.group,
        domain_id: localStorage.getItem("loginDomainId"),
        //用户id
        id: item.id
      };
     
    //获取已绑定设备组
      this.$http({
        method: "post",
        url: "/api/user/supplyGroup",
        data: {
          id: localStorage.getItem("loginDomainId"), //域id
          user_id: this.form.id
        }
      }).then(res => {
        this.valueTree = [];
        for (var i = 0; i < res.data.message.length; i++) {
          if (res.data.message[i].status == 1) {
            this.valueTree.push(res.data.message[i].id);
          }
        }
        // this.shuzu2 = this.value1;
        this.detailsVisible = true;
           this.$http({
        method:'post',
        url: "/api/group/hmidetail",
        data: {
          id: this.valueTree
        }
      }).then(res => {
        // console.log(res.data.message[0].user_name)   输入h
        console.log(333333333333333);
        // console.log(res.data.message);
         this.dataTree=res.data.message;
          console.log(this.dataTree)
        //  this.tableData = res.data.message;
      });
      });
      
    },
    detailsOut(){
      // this.handleClose();
      // // console.log("移出")
      // alert(111)
    },
    detailsOver(){
  console.log("移入")
    },
    //删除
    handleDelete(index, row) {
      this.idx = index;

      const item = this.tableData[index];
      this.form = {
        // remark: item.remark,
        user_name: item.user_name,
        remark: item.remark,
        phone: item.phone,
        email: item.email,
        hmi: item.hmi,
        group: item.group,
        domain_id: localStorage.getItem("loginDomainId"),
        //用户id
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
    //保存设备组管理
    saveHmiGroup() {
      //  alert(this.value1)
      // this.$store.commit('savetrArray',this.value1);
      // alert(this.value1.length)
      this.$set(this.tableData, this.idx, this.form);
      this.dialogFormVisible1 = false;
      this.$message.success(`修改第 ${this.idx + 1} 行成功`);
      this.getData();
      // localStorage.setItem('tr1',JSON.stringify(this.value1));
    },
    //保存设备管理
    saveHmi() {
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
    //确定组详情
    saveDetails(){
      this.$set(this.tableData, this.idx, this.form);
      this.detailsVisible = false;
    },
    // 确定删除
    deleteRow() {
      // this.$set(this.tableData, this.idx, this.form);
      // this.tableData.splice(this.idx, 1);
      // const a=this.form.id;
      // alert(this.form.id)
      this.$http({
        method: "get",
        url: "/api/user/delete",
        params: {
          user_id: [this.form.id]
        }
      }).then(res => {
        if(res.data.status == "S"){
this.$message.success("删除成功");
this.$set(this.tableData, this.idx, this.form);
this.tableData.splice(this.idx, 1);
        }else if(res.data.code == 201){
this.$message.success("请先进行设备组解绑");
        }else if(res.data.code == 202){
          this.$message.success("请先进行设备解绑");
        }
        // console.log(res.data.message[0].user_name)   输入h
        console.log(333333333333333);
        console.log(res);
        //  this.tableData = res.data.message;
      });
      // this.$message.success("删除成功");
      this.delVisible = false;
    }
  }
};
</script>

<style >
.one {
  background: #f56c6c;
}
.search {
  float: right;
}
.handle-box {
  margin-bottom: 15px;
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
.add-user {
  margin-left: 40px;
  border: 10px;
}
.el-table td,
.el-table th.is-leaf {
  border-bottom: 1px solid #dcdee2;
}

.el-table--border td {
  border-right: 1px solid #dcdee2;
}

.el-table--enable-row-hover .el-table__body tr:hover > td {
  background-color: #e8e9ea;
  /* color: #5bcf01; */
}
/* .el-table .warning-row {
    background: oldlace;
  }

  .el-table .success-row {
    background: #f0f9eb;
  } */
.container {
  padding: 15px 30px;
  border-radius: 10px;
}
.el-button + .el-button {
  margin-left: 0px;
}
/* .delete1{
   margin-left: 10px;
} */

/* .pagination{
  padding-right: 800px;
} */
table {
  border-collapse: inherit;
}

.el-table th > .cell {
  text-align: center;
}
.el-table .cell {
  text-align: center;
}
.add-user {
  margin-left: 30px;
}
</style>
