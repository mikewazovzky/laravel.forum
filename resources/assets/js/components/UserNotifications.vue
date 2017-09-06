<template>
    <li class="dropdown" v-if="notifications.length">
        <a href="#" class="dropdown-toggle" data-toggle="dropdown">
            <span class="glyphicon glyphicon-bell"></span>
        </a>
        <ul class="dropdown-menu">
            <li v-for="notification in notifications">
                <a :href="notification.data.link" 
                    v-text="notification.data.message" 
                    @click="markAsRead(notification)">
                </a>
            </li>
        </ul>
    </li>
</template>

<script>
    export default {
        data() {
            return {
                notifications: []
            };
        },

        computed: {
            endpoint() {
                return `/profiles/${window.App.user.name}/notifications`;
            }
        },

        created() {
            axios.get(this.endpoint)
                .then(({ data }) => { this.notifications = data; });
        },

        methods: {
            markAsRead(notification) {
                axios.delete(`${this.endpoint}/${notification.id}`);
            }
        }
    };
</script>