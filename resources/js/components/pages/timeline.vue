<template>
  <div class="feedPosts">
    <mu-card v-for="(f,index) in feeds">
      <mu-card-header :key="index" :title="f.user.first_name+ ' '+f.user.last_name">
        <mu-avatar slot="avatar">
          <img :src="(f.user.profile != null) ? f.user.profile : '/img/profile.jpg'">
        </mu-avatar>
      </mu-card-header>
      <mu-card-media>
        <img :src="'/storage/medias/'+f.thumb_path">
      </mu-card-media>
      <mu-card-text>{{f.caption}}</mu-card-text>
      <mu-card-actions>
        <mu-button flat>like</mu-button>
      </mu-card-actions>
    </mu-card>
  </div>
</template>
<script>
export default {
  data() {
    return {
      feeds: []
    };
  },
  mounted:async function() {
    let _this = this;
    let response = await this.follow("qqwe23");
    //console.log(response);
    if (this.$auth.check()) {
      Vue.axios.get("/api/feeds").then(function(data) {
        //console.log(data.data.data);
        _this.feeds = data.data.data;
      });
    }
  }
};
</script>

