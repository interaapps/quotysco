<template>
    <div>
        <br><br><br><br><br>
        <div class="contents">
            <blog-layout :blog="blog">
                <div>
                    <input ref="profilePicInput" style="display: none" @change="setImage" accept="image/*" type="file">

                    <a @click="$router.push('.')" class="button">Back to Blog</a>
                    <br><br>

                    <label>PROFILE-PICTURE</label>
                    <img @click="$refs.profilePicInput.click()" id="profile-picture" :src="editBlog.image" alt="">

                    <label>DISPLAY NAME</label>
                    <input type="text" class="input" v-model="editBlog.display_name">
                    <br>

                    <label>DESCRIPTION</label>
                    <textarea class="input" v-model="editBlog.description" />
                    <br>
                    <label>WEBSITE</label>
                    <input type="text" class="input" v-model="editBlog.website">
                    <br>
                    <a style="float: right" @click="save" class="button">SAVE</a>

                    <br><br><br>
                    <label>MEMBERS</label>
                    <div>
                        <div v-for="(member, i) of members" :key="i" class="member">
                            <img :src="member.user.profile_picture">
                            <span>{{member.user.display_name}}</span>
                            <div v-if="member.role != 'OWNER'">
                                <select v-model="member.role" @change="setMemberRole(member.user.id, member.role)">
                                    <option value="ADMIN">Admin</option>
                                    <option value="WRITER">Writer</option>
                                </select>
                                <a class="button danger" @click="removeMemberRole(member.user.id)">Remove</a>
                            </div>
                        </div>
                    </div>
                    <a style="float: right" @click="$refs.addMemberModal.open()" class="button">ADD MEMBER</a>

                    <div style="display:none">{{t}}</div>
                </div>
            </blog-layout>
        </div>
        <Modal mtitle="Add Member" ref="addMemberModal">
            <label>NAME</label>
            <input type="text" v-model="newMemberName" class="input">
            <p style="color: #EE3232">{{newMemberError}}</p>
            <a class="button" @click="addMember" style="margin-top: 10px; float: right">Add Member</a>
        </Modal>
    </div>
</template>
<script>
import BlogLayout from '../components/BlogLayout.vue';
import Modal from '../components/Modal.vue';

export default {
  components: { BlogLayout, Modal },
    data: ()=>({
        blog: {},
        editBlog: {},
        members: [],
        newMemberName: "",
        newMemberError: "",
        t:0
    }),
    mounted(){
        this.load()
    },
    methods:{
        load(){
            return this.api.getBlog(this.$route.params.blog)
                .then(async res => {
                    this.blog = res
                    this.editBlog = {...res}
                    this.$store.state.pageTitle = `${this.blog.display_name} - Settings`

                    this.api.getBlogMembers(this.$route.params.blog)
                        .then(res=>{
                            this.members = res.data
                        })
                })
        },
        async save(){
            await this.api.updateBlog(this.blog.name, this.editBlog)
            this.load()
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
                this.editBlog.image = res.url
                this.t = Math.random()
            })
        },
        addMember(){
            this.api.addBlogMember(this.blog.name, this.newMemberName)
                .then(()=>{
                    this.$refs.addMemberModal.close()
                    this.load()
                })
        },
        setMemberRole(id, role){
            this.api.updateBlogMember(this.blog.name, id, { role })
        },
        removeMemberRole(id){
            this.api.removeBlogMember(this.blog.name, id)
                .then(()=>{
                    this.load()
                })
        }
    }
}
</script>
<style lang="scss" scoped>

#profile-picture {
    width: 100px;
    height: 100px;
    margin: 0px 30px;
    cursor: pointer;
    margin-bottom: 30px;
    display: block;
    border-radius: 500px;
    border: #EEEEEE solid 4px;
    box-shadow: 0px 0px 24px 0px #00000011;
}
.member {
    margin-bottom: 10px;
    img {
        width:  35px;
        height: 35px;
        vertical-align: middle;
        margin-right: 15px;
        border: #EEEEEE solid 3px;
        border-radius: 30px;
    }
    span {
        font-size: 22px;
        vertical-align: middle;
    }
    div {
        float: right;
        select {
            background: rgb(221, 221, 221);
            border: none;
            padding: 4px;
            font-size: 17px;
            border-radius: 8px;
            margin-right: 10px;
        }
    }
}
</style>