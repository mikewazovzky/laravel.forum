const user = window.App.user;

module.exports =  {
    updateReply(reply) {
        return reply.user_id === user.id;
    },

    markBestReply(reply) {
        return reply.thread.user_id === user.id;
    }
};
