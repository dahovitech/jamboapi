<template>
    <div class="flex h-full overflow-hidden">
        <div class="w-96 overflow-x-hidden h-full bg-white">
            <project-header :project="project" class="bg-white"></project-header>

            <content-sidebar :project="project"></content-sidebar>
        </div>

        <div class="p-4 w-full overflow-x-auto">
            <media-library :project="project"></media-library>
        </div>
    </div>
</template>

<script>
import ProjectHeader from '../Project/ProjectHeader'
import ContentSidebar from './ContentSidebar'
import MediaLibrary from '../MediaLibrary.vue'

export default {
    components: {
        ProjectHeader,
        ContentSidebar,
        MediaLibrary,
    },

    data(){
        return {
            project: {},
        }
    },

    methods: {
        getProject(){
            axios.get('/admin/projects/'+this.$route.params.project_id).then((response) => {
                this.project = response.data;
            });
        },
    },

    mounted(){
        this.getProject();
    },
}
</script>