<template>
    <div class="bg-white mb-2 rounded-sm p-4 pb-2">
        <h4 class="mb-2 font-bold text-lg flex justify-between items-center h-10">
            <div>Forms</div>
        </h4>
        <div class="text-sm bg-yellow-100 p-2 text-gray-600" v-if="forms.length == 0">This collection doesn't have any forms yet. Create a new one.</div>
        <ul>
            <li class="mb-2" v-for="form in forms" :key="form.id">
                <router-link :to="{ name: 'projects.content.forms.show', params: { project_id: $route.params.project_id, col_id: $route.params.col_id, form_id: form.id}}" class="block w-full p-2 cursor-pointer hover:bg-gray-100 rounded">{{ form.name }}</router-link>
            </li>
        </ul>
    </div>
</template>

<script>

export default {
    props: ['forms'],
}
</script>
