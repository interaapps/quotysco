<template>
    <div>
        <div id="post" class="contents"> 
            <input ref="profilePicInput" style="display: none" @change="setImage" accept="image/*" type="file">

            <i id="change-photo" @click="$refs.profilePicInput.click()" class="uil uil-image"></i>
            
            <input id="title" v-model="post.title" placeholder="Title">

            <img v-if="post.image" id="post-banner" :src="post.image">
            <i v-if="post.image" @click="post.image = null; t=Math.random()" style="position: absolute; font-size: 30px; color: #878787; margin-left: -37px; margin-top: 11px; cursor: pointer" class="uil uil-times"></i>

            <div @click="save(generateURL())" id="save-button" class="big-action-button">PUBLISH</div>
            <a class="user" :style="{cursor: $route.meta.edit?'':pointer}" @click="!$route.meta.edit? showUserSelection = !showUserSelection :0">
                <img class="profile-pic" :src="currentBlog.image" alt="">
                <span>{{currentBlog.type == 'USER' ? currentBlog.name : $store.state.auth.user.name+' @ '+currentBlog.name}}</span>
                <svg v-if="!$route.meta.edit" style="vertical-align: middle; margin-left: 10px; transition: 0.3s" :style="{transform: showUserSelection ? 'rotate(184deg)' : ''}" width="22" height="22" viewBox="0 0 22 22" fill="none" xmlns="http://www.w3.org/2000/svg"><rect x="4.45508" y="5.01172" width="12.7283" height="3.47135" transform="rotate(45 4.45508 5.01172)" fill="#A4A4A4"/><rect x="20" y="7.45312" width="12.7283" height="3.47135" transform="rotate(135 20 7.45312)" fill="#A4A4A4"/></svg>
            </a>
            <div id="blog-selection" v-if="!$route.meta.edit && showUserSelection">
                <a class="user" v-for="blog in $store.state.auth.blogs" :key="blog.id" @click="showUserSelection = false; currentBlog = blog">
                    <img class="profile-pic" :src="blog.image" alt="">
                    <span>{{blog.type == 'USER' ? blog.name : $store.state.auth.user.name+' @ '+blog.name}}</span>
                </a>
            </div>
            
            <div id="action-button" style="opacity: 0.2">
                <div class="action-button">
                    <i class="uil uil-heart"></i>
                    <span>-</span>
                </div>
                <div class="action-button">
                    <i class="uil uil-comment"></i>
                    <span>-</span>
                </div>
            </div>

            <div id="post-contents">
                <div v-for="(part, index) of post.contents.contents" :key="index" class="element">
                    <input    :ref='"el"+index' type="text" class="h1-fake" v-if="part.type=='H1'" placeholder="title"  v-model="part.contents">
                    <input    :ref='"el"+index' type="text" class="h2-fake" v-if="part.type=='H2'" placeholder="title"  v-model="part.contents">
                    <input    :ref='"el"+index' type="text" class="h3-fake" v-if="part.type=='H3'" placeholder="title"  v-model="part.contents">
                    <input    :ref='"el"+index' type="text" class="h4-fake" v-if="part.type=='H4'" placeholder="title"  v-model="part.contents">
                    <textarea :ref='"el"+index' @input="resizeMeWithContents($event.target)" v-else-if="part.type=='TEXT'" style="height: 33px;" placeholder="Text in here..." v-model="part.contents"></textarea>
                    <img v-else-if="part.type=='IMAGE'" :src="part.url" :width="part.width ? part.width : ''" />
                    <iframe v-else-if="part.type=='YOUTUBE'" width="560" height="315" :src="'https://www.youtube.com/embed/'+part.id" frameborder="0"  allowfullscreen></iframe>
                    <pastefy-embed v-else-if="part.type=='PASTEFY'" :pasteid="part.id" />

                    <div class="element-options">

                        <i style="padding: 0px; font-size: 32px" v-if='part.type=="TEXT"' @click="addTextFormat('**%**', part, $refs['el'+index])" class="uil uil-bold"></i>
                        <i style="padding: 0px; font-size: 32px" v-if='part.type=="TEXT"' @click="addTextFormat('_%_', part, $refs['el'+index])" class="uil uil-italic"></i>
                        <i v-if='part.type=="TEXT"' @click="addTextFormat('[%](https://quotysco.ga)', part, $refs['el'+index])" class="uil uil-link"></i>
                        <i v-if='part.type=="TEXT"' @click="addTextFormat('\n```javascript\n%\n```\n', part, $refs['el'+index])" class="uil uil-brackets-curly"></i>
                        <i style="padding: 0px; font-size: 32px" v-if='part.type=="TEXT"' @click="addTextFormat('\n# % \n', part, $refs['el'+index])" class="uil uil-text"></i>
                        
                        <i  v-if='part.type=="TEXT"' @click="addTextFormat('\n-% \n- \n', part, $refs['el'+index])" class="uil uil-list-ul"></i>
                        <i  v-if='part.type=="TEXT"' @click="addTextFormat('\n1. %\n2. \n3. \n', part, $refs['el'+index])" class="uil uil-list-ol"></i>
                        
                        <i v-if='part.type=="YOUTUBE"' @click="part.id = getYouTubeURL(prompt('Enter YouTube URL')); resizeAll(true)" class="uil uil-presentation-play"></i>

                        <i style="padding: 0px; font-size: 32px" v-if="index != 0" @click="moveUp(index)" class="uil uil-angle-up"></i>
                        <i style="padding: 0px; font-size: 32px" v-if="index != post.contents.contents.length-1" @click="moveDown(index)" class="uil uil-angle-down"></i>
                        <i @click="deleteElement(index)" class="uil uil-times"></i>
                    </div>
                </div>
                <div class="add-contents">
                    <i @click="addElement({type: 'TEXT', contents: 'Hello world!'})" style="font-size: 32px; padding: 1px" class="uil uil-text"></i>
                    <i @click="$refs.profilePicInput.click()" class="uil uil-scenery"></i>
                    <i @click="addElement({ type: 'YOUTUBE', id: getYouTubeURL(prompt('Please enter a YouTube URL')) })" class="uil uil-presentation-play"></i>
                    <!--<i class="uil uil-arrow"></i>-->

                    <img @click="addElement({ type: 'PASTEFY', id: getPastefyURL(prompt('Please enter a Pastefy URL')) })" src="https://cdn.interaapps.de/icon/interaapps/pastefy.png">
                </div>
            </div>
            
            <br><br><br><br>
            <h4 style="color: #656565">CATEGORIES:</h4>
            <br>
            <span v-for="(category, i) in post.categories" :key="category" class="category">
                <span>{{category.display_name}}</span>
                <svg class="remove" @click="post.categories.splice(i, 1)" viewBox="0 0 17 17" fill="none" xmlns="http://www.w3.org/2000/svg"><rect x="14.1572" y="16.2773" width="19" height="3" transform="rotate(-135 14.1572 16.2773)" fill="#FFFFFF"/><rect x="0.72168" y="14.1562" width="19" height="3" transform="rotate(-45 0.72168 14.1562)" fill="#FFFFFF"/></svg>    
            </span>
            <a @click="$refs.addCategoryModal.open()" style="cursor: pointer; padding: 4px 7px; font-size: 18px; background: #00000011; border: none; border-radius: 6px; padding-top: 1px">Add Category</a>
            <br><br>

            <h4 style="color: #656565">URL:</h4>
            <p style="color: #878787; overflow-wrap: anywhere;">{{getCurrentLocation()}}</p>
            <a style="display: none">{{random}}</a>

            <br>
            <h4 style="color: #656565">Status:</h4>
            <br>
            <select v-model="post.state" style="padding: 4px 7px; font-size: 18px; background: #00000011; border: none; border-radius: 6px; padding-top: 1px">
                <option value="PUBLISHED">Published</option>
                <option value="DRAFT">Draft</option>
                <option value="UNLISTED">Unlisted</option>
            </select>
        </div>
        <div style="display:none">{{t}}</div>
        <Modal mtitle="Add Category" ref="addCategoryModal">
            <input v-model="categorySearch" type="text" placeholder="Search" class="input">
            <div id="category-list">
                <a 
                    v-for="(category, i) of categories.filter(c=>c.name.toLowerCase().replaceAll(' ','').includes(categorySearch.toLowerCase().replaceAll(' ',''))||c.display_name.toLowerCase().replaceAll(' ','').includes(categorySearch.toLowerCase().replaceAll(' ','')))" 
                    :key="i" 
                    @click="post.categories.push(category); $refs.addCategoryModal.close(); categorySearch = ''"
                >
                    {{category.display_name}}
                </a>
            </div>
        </Modal>
    </div>
</template>
<script>
import PastefyEmbed from "../components/external/PastefyEmbed";
import Modal from '../components/Modal.vue';
export default {
    data: ()=>({
        currentBlog: {type: "USER","image":"https://cdn.interaapps.de/service/accounts/images/users/53_1393079f1b63bdc89b414e3e247925de89126637fd0fca24fa448a1c0b8fade5849d518fc3dc6b67645bced3da502b4a80327493ebc074de12b3b5b6546d5fc3.png","updated_at":"2021-05-24 17:49:40","success":true,"name":"@JulianFun123","description":"My new awesome personal Blog!","created_at":"2021-05-24 17:49:40","layout_type":"LEFT_NAVIGATION","id":1,"display_name":"JulianFun123"},
        post: {
            title: "",
            contents: {
                contents: [
                    {
                        type: "TEXT",
                        contents: ""
                    }
                ]
            },
            state: "PUBLISHED",
            categories: []
        },
        categorySearch: "",
        random: 0,
        showUserSelection: false,
        categories: [],
        selectedCategory: "",
        t:0
    }),
    created(){
        this.api.get("/api/v1/categories")
            .then(this.api.handleRequest)
            .then(res => {
                this.categories = res.data
            })
        if (this.$route.meta.edit) {
            this.api.getBlogPost(this.$route.params.blog, this.$route.params.post)
                .then(res => {
                    this.post = res
                    this.$store.state.pageTitle = `${this.post.blog.display_name} - ${this.post.title} (Edit)`
                    setTimeout(()=>{
                        this.resizeAll()
                    },100)
                })
        }
    },
    components: {
        PastefyEmbed,
        Modal
    },
    methods: {
        resizeMeWithContents(element){
            element.style.height = "5px"
            element.style.height = (element.scrollHeight+5)+"px";
        },
        moveUp(index){
            [this.post.contents.contents[index], this.post.contents.contents[index-1]] = [this.post.contents.contents[index-1], this.post.contents.contents[index]]
            this.random++
            setTimeout(()=>this.resizeAll(), 50)
        },
        moveDown(index){
            [this.post.contents.contents[index], this.post.contents.contents[index+1]] = [this.post.contents.contents[index+1], this.post.contents.contents[index]]
            this.random++
            setTimeout(()=>this.resizeAll(), 50)
        },
        resizeAll(timeout = false){
            if (timeout) {
                setTimeout(this.resizeAll, 50)
                return
            }
            for (const i in this.$refs) {
                const el = this.$refs[i][0]
                if (el && el.tagName.toLowerCase() == 'textarea') {
                    this.resizeMeWithContents(el)
                }
            }
        },
        addElement(el){
            this.post.contents.contents.push(el)
            setTimeout(()=>this.resizeAll(), 50)
        },
        deleteElement(index){
            this.post.contents.contents.splice(index, 1)
            this.random++
        },
        addImage(event){
            const data = new FormData()
            
            data.append('file', event.target.files[0])

            fetch(process.env.VUE_APP_BASE_URL+"/api/v1/files/upload_image", {
                method: 'POST',
                headers: {
                    "Authorization": this.api.options.header["Authorization"]
                },
                body: data
            }).then(res=>res.json()).then((res)=>{
                event.target.value = null
                this.addElement({
                    type: "IMAGE",
                    url: res.url
                })
            })
        },
        setImage(event){
            const data = new FormData()
            data.append('file', event.target.files[0])

            fetch(process.env.VUE_APP_BASE_URL+"/api/v1/files/upload_image", {
                method: 'POST',
                headers: {
                    "Authorization": this.api.options.header["Authorization"]
                },
                body: data
            }).then(res=>res.json()).then((res)=>{
                event.target.value = null
                this.post.image = res.url
                this.t = Math.random()
            })
        },
        getYouTubeURL(video){
            return video.replaceAll("https://", "").replaceAll("www.", "").replaceAll("youtube.com/watch?v=", "").replaceAll("youtu.be/", "")
        },
        getPastefyURL(url){
            return url.replaceAll("https://", "").replaceAll("www.", "").replaceAll("pastefy.ga/", "")
        },
        getCurrentLocation(){
            if (this.$route.meta.edit) {
                return this.post.url
            }
            return window.location.origin+"/"+this.currentBlog.name+"/"+this.generateURL();                
        },
        generateURL(){
            let ret = this.post.title.toLowerCase()
                .replaceAll(' ', '-')
                .replaceAll("¹", "1").replaceAll("²", "2").replaceAll("³", "3").replaceAll("⁴", "4").replaceAll("⁵", "5")
                .replace(/[^a-zA-Z0-9-_]/g, '')
            if (ret == '')
                return '_'
            return ret
        },
        save(url){
            if (this.$route.meta.edit) {
                this.api.put("/api/v1/posts/"+this.currentBlog.name+"/"+this.post.url, this.post)
                    .then(this.api.handleRequest)
                    .then(() => {
                        this.$router.push("/"+this.currentBlog.name+"/"+this.post.url)
                    })
            } else {
                this.api.getBlogPost(this.currentBlog.name, url)
                    .then(() => {
                        this.save(this.generateURL()+"1")
                    }).catch(()=>{
                        this.api.put("/api/v1/posts/"+this.currentBlog.name+"/"+url, this.post)
                            .then(this.api.handleRequest)
                            .then(() => {
                                this.$router.push("/"+this.currentBlog.name+"/"+url)
                            })
                    });
            }
        },
        prompt(a){
            return prompt(a)
        },
        addCategory(){
            if (this.selectedCategory != ''&& !this.post.categories.includes(this.selectedCategory)) {
                this.post.categories.push(this.selectedCategory)
            }
            this.selectedCategory = ""
        },
        addTextFormat(t, part, textarea){
            textarea = textarea[0]
            const caretPos = textarea.getCaretPosition()
            let newCaretPos = false
            if (textarea.hasSelection()) {
                const newCaret = caretPos+textarea.value.substring(textarea.selectionStart, textarea.selectionEnd).length
                const oldStart = textarea.selectionStart
                const oldEnd = textarea.selectionEnd
                const startC = t.substr(0,t.indexOf('%'))
                const endC = t.substr(t.indexOf('%')+1)
                part.contents = part.contents.substr(0,textarea.selectionStart)
                    +startC
                    +part.contents.substr(textarea.selectionStart, textarea.selectionEnd-1)
                    +endC
                    +part.contents.substr(textarea.selectionEnd)
                
                newCaretPos = newCaret+1
                textarea.selectionStart = oldStart+1
                textarea.selectionEnd = oldEnd+1
            } else {
                const i = t.replaceAll('%', 'Enter Text')
                part.contents = (part.contents.substr(0,caretPos)+i+part.contents.substr(caretPos))
                newCaretPos = caretPos+t.indexOf('%')+"Enter Text".length
            }
            this.resizeAll(true)
            setTimeout(()=>{
                textarea.select()
                textarea.setCaretPosition(newCaretPos)
            }, 100)
        }
    }
}
</script>

<style lang="scss" scoped>
#post {
    margin-top: 120px;
    textarea {
        display: block;
        width: 100%;
        background: transparent;
        border: none;
        font-size: 26px;
        outline: none;
    }

    textarea, input {
        border: 3px transparent solid;
        border-radius: 10px;
        background: none;
        resize: none;
        overflow: hidden;

        &:hover, &:focus {
            border: 2.5px #00000011 dashed;
        }
    }

    #action-button {
        opacity: 0.7;
    }

    #title {
        width: calc(100% - 50px) !important;
    }

    #change-photo {
        font-size: 40px;
        color: #656565;
        float: right;
        margin-top: 5px;
        cursor: pointer;
    }

    .element {
        position: relative;
        .element-options {
            display: none;
            background: #FFFD;
            backdrop-filter: blur(5px);
            box-shadow: 0px 0px 10px 6px #00000015;
            width: fit-content;
            border-radius: 13px;
            position: absolute;
            bottom: -40px;
            right: 0px;

            a {
                font-size: 20px;
                vertical-align: middle;
                margin: 0px 10px;
                cursor: pointer;
                &:hover {
                    font-weight: 600;
                }
            }

            i {
                font-size: 25px;
                margin: 3px 3px;
                cursor: pointer;
                padding: 4px;
                vertical-align: middle;
                &:hover {
                    background: #00000007;
                    border-radius: 10px;
                }
            }

            svg, img {
                height: 30px;
                width:  30px;
                cursor: pointer;
                padding: 2px;
                display: inline-block;
                vertical-align: middle;
                margin: 6px 3px;
                &:hover {
                    background: #00000011;
                    border-radius: 4px;
                }
            }
        }

        &:hover {
            .element-options {
                display: block;
            }
        }

        textarea:focus ~ .element-options, input:focus ~ .element-options {
            display: block;
        }
    }
}

.add-contents {
    padding: 0px 3px;
    background: #FFFD;
    backdrop-filter: blur(5px);
    box-shadow: 0px 0px 10px 5px #00000010;
    display: block;
    width: fit-content;
    border-radius: 13px;
    
    i {
        font-size: 27px;
        margin: 3px 3px;
        cursor: pointer;
        padding: 4px;
        vertical-align: middle;
        &:hover {
            background: #00000009;
            border-radius: 10px;
        }
    }
    
    svg, img {
        height: 33px;
        width:  33px;
        cursor: pointer;
        padding: 2px;
        display: inline-block;
        vertical-align: middle;
        margin: 7px 5px;
        &:hover {
            background: #00000009;
            border-radius: 10px;
        }
    }
}

#blog-selection {
    position: absolute;
    background: #FFF;
    box-shadow: 0px 1px 5px 5px #00000011;
    padding: 7px 10px;
    z-index: 100;
    border-radius: 10px;

    .user {
        border-radius: 10px;
        display: block;
        cursor: pointer;
        padding: 5px 8px;
        &:hover {
            background: #00000011;;
        }
    }
}

#save-button {
    float: right;
}

#category-list {
    margin-top: 10px;
    height: 300px;
    overflow: auto;
    a {
        display: block;
        width: 100%;
        padding: 5px;
        font-size: 18px;
        cursor: pointer;
        border-radius: 7px;
        &:hover {
            background: #00000011;
        }
    }
}

@media screen and (max-width: 720px) {
    #post {

        .element {
            position: relative;
            padding-bottom: 20px;
            .element-options {
                bottom: -35px;
            }
        }
        #save-button {
            float: none;
            margin-bottom: 20px;
            display: block;
            padding: 6px;
        }
    }
    
}
</style>