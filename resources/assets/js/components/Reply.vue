<template>
    <div :id="'reply-' + id" class="panel panel-default">
        <div class="panel-heading">
            <div class="level">
                <h5 class="flex">
                    <a href="'/profiles/' + data.owner.name" v-text="data.owner.name"></a> 
                    wrote 
                    <em><span v-text="ago"></span></em>...                  
                </h5>
                <div>
                    <favorite v-if="signedIn" :reply="data"></favorite>
                </div>
            </div>                        
        </div>        

        <div class="panel-body">
            <div v-if="editing">
                <div class="form-group">
                    <textarea class="form-control" v-model="body"></textarea>
                </div>                
                <button class="btn btn-primary btn-xs" @click="update">Update</button>
                <button class="btn btn-xs" @click="cancel">Cancel</button>
            </div>      
            <div v-else v-text="body"></div>
        </div>

        <div class="panel-footer level" v-if="canUpdate">
            <button class="btn btn-xs mr-1" @click="editing = true">Edit</button>
            <button class="btn btn-danger btn-xs" @click="destroy">Delete</button>
        </div>  
    </div>
</template>

<script>
    import moment from 'moment';
    import Favorite from './Favorite.vue';

    export default {
        props: ['data'],

        components: { Favorite },

        data() {
            return {
                editing: false,
                id: this.data.id,
                body: this.data.body
            };
        },

        computed: {
            signedIn() {
                return window.App.signedIn;
            },

            ago() {
                return moment(this.data.created_at).fromNow() + '...';
            },

            canUpdate() {
                // if (!window.App.user) return false;
                // return this.data.user_id == window.App.user.id;

                // Vue.authorize() is available globally
                return this.authorize( user => this.data.user_id == user.id);
            }
        },

        methods: {
            cancel() {
                this.editing = false;
                this.body = this.data.body;
            },

            update() {
                axios.patch(`/replies/${this.data.id}`, { body: this.body })
                .then( () => {
                    this.editing = false;
                    flash('Your reply\'s been updated.');                    
                })
                .catch( (error) => {
                    this.body = this.data.body;
                    console.log(error);
                });
            },

            destroy() {
                axios.delete(`/replies/${this.id}`)
                    .then( response => { 

                        // $(this.$el).fadeOut(300, () => 
                        //     flash('Your replay has been deleted.')
                        // );

                        this.$emit('deleted', this.data.id);
                    });
            }
        }
    };
</script>
