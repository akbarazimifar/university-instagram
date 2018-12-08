<template>
  <div class="feedPosts">
    <mu-card v-for="(f,index) in feeds" :key="index">
      <mu-card-header :key="index" :title="f.user.first_name+ ' '+f.user.last_name">
        <mu-avatar slot="avatar">
          <img :src="(f.user.profile != null) ? '/storage/profiles/'+f.user.profile.thumb_path : '/img/profile.jpg'">
        </mu-avatar>
      </mu-card-header>
      <mu-card-media>
        <mu-button icon class="showImageButton" @click="openDialog(f,index)">
          <mu-icon value="remove_red_eye"></mu-icon>
        </mu-button>
        <img :src="'/storage/medias/'+f.thumb_path">
      </mu-card-media>
      <mu-card-text>{{f.caption}}</mu-card-text>
      <mu-card-actions>
        <mu-button color="error" v-if="f.is_liked" @click="unLikePost(f.user.username,f.id,index)">
          <mu-icon right value="favorite"></mu-icon>پسندیدم
        </mu-button>
        <mu-button v-if="!f.is_liked" @click="likePost(f.user.username,f.id,index)">
          <mu-icon right value="favorite_border"></mu-icon>میپسندم
        </mu-button>
      </mu-card-actions>
    </mu-card>

    <mu-dialog width="360" transition="slide-bottom" fullscreen :open.sync="openFullscreen">
      <mu-appbar color="primary" title>
        <mu-button slot="right" icon @click="closeFullscreenDialog">
          <mu-icon value="close"></mu-icon>
        </mu-button>
      </mu-appbar>
      <div style="padding: 24px;" class="rtl">
        <mu-container>
          <div class="imgOrg">
            <img :src="'/storage/medias/'+selectedPost.file_path">
          </div>

          <div class="detailsOrg">
            <mu-row>
              <p>{{selectedPost.caption}}</p>
            </mu-row>
            <br>
            <mu-row>
              <span>تعداد لایک ها : {{selectedPost.likes_count}}</span>
            </mu-row>
            <mu-row>
              <mu-button
                color="error"
                v-if="selectedPost.is_liked"
                @click="unLikePost(selectedPost.user.username,selectedPost.id,selectedPost.index)"
              >
                <mu-icon right value="favorite"></mu-icon>پسندیدم
              </mu-button>
              <mu-button
                v-if="!selectedPost.is_liked"
                @click="likePost(selectedPost.user.username,selectedPost.id,selectedPost.index)"
              >
                <mu-icon right value="favorite_border"></mu-icon>میپسندم
              </mu-button>
            </mu-row>
          </div>
        </mu-container>
      </div>
    </mu-dialog>
  </div>
</template>
<script>
export default {
  data() {
    return {
      feeds: [],
      openFullscreen: false,
      selectedPost: {}
    };
  },
  methods: {
    unLikePost(username, id, index) {
      this.unLike(username, id);
      this.feeds[index].likes_count--;
      this.feeds[index].is_liked = false;
    },
    likePost(username, id, index) {
      this.like(username, id);
      this.feeds[index].likes_count++;
      this.feeds[index].is_liked = true;
    },
    closeFullscreenDialog() {
      this.openFullscreen = false;
    },
    openDialog(post, index) {
      this.selectedPost = post;
      this.selectedPost.index = index;
      this.openFullscreen = true;
    }
  },
  mounted: async function() {
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

