<template>
    <div>
        asdasd
    </div>
</template>

<script>
    export default {
        created() {
           
        },
        mounted: function () {
           
        },
        methods: {
            logout: function () {
                this.$auth.logout({
                    makeRequest: true,
                    success(data) {
                    },
                    error() {
                        console.log('error ' + this.context);
                    }
                }); 
            }
        }

    }
</script>
