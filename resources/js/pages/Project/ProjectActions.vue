<template>
    <div class="flex items-center">
        <div @click="ExportSubmit" class="text-center text-gray-600 cursor-pointer mr-4"> <i
                class="fas fa-file-export"></i>Export</div>
        <router-link v-if="typeof project.id !== 'undefined' && checkRole(['admin' + project.id])"
            :to="{ name: 'projects.settings', params: { project_id: project.id } }" :exact-active-class="'bg-none'"
            class="text-center text-gray-600 cursor-pointer"><i class="fas fa-cog"></i>Settings</router-link>
    </div>
</template>
  
<script>
import checkRole from '../../checkrole'


export default {
   
    props: ['project'],

    methods: {
        checkRole,
        ExportSubmit() {
            axios({
                method: 'get',
                url: '/admin/projects/export/' + this.$route.params.project_id,
                responseType: 'arraybuffer' // Use 'arraybuffer' to handle binary data
                })
                .then(response => {
                    const blob = new Blob([response.data], { type: 'application/json' });
                    const url = window.URL.createObjectURL(blob);
                    const link = document.createElement('a');
                    link.href = url;
                    link.setAttribute('download', 'project_export.json');
                    document.body.appendChild(link);
                    link.click();
                })
                .catch(error => {
                    console.error(error);
                    // Handle error here
                });
        }

    }
}
</script>