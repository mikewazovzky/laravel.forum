<template>
    <div>
        <div v-for="(reply, index) in items" :key="reply.id">
            <reply :data="reply" @deleted="remove(index)"></reply>
        </div>

        <new-reply :endpoint="endpoint" @created="add"></new-reply>     

    </div>
</template>

<script>
    import Reply from './Reply.vue';
    import NewReply from './NewReply.vue';

    export default {
        components: { Reply, NewReply },

        props: ['data'],

        data() {
            return {
                items: this.data
            };
        },

        computed: {
            endpoint() {
                return location.pathname + '/replies';
            }
        },

        methods: {
            add(data) {
                this.items.push(data);
                this.$emit('added');

                flash('Your reply has been posted.');                
            },

            remove(index) {
                this.items.splice(index, 1);
                this.$emit('removed');
                
                flash('Your replay has been deleted.');
            }
        }
    }
</script>