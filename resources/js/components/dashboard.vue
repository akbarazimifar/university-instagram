<template>
  <mu-row v-if="loaded">
    <mu-col span="12" sm="12" md="12" lg="9" xl="9" class="container">
      <router-view></router-view>
    </mu-col>
    <mu-col span="12" sm="0" md="0" lg="3" xl="3" class="sidebar">
      <div class="profileSelction">
        <img
          :src="($auth.user().profile !== null) ? '/storage/profiles/'+$auth.user().profile.thumb_path : '/img/profile.jpg'"
        >
        <h2>{{$auth.user().first_name + ' ' + this.$auth.user().last_name}}</h2>
      </div>
      <div class="options">
        <mu-paper :z-depth="1">
          <mu-list>
            <mu-list-item button :ripple="true" to="/feeds">
              <mu-list-item-title>جدیدترین رسانه ها</mu-list-item-title>
              <mu-list-item-action>
                <mu-icon value="home"></mu-icon>
              </mu-list-item-action>
            </mu-list-item>
            <mu-list-item button :ripple="true" to="/search">
              <mu-list-item-title>جستجو</mu-list-item-title>
              <mu-list-item-action>
                <mu-icon value="search"></mu-icon>
              </mu-list-item-action>
            </mu-list-item>
            <mu-list-item button :ripple="true" to="/upload">
              <mu-list-item-title>آپلود رسانه</mu-list-item-title>
              <mu-list-item-action>
                <mu-icon value="cloud_upload"></mu-icon>
              </mu-list-item-action>
            </mu-list-item>
            <mu-list-item
              button
              :ripple="true"
              :to="{name:'profile', params:{username: this.$auth.user().username}}"
            >
              <mu-list-item-title>پروفایل</mu-list-item-title>
              <mu-list-item-action>
                <mu-icon value="person"></mu-icon>
              </mu-list-item-action>
            </mu-list-item>
            <mu-list-item button :ripple="true" @click="$auth.logout()">
              <mu-list-item-title>خـروج</mu-list-item-title>
              <mu-list-item-action>
                <mu-icon value="power_settings_new"></mu-icon>
              </mu-list-item-action>
            </mu-list-item>
          </mu-list>
        </mu-paper>
      </div>
    </mu-col>

    <mu-container class="RespAppbar rtl">
      <mu-bottom-nav>
        <mu-bottom-nav-item title="جدیدترین رسانه ها" icon="home" to="/feeds"></mu-bottom-nav-item>
        <mu-bottom-nav-item title="جستجو" icon="search" to="/search"></mu-bottom-nav-item>
        <mu-bottom-nav-item title="آپلود رسانه" icon="cloud_upload" to="/upload"></mu-bottom-nav-item>
        <mu-bottom-nav-item
          title="پروفایل"
          icon="person"
          :to="{name:'profile', params:{username: this.$auth.user().username}}"
        ></mu-bottom-nav-item>
        <mu-bottom-nav-item title="خـروج" icon="power_settings_new" @click="$auth.logout()"></mu-bottom-nav-item>
      </mu-bottom-nav>
    </mu-container>
  </mu-row>
</template>

<script>
export default {
  data() {
    return {
      loaded: false
    };
  },
  created() {},
  mounted: function() {
    //console.log(this.$auth.check());
    if (!this.$auth.check()) {
      Vue.router.push("login");
    } else {
      //Vue.router.push("feeds");
      this.loaded = true;
      //console.log(this.$auth.user())
    }
  },
  methods: {
    logout: function() {
      this.$auth.logout({
        makeRequest: true,
        success(data) {},
        error() {
          console.log("error " + this.context);
        }
      });
    }
  }
};
</script>
