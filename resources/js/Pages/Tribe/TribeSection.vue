<template>
    <div class="border-b border-gray-200 pb-5 flex space-x-2">
        <img :src="tribe.img" class="h-6 w-6 items-center">
        <h3 class="text-base font-semibold leading-6 text-gray-900 capitalize">{{ tribe.name }}</h3>
    </div>
</template>

<script setup>
import { defineProps } from 'vue'

defineProps({
    tribe: {
        type: Object,
        required: true
    }
})
</script>

<style scoped>

</style>
