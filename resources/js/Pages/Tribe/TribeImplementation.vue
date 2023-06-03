<template>
    <TribeSection :tribe="tribe" />
    <div class="space-y-2" v-if="isReady">
        <TribeAlert v-if="tribe_data.status === 'Missing Configuration'" :url="tribe_data.url" />
        <TribeRegistration v-if="tribe_data.status === 'Incomplete Setup' || tribe_data.status === 'Not Registered'" :register-url="tribe_data.url" />
        <TribeSettings v-if="tribe_data.can_manage" :tribe="tribe" />
        <EnableTribe v-if="tribe_data.status === 'Disabled'" :tribe="tribe" />

    </div>

</template>

<script setup>

import {defineProps, ref, watch} from 'vue'
import TribeSection from "@/Pages/Tribe/TribeSection.vue";
import TribeAlert from "@/Pages/Tribe/TribeAlert.vue";
import TribeRegistration from "./TribeRegistration.vue";
import axios from "axios";
import TribeSettings from "./TribeSettings.vue";
import EnableTribe from "./EnableTribe.vue";

const props = defineProps({
    tribe: {
        type: Object,
        required: true
    }
})

const isReady = ref(false)
const tribe_data = ref({
    status: '',
    url: '',
    can_manage: false
})

axios.get(route('tribe.show', props.tribe.id))
    .then(response => {
        isReady.value = true
        tribe_data.value = response.data
    })


</script>

