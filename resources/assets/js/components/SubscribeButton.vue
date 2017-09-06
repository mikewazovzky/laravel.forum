<template>
    <button :class="classes" @click="toggleSubscription" v-text="textContent"></button>
</template>

<script>
    export default {
        props: ['subscribed'],

        data() {
            return {
                active: !this.subscribed
            };
        },

        computed: {
            endpoint() {
                return `${location.pathname}/subscriptions`;
            },

            classes() {
                return ['btn', this.active ? 'btn-primary' : 'btn-default'];
            },

            textContent() {
                return this.active ? 'Subscribe' : 'Unsubscribe';
            }
        },

        methods: {
            toggleSubscription() {
                const requestType = this.active ? 'post' : 'delete';
                axios[requestType](this.endpoint)
                .then(response => this.active = ! this.active)
                .catch(errors => console.error(errors));                
            }
        }
    }
</script>