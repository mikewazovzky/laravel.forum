<template>
    <div class="alert alert-flash" 
        :class="'alert-' + level" 
        role="alert" 
        v-show="show" 
        v-text="body">
    </div>
</template>

<script>
    export default {
        props: ['message'],
        data() {
            return {
                body: '',
                level: 'success',
                show: false
            };
        },
        created() {
            if (this.message) {
                this.flash({ message: this.message, level: 'success' });
            }

            window.events.$on('flash', data => this.flash(data));
        },

        methods: {
            flash(data) {
                this.body = data.message;
                this.level = data.level;
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
