<template>
    <div class="flex h-full overflow-hidden">
        <div class="w-96 overflow-x-hidden bg-white">
            <project-header :project="project" class="bg-white"></project-header>

            <content-sidebar :project="project"></content-sidebar>
        </div>

        <div class="p-4 w-full overflow-x-auto">
            <div class="mb-2 py-2 font-bold text-lg flex justify-end">
                <div class="flex-1">
                    {{ collection.name }} <small class="text-gray-500 font-normal"> / Forms</small>
                </div>
            </div>

            <div class="space-y-10">
                <div class="grid grid-cols-1 gap-4 sm:grid-cols-1 md:grid-cols-2 lg:grid-cols-3 xl:grid-cols-4">
                    <div @click="openNewFormModal = true" class="bg-green-500 hover:bg-green-600 text-white cursor-pointer items-center flex justify-center py-5">
                        <i class="fas fa-plus-circle md:mr-4 text-sm md:text-xl"></i>
                        Create a New Form
                    </div>

                    <router-link :to="{ name: 'projects.content.forms.show', params: { project_id: $route.params.project_id, col_id: $route.params.col_id, form_id: form.id } }" v-for="form in forms" :key="form.id" class="bg-white hover:bg-gray-100 px-10 py-6 text-gray-900 border border-gray-100 cursor-pointer items-center rounded-sm relative">
                        <span class="font-bold mt-3 block text-lg">{{ form.name }}</span>
                        <span class="text-sm block">{{ form.description }}</span>
                    </router-link>
                </div>
            </div>
        </div>

         <ui-modal :show="openNewFormModal" @close="closeNewFormModal">
            <template #title>
                Create a New Form
            </template>

            <template #content>
                <div class="mt-4 pb-4">
                    <div class="mt-2">
                        <label v-formlabel>Form Name</label>
                        <input type="text" v-model="new_form.name" autofocus v-forminput>
                        <p class="text-sm text-red-600 mt-2">{{ new_form.errors.name[0] }}</p>
                    </div>
                    <div class="mt-6">
                        <label v-formlabel>Description</label>
                        <input type="text" v-model="new_form.description" v-forminput>
                    </div>
                </div>
            </template>

            <template #footer>
                <ui-button color="gray-50" hover="gray-200" @click.native="closeNewFormModal">
                    <span class="text-gray-800">Cancel</span>
                </ui-button>

                <ui-button color="indigo-500" @click.native="saveNew" :disabled="processing_new_form" class="w-48">
                    <i v-if="processing_new_form" class="fas fa-spinner fa-spin"></i>
                    Create New Form
                </ui-button>
            </template>
        </ui-modal>
    </div>
</template>

<script>
import ProjectHeader from '../Project/ProjectHeader'
import ContentSidebar from './ContentSidebar'
import UiButton from '../../UI/Button.vue'
import UiModal from '../../UI/Modal.vue'
import UiDropdown from '../../UI/Dropdown.vue'
import ContentFormsSidebar from './ContentFormsSidebar.vue'

export default {
    components: {
        ProjectHeader,
        ContentSidebar,
        UiButton,
        UiModal,
        UiDropdown,
        ContentFormsSidebar,
    },

    data(){
        return {
            project: {},
            collection: {},
            forms: {},
            openNewFormModal: false,
            new_form: {
                errors: {
                    name: '',
                }
            },
            processing_new_form: false,
        }
    },

    methods: {
        getForms(){
            axios.get('/admin/content/forms/'+this.$route.params.project_id+'/'+this.$route.params.col_id).then((response) => {
                this.project = response.data.project;
                this.collection = response.data.collection;
                this.forms = response.data.forms;
                this.new_form.project_id = response.data.project.id;
                this.new_form.collection_id = response.data.collection.id;

                this.collection.fields.forEach(element => {
                    element.options = JSON.parse(element.options);
                });
            });
        },

        closeNewFormModal(){
            this.openNewFormModal = false;
            this.new_form = {
                errors: {
                    name: '',
                }
            };
            this.processing_new_form = false;
        },

        saveNew(){
            axios.post('/admin/content/forms/'+this.$route.params.project_id+'/'+this.$route.params.col_id, this.new_form).then((response) => {
                this.closeNewFormModal();
                this.$toast.success('New form created.');
                this.$router.push({ name: 'projects.content.forms.show', params: { project_id: this.project.id, collection_id: this.collection.id, form_id: response.data.id } });
            }, (error) => {
                if(error.response.status == 422){
                    this.new_form.errors = error.response.data.errors;
                }
            });
        },
    },

    mounted(){
        this.getForms();
    },

    watch: {
        '$route.params.col_id'(newId, oldId) {
            this.getForms();
        },
    },
}
</script>
