<template>
    <div >
    </div>
</template>

<script>
    export default {
        mounted() {
            if(!this.$auth.check())
                Vue.router.push("login");
            else {
                Vue.router.push("dashboard");
            }
        },
    }
</script>