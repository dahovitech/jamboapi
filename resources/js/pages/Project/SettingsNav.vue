<template>
    <div class="p-4 bg-white">
        <h4 class="mb-2 p-2 font-bold text-lg">Settings</h4>

        <ul>
            <li class="mb-2">
                <router-link v-if="typeof project.id !== 'undefined'" :to="{name: 'projects.settings', params: { project_id: project.id }}" class="block w-full p-2 cursor-pointer hover:bg-gray-100 rounded">Project</router-link>
            </li>
            <li class="mb-2">
                <router-link v-if="typeof project.id !== 'undefined' && checkRole(['super_admin'])" :to="{name: 'projects.settings.locales', params: { project_id: project.id }}" class="block w-full p-2 cursor-pointer hover:bg-gray-100 rounded">Localization</router-link>
            </li>
            <li class="mb-2">
                <router-link v-if="typeof project.id !== 'undefined' && checkRole(['super_admin'])" :to="{name: 'projects.settings.users', params: { project_id: project.id }}" class="block w-full p-2 cursor-pointer hover:bg-gray-100 rounded">Users & Roles</router-link>
            </li>
            <li class="mb-2">
                <router-link v-if="typeof project.id !== 'undefined' && checkRole(['super_admin'])" :to="{name: 'projects.settings.api', params: { project_id: project.id }}" class="block w-full p-2 cursor-pointer hover:bg-gray-100 rounded">API Access</router-link>
            </li>
            <li class="mb-2">
                <router-link v-if="typeof project.id !== 'undefined' && checkRole(['super_admin'])" :to="{name: 'projects.settings.webhooks', params: { project_id: project.id }}" class="block w-full p-2 cursor-pointer hover:bg-gray-100 rounded">Webhooks</router-link>
            </li>
        </ul>
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
