<script>
    export default {
        props: ['attributes'],

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


            }
        }
    };
</script>
