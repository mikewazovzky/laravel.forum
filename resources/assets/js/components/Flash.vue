<template>
    <div class="alert alert-success alert-flash" role="alert" v-show="show">
        <strong>Success!</strong> <span v-text="body"></span>.
    </div>
</template>

<script>
    export default {
        props: ['message'],
        data() {
            return {
                body: '',
                show: false
            };
        },
        created() {
            if (this.message) {
                this.flash(this.message);
            }

            window.events.$on('flash', message => this.flash(message));
        },

        methods: {
            flash(message) {
                this.body = message;
                this.show = true;

                this.hide();
            },

            hide() {
                setTimeout(() => {
                    this.show = false;
                }, 2000);
            }
        }
    };
</script>

<style scoped>
    .alert-flash { position: fixed; bottom: 20px; right: 20px ; }
</style>
