<template>
    <div class="flex h-full overflow-hidden">
        <div class="w-96 overflow-x-hidden h-full bg-white">
            <project-header :project="project" class="bg-white"></project-header>

            <collection-sidebar :project="project"></collection-sidebar>
        </div>
    </div>
</template>

<script>
import CollectionSidebar from './CollectionSidebar'
import ProjectHeader from '../Project/ProjectHeader'

export default {
    components: {
        CollectionSidebar,
        ProjectHeader
    },

    data(){
        return {
            project: {},
        }
    },

    methods: {
        getProject(){
            axios.get('/admin/collections/project/'+this.$route.params.project_id).then((response) => {
                this.project = response.data;
            });
        },
    },

    mounted(){
        this.getProject();
    },
}
</script>