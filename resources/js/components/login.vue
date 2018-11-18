<template>
    <div class="app-content content bg-full-screen-image">
       dsfsdf 
    </div>
</template>

<script>
    export default {
        data() {
            return {
                LoginError: false,
                isLogingin: false,
                dots: "...",
                data: {
                    body: {
                        username: 'asd@asd.asd',
                        password: 'asdasdasd',
                        grant_type: 'password',
                        client_id: window.client_id,
                        client_secret: window.client_secret,

                    },
                    rememberMe: false,
                    fetchUser: true
                },
                usernameAlert: false,
                passwordAlert: false,
                passwordAlertText: ""
            };
        },
        mounted() {
           
        },
        watch: {
            
        },
        methods: {
            validEmail: function (email) {
                var re = /^(([^<>()\[\]\\.,;:\s@"]+(\.[^<>()\[\]\\.,;:\s@"]+)*)|(".+"))@((\[[0-9]{1,3}\.[0-9]{1,3}\.[0-9]{1,3}\.[0-9]{1,3}\])|(([a-zA-Z\-0-9]+\.)+[a-zA-Z]{2,}))$/;
                return re.test(email);
            },
            login() {
                this.LoginError = false;
                if (!this.validEmail(this.data.body.username))
                    this.usernameAlert = true;
                else
                    this.usernameAlert = false;
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
                setTimeout(function () {
                    _this.$auth.login({
                        data: _this.data.body,
                        rememberMe: true,
                        redirect: '/dashboard',
                    })
                        .then(data => {
                            /*_this.isLogingin = false;
                            _this.axios.get("api/user/self").then(function (data) {
                                console.log(JSON.stringify(data.data));
                            }).catch(function (data) {

                            });*/
                        }, (res) => {
                            _this.isLogingin = false;
                            _this.LoginError = true;
                        });
                }, 500);
            }
        }
    }
</script>