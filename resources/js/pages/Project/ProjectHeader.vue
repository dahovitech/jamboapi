<template>
    <div class="p-4 flex border-b">
        <div class="w-full">
            <div class="text-xl font-bold">
                {{ project.name }}
            </div>
            <div class="text-sm">{{ project.description }}</div>
        </div>
        <div class="flex items-center">
            <router-link v-if="typeof project.id !== 'undefined' && checkRole(['admin'+project.id])" :to="{name: 'projects.settings', params: { project_id: project.id }}" :exact-active-class="'bg-none'" class="text-xl text-gray-600 cursor-pointer"><i class="fas fa-cog"></i></router-link>
        </div>
    </div>
</template>

<script>
import checkRole from '../../checkrole'

export default {
    props: ['project'],

    methods: {
        checkRole
    }
}
</script>