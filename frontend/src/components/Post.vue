<template>
    <div>
        <router-link :to="'/'+post.blog.name+'/'+post.url" class="post">
            <span class="date">{{getDate()}}</span>
            <div class="user">
                <img class="profile-pic" :src="post.blog.image" alt="">
                <!--<span>{{post.blog.type == 'USER' ? post.author.name : post.author.name+' @ '+post.blog.name}} <i class="uil uil-check-circle verified-badge" v-if="post.blog.verified" /></span>-->
                <span>{{post.blog.name}} <i class="uil uil-check-circle verified-badge" v-if="post.blog.verified" /></span>
            </div>
            <img v-if="post.image" class="post-banner" :src="post.image" alt="">
            <h1>{{post.title}}<i class="uil uil-lock" style="margin-left:10px" v-if="post.state != 'PUBLISHED'" /></h1>

            <div class="action-button">
                <div class="action-button" :class="{liked:post.liked}" :style="{color: post.liked ? '#FF4343' : '#767676'}">
                    <i class="uil uil-heart"></i>
                    <span>{{post.likes_count}}</span>
                </div>
                <div class="action-button">
                    <i class="uil uil-comment"></i>
                    <span>{{post.comments_count}}</span>
                </div>
            </div>
        </router-link>
    </div>
</template>
<script>
export default {
    name: "post",
    props: {
        post: {default: {}}
    },
    methods: {
        getDate(){
            const date = new Date(this.post.created_at)
            return date.getFullYear()+"-"+date.getMonth()+"-"+date.getDate()
        }
    }
}
</script>
<style lang="scss" scoped>
.post {
    display: block;
    text-decoration: none;
    color: #434343;
    margin-bottom: 20px;
    padding: 10px;
    position: relative;
    h1 {
        font-weight: 500;
        color: #434343;
        font-size: 26px;
    }

    .post-banner {
        width: 100%;
        border-radius: 10px;
        margin-top: 5px;
        margin-bottom: 15px;
    }

    .user {
        margin-bottom: 10px;
        .profile-pic {
            border: #dddddd solid 3px;
            border-radius: 50%;
            width:  40px;
            height: 40px;
            vertical-align: middle;
        }

        span {
            vertical-align: middle;
            font-size: 23px;
            margin-left: 13px;
            font-weight: 500;
        }
    }

    .date {
        float: right;
        font-size: 20px;
        color: #808080;
        margin-right: 10px;
        margin-top: 8px;
        font-weight: 500;
    }

    .action-button {
        margin-top: 6px;
        color: #767676;

        .action-button {
            cursor: pointer;
            display: inline-block;
            margin-right: 20px;
            padding: 4px 2px;
            i {
                display: inline-block;
                margin: auto;
                font-size: 26px;
                vertical-align: middle;
            }
            span {
                display: inline-block;
                margin: auto;
                text-align: center;
                margin-left: 10px;
                font-size: 20px;
                vertical-align: middle;
            }

        }
    }

    &:hover {
        border-radius: 10px;
        background: #00000006;
    }

}

@media screen and (max-width: 720px) {
    .post {
        .date {
            bottom: 13px;
            right:  13px;
            margin-right: 0px;
            position: absolute;
        }
    }
}
</style>