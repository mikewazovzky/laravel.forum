<script>
    import Replies from '../components/Replies.vue';

    export default {
        props: ['dataRepliesCount'],
        components: { Replies },

        data() {
            return {
                repliesCount: this.dataRepliesCount
            };
        }
    }
</script>