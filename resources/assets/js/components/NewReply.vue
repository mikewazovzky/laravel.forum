<template>
    <div>
        <div v-if="signedIn" >
            <div class="form-group">
                <textarea name="body" 
                    id="body"                     
                    class="form-control" 
                    placeholder="post a reply..."  
                    rows="4" 
                    required 
                    v-model="body">
                </textarea>
            </div>
            <button class="btn btn-primary" @click="addReply">Post</button>        
        </div>
        <p v-else class="text-center">Pls. <a href="/login">sign in</a> to participate in this discussion.</p>
    </div>
</template>

<script>
    import 'jquery.caret';
    import 'at.js';

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

        mounted() {
            const atwho = $('#body').atwho({
                at: "@",
                // data: 'http://localhost:8888/users.php'
                delay: 400,
                callbacks: {
                    remoteFilter(query, callback) {
                        $.getJSON('/api/users', { name: query }, function(usernames) {
                            callback(usernames);
                        });
                    }
                }
            });
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