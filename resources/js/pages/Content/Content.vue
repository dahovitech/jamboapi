<template>
    <div class="flex h-full overflow-hidden">
        <div class="w-96 overflow-x-hidden h-full bg-white">
            <project-header :project="project" class="bg-white"></project-header>

            <content-sidebar :project="project" class="h-full"></content-sidebar>
        </div>
    </div>
</template>

<script>
import ProjectHeader from '../Project/ProjectHeader'
import ContentSidebar from './ContentSidebar'

export default {
    components: {
        ProjectHeader,
        ContentSidebar
    },

    data(){
        return {
            project: {},
        }
    },

    methods: {
        getProject(){
            axios.get('/admin/content/project/'+this.$route.params.project_id).then((response) => {
                this.project = response.data;
            });
        },
    },

    mounted(){
        this.getProject();
    },
}
</script>