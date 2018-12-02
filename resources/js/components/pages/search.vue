<template>
  <div class="searchSection">
    <form @submit.prevent='search'>
      <mu-text-field class="rtl" v-model="searchInput" label="جستوجو..." label-float @submit='search'></mu-text-field>
    </form>
    <div class="result">
        <mu-card v-for="result in searchResult">
          <mu-card-header :title="result.username + ((result.profile_status !== 'PUBLIC') ? ' 🔒' : '')" :sub-title="result.first_name + ' ' + result.last_name">
            <mu-avatar slot="avatar">
              <img :src="result.profile" v-if="result.profile != null">
              <img src="/img/profile.jpg" v-else>
            </mu-avatar>
          </mu-card-header>
          <mu-card-text>
            دنبال‌کننده‌ها: {{result.followers_count}}<br>
            دنبال‌شنوده‌ها: {{result.followings_count}}<br>
            پست‌ها: {{result.medias_count}}

          </mu-card-text>
          <mu-card-actions>
            <mu-button flat>دنبال کن</mu-button>
            <mu-button flat>مشاهده پروفایل</mu-button>
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
  mounted() {
    
  },
  methods: {
    search: function () {
      this.searchResult = []
      let _this = this
      Vue.axios.get("/api/search", {
        params: {
          query: this.searchInput
        }
      }).then((response) => {_this.searchResult = response.data.data})
    }
  }
};
</script>

