<template>
    <div>
        <h4 class="mb-2 py-2 font-bold text-lg">
            <i class="fas fa-images text-gray-600"></i> Media Library
            <ui-button v-if="!showUploader" color="indigo-500" class="float-right" @click.native="showUploader = true"><i class="fas fa-upload text-white mr-3"></i> Upload Files</ui-button>

            <ui-button v-if="!showUploader && selectedFiles.length !== 0 && !disableDelete" color="red-500" class="float-right mr-2" @click.native="deleteSelected()"><i class="fas fa-trash text-white mr-3"></i> Delete ({{this.selectedFiles.length}})</ui-button>

            <ui-button v-if="!showUploader && hasInsertListener" color="green-500" class="float-right mr-2" @click.native="insertFiles()"><i class="fas fa-plus text-white mr-3"></i> Insert Files ({{this.selectedFiles.length}})</ui-button>
            
            <ui-button v-if="showUploader" color="green-500" class="float-right" @click.native="hideUploader"><i class="fas fa-chevron-left text-white mr-3"></i> File List</ui-button>
            
            <ui-button color="indigo-500" class="float-right mr-2" v-if="showUploader && files.length != 0 && (!$refs.upload || !$refs.upload.active)" @click.native="$refs.upload.active = true">  Start Upload </ui-button>
        </h4>

        <div v-if="!showUploader">
            <div class="flex w-full mt-4 clear-both">
                <div class="w-full">
                    <span class="leading-snug font-normal absolute text-center text-gray-400 absolute bg-transparent rounded-sm text-base items-center justify-center w-8 pl-3 py-3">
                        <i class="fas fa-search"></i>
                    </span>
                    <input type="text" v-model="search" @keyup="getMedia()" placeholder="Search for files..." class="px-3 py-3 placeholder-gray-400 text-gray-700 bg-white rounded-sm text-sm w-full pl-10 border-gray-200 focus:border-gray-300">
                </div>

                <div class="bg-white rounded-sm text-sm px-3 flex items-center text-center border border-gray-200 ml-2">
                    <input type="checkbox" class="flex" v-model="selectAll" @click="selectAllFiles()">
                </div>
            </div>

            <div class="grid grid-cols-8">
                <div class="col-span-6">
                    <div>
                        <div class="my-5 grid grid-cols-1 md:grid-cols-2 lg:grid-cols-4 xl:grid-cols-6 gap-4">
                            <div v-for="(file, index) in media.data" :key="file.id" class="relative rounded-sm bg-white border-4" :class="{'border-indigo-500': selectedFile && selectedFile.id == file.id}">

                                <input type="checkbox" class="absolute inset-0 m-3" :checked="checkIfSelected(file)" v-show="checkIfSelected(file)">

                                <div>
                                    <div class="w-full h-32 absolute top-0 left-0 bg-black bg-opacity-70 items-center text-center flex opacity-0 hover:opacity-100 z-10">
                                        <div class="absolute inset-0 w-full text-left z-10"  @click="selectFile(file)">
                                            <input type="checkbox" class="m-3" :checked="checkIfSelected(file)">
                                        </div>
                                        <div class="w-full z-20">
                                            <div class="text-sm w-full">
                                                <a :href="file.full_url" target="_blank">
                                                    <i v-if="file.type == 'jpg' || file.type == 'jpeg' || file.type == 'png' || file.type == 'bmp' || file.type == 'gif'  || file.type == 'webp'" class="fa fa-eye text-gray-100 cursor-pointer hover:text-gray-200 text-lg mr-2"></i>
                                                    <i v-else class="fa fa-file-download text-gray-100 cursor-pointer hover:text-gray-200 text-lg mr-2"></i>
                                                </a>
                                                
                                                <i v-if="!disableDelete" class="fa fa-trash-alt text-gray-100 cursor-pointer hover:text-gray-200 text-lg ml-2" @click="deleteFile(file, index)"></i>
                                            </div>
                                        </div>
                                    </div>

                                    <span v-if="file.type == 'jpg' || file.type == 'jpeg' || file.type == 'png' || file.type == 'gif' || file.type == 'webp'">
                                        <img class="w-full h-32 object-cover"  :src="file.full_url_thumb" />
                                    </span>
                                    
                                    <div class="w-full h-32 object-cover flex items-center text-center" v-else>
                                        <div class="w-full">
                                            <i v-if="file.type == 'pdf'" class="far fa-file-pdf text-5xl text-red-500"></i>
                                            <i v-else-if="file.type == 'avi' || file.type == 'mp4' || file.type == 'mov' || file.type == 'webm'" class="far fa-file-video text-5xl text-blue-500"></i>
                                            <i v-else-if="file.type == 'wav' || file.type == 'ogg' || file.type == 'mpeg'" class="far fa-file-audio text-5xl text-yellow-500"></i>
                                            <i v-else-if="file.type == 'xls' || file.type == 'xlsx'" class="far fa-file-excel text-5xl text-green-500"></i>
                                            <i v-else-if="file.type == 'doc' || file.type == 'docx'" class="far fa-file-word text-5xl text-blue-500"></i>
                                            <i v-else-if="file.type == 'zip'" class="far fa-file-archive text-5xl text-yellow-300"></i>
                                            <i v-else class="far fa-file text-5xl text-gray-400"></i>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>

                        <pagination class="float-left" :data="media" size="small" :limit="3" @pagination-change-page="getMedia"></pagination>

                        <div class="text-sm italic float-right text-gray-500">
                            total {{ media.total }} files, {{ media.from }} - {{ media.to }} showing
                        </div>
                    </div>
                </div>
                <div class="col-span-2 m-4 mt-5 p-4 border border-gray-200 rounded-sm bg-gray-100">
                    <div v-if="selectedFile !== null">
                        <h3 class="font-semibold text-sm mb-4">FILE DETAILS</h3>

                        <div>
                            <div class="float-left mr-2 w-28 bg-white">
                                <span v-if="selectedFile.type == 'jpg' || selectedFile.type == 'jpeg' || selectedFile.type == 'png' || selectedFile.type == 'gif' || selectedFile.type == 'webp'">
                                    <img class="w-full h-28 object-cover rounded-sm border rounded-sm"  :src="selectedFile.full_url_thumb" />
                                </span>
                                        
                                <div class="w-full h-28 object-cover flex items-center text-center border rounded-sm" v-else>
                                    <div class="w-full">
                                        <i v-if="selectedFile.type == 'pdf'" class="far fa-file-pdf text-5xl text-red-500"></i>
                                        <i v-else-if="selectedFile.type == 'avi' || selectedFile.type == 'mp4' || selectedFile.type == 'mov' || selectedFile.type == 'webm'" class="far fa-file-video text-5xl text-blue-500"></i>
                                        <i v-else-if="selectedFile.type == 'wav' || selectedFile.type == 'ogg' || selectedFile.type == 'mpeg'" class="far fa-file-audio text-5xl text-yellow-500"></i>
                                        <i v-else-if="selectedFile.type == 'xls' || selectedFile.type == 'xlsx'" class="far fa-file-excel text-5xl text-green-500"></i>
                                        <i v-else-if="selectedFile.type == 'doc' || selectedFile.type == 'docx'" class="far fa-file-word text-5xl text-blue-500"></i>
                                        <i v-else-if="selectedFile.type == 'zip'" class="far fa-file-archive text-5xl text-yellow-300"></i>
                                        <i v-else class="far fa-file text-5xl text-gray-400"></i>
                                    </div>
                                </div>
                            </div>

                            <div class="float-left text-sm">
                                <div class="font-semibold">{{ selectedFile.name }}</div>
                                <div>{{ selectedFile.created_at | date('D MMM YYYY') }}</div>
                                <div>{{ selectedFile.size | prettyBytes }}</div>
                                <div v-if="selectedFile.width !== null && selectedFile.height !== null">
                                    {{ selectedFile.width }} x {{ selectedFile.height }}
                                </div>
                            </div>
                            <div class="clear-left"></div>
                        </div>

                        <div class="mt-6 clear-both">
                            <div>
                                <label v-formlabel>File Name</label>
                                <input type="text" v-model="fileUpdateData.name" class="text-sm w-full rounded-sm border-gray-300">
                            </div>
                            <div class="mt-4">
                                <label v-formlabel>Caption</label>
                                <input type="text" v-model="fileUpdateData.caption" class="text-sm w-full rounded-sm border-gray-300">
                            </div>
                            <div class="mt-4">
                                <label v-formlabel>File Path</label>
                                <div class="flex rounded-sm cursor-pointer" @click="copyToClipboard(selectedFile.full_url)">
                                    <span class="inline-flex items-center px-3 rounded-l-sm border border-r-0 border-gray-300 bg-gray-50 text-gray-500 text-sm cursor-pointer"><i class="far fa-copy"></i></span>
                                    <input type="text" readonly disabled :value="selectedFile.full_url" class="text-sm w-full rounded-sm border-gray-300 rounded-l-none cursor-pointer">
                                </div>
                            </div>
                            <div class="mt-4">
                                <ui-button color="indigo-500" @click.native="updateFileSubmit()">Update File</ui-button>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <div v-if="showUploader">
             <div class="mt-4">
                <div class="mt-1 flex justify-center px-6 pt-5 pb-6 border-2 border-gray-300 border-dashed rounded-md">
                    <div class="space-y-1 text-center">
                        <svg class="mx-auto h-12 w-12 text-gray-400" stroke="currentColor" fill="none" viewBox="0 0 48 48" aria-hidden="true">
                            <path d="M28 8H12a4 4 0 00-4 4v20m32-12v8m0 0v8a4 4 0 01-4 4H12a4 4 0 01-4-4v-4m32-4l-3.172-3.172a4 4 0 00-5.656 0L28 28M8 32l9.172-9.172a4 4 0 015.656 0L28 28m0 0l4 4m4-24h8m-4-4v8m-12 4h.02" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" />
                        </svg>
                        <div class="flex text-sm text-gray-600 text-center">
                            <label for="file-upload" class="relative rounded-md font-medium text-indigo-600 hover:text-indigo-500 w-full">
                                <file-upload
                                    :post-action="'/admin/media/upload/'+$route.params.project_id"
                                    :multiple="true"
                                    :size="upload_max_filesize"
                                    :drop="true"
                                    :drop-directory="true"
                                    :headers="headers"
                                    :data="uploadData"
                                    :thread="4"
                                    v-model="files"
                                    @input-filter="inputFilter"
                                    ref="upload">
                                    <span class="cursor-pointer">Select a file or drag and drop</span>
                                </file-upload>
                            </label>
                        </div>
                        <p class="text-xs text-gray-500" v-if="upload_max_filesize !== null">
                            Up to {{ upload_max_filesize | prettyBytes(1024) }}
                        </p>
                        <p class="text-sm text-gray-500 pt-5 w-full text-center" v-if="typeof project.id !== 'undefined' && checkRole(['admin'+project.id]) && project.s3">
                            <i class="fas fa-hdd"></i>
                            Default upload disk is <strong>{{ project.disk == 's3' ? 'AWS S3' : 'Local' }}</strong>. You can change it in <router-link :to="{name: 'projects.settings', params: { project_id: project.id }}" class="font-bold text-indigo-500">settings</router-link>.
                        </p>
                    </div>
                </div>

                <div v-if="files.length != 0" class="my-5 grid grid-cols-1 md:grid-cols-2 lg:grid-cols-4 xl:grid-cols-6 gap-4">
                    <div v-for="file in files" :key="file.id" class="relative">
                        <img class="w-full h-32 object-cover" v-if="file.thumb" :src="file.thumb" />
                        <div class="w-full h-32 object-cover border border-gray-200 flex items-center text-center" v-if="!file.thumb">
                            <div class="w-full">
                                <i v-if="file.type == 'application/pdf'" class="far fa-file-pdf text-5xl text-red-500"></i>
                                <i v-else-if="file.type == 'video/avi' || file.type == 'video/mp4' || file.type == 'video/quicktime' || file.type == 'video/webm'" class="far fa-file-video text-5xl text-blue-500"></i>
                                <i v-else-if="file.type == 'audio/wav' || file.type == 'audio/ogg' || file.type == 'audio/mpeg'" class="far fa-file-audio text-5xl text-yellow-500"></i>
                                <i v-else-if="file.type == 'application/vnd.ms-excel' || file.type == 'application/vnd.openxmlformats-officedocument.spreadsheetml.sheet'" class="far fa-file-excel text-5xl text-green-500"></i>
                                <i v-else-if="file.type == 'application/msword' || file.type == 'application/vnd.openxmlformats-officedocument.wordprocessingml.document'" class="far fa-file-word text-5xl text-blue-500"></i>
                                <i v-else-if="file.type == 'application/zip'" class="far fa-file-archive text-5xl text-yellow-300"></i>
                                <i v-else class="far fa-file text-5xl text-gray-400"></i>
                            </div>
                        </div>

                        <div class="w-full mt-1">
                            <div class="text-xs float-left" :title="file.name">
                                {{ file.name.substring(0,16) }}
                                <span v-if="file.name.length > 16">...</span>
                                <div>{{ file.size | prettyBytes }}</div>
                            </div>
                            <div class="text-sm float-right"><i v-if="!file.success" class="fa fa-times-circle text-red-400 cursor-pointer hover:text-red-600" @click.prevent="$refs.upload.remove(file)"></i></div>
                        </div>
                        <div class="absolute inset-0 bg-white bg-opacity-90 w-full h-32 p-4 flex items-center text-center border border-gray-200" v-if="file.active || file.error || file.success">
                            <div class="relative pt-1 w-full">
                                <div class="flex mb-2 items-center justify-between">
                                    <div>
                                        <span class="text-xs font-semibold inline-block py-1 rounded-sm" :class="{ 'text-green-500': file.success, 'text-red-500': file.error, 'text-blue-500': file.active }">
                                            <span v-if="file.error">{{file.error}}</span>
                                            <span v-else-if="file.success">done</span>
                                            <span v-else-if="file.active">uploading</span>
                                        </span>
                                    </div>
                                    <div class="text-right">
                                        <span class="text-xs font-semibold inline-block" :class="{ 'text-green-500': file.success, 'text-red-500': file.error, 'text-blue-500': file.active }">
                                            {{file.progress}}%
                                        </span>
                                    </div>
                                </div>
                                <div class="overflow-hidden h-2 mb-4 text-xs flex rounded" :class="{ 'bg-green-200': file.success, 'bg-red-200': file.error, 'bg-blue-200': file.active }">
                                    <div :style="{width: file.progress + '%'}" class="shadow-none flex flex-col text-center whitespace-nowrap text-white justify-center" :class="{ 'bg-green-500': file.success, 'bg-red-500': file.error, 'bg-blue-500': file.active }"></div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>

    </div>
</template>

<script>
import Vue from 'vue'
import Clipboard from 'v-clipboard'
Vue.use(Clipboard)
import UiButton from '../UI/Button.vue'
import FileUpload from 'vue-upload-component'
import checkRole from '../checkrole'

export default {
    components: {
        UiButton,
        FileUpload
    },

    props: {
        'project': {}, 
        'selected': { 
                default: function () {
                    return []
                }
            },
        'disableDelete': {
            default: false
        },
        media_type: {
            type: Number
        }
    },

    data(){
        return {
            media: {},
            search: null,
            selectedFiles: [],
            selectedFile: null,
            showUploader: false,
            selectAll: false,

            files: [],
            headers: {
                'X-Csrf-Token': document.head.querySelector('meta[name="csrf-token"]').content,
            },
            uploadData: {
                project_id: null
            },
            upload_max_filesize: null,

            fileUpdateData: {}
        }
    },

    methods: {
        getMedia(page){
            if (typeof page === 'undefined') {
                page = 1;
            }

            axios.get('/admin/media/get-files/'+this.project.id+'?page='+page, { params: { search: this.search }}).then((response) => {
                this.media = response.data.media;
                this.uploadData.project_id = this.project.id;
                this.upload_max_filesize = response.data.upload_max_filesize;
            });

            this.selectAll = false;
        },

        hideUploader(){
            this.showUploader = false;
            for (let i = 0; i < this.files.length; i++) {
                const element = this.files[i];
                if(element.success && this.hasInsertListener){
                    this.selectedFiles.push(element.response.id);
                }
            }
            this.$forceUpdate();
            this.files = [];
            this.getMedia();
        },

        inputFilter(newFile, oldFile, prevent) {
            if (newFile && !oldFile) {
                if (/(\/|^)(Thumbs\.db|desktop\.ini|\..+)$/.test(newFile.name)) {
                    return prevent()
                }
                if (/\.(php5?|html?|jsx?)$/i.test(newFile.name)) {
                    return prevent()
                }
            }

            if (newFile && newFile.error === "" && newFile.file && (!oldFile || newFile.file !== oldFile.file)) {
                newFile.blob = ''
                let URL = (window.URL || window.webkitURL)
                if (URL) {
                    newFile.blob = URL.createObjectURL(newFile.file)
                }
                newFile.thumb = ''
                if (newFile.blob && newFile.type.substr(0, 6) === 'image/') {
                    newFile.thumb = newFile.blob
                }
            }
        },

        selectFile(file){
            if(this.selectedFile !== null && this.selectedFile.id === file.id){
                let lastFile = this.selectedFiles[this.selectedFiles.length -2];
                if(lastFile !== undefined){
                    this.selectedFile = this.media.data.find(f => (f.id === lastFile));
                    this.fileUpdateData = { ...this.selectedFile };
                    this.fileUpdateData.name = this.fileUpdateData.name.split('.')[0];
                } else {
                    this.selectedFile = null;
                }
            } else {
                if(!this.checkIfSelected(file)){
                    this.selectedFile = file;
                    this.fileUpdateData = { ...file };
                    this.fileUpdateData.name = this.fileUpdateData.name.split('.')[0];
                }
            }
            
            this.selectAll = false;

            if(this.media_type == 1){
                if (this.selectedFiles.includes(file.id)) {
                    this.selectedFiles.splice(this.selectedFiles.findIndex(v => v === file.id), 1);
                } else {
                    this.selectedFiles = [];
                    this.selectedFiles.push(file.id);
                }
            } else {
                if (this.selectedFiles.includes(file.id)) {
                    this.selectedFiles.splice(this.selectedFiles.findIndex(v => v === file.id), 1);
                } else {
                    this.selectedFiles.push(file.id);
                }
            }
        },

        selectAllFiles(){
            if(!this.selectAll){
                for (let i = 0; i < this.media.data.length; i++){
                    if (!this.selectedFiles.includes(this.media.data[i].id)) {
                        this.selectedFiles.push(this.media.data[i].id);
                    }
                }
                let lastFile = this.selectedFiles[this.selectedFiles.length -1];
                if(lastFile !== undefined){
                    this.selectedFile = this.media.data.find(f => (f.id === lastFile));
                    this.fileUpdateData = { ...this.selectedFile };
                    this.fileUpdateData.name = this.fileUpdateData.name.split('.')[0];
                }
            } else {
                this.selectedFiles = [];
                this.selectedFile = null;
                this.fileUpdateData = {};
            }
        },

        checkIfSelected(file){
            return this.selectedFiles.includes(file.id);
        },

        insertFiles(){
            this.$emit('insert', this.selectedFiles);
            this.selectAll = false;
        },

        deleteFile(file, index){
            this.$swal.fire({
                title: 'Are you sure',
                text: "you want to delete this file?",
            }).then((result) => {
                if (result.value) {
                    axios.delete('/admin/media/delete/'+this.$route.params.project_id+'/'+file.id, { params: { project_id: this.project.id }}).then((response) => {
                        this.$toast.success('File deleted!');
                        this.getMedia();
                        
                        for (let i = 0; i < this.selectedFiles.length; i++) {
                            const element = this.selectedFiles[i];
                            if(file.id === element)
                                this.$delete(this.selectedFiles, i);
                        }
                        
                        if(this.selectedFile.id == file.id){
                            this.selectedFile = null;
                            this.fileUpdateData = {};
                        }
                    });
                }
            });
        },
    
        deleteSelected(){
            this.$swal.fire({
                title: 'Are you sure',
                text: "you want to delete these "+this.selectedFiles.length+" files?",
            }).then((result) => {
                if (result.value) {
                        axios.post('/admin/media/delete-selected/'+this.$route.params.project_id, { files: this.selectedFiles }).then((response) => {
                        this.$toast.success('Files deleted!');
                        this.getMedia();
                        this.selectedFiles = [];
                        this.selectedFile = null;
                        this.fileUpdateData = {};
                    });
                }
            });
        },

        copyToClipboard(str){
            this.$clipboard(str);
            this.$toast.success('Copied to clipboard');
        },

        updateFileSubmit(){
            axios.post('/admin/media/update/'+this.$route.params.project_id+'/'+this.fileUpdateData.id, this.fileUpdateData).then((response) => {
                    this.$toast.success('File updated!');
                    this.selectedFile = response.data;
                    this.fileUpdateData = { ...response.data };
                    this.fileUpdateData.name = this.fileUpdateData.name.split('.')[0];
                    this.getMedia();
                });
        },

        checkRole
    },

    computed:{
        hasInsertListener(){
            return this.$listeners && this.$listeners.insert
        },
    },

    watch: {
        'project.id'(newId, oldId) {
            this.getMedia();
        },
        'selected'(newD, oldD) {
            this.selectedFiles =  newD;
        }
    }
}
</script>