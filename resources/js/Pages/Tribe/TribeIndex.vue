<template>
    <div class="space-y-3">
        <PageHeader :page-title="pageTitle" />

        <TribeImplementation v-for="tribe in tribes" :tribe="tribe" :key="tribe.id" />
    </div>
</template>

<script setup>
import PageHeader from "@/Shared/Layout/PageHeader.vue"
import TribeImplementation from "@/Pages/Tribe/TribeImplementation.vue";

const pageTitle = 'Tribes';

defineProps({
    tribes: {
        type: Array,
        required: true
    }
})
</script>
