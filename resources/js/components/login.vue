<template>
    <mu-container class="demo-container">
        <mu-row gutter>
            <mu-col span="12" sm="12" md="8" lg="6" xl="4" class="maginAuto">
                <mu-card class='rtl loginCard'>
                    <mu-card-title title="ورود کاربران" sub-title="برای ورود به پنل کاربری ایمیل و رمز عبور خود را وارد کنید"></mu-card-title>
                    <mu-card-text>
                        <mu-form ref="form" :model="data.body" class="rtl">
                            <mu-form-item label="ایمیل" prop="username" :rules="usernameRules">
                                <mu-text-field v-model="data.body.username" prop="username"></mu-text-field>
                            </mu-form-item>
                            <mu-form-item label="رمزعبور" prop="password" :rules="passwordRules">
                                <mu-text-field type="password" v-model="data.body.password" prop="password"></mu-text-field>
                            </mu-form-item>
                            <transition name="slideDown">
                                <mu-form-item v-if="LoginError && errorText.length > 0">
                                    <mu-alert color="error">
                                        {{errorText}}
                                    </mu-alert>
                                </mu-form-item>
                            </transition>
                            <mu-form-item>
                                <mu-button :disabled="isLogingin" color="primary" @click="submit">ورود</mu-button>
                                <router-link to="/register" tag="mu-button">ثبت نام</router-link>
                            </mu-form-item>
                        </mu-form>
                    </mu-card-text>
                </mu-card>
            </mu-col>
        </mu-row>
    </mu-container>
</template>
<style>
.loginCard {
  margin-top: 50px;
}
</style>
<script>
export default {
  data() {
    return {
      usernameRules: [
        {
          validate: val => !!val,
          message: "لطفا ایمیل را وارد کنید"
        },
        {
          validate: val => this.validEmail(val),
          message: "ایمیل وارد شده معتبر نمی باشد"
        }
      ],
      passwordRules: [
        {
          validate: val => !!val,
          message: "رمز عبور را وارد کنید"
        },
        {
          validate: val => val.length >= 8,
          message: "رمز عبور باید بیشتر از 8 کارکتر باشد"
        }
      ],
      LoginError: false,
      isLogingin: false,
      data: {
        body: {
          username: "asd@asd.asd",
          password: "asdasdasd",
          grant_type: "password",
          client_id: window.client_id,
          client_secret: window.client_secret
        },
        rememberMe: false,
        fetchUser: true
      },
      errorText: ""
    };
  },
  mounted() {},
  watch: {
    "data.body.username": function(newVal) {
      if (newVal.length > 0) {
        $("input[prop='username']").style.direction = "ltr";
      } else {
        $("input[prop='username']").style.direction = "rtl";
      }
    },
    "data.body.password": function(newVal) {
      if (newVal.length > 0) {
        $("input[prop='password']").style.direction = "ltr";
      } else {
        $("input[prop='password']").style.direction = "rtl";
      }
    }
  },
  methods: {
    submit() {
      this.$refs.form.validate().then(result => {
        if (result) {
          this.LoginError = false;
          this.isLogingin = true;
          this.$auth.login({
            data: this.data.body,
            rememberMe: true,
            redirect: "/home",
            success: function() {
              this.LoginError = false;
              this.isLogingin = false;
            },
            error: function(val) {
              console.log(JSON.stringify(val.response.data.message));
              this.errorText = val.response.data.message;
              this.LoginError = true;
              this.isLogingin = false;
            }
          });
        }
      });
    },
    validEmail: function(email) {
      var re = /^(([^<>()\[\]\\.,;:\s@"]+(\.[^<>()\[\]\\.,;:\s@"]+)*)|(".+"))@((\[[0-9]{1,3}\.[0-9]{1,3}\.[0-9]{1,3}\.[0-9]{1,3}\])|(([a-zA-Z\-0-9]+\.)+[a-zA-Z]{2,}))$/;
      return re.test(email);
    },
    login() {
      this.LoginError = false;
      if (!this.validEmail(this.data.body.username)) this.usernameAlert = true;
      else this.usernameAlert = false;
      if (this.data.body.password.length === 0) {
        this.passwordAlert = true;
        this.passwordAlertText = "رمز عبور خود را وارد کنید";
      } else {
        this.passwordAlert = false;
      }
      if (this.data.body.password.length < 6) {
        this.passwordAlert = true;
        this.passwordAlertText = "رمز عبور باید بیشتر از 6 کاراکتر باشد.";
      } else {
        this.passwordAlert = false;
      }
      if (this.passwordAlert || this.usernameAlert) return;
      this.isLogingin = true;
      var _this = this;
      setTimeout(function() {
        _this.$auth
          .login({
            data: _this.data.body,
            rememberMe: true,
            redirect: "/dashboard"
          })
          .then(
            data => {
              /*_this.isLogingin = false;
                                              _this.axios.get("api/user/self").then(function (data) {
                                                  console.log(JSON.stringify(data.data));
                                              }).catch(function (data) {

                                              });*/
            },
            res => {
              _this.isLogingin = false;
              _this.LoginError = true;
            }
          );
      }, 500);
    }
  }
};
</script>
