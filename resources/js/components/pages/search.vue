<template>
  <div class="searchSection">
    <form @submit.prevent="search">
      <mu-text-field
        class="rtl"
        v-model="searchInput"
        label="جستجو..."
        label-float
        @submit="search"
      ></mu-text-field>
    </form>
    <div class="result">
      <mu-card v-for="(result,i) in searchResult" :key="i+1">
        <mu-card-header
          :title="result.username + ((result.profile_status !== 'PUBLIC') ? ' 🔒' : '')"
          :sub-title="result.first_name + ' ' + result.last_name"
        >
          <mu-avatar slot="avatar">
            <img
              :src="(result.profile !== null) ? '/storage/profiles/'+result.profile.thumb_path : '/img/profile.jpg'"
            >
          </mu-avatar>
        </mu-card-header>
        <mu-card-text>
          دنبال‌کننده‌ها: {{result.followers_count}}
          <br>
          دنبال‌شنوده‌ها: {{result.followings_count}}
          <br>

          پست‌ها: {{result.medias_count}}
        </mu-card-text>
        <mu-card-actions>
          <mu-button v-if="result.is_followed" flat @click="unfollow_result(i)">دنبال نکن</mu-button>
          <mu-button v-else color="info" @click="follow_result(i)">دنبال کن</mu-button>
          <mu-button
            class="inline-table"
            flat
            :to="{name : 'profile',params :{username : result.username}}"
          >مشاهده پروفایل</mu-button>
        </mu-card-actions>
      </mu-card>
    </div>
  </div>
</template>
<script>
export default {
  data() {
    return {
      searchInput: "",
      searchResult: []
    };
  },
  mounted() {},
  methods: {
    search: function() {
      this.searchResult = [];
      let _this = this;
      Vue.axios
        .get("/api/search", {
          params: {
            query: this.searchInput
          }
        })
        .then(response => {
          _this.searchResult = response.data.data;
        });
    },
    follow_result(index) {
      this.follow(this.searchResult[index].username);
      this.searchResult[index].followers_count++;
      this.searchResult[index].is_followed = true;
    },
    unfollow_result(index) {
      this.unFollow(this.searchResult[index].username);
      this.searchResult[index].followers_count--;
      this.searchResult[index].is_followed = false;
    }
  }
};
</script>

