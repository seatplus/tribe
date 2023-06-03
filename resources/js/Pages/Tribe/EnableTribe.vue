<template>
    <div class="bg-white shadow sm:rounded-lg">
        <SwitchGroup as="div" class="px-4 py-5 sm:p-6">
            <SwitchLabel as="h2" class="text-2xl font-bold leading-7 text-gray-900 sm:truncate sm:text-3xl sm:tracking-tight" passive>Squads</SwitchLabel>
            <div class="mt-2 sm:flex sm:items-start sm:justify-between">
                <div class="max-w-xl text-sm text-gray-500">
                    <SwitchDescription>Enable the tribe squad management</SwitchDescription>
                </div>
                <div class="mt-5 sm:ml-6 sm:mt-0 sm:flex sm:flex-shrink-0 sm:items-center">
                    <Switch v-model="form.enabled" :class="[form.enabled ? 'bg-indigo-600' : 'bg-gray-200', 'relative inline-flex h-6 w-11 flex-shrink-0 cursor-pointer rounded-full border-2 border-transparent transition-colors duration-200 ease-in-out focus:outline-none focus:ring-2 focus:ring-indigo-600 focus:ring-offset-2']">
                        <span aria-hidden="true" :class="[form.enabled ? 'translate-x-5' : 'translate-x-0', 'inline-block h-5 w-5 transform rounded-full bg-white shadow ring-0 transition duration-200 ease-in-out']" />
                    </Switch>
                </div>
            </div>
        </SwitchGroup>
    </div>
</template>


<script setup>


import {ref, watch} from 'vue'
import { Switch, SwitchDescription, SwitchGroup, SwitchLabel } from '@headlessui/vue'
import {useForm} from "@inertiajs/vue3";

const enabled = ref(false)

const props = defineProps({
    tribe: {
        type: Object,
        required: true
    }
})

const form = useForm({
    enabled: enabled.value
})

watch(() => form.enabled, (newEnabled) => {
    if(newEnabled) {
        form.post(route('tribe.update', props.tribe.id))
    }
})

</script>

