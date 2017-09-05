export default {
    data() {
        return {
            items: []
        };
    },
  
    methods: {
        add(data) {
            this.items.push(data);
            this.$emit('added');

            flash('Your item has been posted.');                
        },

        remove(index) {
            this.items.splice(index, 1);
            this.$emit('removed');

            flash('Your item has been deleted.');
        }
    }  
}