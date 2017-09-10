<template>
    <input type="file" accept="image/*" @change="onChange">
</template>

<script>
    export default {
        methods: {
            onChange(e) {
                if (!e.target.files.length) return;
                const file = e.target.files[0];
                const reader = new FileReader();
                reader.readAsDataURL(file);
                reader.onload = e => {
                    const src = e.target.result;
                    this.$emit('loaded', { src, file });
                };
            }
        }
    }
</script>