<template>
    <mu-container class="demo-container">
        <mu-row gutter>
            <mu-col span="12" sm="12" md="12" lg="8" xl="8" class="maginAuto">
                <mu-card class='rtl loginCard'>
                    <mu-card-title title="ثبت نام" sub-title="برای ثبت نام تمام فیلد های زیر را تکمیل کنید"></mu-card-title>
                    <mu-card-text>
                        <mu-form ref="form" :model="data.body" class="rtl">
                          <mu-row gutter>
                              <mu-col span="12" sm="12" md="12" lg="6" xl="6">
                                <mu-form-item label="نام" prop="first_name" :rules="nameRules">
                                <mu-text-field v-model="data.body.first_name" prop="first_name"></mu-text-field>
                            </mu-form-item>
                            <mu-form-item label="نام خانوادگی" prop="last_name" :rules="nameRules">
                                <mu-text-field v-model="data.body.last_name" prop="last_name"></mu-text-field>
                            </mu-form-item>
                            <mu-form-item label="نام کاربری" prop="username" :rules="nameRules">
                                <mu-text-field v-model="data.body.username" prop="username"></mu-text-field>
                            </mu-form-item>
                              </mu-col>
                              <mu-col span="12" sm="12" md="12" lg="6" xl="6">
                                <mu-form-item label="ایمیل" prop="email" :rules="usernameRules">
                                <mu-text-field v-model="data.body.email" prop="email"></mu-text-field>
                            </mu-form-item>
                            <mu-form-item label="رمزعبور" prop="password" :rules="passwordRules">
                                <mu-text-field type="password" v-model="data.body.password" prop="password"></mu-text-field>
                            </mu-form-item>
                            <mu-form-item label="تکرار رمزعبور" prop="password_confirm" :rules="password_confirmRules">
                                <mu-text-field type="password" v-model="data.body.password_confirm" prop="password_confirm"></mu-text-field>
                            </mu-form-item>
                              </mu-col>
                          </mu-row>
                            <transition name="slideDown">
                                <mu-form-item v-if="registerError && errorText.length > 0">
                                    <mu-alert color="error">
                                        <ul>
                                          <li v-for="i in errorText">{{i}}</li>
                                        </ul>
                                    </mu-alert>
                                </mu-form-item>
                                </transition>
                                <transition name="slideDown">
                                <mu-form-item v-if="registerSuccess && LoginError==false">
                                    <mu-alert color="success">
                                        <span>عضویت شما با موفقیت انجام شد. در حال ورود خودکار...</span>
                                    </mu-alert>
                                </mu-form-item>
                            </transition>
                            <transition name="slideDown">
                                <mu-form-item v-if="LoginError">
                                    <mu-alert color="error">
                                        <span>مشکلی در ورود خود کار به وجود آمده است. برای ورود به   </span> <router-link to="/login">این صفحه </router-link> <span>  مراجعه کنید </span>
                                    </mu-alert>
                                </mu-form-item>
                            </transition>
                            <mu-form-item>
                                <mu-button :disabled="isRegistering && isLogingin" color="primary" @click="submit">ثبت نام</mu-button>
                                <router-link to="/login" tag="mu-button">صفحه ورود</router-link>
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
      registerError: false,
      isRegistering: false,
      registerSuccess: false,
      data: {
        body: {
          email: "",
          last_name: "",
          first_name: "",
          username: "",
          password: "",
          password_confirm: "",
          grant_type: "password",
          client_id: window.client_id,
          client_secret: window.client_secret
        },
        rememberMe: false,
        fetchUser: true
      },
      LoginError: false,
      isLogingin: false,
      errorText: [],
      nameRules: [
        {
          validate: val => !!val,
          message: "این فیلد باید پر شود."
        }
      ],
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
      password_confirmRules: [
        {
          validate: val => !!val,
          message: "رمز عبور را وارد کنید"
        },
        {
          validate: val => val == this.data.body.password,
          message: "رمزهای عبور مشابه وارد نشده اند."
        }
      ]
    };
  },
  mounted() {},
  watch: {
    "data.body.email": function(newVal) {
      if (newVal.length > 0) {
        $("input[prop='email']").style.direction = "ltr";
      } else {
        $("input[prop='email']").style.direction = "rtl";
      }
    },
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
    },
    "data.body.password_confirm": function(newVal) {
      if (newVal.length > 0) {
        $("input[prop='password_confirm']").style.direction = "ltr";
      } else {
        $("input[prop='password_confirm']").style.direction = "rtl";
      }
    }
  },
  methods: {
    submit() {
      this.$refs.form.validate().then(result => {
        if (result) {
          this.registerError = false;
          this.isRegistering = true;
          this.registerSuccess = false;
          let _this = this;
          this.$auth.register({
            data: this.data.body,
            success: function(data) {
              _this.registerSuccess = true;
              _this.isRegistering = false;
              setTimeout(function() {
                _this.isLogingin = true;
                let loginArr = _this.data.body;
                loginArr["username"] = _this.data.body.email;
                _this.$auth.login({
                  data: _this.data.body,
                  rememberMe: true,
                  redirect: "/home",
                  success: function() {
                    _this.LoginError = false;
                    _this.isLogingin = false;
                  },
                  error: function(val) {
                    _this.LoginError = true;
                    _this.isLogingin = false;
                  }
                });
              }, 2000);
            },
            error: function(res) {
              _this.registerError = true;
              console.log(res.response.data);
              let errorArr = [];
              Object.keys(res.response.data.error).forEach(function(key) {
                Object.keys(res.response.data.error[key]).forEach(function(
                  index
                ) {
                  errorArr.push(res.response.data.error[key][index]);
                });
              });
              _this.isRegistering = false;
              _this.errorText = errorArr;
            },
            rememberMe: true
          });
        }
      });
    },
    validEmail: function(email) {
      var re = /^(([^<>()\[\]\\.,;:\s@"]+(\.[^<>()\[\]\\.,;:\s@"]+)*)|(".+"))@((\[[0-9]{1,3}\.[0-9]{1,3}\.[0-9]{1,3}\.[0-9]{1,3}\])|(([a-zA-Z\-0-9]+\.)+[a-zA-Z]{2,}))$/;
      return re.test(email);
    },
    login() {}
  }
};
</script>
