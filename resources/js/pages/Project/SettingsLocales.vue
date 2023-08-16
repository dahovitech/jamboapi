<template>
    <div class="flex h-full">
        <div class="w-96 h-full bg-white overflow-x-hidden">
            <project-header :project="project" class="bg-white"></project-header>

            <settings-nav :project="project"></settings-nav>
        </div>

        <div class="w-full overflow-x-hidden">
            <div class="p-4">
                <h4 class="mb-2 p-2 font-bold text-xl">Localization</h4>

                <div class="bg-white mt-2 rounded-md p-4 w-full xl:w-3/5">
                    <div>
                        <div class="w-full flex justify-between">
                            <div class="text-lg font-bold">Available Locales</div>
                        </div>
                        <div class="overflow-x-auto mt-1 flex rounded-sm">
                            <table class="min-w-full divide-y divide-gray-200 border">
                                <tbody class="bg-white divide-y divide-gray-200">
                                    <tr v-for="locale in project.locales" :key="locale">
                                        <td class="px-6 py-3 text-sm align-top">
                                            {{ locale }}
                                        </td>
                                        <td class="px-6 py-3 text-sm align-top">
                                            {{ getLocale(locale) }}
                                        </td>
                                        <td class="px-6 py-3 text-sm w-px text-center font-bold whitespace-nowrap">
                                            <div v-if="locale == project.default_locale">Default</div>
                                            <div v-else class="ml-2 cursor-pointer text-indigo-500 py-1 px-3 rounded-sm hover:bg-gray-100" @click="setDefaultLocale(locale)">Set as default</div>
                                        </td>
                                        <td class="px-6 py-3 text-sm w-px text-center">
                                            <div v-if="locale != project.default_locale" class="ml-2 cursor-pointer text-red-500 py-1 px-3 rounded-sm hover:bg-gray-100" @click="deleteLocale(locale)">
                                                <i class="fa fa-trash-alt"></i>
                                            </div>
                                        </td>
                                    </tr>
                                </tbody>
                            </table>
                        </div>
                    </div>

                    <div class="mt-10">
                        <div class="w-full flex justify-between">
                            <div class="text-lg font-bold">Add a Locale</div>
                        </div>

                        <div class="flex">
                            <div class="w-1/2 mr-2">
                                <v-select :options="locales" :get-option-key="(o) => o.id" :get-option-label="(o) => o.id+' - '+o.name" :reduce="(o) => o.id" :clearable="false" class="v-select" :value="(option) => option[0]" placeholder="Select Locale" v-model="addLocaleData"></v-select>
                            </div>

                            <ui-button color="indigo-500" @click.native="addLocale">+ Add</ui-button>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</template>

<script>
import ProjectHeader from './ProjectHeader'
import SettingsNav from './SettingsNav'
import UiButton from '../../UI/Button.vue'
import localesJson from '../../locales'

export default {
    components: {
        ProjectHeader,
        SettingsNav,
        UiButton,
    },

    data(){
        return {
            project: {},
            locales: [],
            addLocaleData: null
        }
    },

    methods: {
        getProject(){
            axios.get('/admin/projects/settings/locales/'+this.$route.params.project_id).then((response) => {
                this.project = response.data
                if(this.project.locales !== null)
                    this.project.locales = response.data.locales.split(',');
            });
        },

        getLocale(locale){
            return localesJson[locale];
        },

        addLocale(){
            axios.post('/admin/projects/settings/locales/add/'+this.$route.params.project_id, { locale: this.addLocaleData }).then((response) => {
                this.$toast.success('Locale added to the project.');
                this.getProject();
                this.addLocaleData = null;
            },(error) => {
                if(error.response.status == 422){
                    this.$toast.error('Locale has already added.');
                }
            });
        },

        setDefaultLocale(locale){
            this.$swal.fire({
                title: 'Are you sure',
                text: "you want to change the default locale for this project?",
            }).then((result) => {
                if (result.value) {
                    axios.post('/admin/projects/settings/locales/change-default-locale/'+this.project.id, {locale: locale}).then((response) => {
                        this.$toast.success('Default locale has been changed.');
                        this.getProject();
                    });
                }
            });
        },

        deleteLocale(locale){
            this.$swal.fire({
                title: 'Are you sure',
                text: "you want to delete this locale?",
            }).then((result) => {
                if (result.value) {
                    axios.post('/admin/projects/settings/locales/delete-locale/'+this.project.id, {locale: locale}).then((response) => {
                        this.$toast.success('Locale deleted.');
                        this.getProject();
                    },(error) => {
                        if(error.response.status == 422){
                            this.$toast.error('Default locale can not be deleted.');
                        }
                    });
                }
            });
        }
    },
    

    mounted(){
        this.getProject();

        Object.entries(localesJson).forEach((item, key) => {
            this.locales.push({id: item[0], name: item[1]});
        });
    }
}
</script>