<script>
    import Favorite from './Favorite.vue';

    export default {
        props: ['attributes'],

        components: { Favorite },

        data() {
            return {
                editing: false,
                body: this.attributes.body
            };
        },

        methods: {
            cancel() {
                this.editing = false;
                this.body = this.attributes.body;
            },

            update() {
                axios.patch(`/replies/${this.attributes.id}`, { body: this.body })
                .then( () => {
                    this.editing = false;
                    flash('Your reply\'s been updated.');                    
                })
                .catch( (error) => {
                    this.body = this.attributes.body;
                    console.log(error);
                });
            },

            destroy() {
                axios.delete(`/replies/${this.attributes.id}`)
                    .then( response => { 
                        $(this.$el).fadeOut(300, () => 
                            flash('Your replay has been deleted.')
                        );
                    });
            }
        }
    };
</script>
