<template>
    <div class="rtl">
        <mu-card class="mainContainer">
            <mu-container class="profile">
                <mu-row>
                    <mu-col span="12" sm=6>
                        <mu-card-header class="profileHeader" :title="user.first_name + ' ' + user.last_name"
                            :sub-title="user.username">
                            <mu-avatar slot="avatar" size=100>
                                <img :src="(user.profile !== null) ? user.profile : '/img/profile.jpg'">
                            </mu-avatar>
                        </mu-card-header>
                    </mu-col>
                    <mu-col span="12" sm=6>
                        <mu-card-text>
                            دنبال‌کننده‌ها: {{user.followers_count}}<br>
                            دنبال‌شنوده‌ها: {{user.followings_count}}<br>
                            پست‌ها: {{user.medias_count}}

                        </mu-card-text>
                        <mu-card-actions>
                            <router-link tag='mu-button' to="/editProfile">ویرایش پروفایل</router-link>
                        </mu-card-actions>
                    </mu-col>
                </mu-row>

                <h3>رسانه ها</h3>
                <div class="medias">
                    <div v-if="medias.length > 0">
                        <mu-card v-for="f in medias" :key="f.id">
                            <mu-card-media>
                                <!-- <img :src="'/storage/medias/' + f.thumb_path"> -->
                                <img :src="'/storage/medias/' + f.file_path">
                            </mu-card-media>
                            <mu-card-text>{{f.caption}}</mu-card-text>
                            <mu-card-actions>
                                <mu-button flat>like</mu-button>
                            </mu-card-actions>
                        </mu-card>
                    </div>
                    <div v-if="!medias.length && mediasRequestSent">
                        رسانه ای برای نمایش وجود ندارد.
                    </div>
                    <div v-if="!medias.length && !mediasRequestSent">
                        در حال بارگذاری رسانه ها
                    </div>
                </div>
            </mu-container>
        </mu-card>


    </div>
</template>

<script>
    export default {
        data() {
            return {
                user: {},
                medias: [],
                mediasRequestSent : false,
            }
        },
        methods: {
            getUser() {
                Vue.axios.get("/api/" + this.$route.params.username + "/.").then(
                    resp => this.user = resp.data
                )
            },
            getUserMedia() {
                Vue.axios.get("/api/" + this.$route.params.username + "/medias").then(
                    resp => {this.medias = resp.data.data;this.mediasRequestSent = true;}
                )
            },
            followUser(index) {
                this.follow(index)
                this.user.is_followed = true;
            },
            unfollowUser(index) {
                this.unFollow(index)
                this.user.is_followed = true;
            }
        },
        created() {
            this.getUser()
        },
        mounted() {
            this.getUserMedia()
        }
    }

</script>
