<template>

</template>

<script>
export default {
  data () {
    return {
      currentUser: {}
    }
  },
  methods: {
    getUserInfo() {
      Vue.axios.get("/api/user/self").then(
        response => console.log(response)
      )
    }
  },
  created () {
    this.getUserInfo();
  }
}
</script>
