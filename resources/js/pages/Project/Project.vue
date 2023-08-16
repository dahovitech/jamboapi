<template>
    <div>
        <div class="w-full xl:w-1/2 lg:w-3/4 m-auto mt-5">
            <project-header :project="project" class="mb-3"></project-header>
            
            <div class="grid grid-cols-1 sm:grid-cols-2">
                <div class="col p-4" v-if="checkRole(['admin'+project.id])">
                    <div class="inline-flex mb-5">
                        <div class="mr-4 text-gray-100 bg-yellow-900 rounded-sm text-xl p-4 h-full items-center content-center"><i class="fas fa-table"></i></div>
                        <div>
                            <h3 class="font-bold text-lg">Set up Collections</h3>
                            <div class="text-sm">Add new collections to your projects, edit fields to create your schema.</div>
                        </div>
                    </div>

                    <collection-sidebar :project="project" class="shadow-md rounded-sm"></collection-sidebar>
                </div>
                <div class="col p-4">
                    <div class="inline-flex mb-5">
                        <div class="mr-4 text-gray-100 bg-green-400 rounded-sm text-xl p-4 h-full items-center content-center"><i class="fas fa-edit"></i></div>
                        <div>
                            <h3 class="font-bold text-lg">Create Content</h3>
                            <div class="text-sm">Using the collection schema structure create content for your project.</div>
                        </div>
                    </div>

                    <content-sidebar :project="project" class="shadow-md rounded-sm"></content-sidebar>
                </div>
            </div>
        </div>
    </div>
</template>

<script>
import ProjectHeader from './ProjectHeader'
import CollectionSidebar from '../Collection/CollectionSidebar'
import ContentSidebar from '../Content/ContentSidebar'
import checkRole from '../../checkrole'

export default {
    components: {
        ProjectHeader,
        CollectionSidebar,
        ContentSidebar
    },

    data(){
        return {
            project: {},
        }
    },

    methods: {
        checkRole,

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