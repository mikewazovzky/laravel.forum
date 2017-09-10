<template>
    <div>
        <div class="level">
            <img :src="avatar" width="80" height="80" class="mr-1"> 
            <h1 v-text="user.name"></h1>                       
        </div>
        <form v-if="canUpdate" method="POST" action="route('avatar', $profileUser)" enctype="multipart/form-data">
            <image-upload name="avatar" @loaded="onLoad"></image-upload>
        </form>
    </div>
</template>

<script>
    import ImageUpload from './ImageUpload';
    export default {
        props: ['user'],

        components: { ImageUpload },

        data() {
            return {
                avatar: this.user.avatar_path
            }
        },

        computed: {
            canUpdate() {
                return this.authorize(signedInUser => signedInUser.id === this.user.id);
            }
        },

        methods: {
            onLoad(avatar) {
                this.avatar = avatar.src;
                this.persist(avatar.file)
            },

            persist(avatar) {
                let data = new FormData();
                data.append('avatar', avatar);
                axios.post(`/api/users/${this.user.name}/avatar`, data)
                    .then(() => flash('Avatar uploaded.'))
                    .catch(() => console.error('Faild to load a file'));
            }
        }
    }
</script>