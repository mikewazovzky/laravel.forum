<template>
    <div :id="'reply-' + id" class="panel" :class="isBest ? 'panel-success' : 'panel-default'" >
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
                <form @submit.prevent="update">
                    <div class="form-group">
                        <textarea class="form-control" v-model="body" required></textarea>
                    </div>
                    <button class="btn btn-primary btn-xs">Update</button>
                    <button type="button" class="btn btn-xs" @click="cancel">Cancel</button>
                </form>
            </div>
            <div v-else v-html="body"></div>
        </div>

        <div class="panel-footer level">
            <div v-if="authorize('updateReply', reply)">
                <button class="btn btn-xs mr-1" @click="editing = true">Edit</button>
                <button class="btn btn-danger btn-xs" @click="destroy">Delete</button>
            </div>
            <button class="btn btn-default btn-xs ml-a" @click="markBestReply" v-show="! isBest">Best Reply?</button>
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
                body: this.data.body,
                isBest: false,
                reply: this.data
            };
        },

        computed: {
            ago() {
                return moment(this.data.created_at).fromNow() + '...';
            }
        },

        methods: {
            cancel() {
                this.editing = false;
                this.body = this.data.body;
            },

            update() {
                axios.patch(`/replies/${this.data.id}`, { body: this.body })
                    .then(() => {
                        this.editing = false;
                        flash('Your reply\'s been updated.');
                    })
                    .catch((error) => {
                        this.body = this.data.body;
                        flash(`ERROR ${error.response.status}: ${error.response.data}`, 'danger');
                    });
            },

            destroy() {
                axios.delete(`/replies/${this.id}`)
                    .then(response => this.$emit('deleted', this.data.id));
            },

            markBestReply() {
                axios.post('/replies/' + this.id + '/best')
                    .then(response => this.isBest = true);
            }
        }
    };
</script>
