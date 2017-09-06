<script>
    import Replies from '../components/Replies.vue';
    import SubscribeButton from '../components/SubscribeButton.vue';

    export default {
        props: ['dataRepliesCount'],
        components: { Replies, SubscribeButton },

        data() {
            return {
                repliesCount: this.dataRepliesCount
            };
        }
    }
</script>