<template>
  <!-- <div>
    <Button class="create-domain" type="primary" @click="modal1 = true">新建域</Button>
    <Modal v-model="modal1" title="新建域" @on-ok="ok" @on-cancel="cancel">
      <Form>
        <FormItem label="域名">
          <Input type="text" v-model="dName"></Input>
        </FormItem>
      </Form>
    </Modal>

  </div> -->
  <div  style="background:#eee;padding: 20px">
        <Card :bordered="false" class="card">
            <p slot="title">No border title</p>
            <p>Content of no border type. Content of no border type. Content of no border type. Content of no border type. </p>
        </Card>
    </div>

</template>
<script>
export default {
  data() {
    return {
      modal1: false,
      dName: ""
    };
  },
  methods: {
    ok() {
      this.$Message.info("Clicked ok");
    },
    cancel() {
      this.$Message.info("Clicked cancel");
    },
    creatDomain() {}
  }
};
</script>

<style rel="stylesheet/scss" lang="scss" scope>
.create-domain{
  width: 280px;
  height: 180px;
  border-radius: 20px;
  font-size: 32px;
}
.card{
 color: white;
    font-weight: bold;
    padding: 10px;
    cursor: pointer;   
    transition: box-shadow 0.5s;
    -webkit-transition: box-shadow 0.5s;
     box-shadow:5px 5px 10px gray;
}
</style>
