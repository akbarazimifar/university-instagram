<template>
  <div class="rtl">
    <mu-card class="mainContainer">
      <mu-container class="profile">
        <mu-row>
          <mu-col span="12" sm="6">
            <mu-card-header
              v-if="$auth.user().username != $route.params.username"
              class="profileHeader"
              :title="user.first_name + ' ' + user.last_name"
              :sub-title="user.username"
            >
              <mu-avatar slot="avatar" size="100">
                <img :src="(user.profile !== null) ? '/storage/profiles/'+user.profile.thumb_path : '/img/profile.jpg'">
              </mu-avatar>
            </mu-card-header>

            <mu-card-header
              v-if="$auth.user().username == $route.params.username"
              class="profileHeader"
              :title="$auth.user().first_name + ' ' + $auth.user().last_name"
              :sub-title="$auth.user().username"
            >
              <mu-avatar slot="avatar" size="100">
                <img
                  :src="($auth.user().profile !== null) ? '/storage/profiles/'+$auth.user().profile.thumb_path : '/img/profile.jpg'"
                >
              </mu-avatar>
            </mu-card-header>
          </mu-col>
          <mu-col span="12" sm="6">
            <mu-card-text>
              دنبال‌کننده‌ها: {{user.followers_count}}
              <br>
              دنبال‌شنوده‌ها: {{user.followings_count}}
              <br>
              پست‌ها: {{user.medias_count}}
            </mu-card-text>
            <mu-card-actions
              v-if="user.username != undefined  && $route.params.username == $auth.user().username"
            >
              <mu-button @click="openFileUpload()">ویرایش پروفایل</mu-button>
            </mu-card-actions>
            <mu-card-actions
              v-if="user.username != undefined && $route.params.username != $auth.user().username"
            >
              <mu-button
                v-if="user.is_followed"
                flat
                @click="unfollowUser(user.username)"
                class="border"
              >
                دنبال
                نکن
              </mu-button>
              <mu-button v-else color="info" @click="followUser(user.username)">دنبال کن</mu-button>
            </mu-card-actions>
          </mu-col>
        </mu-row>

        <h3>رسانه ها</h3>
        <div class="medias">
          <div v-if="medias.length > 0">
            <mu-card v-for="(f, index) in medias" :key="f.id">
              <mu-card-media>
                <img :src="'/storage/medias/' + f.thumb_path" @click="openDialog(f,index)">
              </mu-card-media>
            </mu-card>
          </div>
          <div v-if="!medias.length && mediasRequestSent">رسانه ای برای نمایش وجود ندارد.</div>
          <div v-if="!medias.length && !mediasRequestSent">در حال بارگذاری رسانه ها</div>
        </div>
      </mu-container>
    </mu-card>

    <mu-dialog title="ویرایش پروفایل" width="700" :open.sync="openUserpicDialog" class="rtl">
      <mu-form ref="form" :model="editProfile" class="rtl">
        <mu-row gutter>
          <mu-col span="12" sm="12" md="12" lg="6" xl="6">
            <mu-form-item label="نام" prop="first_name" :rules="nameRules">
              <mu-text-field v-model="editProfile.first_name" prop="first_name"></mu-text-field>
            </mu-form-item>
            <mu-form-item label="نام خانوادگی" prop="last_name">
              <mu-text-field v-model="editProfile.last_name" prop="last_name"></mu-text-field>
            </mu-form-item>
            <mu-form-item label="نوع صفحه">
              <mu-checkbox
                v-model="editProfile.profileStatus"
                value="lock"
                uncheck-icon="public"
                checked-icon="lock"
                :label="(editProfile.profileStatus) ? 'خصوصی' : 'عمومی'"
              ></mu-checkbox>
            </mu-form-item>
          </mu-col>
          <mu-col span="12" sm="12" md="12" lg="6" xl="6">
            <mu-form-item label="رمزعبور" prop="password" :rules="passwordRules">
              <mu-text-field type="password" v-model="editProfile.password" prop="password"></mu-text-field>
            </mu-form-item>
            <mu-form-item
              label="تکرار رمزعبور"
              prop="password_confirm"
              :rules="password_confirmRules"
            >
              <mu-text-field
                type="password"
                v-model="editProfile.password_confirm"
                prop="password_confirm"
              ></mu-text-field>
            </mu-form-item>
            <mu-form-item label="عکس پروفایل">
              <input type="file" ref="fileupload" @change="onFileChanged">
            </mu-form-item>
          </mu-col>
        </mu-row>
      </mu-form>
      <span v-if="saveSucceed" class="mu-form-item__success right">پروفایل شما با موفقیت ویرایش شد.</span>
      <span v-if="saveError" class="mu-form-item__error right">مشکلی در ثبت اطلاعات رخ داده است.</span>
      <mu-button slot="actions" flat color="primary" @click="save">ثبت</mu-button>
      <mu-button slot="actions" flat @click="closeSimpleDialog">لغو</mu-button>
    </mu-dialog>

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
                @click="unLikePost(selectedPost.id,selectedPost.index)"
              >
                <mu-icon right value="favorite"></mu-icon>پسندیدم
              </mu-button>
              <mu-button
                v-if="!selectedPost.is_liked"
                @click="likePost(selectedPost.id,selectedPost.index)"
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
      user: {},
      medias: [],
      mediasRequestSent: false,
      openFullscreen: false,
      openUserpicDialog: false,
      selectedPost: {},
      selectedFile: null,
      saveSucceed: false,
      saveError: false,

      editProfile: {
        last_name: this.$auth.user().last_name,
        first_name: this.$auth.user().first_name,
        password: "",
        password_confirm: "",
        profile_status: this.getStatus(this.$auth.user().profile_status),
        profile_photo: null
      },
      nameRules: [
        {
          validate: val => !!val,
          message: "این فیلد باید پر شود."
        }
      ],
      passwordRules: [
        {
          validate: val => !(val.length > 0 && val.length < 8),
          message: "رمز عبور باید بیشتر از 8 کارکتر باشد"
        }
      ],
      password_confirmRules: [
        {
          validate: val => val == this.editProfile.password,
          message: "رمزهای عبور مشابه وارد نشده اند."
        }
      ]
    };
  },
  methods: {
    getUser() {
      Vue.axios
        .get("/api/" + this.$route.params.username + "/.")
        .then(resp => (this.user = resp.data));
    },
    getUserMedia() {
      Vue.axios
        .get("/api/" + this.$route.params.username + "/medias")
        .then(resp => {
          this.medias = resp.data.data;
          this.mediasRequestSent = true;
        });
    },
    followUser(username) {
      this.follow(username);
      this.user.is_followed = true;
      this.user.followers_count++;
    },
    unfollowUser(username) {
      this.unFollow(username);
      this.user.is_followed = false;
      this.user.followers_count--;
    },
    closeFullscreenDialog() {
      this.openFullscreen = false;
    },
    openDialog(post, index) {
      this.selectedPost = post;
      this.selectedPost.index = index;
      this.openFullscreen = true;
    },
    unLikePost(id, index) {
      this.unLike(this.$route.params.username, id);
      this.medias[index].likes_count--;
      this.medias[index].is_liked = false;
    },
    likePost(id, index) {
      this.like(this.$route.params.username, id);
      this.medias[index].likes_count++;
      this.medias[index].is_liked = true;
    },
    openFileUpload() {
      this.openUserpicDialog = true;
    },
    onFileChanged(event) {
      this.editProfile.profile_photo = event.target.files[0];
      console.log(this.editProfile.profile_photo);
    },
    closeSimpleDialog() {
      this.openUserpicDialog = false;
    },
    getStatus(status) {
      if (status == "PUBLIC") return false;
      else return true;
    },
    save() {
      let _this = this;
      _this.saveError = false;
      _this.saveSucceed = false;
      this.$refs.form.validate().then(result => {
        if (result) {
          const formData = new FormData();
          formData.append("first_name", _this.editProfile.first_name);
          if (_this.editProfile.profile_photo != null)
            formData.append(
              "profile_photo",
              _this.editProfile.profile_photo,
              _this.editProfile.profile_photo.name
            );
          if (_this.editProfile.last_name.length)
            formData.append("last_name", _this.editProfile.last_name);
          if (_this.editProfile.profile_status)
            formData.append("profile_status", "PRIVATE");
          else formData.append("profile_status", "PUBLIC");
          if (_this.editProfile.password.length) {
            formData.append("password", _this.editProfile.password);
            formData.append(
              "password_confirmation",
              _this.editProfile.password_confirm
            );
          }
          Vue.axios
            .post("/api/" + _this.$auth.user().username + "/edit", formData)
            .then(resp => {
              _this.saveSucceed = true;
              _this.saveError = false;
              _this.$auth.fetch();
            })
            .catch(error => {
              _this.saveSucceed = false;
              _this.saveError = true;
            });
        }
      });
    }
  },
  created() {
    this.getUser();
  },
  mounted() {
    this.getUserMedia();
  },
  fileUploadClear() {
    const input = this.$refs.fileupload;
    input.type = "text";
    input.type = "file";
  }
};
</script>
