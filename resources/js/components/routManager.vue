<template>
    <transition name="fade" tag="div">
        <router-view v-if="$auth.ready()"></router-view>
    </transition>
</template>

<script>
    export default {
        mounted(){
            //console.log(this.$auth.ready());
        }
    }
</script>