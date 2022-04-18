<template>
    <div>
        <div id="navigation" :class="{scrolled: scrolled, 'no-bg': $route.meta.navNoBG}">
            <div id="nav-contents">
                <router-link to="/" id="logo" :class="{scrolled: scrolled}"><!--{{scrolled ? 'Q' : 'Quotysco'}}-->
                    <div id="logo-bg"></div>
                    <span>Quotysco</span>
                    <beta-text v-if="typeof $store.state.pageTitle == 'string'">
                        {{$store.state.pageTitle}}
                    </beta-text>
                    <beta-text v-else-if="$store.state.pageTitle">
                        {{$store.state.pageTitle.text}}
                        <i class="uil uil-check-circle verified-badge" v-if="$store.state.pageTitle.verified" />
                    </beta-text>
                </router-link>

                <div id="right">
                    
                    <router-link v-if="$store.state.auth.loggedIn" to="/create-post" class="nav-icon-button"><i id="create-post-button" class="uil uil-pen"></i></router-link>
                    
                    <!--<router-link to="/search" class="nav-icon-button"><i class="uil uil-search"></i></router-link>-->
                    
                    <div v-if="$store.state.auth.loggedIn" id="profile-pic">
                        <img @click="menuOpened = !menuOpened" :src="$store.state.auth.user.profile_picture" alt="">
                        <div id="menu" v-show="menuOpened" @click="menuOpened = false">
                            <router-link to="/" style="margin-top: 0px">
                                <i class="uil uil-estate"></i>
                                <span>Home</span>
                            </router-link>
                            <router-link to="/following">
                                <i class="uil uil-star"></i>
                                <span>Following</span>
                            </router-link>
                            <a href="https://accounts.interaapps.de">
                                <i class="uil uil-user"></i>
                                <span>My Account</span>
                            </a>

                            <label>BLOGS</label>
                            <router-link v-for="(blog, i) of $store.state.auth.blogs" :to="`/${blog.name}`" :key="i">
                                <img :src="blog.image">
                                <span>{{blog.display_name}}</span>
                            </router-link>
                            <a @click="$refs.createBlogModal.open()">
                                <i class="uil uil-plus"></i>
                                <span>Create Blog</span>
                            </a>
                        </div>
                    </div>
                    <a v-else id="login-button" @click="login" style="margin-top: 3.5px;" class="big-action-button">LOGIN</a>
                </div>
            </div>
        </div>
        <Modal mtitle="Create Blog" ref="createBlogModal">
            <label>NAME</label>
            <input v-model="newBlog.name" pattern="[A-Za-z0-9-_]*" type="text" class="input">
            <a @click="createBlog" class="button" style="margin-top: 10px; float: right">Create Blog</a>
            <p style="color: #ee3232">{{blogCreationError}}</p>
        </Modal>
    </div>
</template>

<script>
import {login} from '../main'
import Modal from './Modal.vue'
export default {
  components: { Modal },
    data: ()=>({
        scrolled: false,
        menuOpened: false,
        newBlog: {name: ""},
        blogCreationError: ""
    }),
    mounted(){
        window.addEventListener("scroll", ()=>{
            this.scrolled = window.scrollY > 0
        })
    },
    methods: {
        login(){
            login()
        },
        createBlog(){
            if (this.newBlog.name.length > 3 && /^[A-Za-z0-9-_]*$/.test(this.newBlog.name)) {
                this.api.createBlog({
                    name: this.newBlog.name
                }).then(()=>{
                    this.$router.push("/"+this.newBlog.name+"/@settings")
                    this.newBlog.name = ""
                    this.$refs.createBlogModal.close()
                    this.api.loadUser()
                }).catch(()=>{
                    this.blogCreationError = "Name already given"
                })
            } else {
                this.blogCreationError = "Name can only contain letters, numbers and dashes"
            }
        }
    },
    watch: {
        $route(){
            window.scrollTo(0,0)
        }
    }
}
</script>
<style lang="scss" scoped>
#navigation {
    position: fixed;
    left: 0px;
    top: 0px;
    width: 100%;
    z-index: 1000;
    transition: 0.2s background;

    border-bottom: 2px solid #89898932;

    height: 66px;

    #nav-contents {
        background: #FFF;
    }
    &.no-bg {
        border-bottom: none;
        #nav-contents {
            background: transparent;
        }
    }
    &.scrolled {
        border-bottom: 2px solid rgb(240, 240, 240);
        background: #FFF;
        
    }
    

    #nav-contents {
        margin: auto;
        max-width: 900px; // 1140px;
        padding: 6px;
        padding-bottom: 2px;
        height: 64px;
        
        #logo {
            vertical-align: middle;
            font-size: 35px;
            font-weight: 500;
            color: #131313;
            text-decoration: none;
            position: relative;
            transition: 0.3s;
            span {
                overflow: hidden;
                display: inline-block;
                max-width: 165px;
                position: relative;
                transition: 0.3s;
            }
            beta-text {
                font-size: 22px;
                top: 5px;
                white-space: nowrap;
                position: absolute;
                padding-left: 25px;
                font-weight: 600;
                color: #c2c2c2;
                opacity: 0;
                transition-delay: 0s;
                letter-spacing: -0.3px;

                max-width: 670px;
                overflow: hidden;
            }
            #logo-bg {
                width: 160px;
                height: 25px;
                background: #3EE1CE77;
                border-radius: 6px;
                position: absolute;
                margin-top: 18px;
                margin-left: 10px;
                transition-delay: 1.5s;
                transition: 0.4s;
            }
            
            &.scrolled {
                span {
                    max-width: 27px;
                }
                #logo-bg {
                    background: #3EE1CE99;
                    width:  45px;
                    height: 45px;
                    margin-top: 1px;
                    margin-left: -7px;
                    border-radius: 10px;
                }
                beta-text {
                    top: 0px;
                    opacity: 1 !important;
                    transition: 0.3s;
                    transition-delay: 1s;
                }
            }


            &:hover {
                #logo-bg {
                    background: #3EE1CEAA;
                    margin-top: 18px;
                    margin-left: 10px;
                    width: 160px;
                    height: 25px;
                    border-radius: 6px;
                }
                span {
                    max-width: 165px;
                }

                beta-text {
                    top: 5px;
                    opacity: 0.1s;
                    transition-delay: 0s;
                    opacity: 0 !important;
                }
            }
        }

        #right {
            vertical-align: middle;
            float: right;
            #profile-pic {
                display: inline-block;
                position: relative;
                margin-top: 2px;
                &>img {
                    vertical-align: middle;
                    width:  46px;
                    height: 46px;
                    border: #ddd 3px solid;
                    border-radius: 50%;
                    object-fit: cover;
                    background-color: #FFF;
                    cursor: pointer;
                }
                #menu {
                    display: block;
                    position: absolute;
                    right: 0px;
                    background: #FFF;
                    border: 2px solid #f0f0f0;
                    top: 65px;
                    width: 200px;
                    border-radius: 13px;
                    padding: 5px;
                    label {
                        padding: 5px 7px;
                        margin-bottom: 0px;
                        margin-top: 7px;
                    }
                    a {
                        text-decoration: none;
                        border-radius: 8px;
                        display: block;
                        padding: 4px;
                        margin-top: 4px;
                        color: #434343;
                        cursor: pointer;
                        img {
                            width:  30px;
                            height: 30px;
                            vertical-align: middle;
                            margin-right: 10px;
                            border-radius: 20px;
                            border: #dddddd solid 2px;
                        }
                        i {
                            font-size: 24px;
                            vertical-align: middle;
                            margin-right: 11px;
                            margin-left: 4px;
                        }
                        span {
                            font-size: 20px;
                            color: #434343;
                            text-decoration: none;
                            vertical-align: middle;
                        }
                        &:hover {
                            background: #0000000A;
                        }
                    }
                }
            }
        }
    }
    .nav-icon-button {
        margin-right: 13px;
        i {
            font-size: 23px;
            vertical-align: middle;
            padding: 6px;
            color: #656565;
            cursor: pointer;
            border-radius: 60px;
            &:hover {
                background: #00000011;
            }
        }
    }
}
@media screen and (max-width: 720px) {
    #navigation {
        #nav-contents {
            padding-right: 18px;
            padding-left:  18px;
            #logo {
                span {
                    max-width: 27px;
                }
                #logo-bg {
                    background: #3EE1CE99;
                    width:  45px;
                    height: 45px;
                    margin-top: 1px;
                    margin-left: -7px;
                    border-radius: 10px;
                }
                beta-text {
                    display: none;
                }
            }
        }
    }
}
</style>