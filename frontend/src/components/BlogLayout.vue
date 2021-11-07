<template>
    <div>
        <div id="blog-header" class="layout-left">
            <i v-if="blog.member_of" @click="$router.push(`/${blog.name}/@settings`)" id="settings-button" class="uil uil-cog"></i>
            <img @click="$router.push(`/${blog.name}`)" :src="blog.image">
            <h1>{{blog.display_name}} <i class="uil uil-check-circle verified-badge" v-if="blog.verified" /></h1>
            <h2 v-if="blog.display_name != blog.name && '@'+blog.display_name != blog.name">{{blog.name}}</h2>
            <p>
                {{blog.description}}
                <a v-if="blog.website" target="_blank" :href="blog.website" :title="blog.website">{{blog.website}}</a>
            </p>
            <br>
            <br>
            <a @click="follow" class="big-action-button" style="padding: 3px 7px; font-size: 17px; width: 100%" :class="{fill: following}">{{following ? 'Following' : 'Follow'}} • {{followers}}</a>
        </div>
        <div id="posts" class="layout-left">   
            <slot />
        </div>
    </div>
</template>
<script>
import { login } from '../main'
export default {
    name: "blog-layout",
    props: {
        blog: {default: {}},
    },
    data(){
        return {
            following: this.blog.following,
            followers: this.blog.follower_count,
        }
    },
    watch:{
        blog(to){
            this.following = to.following
            this.followers = to.followers
        }
    },
    methods: {
        follow(){
            if (this.$store.state.auth.loggedIn){
                this.api.follow(this.blog.name)
                    .then(()=>{
                        this.api.isFollowing(this.blog.name)
                            .then(res=>{
                                this.following = res
                                if (this.following)
                                    this.followers++
                                else
                                    this.followers--
                            })
                    })
            } else {
                login()
                    .then(()=>{
                        this.follow()
                    })
            }
        }
    }
}
</script>
<style lang="scss" scoped>
#blog-header {
    img {
        width: 100px;
        height: 100px;
        margin: auto;
        margin-bottom: 30px;
        display: block;
        border-radius: 500px;
        border: #EEEEEE solid 4px;
        box-shadow: 0px 0px 24px 0px #00000011;
    }

    #settings-button {
        font-size: 26px;
        padding:3px;
        border-radius: 100px;
        box-shadow: 0px 2px 8px 0px #00000044;
        position: absolute;
        margin-top: 67px;
        margin-left: 143px;
        background: #FFF;
        cursor: pointer;
        transition: 0.3s;
        color: rgb(80, 80, 80);
        &:hover {
            box-shadow: 0px 2px 9px 0px #00000066;
        }
    }

    h1 {
        font-weight: 500;
        font-size: 27.5px;
    }
    h2 {
        font-weight: 500;
        font-size: 20px;
        margin-bottom: 20px;
        color: rgb(184, 184, 184);
    }
    p {
        font-size: 23px;
        a {
            color: #3EE1CE;
            font-size: 21px;
            display: block;
            margin-top: 8px;
            overflow: hidden;
        }
    }

    &.layout-left {
        display: inline-block;
        width: 240px;
        margin-right: 60px;
        vertical-align: top;
    }
}
#posts {
    &.layout-left {
        width: calc(100% - 300px);
        display: inline-block;
        vertical-align: top;
        .post {
            display: block;
        }

    }
}

@media screen and (max-width: 720px) {
    #blog-header {
        
        &.layout-left {
            width: 100%;
            display: block;
            h1, h2 {
                text-align: center;
            }
            p {
                margin-top: 20px;
            }
        }
    }
    #posts {
        &.layout-left {
            margin-top: 40px;
            display: block;
            width: 100%;
        }
    }
}
</style>