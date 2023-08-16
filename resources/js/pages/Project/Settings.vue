<template>
    <div class="flex h-full">
        <div class="w-96 h-full bg-white overflow-x-hidden">
            <project-header :project="project" class="bg-white"></project-header>

            <settings-nav :project="project"></settings-nav>
        </div>

        <div class="w-full overflow-x-hidden">
            <div class="p-4">
                <h4 class="mb-2 p-2 font-bold text-xl">Project Details</h4>

                <div class="bg-white mt-2 rounded-md p-4 w-full xl:w-3/5">
                    <div class="grid grid-cols-1 gap-4">
                        <div>
                            <label v-formlabel>Project Name</label>
                            <div class="mt-1 relative">
                                <input type="text" v-model="editProjectData.name" autofocus v-forminput>
                                <p class="text-sm text-red-600 mt-2">{{ editProjectData.errors.name[0] }}</p>
                            </div>
                        </div>

                        <div>
                            <label v-formlabel>Description</label>
                            <div class="mt-1 relative">
                                <input type="text" v-model="editProjectData.description" v-forminput>
                            </div>
                        </div>

                        <div v-if="project.s3">
                            <label v-formlabel>Default Upload Disk</label>
                            <div class="grid grid-cols-4 space-x-2">
                                <div class="col-span-1">
                                    <label for="default_disk_local" class="p-5 border border-gray-300 rounded-sm text-sm flex items-center space-x-2 cursor-pointer">
                                        <input name="default_disk" id="default_disk_local" type="radio" v-model="editProjectData.disk" value="local">
                                        <span>Local (storage folder)</span>
                                    </label>
                                </div>
                                <div class="col-span-1">
                                    <label for="default_disk_s3" class="p-5 border border-gray-300 rounded-sm text-sm flex items-center space-x-2 cursor-pointer">
                                        <input name="default_disk" id="default_disk_s3" type="radio" v-model="editProjectData.disk" value="s3">
                                        <span>AWS S3</span>
                                    </label>
                                </div>
                            </div>
                        </div>
                        <div>
                            <label v-formlabel></label>
                            <div class="mt-1 relative">
                                <ui-button :color="'indigo-500'" @click.native="saveEdit()">Update Project</ui-button>
                            </div>
                        </div>
                    </div>

                    <hr class="clear-both mt-5 mb-5">

                    <ui-button v-if="checkRole(['super_admin'])" :color="'red-500'" class="float-right" @click.native="deleteProject()"><i class="fa fa-exclamation-triangle"></i> Delete Project</ui-button>

                    <div class="clear-both"></div>
                </div>
            </div>
        </div>
    </div>
</template>

<script>
import ProjectHeader from './ProjectHeader'
import SettingsNav from './SettingsNav'
import UiButton from '../../UI/Button.vue'
import checkRole from '../../checkrole'

export default {
    components: {
        ProjectHeader,
        SettingsNav,
        UiButton
    },

    data(){
        return {
            project: {},
            editProjectData : {
                errors: {
                    name: ''
                }
            }
        }
    },

    methods: {
        checkRole,
        
        getProject(){
            axios.get('/admin/projects/'+this.$route.params.project_id).then((response) => {
                this.project = response.data;
                this.editProjectData.id = response.data.id
                this.editProjectData.name = response.data.name
                this.editProjectData.description = response.data.description
                this.editProjectData.disk = response.data.disk
            });
        },

        saveEdit(){
            axios.post('/admin/projects/update/'+this.project.id, this.editProjectData).then((response) => {
                this.$toast.success('Project updated!');
                this.editProjectData = {
                    errors: {
                        name: ''
                    }
                }
                this.getProject();
            }, (error) => {
                if(error.response.status == 422){
                    this.editProjectData.errors = error.response.data.errors;
                }
            });
        },

        deleteProject(){
            this.$swal.fire({
                title: 'Are you sure',
                text: "you want to delete this project? All the collections and the content will be lost. You won't be able to revert this!",
            }).then((result) => {
                if (result.value) {
                    axios.delete('/admin/projects/delete/'+this.project.id).then((response) => {
                        this.$toast.success('Project deleted.');
                        this.$router.push({ name: 'home' });
                    });
                }
            });
        }
    },

    mounted(){
        this.getProject();
    },
}
</script>