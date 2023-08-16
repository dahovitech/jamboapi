<template>
    <div class="m-3 p-3">
        <div>
            <div class="relative flex w-full flex-wrap items-stretch mb-4">
                <span class="z-9 h-full leading-snug font-normal absolute text-center text-gray-400 absolute bg-transparent rounded-sm text-base items-center justify-center w-8 pl-3 py-3">
                    <i class="fas fa-search"></i>
                </span>
                <input type="text" v-model="search" @keyup="getProjects()" placeholder="Search projects..." class="px-3 py-3 placeholder-gray-400 text-gray-700 bg-white rounded-sm text-sm w-full pl-10 border-gray-200 focus:border-gray-300">
            </div>
        </div>
        <div class="grid grid-cols-1 gap-4 sm:grid-cols-1 md:grid-cols-2 lg:grid-cols-3 xl:grid-cols-5">
            <div v-if="checkRole(['super_admin'])" @click="openNewProjectModal = true" class="bg-gray-100 hover:bg-gray-200 p-10 text-gray-900 cursor-pointer items-center">
                <i class="fas fa-plus-circle md:mr-4 text-sm md:text-2xl"></i>
                Create New Project
            </div>

            <router-link v-for="project in projects" :key="project.id" :to="{name: 'projects', params: { project_id: project.id }}" class="bg-white hover:bg-gray-100 p-10 text-gray-900 border border-gray-100 cursor-pointer items-center rounded-sm">
                <span class="font-bold">{{ project.name }}</span>
                <span class="text-sm block">{{ project.description }}</span>
            </router-link>

        </div>
       

        <ui-modal :show="openNewProjectModal" @close="closeNewProjectModal">
            <template #title>
                Create New Project
            </template>

            <template #content>
                <div class="mt-4 pb-4">
                    <form @submit.prevent="addNewProjectSubmit">
                        <div class="mt-2">
                            <label v-formlabel>Project Name</label>
                            <input type="text" v-model="new_project.name" autofocus v-forminput>
                            <p class="text-sm text-red-600 mt-2">{{ new_project.errors.name[0] }}</p>
                        </div>
                        <div class="mt-6">
                            <label v-formlabel>Description</label>
                            <input type="text" v-model="new_project.description" v-forminput>
                        </div>
                        <div class="mt-6">
                            <label v-formlabel>Default Locale</label>
                            <v-select :options="locales" :get-option-key="(o) => o.id" :get-option-label="(o) => o.id+' - '+o.name" :reduce="(o) => o.id" :clearable="false" class="v-select" :value="(option) => option[0]" placeholder="Select Locale" v-model="new_project.default_locale"></v-select>
                        </div>
                        <div class="mt-6">
                            <div class="grid grid-cols-3 gap-4">
                                <div class="col-span-1">
                                    <div class="p-5 border border-gray-300 rounded-sm text-sm space-x-2 h-32 relative">
                                        <label for="blank_project" class="absolute inset-0 w-full h-full cursor-pointer"></label>
                                        <div class="flex">
                                            <input type="radio" id="blank_project" v-model="new_project.type" value="1">
                                            <div class="ml-2">Blank</div>
                                        </div>
                                    </div>
                                </div>
                                <div class="col-span-1">
                                    <div class="p-5 border border-gray-300 rounded-sm text-sm space-x-2 h-32 relative">
                                        <label for="blog_template" class="absolute inset-0 w-full h-full cursor-pointer"></label>
                                        <div class="flex mb-2">
                                            <input type="radio" id="blog_template" v-model="new_project.type" value="2">
                                            <div class="ml-2">Blog Template</div>
                                        </div>
                                        <div>(Pages, Posts, Categories, Authors, Tags, Comments, Globals)</div>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </form>
                </div>
            </template>

            <template #footer>
                <ui-button color="gray-50" hover="gray-200" @click.native="closeNewProjectModal">
                    <span class="text-gray-800">Cancel</span>
                </ui-button>

                <ui-button color="indigo-500" @click.native="addNewProjectSubmit" :class="{ 'opacity-25': processing }" :disabled="processing">
                    Create New Project
                </ui-button>
            </template>
        </ui-modal>
    </div>
</template>

<script>
import UiModal from '../UI/Modal.vue'
import UiButton from '../UI/Button.vue'
import checkRole from '../checkrole'
import localesJson from '../locales.json'

export default {
    components: {
        UiModal,
        UiButton,
    },

    data(){
        return {
            openNewProjectModal: false,
            new_project: {
                default_locale: 'en',
                type: 1,
                errors: {
                    name: ''
                }
            },
            projects: {},
            processing: false,
            search: '',
            locales: []
        }
    },

    methods: {
        checkRole,
        
        addNewProjectSubmit(){
            this.processing = true;

            axios.post('/admin/projects', this.new_project)
                .then((response) => {
                    this.$toast.success('New project created.');
                    this.closeNewProjectModal();
                    this.projects.unshift(response.data);
                }, (error) => {
                    if(error.response.status == 422){
                        this.new_project.errors = error.response.data.errors;
                        this.processing = false;
                    }
                });
        },
        closeNewProjectModal(){
            this.openNewProjectModal = false;
            this.new_project = {
                default_locale: 'en',
                type: 1,
                errors: {
                    name: ''
                }
            },
            this.processing = false;
        },
        getProjects(){
            axios.get('/admin/projects', { params: { search: this.search }}).then((response) => {
                this.projects = response.data;
            });
        }
    },

    mounted(){
        this.getProjects();

        Object.entries(localesJson).forEach((item, key) => {
            this.locales.push({id: item[0], name: item[1]});
        });
        
    }
};
</script>