<template>
  <div>
    <Button class="create-domain" type="primary" @click="modal1 = true">新建域</Button>
    <Modal v-model="modal1" title="新建域" @on-ok="ok" @on-cancel="cancel">
      <Form>
        <FormItem label="域名">
          <Input type="text" v-model="dName"></Input>
        </FormItem>
      </Form>
    </Modal>
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
</style>
