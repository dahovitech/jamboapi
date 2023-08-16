<template>
    <div class="p-5 w-full m-auto">
        <div class="flex-1 p-4" v-if="!submitSuccess">
            <h3 class="text-lg font-bold mb-2">{{ form.name }}</h3>
            <h5>{{ form.description }}</h5>

            <form class="space-y-6 mt-5 w-full">
                <div class="bg-gray-50 p-2 w-full relative" v-for="field in form.fields" :key="field.id">
                    <label v-formlabel>
                        {{field.label}}
                    </label>
                    <div class="mt-1 relative">
                        <div v-if="field.type == 'text'">
                            <div v-if="field.options.repeatable">
                                <div v-for="(input,index) in newData.data[field.name]" :key="index">
                                    <div class="flex space-between">
                                        <div class="relative flex w-full flex-wrap items-stretch">
                                            <input type="text" v-model="input.value" :placeholder="field.placeholder" class="mb-1" v-forminput>
                                        </div>
                                        <div class="w-auto h-auto text-right" v-if="(index !== 0)">
                                            <div class="cursor-pointer text-sm border border-red-500 rounded-sm text-white bg-red-500 p-3 ml-2 text-center hover:bg-red-400" @click="removeLineFromRepeatableField(field, index)"><i class="fas fa-trash-alt"></i></div>
                                        </div>
                                    </div>
                                    <p class="text-sm text-red-600 mt-1 mb-1" v-if="newData.errors['data.'+field.name+'.'+index+'.value']">{{ newData.errors['data.'+field.name+'.'+index+'.value'][0] }}</p>
                                </div>
                                <div class="cursor-pointer text-xs rounded-sm text-white bg-indigo-500 p-1  w-32 text-center mt-1 hover:bg-indigo-400" @click="addNewLineToRepeatableField(field)">+ Add a new line</div>
                            </div>
                            <div v-else>
                                <input v-if="field.slug_field === undefined" type="text" v-model="newData.data[field.name]" :placeholder="field.placeholder" v-forminput>
                                <input v-else type="text" v-model="newData.data[field.name]" :placeholder="field.placeholder" v-forminput @input="newData.data[field.slug_field] = $slugify(newData.data[field.name]);">
                            </div>
                        </div>
                        <div v-if="field.type == 'longtext'">
                            <div v-if="field.options.repeatable">
                                <div v-for="(input,index) in newData.data[field.name]" :key="index">
                                    <div class="flex space-between">
                                        <div class="relative flex w-full flex-wrap items-stretch">
                                            <textarea v-model="input.value" :placeholder="field.placeholder" class="mb-1" v-forminput></textarea>
                                        </div>
                                        <div class="w-auto h-auto text-right" v-if="(index !== 0)">
                                            <div class="cursor-pointer text-sm border border-red-500 rounded-sm text-white bg-red-500 p-3 ml-2 text-center hover:bg-red-400" @click="removeLineFromRepeatableField(field, index)"><i class="fas fa-trash-alt"></i></div>
                                        </div>
                                    </div>
                                    <p class="text-sm text-red-600 mt-1 mb-1" v-if="newData.errors['data.'+field.name+'.'+index+'.value']">{{ newData.errors['data.'+field.name+'.'+index+'.value'][0] }}</p>
                                </div>
                                <div class="cursor-pointer text-xs rounded-sm text-white bg-indigo-500 p-1  w-32 text-center mt-1 hover:bg-indigo-400" @click="addNewLineToRepeatableField(field)">+ Add a new line</div>
                            </div>
                            <div v-else>
                                <textarea v-model="newData.data[field.name]" :placeholder="field.placeholder" v-forminput></textarea>
                            </div>
                        </div>
                        <div v-if="field.type == 'richtext'" class="w-full relative">
                            <quill-editor
                                :ref="'quillEditor_'+field.name"
                                :options="{ modules: { toolbar: '#toolbar_'+field.name, imageResize: {} }, placeholder: field.placeholder }"
                                 v-model="newData.data[field.name]"
                                class="h-24 mb-2 rounded-sm border-gray-200"
                            >
                                <div :id="'toolbar_'+field.name" slot="toolbar">
                                    <span class="ql-formats">
                                        <select class="ql-header">
                                            <option selected="selected"></option>
                                            <option value="1"></option>
                                            <option value="2"></option>
                                            <option value="3"></option>
                                            <option value="4"></option>
                                            <option value="5"></option>
                                            <option value="6"></option>
                                        </select>
                                    </span>

                                    <span class="ql-formats">
                                        <button class="ql-bold"></button>
                                        <button class="ql-italic"></button>
                                        <button class="ql-underline"></button>
                                        <button class="ql-strike"></button>
                                    </span>

                                    <span class="ql-formats">
                                        <select class="ql-size">
                                            <option value="small"></option>
                                            <option selected></option>
                                            <option value="large"></option>
                                            <option value="huge"></option>
                                        </select>
                                    </span>

                                    <span class="ql-formats">
                                        <button class="ql-align"></button>
                                        <button class="ql-align" value="center"></button>
                                        <button class="ql-align" value="right"></button>
                                        <button class="ql-align" value="justify"></button>
                                    </span>

                                    <span class="ql-formats">
                                        <button class="ql-list" value="ordered"></button>
                                        <button class="ql-list" value="bullet"></button>
                                        <button class="ql-list" value="check"></button>
                                    </span>

                                    <span class="ql-formats">
                                        <button class="ql-indent" value="-1"></button>
                                        <button class="ql-indent" value="+1"></button>
                                        <select class="ql-color"></select>
                                        <select class="ql-background"></select>
                                        <button class="ql-code-block"></button>
                                    </span>

                                    <span class="ql-formats">
                                        <button class="ql-link"></button>
                                        <button class="ql-video"></button>
                                    </span>

                                    <span class="ql-formats">
                                        <button class="ql-clean"></button>
                                    </span>
                                </div>
                            </quill-editor>
                            <div class="clear-both h-16"></div>
                        </div>
                        <div v-if="field.type == 'email'">
                            <div v-if="field.options.repeatable">
                                <div v-for="(input,index) in newData.data[field.name]" :key="index">
                                    <div class="flex space-between">
                                        <div class="relative flex w-full flex-wrap items-stretch mb-1">
                                            <div class="flex rounded-sm shadow-sm w-full">
                                                <span class="inline-flex items-center px-3 rounded-l-sm border border-r-0 border-gray-300 bg-gray-50 text-gray-500 text-sm"><i class="fa fa-at"></i></span>
                                                <input type="email" v-model="input.value" :placeholder="field.placeholder" v-forminput class="rounded-l-none">
                                            </div>
                                        </div>
                                        <div class="w-auto h-auto text-right" v-if="(index !== 0)">
                                            <div class="cursor-pointer text-sm border border-red-500 rounded-sm text-white bg-red-500 p-3 ml-2 text-center hover:bg-red-400" @click="removeLineFromRepeatableField(field, index)"><i class="fas fa-trash-alt"></i></div>
                                        </div>
                                    </div>
                                    <p class="text-sm text-red-600 mt-1 mb-1" v-if="newData.errors['data.'+field.name+'.'+index+'.value']">{{ newData.errors['data.'+field.name+'.'+index+'.value'][0] }}</p>
                                </div>
                                <div class="cursor-pointer text-xs rounded-sm text-white bg-indigo-500 p-1  w-32 text-center mt-1 hover:bg-indigo-400" @click="addNewLineToRepeatableField(field)">+ Add a new line</div>
                            </div>
                            <div v-else>
                                <div class="mt-1 flex rounded-sm shadow-sm">
                                    <span class="inline-flex items-center px-3 rounded-l-sm border border-r-0 border-gray-300 bg-gray-50 text-gray-500 text-sm"><i class="fa fa-at"></i></span>
                                    <input type="email" v-model="newData.data[field.name]" :placeholder="field.placeholder" v-forminput class="rounded-l-none">
                                </div>
                            </div>
                        </div>
                        <div v-if="field.type == 'password'">
                            <div class="mt-1 flex rounded-sm shadow-sm">
                                <span class="inline-flex items-center px-3 rounded-l-sm border border-r-0 border-gray-300 bg-gray-50 text-gray-500 text-sm"><i class="fa fa-lock"></i></span>
                                <input type="password" v-model="newData.data[field.name]" v-forminput class="rounded-l-none">
                            </div>
                        </div>
                        <div v-if="field.type == 'number'">
                            <div v-if="field.options.repeatable">
                                <div v-for="(input,index) in newData.data[field.name]" :key="index">
                                    <div class="flex space-between">
                                        <div class="relative flex w-full flex-wrap items-stretch mb-1">
                                            <input type="number" step="any" v-model="input.value" v-forminput>
                                        </div>
                                        <div class="w-auto h-auto text-right" v-if="(index !== 0)">
                                            <div class="cursor-pointer text-sm border border-red-500 rounded-sm text-white bg-red-500 p-3 ml-2 text-center hover:bg-red-400" @click="removeLineFromRepeatableField(field, index)"><i class="fas fa-trash-alt"></i></div>
                                        </div>
                                    </div>
                                    <p class="text-sm text-red-600 mt-1 mb-1" v-if="newData.errors['data.'+field.name+'.'+index+'.value']">{{ newData.errors['data.'+field.name+'.'+index+'.value'][0] }}</p>
                                </div>
                                <div class="cursor-pointer text-xs rounded-sm text-white bg-indigo-500 p-1  w-32 text-center mt-1 hover:bg-indigo-400" @click="addNewLineToRepeatableField(field)">+ Add a new line</div>
                            </div>
                            <div v-else>
                                <input type="number" step="any" v-model="newData.data[field.name]" v-forminput>
                            </div>
                        </div>
                        <div class="w-full xl:w-1/4" v-if="field.type == 'enumeration'">
                            <v-select :multiple="field.options.multiple" :options="field.options.enumeration" :selectable="selected => newData.data[field.name] !== undefined ? !newData.data[field.name].includes(selected) : []" class="v-select" placeholder="Select" v-model="newData.data[field.name]"></v-select>
                        </div>
                        <div v-if="field.type == 'boolean'">
                            <label for="toggleB" class="flex w-1 items-center cursor-pointer">
                                <div class="relative">
                                    <input type="checkbox" id="toggleB" class="sr-only" v-model="newData.data[field.name]">
                                    <div class="block bg-gray-600 w-14 h-8 rounded-full"></div>
                                    <div class="dot absolute left-1 top-1 bg-white w-6 h-6 rounded-full transition"></div>
                                </div>
                            </label>
                        </div>
                        <div v-if="field.type == 'color'" class="w-2/3 xl:w-1/4">
                            <div v-if="field.options.repeatable">
                                <div v-for="(input,index) in newData.data[field.name]" :key="index">
                                    <div class="flex space-between">
                                        <div class="relative flex w-full flex-wrap items-stretch mb-1">
                                            <colorpicker v-model="input.value" />
                                        </div>
                                        <div class="w-auto h-auto text-right" v-if="(index !== 0)">
                                            <div class="cursor-pointer text-sm border border-red-500 rounded-sm text-white bg-red-500 px-3 py-3 pb-2 ml-2 text-center hover:bg-red-400" @click="removeLineFromRepeatableField(field, index)"><i class="fas fa-trash-alt"></i></div>
                                        </div>
                                    </div>
                                    <p class="text-sm text-red-600 mt-1 mb-1" v-if="newData.errors['data.'+field.name+'.'+index+'.value']">{{ newData.errors['data.'+field.name+'.'+index+'.value'][0] }}</p>
                                </div>
                                <div class="cursor-pointer text-xs rounded-sm text-white bg-indigo-500 p-1  w-32 text-center mt-1 hover:bg-indigo-400" @click="addNewLineToRepeatableField(field)">+ Add a new line</div>
                            </div>
                            <div v-else>
                                <colorpicker v-model="newData.data[field.name]" />
                            </div>
                        </div>
                        <div v-if="field.type == 'date'" class="w-full xl:w-1/4">
                            <div v-if="field.options.repeatable">
                                <div v-for="(input,index) in newData.data[field.name]" :key="index">
                                    <div class="flex space-between">
                                        <div class="relative flex w-full flex-wrap items-stretch mb-1">
                                            <v-date-picker v-model="input.value" :mode="field.options.timepicker ? 'dateTime' : 'date'">
                                                <template v-slot="{ inputValue, togglePopover }">
                                                    <div class="mt-1 flex rounded-sm shadow-sm">
                                                        <span class="inline-flex items-center px-3 rounded-l-sm border border-r-0 border-gray-300 bg-gray-50 text-gray-500 text-sm cursor-pointer" @click="togglePopover"><i class="fa fa-calendar-alt"></i></span>
                                                        <input type="text" v-forminput :value="inputValue" @click="togglePopover"/>
                                                    </div>
                                                </template>
                                            </v-date-picker>
                                        </div>
                                        <div class="w-auto h-auto text-right" v-if="(index !== 0)">
                                            <div class="cursor-pointer text-sm border border-red-500 rounded-sm text-white bg-red-500 p-3 ml-2 text-center hover:bg-red-400" @click="removeLineFromRepeatableField(field, index)"><i class="fas fa-trash-alt"></i></div>
                                        </div>
                                    </div>
                                    <p class="text-sm text-red-600 mt-1 mb-1" v-if="newData.errors['data.'+field.name+'.'+index+'.value']">{{ newData.errors['data.'+field.name+'.'+index+'.value'][0] }}</p>
                                </div>
                                <div class="cursor-pointer text-xs rounded-sm text-white bg-indigo-500 p-1  w-32 text-center mt-1 hover:bg-indigo-400" @click="addNewLineToRepeatableField(field)">+ Add a new line</div>
                            </div>
                            <div v-else>
                                <v-date-picker v-model="newData.data[field.name]" :mode="field.options.timepicker ? 'dateTime' : 'date'">
                                    <template v-slot="{ inputValue, togglePopover }">
                                        <div class="mt-1 flex rounded-sm shadow-sm">
                                            <span class="inline-flex items-center px-3 rounded-l-sm border border-r-0 border-gray-300 bg-gray-50 text-gray-500 text-sm cursor-pointer" @click="togglePopover"><i class="fa fa-calendar-alt"></i></span>
                                            <input type="text" v-forminput :value="inputValue" @click="togglePopover"/>
                                        </div>
                                    </template>
                                </v-date-picker>
                            </div>
                        </div>
                        <div v-if="field.type == 'time'" class="w-1/2 xl:w-1/12">
                            <div v-if="field.options.repeatable">
                                <div v-for="(input,index) in newData.data[field.name]" :key="index">
                                    <div class="flex space-between">
                                        <div class="relative flex w-full flex-wrap items-stretch mb-1">
                                            <input type="time" v-model="input.value" v-forminput>
                                        </div>
                                        <div class="w-auto h-auto text-right" v-if="(index !== 0)">
                                            <div class="cursor-pointer text-sm border border-red-500 rounded-sm text-white bg-red-500 p-3 ml-2 text-center hover:bg-red-400" @click="removeLineFromRepeatableField(field, index)"><i class="fas fa-trash-alt"></i></div>
                                        </div>
                                    </div>
                                    <p class="text-sm text-red-600 mt-1 mb-1" v-if="newData.errors['data.'+field.name+'.'+index+'.value']">{{ newData.errors['data.'+field.name+'.'+index+'.value'][0] }}</p>
                                </div>
                                <div class="cursor-pointer text-xs rounded-sm text-white bg-indigo-500 p-1  w-32 text-center mt-1 hover:bg-indigo-400" @click="addNewLineToRepeatableField(field)">+ Add a new line</div>
                            </div>
                            <div v-else>
                                <input type="time" v-model="newData.data[field.name]" v-forminput>
                            </div>
                        </div>
                        <div v-if="field.type == 'media'" class="w-full">
                            <file-upload v-show="!processing"
                                :input-id="field.name"
                                :ref="'upload'+field.name"
                                :post-action="'/forms/' + uuid + '/upload'"
                                :multiple="field.options.media.type == '2' ? true : false"
                                :drop="true"
                                :drop-directory="false"
                                :headers="headers"
                                :data="uploadData"
                                :thread="4"
                                v-model="files[field.name]"
                                @input-filter="inputFilter"
                                :size="upload_max_filesize"
                                accept="image/png,image/gif,image/jpeg,image/jpg"
                                :maximum="field.options.media.type == '2' ? 4 : 1"
                                class="w-full border-2 border-gray-300 rounded-md border-dashed hover:border-indigo-300 group">
                                <div class="mt-1 px-6 pt-5 pb-6 text-sm text-gray-400 group-hover:text-indigo-500">
                                    <svg class="mx-auto h-12 w-12" stroke="currentColor" fill="none" viewBox="0 0 48 48" aria-hidden="true">
                                        <path d="M28 8H12a4 4 0 00-4 4v20m32-12v8m0 0v8a4 4 0 01-4 4H12a4 4 0 01-4-4v-4m32-4l-3.172-3.172a4 4 0 00-5.656 0L28 28M8 32l9.172-9.172a4 4 0 015.656 0L28 28m0 0l4 4m4-24h8m-4-4v8m-12 4h.02" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" />
                                    </svg>
                                    <span class="cursor-pointer" v-if="field.options.media.type == '1'">Select a file or drag and drop</span>
                                    <span class="cursor-pointer" v-else-if="field.options.media.type == '2'">Select files or drag and drop</span>
                                    <p class="text-xs text-gray-500" v-if="upload_max_filesize !== null">
                                        jpeg, jpg, png, gif, webp <span v-if="field.options.media.type == '2'">| max: 4 files</span> |
                                        Up to {{ upload_max_filesize | prettyBytes(1024) }}
                                    </p>
                                </div>
                            </file-upload>

                            <div v-if="files[field.name] !== undefined && files[field.name].length != 0" class="grid grid-cols-4 gap-4 mt-2">
                                <div v-for="file in files[field.name]" :key="file.id" class="relative">
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
                                            {{ file.name | truncate(12) }}
                                            <div v-if="file.size !== null">{{ file.size | prettyBytes }}</div>
                                        </div>
                                        <div class="text-sm float-right"><i v-if="!file.success" class="fa fa-times-circle text-red-400 cursor-pointer hover:text-red-600" @click.prevent="removeFile(file, field.name)"></i></div>
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
                        <div class="clear-both"></div>

                        <small class="text-gray-500">
                            {{field.description}}
                        </small>
                    </div>
                    <p class="text-sm text-red-600 mt-1" v-if="newData.errors['data.'+field.name]">{{ newData.errors['data.'+field.name][0] }}</p>
                </div>
            </form>

            <div>
                <ui-button color="indigo-500" class="mt-5 w-32" @click.native="submitForm" :disabled="processing">
                    <i v-if="processing" class="fas fa-spinner fa-spin"></i>
                    {{form.submit_btn_text}}
                </ui-button>

                <span class="text-sm text-red-600 mt-1 ml-2"  v-if="Object.keys(newData.errors).length !== 0">
                    <span v-if="Object.keys(newData.errors).length === 1">
                        {{Object.keys(newData.errors).length}} field have invalid value, please correct it before saving.
                    </span>
                    <span v-else>
                        {{Object.keys(newData.errors).length}} fields have invalid values, please correct them before submitting.
                    </span>
                </span>
            </div>
        </div>
        <div v-else class="border p-4">
            Your form has been submitted successfully.<br>
            Thank you for submitting the form!
        </div>
    </div>
</template>

<script>
import UiButton from '../UI/Button.vue'

import { codemirror } from 'vue-codemirror'
import 'codemirror/lib/codemirror.css'
import 'codemirror/mode/javascript/javascript.js'
import 'codemirror/addon/edit/closebrackets.js'
import 'codemirror/addon/edit/matchbrackets.js'
import 'codemirror/theme/solarized.css'
import "codemirror/addon/display/autorefresh.js";

import { quillEditor } from 'vue-quill-editor'
import imageResize from "quill-image-resize-module";
import 'quill/dist/quill.core.css'
import 'quill/dist/quill.snow.css'
import 'quill/dist/quill.bubble.css'

import FileUpload from 'vue-upload-component'

export default {
    props: ['uuid'],

    components: {
        UiButton,
        codemirror,
        quillEditor,
        FileUpload
    },

    data(){
        return {
            form: {},
            newData: {
                data: {},
                errors: {}
            },
            processing: false,
            submitSuccess: false,

            files: [],
            headers: {
                'X-Csrf-Token': document.head.querySelector('meta[name="csrf-token"]').content,
            },
            uploadData: {},
            upload_max_filesize: null,

            interval: null,
        }
    },

    methods: {
        getForm(){
            axios.post('/forms/' + this.uuid).then((response) => {
                this.form = response.data.form;
                this.form.fields = JSON.parse(this.form.fields);
                this.upload_max_filesize = response.data.upload_max_filesize;

                this.form.fields.forEach(field => {
                    if(field.options.repeatable){
                        this.newData.data[field.name] = [{
                            value: null,
                        }];
                    }
                });
            });
        },

        addNewLineToRepeatableField(field){
            this.newData.data[field.name].push({ value: null });
            this.$forceUpdate();
        },
        removeLineFromRepeatableField(field, index){
            this.newData.data[field.name].splice(index, 1);
            this.$forceUpdate();
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

        removeFile(file, field_name) {
            this.files[field_name] = this.files[field_name].filter(f => f !== file)
        },

        checkuploadstatus(){
            let check = true;

            this.form.fields.forEach((field) => {
                if (field.type === 'media') {
                    if(this.$refs['upload'+field.name] && this.$refs['upload'+field.name][0].active){
                        check = false;
                    }
                }
            })

            if(check){
                this.submitFormData();
                clearInterval(this.interval);
            }
        },

        submitForm(){
            this.processing = true;

            let countMediaFields = 0;
            this.form.fields.forEach((field) => {
                if (field.type === 'media') {
                    countMediaFields++;
                }
            })

            if(countMediaFields > 0){
                this.form.fields.forEach((field) => {
                    if (field.type === 'media') {
                        if(this.$refs['upload'+field.name]){
                            this.$refs['upload'+field.name][0].active = true;
                        }
                    }
                })

                this.interval = setInterval(this.checkuploadstatus, 1000);
            } else{
                this.submitFormData();
            }
        },

        submitFormData(){
            let uploadedFiles = [];
            this.form.fields.forEach((field) => {
                if (field.type === 'media') {
                    if(this.$refs['upload'+field.name]){
                        this.$refs['upload'+field.name][0].files.forEach((file) => {
                            uploadedFiles.push(file.response.id)
                        })
                        this.newData.data[field.name] = uploadedFiles;
                        uploadedFiles = [];
                    }
                }
            })

            axios.post('/forms/submit/'+this.form.uuid, this.newData).then(response => {
                this.newData = {
                    data: {},
                    errors: {}
                }
                this.submitSuccess = true;
                this.processing = false;
            }).catch(error => {
                this.processing = false;
                if(error.response.status == 422){
                    this.newData.errors = error.response.data.errors;
                }
            });
        },
    },

    mounted() {
        this.getForm();
    },
}
</script>
