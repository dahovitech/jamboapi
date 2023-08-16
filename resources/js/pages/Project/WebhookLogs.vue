<template>
    <div class="flex h-full">
        <div class="w-96 h-full bg-white overflow-x-hidden">
            <project-header :project="project" class="bg-white"></project-header>

            <settings-nav :project="project"></settings-nav>
        </div>

        <div class="w-full overflow-x-hidden">
            <div class="p-4">
                <div class="flex justify-between p-2 items-center">
                    <h4 class="mb-2 font-bold text-xl">Webhooks <small>/ {{ webhook.name }} / Logs</small></h4>

                    <ui-button color="indigo-500" @click.native="clearLogs"><i class="fa fa-trash-restore"></i> Clear Logs</ui-button>
                </div>

                <div class="bg-white mt-2 rounded-md p-4 w-full">
                    <div class="mt-2">
                        <div class="overflow-x-auto mt-2 flex rounded-sm">
                            <table v-if="logs != undefined" class="w-full divide-y divide-gray-200 border">
                                <thead class="bg-gray-100">
                                    <tr>
                                        <th scope="col" class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Date</th>
                                        <th scope="col" class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Collection</th>
                                        <th scope="col" class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Action</th>
                                        <th scope="col" class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Status</th>
                                        <th scope="col" class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider w-px">Request / Response</th>
                                    </tr>
                                </thead>

                                <tbody class="bg-white divide-y divide-gray-200">
                                    <tr v-for="log in logs.data" :key="log.id">
                                        <td class="px-6 py-3 text-sm whitespace-nowrap">{{ log.created_at | date('D MMM YYYY, H:mm') }}</td>
                                        <td class="px-6 py-3 text-sm whitespace-nowrap">{{ JSON.parse(log.request).collection }}</td>
                                        <td class="px-6 py-3 text-sm whitespace-nowrap">{{ log.action }}</td>
                                        <td class="px-6 py-3 text-sm whitespace-nowrap font-bold">
                                            <span v-if="log.status == 'success'" class="text-green-600">{{ log.status }}</span>
                                            <span v-else-if="log.status == 'failed'" class="text-red-600">{{ log.status }}</span>
                                        </td>
                                        <td class="px-6 py-3 text-sm whitespace-nowrap text-center">
                                            <span class="text-indigo-500 cursor-pointer hover:bg-gray-100 rounded-sm p-2" @click="showText(log)"><i class="fas fa-align-center"></i></span>
                                        </td>
                                    </tr>
                                </tbody>
                            </table>
                        </div>

                        <div class="mt-2 clear-both flex justify-between items-center">
                            <pagination :data="logs" size="small" :limit="3" @pagination-change-page="getProject"></pagination>

                            <div class="text-sm italic text-gray-500">
                                {{ logs.total }} records, {{ logs.from }} - {{ logs.to }} showing
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <ui-modal :show="openDetailModal" @close="closeDetailModal" maxWidth="3xl">
            <template #content>
                <div class="mt-4">
                    <div class="w-full flex">
                        <div>
                            <div class="text-indigo-700"><span class="text-gray-500">URL:</span> {{ logDetails.url }}</div>
                        </div>
                    </div>

                    <div class="w-full flex space-x-4 mt-5">
                        <div class="w-1/2">
                            <div class="text-xl">Request</div>
                            <div class="border border-gray-200">
                                <textarea v-model="logDetails.request" class="w-full h-64 border-0 text-xs  font-mono"></textarea>
                            </div>
                        </div>
                        <div class="w-1/2">
                            <div class="text-xl">Response</div>
                            <div class="border border-gray-200">
                                <textarea v-model="logDetails.response" class="w-full h-64 border-0 text-xs  font-mono"></textarea>
                            </div>
                        </div>
                    </div>
                </div>
            </template>

            <template #footer>
                <ui-button color="gray-50" hover="gray-200" @click.native="closeDetailModal">
                    <span class="text-gray-800">Close</span>
                </ui-button>
            </template>
        </ui-modal>
    </div>
</template>

<script>
import ProjectHeader from './ProjectHeader'
import SettingsNav from './SettingsNav'
import UiButton from '../../UI/Button.vue'
import UiModal from '../../UI/Modal.vue'

import { codemirror } from 'vue-codemirror'
import 'codemirror/lib/codemirror.css'
import 'codemirror/mode/javascript/javascript.js'
import "codemirror/addon/display/autorefresh.js";

export default {
    components: {
        ProjectHeader,
        SettingsNav,
        UiButton,
        UiModal,
        codemirror
    },

    data(){
        return {
            project: {},
            webhook: {},
            logs: {},
            openDetailModal: false,
            detailType: '',
            logDetails: {
                request: '',
                response: '',
                url: '',
            },

            cmOptions: {
                mode: {
                    name: 'javascript',
                    json: true
                },
                readOnly: true,
                lineWrapping: true,
                autoRefresh: true
            },
        }
    },

    methods: {
        getProject(page){
            if (typeof page === 'undefined') {
                page = 1;
            }

            axios.get('/admin/projects/settings/webhooks/'+this.$route.params.project_id+'/logs/'+this.$route.params.webhook_id+'?page='+page).then((response) => {
                this.project = response.data.project;
                this.webhook = response.data.webhook;
                this.logs = response.data.logs;
            });
        },

        showText(log){
            this.logDetails = {
                request: log.request,
                response: log.response,
                url: log.url
            }
            this.$forceUpdate();
            this.openDetailModal = true;
        },

        closeDetailModal(){
            this.logDetails = {
                request: null,
                response: null,
                url: '',
            };
            this.openDetailModal = false;
            this.$forceUpdate();
        },

        clearLogs(){
            this.$swal.fire({
                title: 'Are you sure',
                text: "you want to delete all logs?",
            }).then((result) => {
                if (result.value) {
                    axios.delete('/admin/projects/settings/webhooks/'+this.$route.params.project_id+'/logs/'+this.$route.params.webhook_id).then((response) => {
                        this.$toast.success('All logs has been deleted.');
                        this.getProject();
                    });
                }
            });
        },
    },

    computed: {},

    mounted(){
        this.getProject();
    },
}
</script>
