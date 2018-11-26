<template>
    <mu-row v-if="loaded">
        <mu-col span="12" sm="12" md="12" lg="9" xl="9" class='container'>
            <router-view>
                
            </router-view>
        </mu-col>
        <mu-col span="12" sm="0" md="0" lg="3" xl="3" class='sidebar'>
            <div class="profileSelction">
                <img src="/img/profile.jpg">
                <h2>هومن حسن زاده</h2>
            </div>
            <div class="options">
                <mu-paper :z-depth="1">
                    <mu-list>
                        <mu-list-item button :ripple="true" :active=true>
                            <mu-list-item-title>جدیدترین رسانه ها</mu-list-item-title>
                            <mu-list-item-action>
                                <mu-icon value="home"></mu-icon>
                            </mu-list-item-action>
                        </mu-list-item>
                        <mu-list-item button :ripple="true">
                            <mu-list-item-title>جستجو</mu-list-item-title>
                            <mu-list-item-action>
                                <mu-icon value="search"></mu-icon>
                            </mu-list-item-action>
                        </mu-list-item>
                        <mu-list-item button :ripple="true">
                            <mu-list-item-title>آپلود رسانه</mu-list-item-title>
                            <mu-list-item-action>
                                <mu-icon value="cloud_upload"></mu-icon>
                            </mu-list-item-action>
                        </mu-list-item>
                        <mu-list-item button :ripple="true">
                            <mu-list-item-title>پروفایل</mu-list-item-title>
                            <mu-list-item-action>
                                <mu-icon value="person"></mu-icon>
                            </mu-list-item-action>
                        </mu-list-item>
                    </mu-list>
                </mu-paper>
            </div>
        </mu-col>
    </mu-row>
</template>

<script>
    export default {
        data(){
            return {
                loaded : false,
            }
        },
        created() {

        },
        mounted: function () {
            console.log(this.$auth.check());
            if(!this.$auth.check()){
                Vue.router.push("login");
            }else{ 
                Vue.router.push("feeds");
                this.loaded = true;
            }
        },
        methods: {
            logout: function () {
                this.$auth.logout({
                    makeRequest: true,
                    success(data) {},
                    error() {
                        console.log('error ' + this.context);
                    }
                });
            }
        }

    }

</script>
