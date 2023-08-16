<template>
    <div class="flex h-full overflow-hidden">
        <div class="w-96 overflow-x-hidden h-full bg-white">
            <project-header :project="project" class="bg-white"></project-header>

            <content-sidebar :project="project"></content-sidebar>
        </div>

        <div class="p-4 w-full overflow-x-auto">
            <content-table v-if="$route.params.col_id !== undefined" :collection_id="parseInt($route.params.col_id)"></content-table>
        </div>
    </div>
</template>

<script>
import ProjectHeader from '../Project/ProjectHeader'
import ContentSidebar from './ContentSidebar'
import ContentTable from './ContentTable'


export default {
    components: {
        ProjectHeader,
        ContentSidebar,
        ContentTable
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