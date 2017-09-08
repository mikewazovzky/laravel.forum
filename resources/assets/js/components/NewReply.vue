<template>
    <div>
        <div v-if="signedIn" >
            <div class="form-group">
                <textarea class="form-control" name="body" placeholder="post a reply..."  rows="4" v-model="body"></textarea>
            </div>
            <button class="btn btn-primary" @click="addReply">Post</button>        
        </div>
        <p v-else class="text-center">Pls. <a href="/login">sign in</a> to participate in this discussion.</p>
    </div>
</template>

<script>
    export default {
        data() {
            return {
                body: ''
            }
        },

        computed: {
            signedIn() {
                return window.App.signedIn;
            },

            endpoint() {
                return location.pathname + '/replies';
            }
        },

        methods: {
            addReply() {
                axios.post(this.endpoint, { body: this.body })
                    .then(response => {
                        this.body = '';
                        this.$emit('created', response.data);
                    })
                    .catch(error => {
                        this.body = '';
                        flash(`ERROR ${error.response.status}: ${error.response.data}`, 'danger');
                    });
            }
        }
    }
</script>