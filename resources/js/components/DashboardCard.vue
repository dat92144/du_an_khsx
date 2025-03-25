<template>
    <div class="bg-white shadow-md p-4 rounded-lg flex items-center gap-4">
        <span class="text-3xl">{{ icon }}</span>
        <div>
            <h5 class="text-lg font-semibold">{{ title }}</h5>
            <p class="text-xl font-bold">{{ value }}</p>
        </div>
    </div>
</template>

<script>
export default {
    props: {
        title: String,
        value: [String, Number],
        icon: String
    }
};
</script>
