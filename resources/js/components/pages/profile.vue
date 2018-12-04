<template>
  <div class="rtl">
    <mu-container class="center">
      <mu-avatar size="128">
        <img :src="(user.profile !== null) ? user.profile : '/img/profile.jpg'">
      </mu-avatar>
      <ul style="">
        <li>{{user.first_name}} {{user.last_name}}</li>
        <li>{{user.username}}</li>
      </ul>
    </mu-container>
  </div>
</template>

<script>
export default {
  data () {
    return {
      user: {}
    }
  },
  methods: {
    getUser () {
      Vue.axios.get("/api/" + this.$route.params.username + "/.").then(
        resp => this.user = resp.data
      )
    },
    getUserMedia () {
      Vue.axios.get("/api/" + this.$route.params.username + "/medias").then(
        resp => console.log(resp)
      )

    }
  },
  created () {
    this.getUser()
  },
  mounted () {
    this.getUserMedia()
  }
}
</script>
