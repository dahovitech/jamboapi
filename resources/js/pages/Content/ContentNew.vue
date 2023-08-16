<template>
    <div class="flex h-full overflow-hidden">
        <div class="w-96 overflow-x-hidden bg-white">
            <project-header :project="project" class="bg-white"></project-header>

            <content-sidebar :project="project"></content-sidebar>
        </div>

        <div class="p-4 w-full overflow-x-auto">
            <div class="mb-2 py-2 font-bold text-lg flex justify-between">
                <div>
                    {{ collection.name }} <small class="text-gray-500 font-normal"> / Create New Content</small>
                </div>
                <div class="flex">
                    <ui-button :color="'indigo-500'" class="rounded-r-none" @click.native="saveNew(false)">Save</ui-button>

                    <div class="flex border-indigo-600 border border-l-1 border-t-0 border-b-0  border-r-0">
                        <ui-dropdown align="right">
                            <template #trigger>
                                <button class="bg-indigo-500 hover:bg-indigo-600 items-center px-2 py-3 border border-transparent rounded-sm text-sm text-white focus:outline-none transition ease-in-out duration-150 rounded-l-none"><i class="fa fa-sort-down"></i></button>
                            </template>

                            <template #content>
                                <ui-button type="button" color="white" hover="gray-200" class="w-full h-full" @click.native="saveNew(false, 'close')">
                                    <div class="text-gray-600 text-sm text-left">Save and close</div>
                                </ui-button>
                                <ui-button type="button" color="white" hover="gray-200" class="w-full h-full" @click.native="saveNew(false, 'new')">
                                    <div class="text-gray-600 text-sm text-left">Save and create new</div>
                                </ui-button>
                            </template>
                        </ui-dropdown>
                    </div>

                    <ui-button :color="'green-500'" class="rounded-r-none ml-2" @click.native="saveNew(true)">Save and Publish</ui-button>
                    <div class="flex border-green-600 border border-l-1 border-t-0 border-b-0  border-r-0">
                        <ui-dropdown align="right" width="60">
                            <template #trigger>
                                <button class="bg-green-500 hover:bg-green-600 items-center px-2 py-3 border border-transparent rounded-sm text-sm text-white focus:outline-none transition ease-in-out duration-150 rounded-l-none"><i class="fa fa-sort-down"></i></button>
                            </template>

                            <template #content>
                                <ui-button type="button" color="white" hover="gray-200" class="w-full h-full" @click.native="saveNew(true, 'close')">
                                    <div class="text-gray-600 text-sm text-left">Save, publish and close</div>
                                </ui-button>
                                <ui-button type="button" color="white" hover="gray-200" class="w-full h-full" @click.native="saveNew(true, 'new')">
                                    <div class="text-gray-600 text-sm text-left">Save, publish and create new</div>
                                </ui-button>
                            </template>
                        </ui-dropdown>
                    </div>
                </div>
            </div>

            <div class="grid grid-cols-5 space-x-4 h-full overflow-hidden">
                <div class="col-span-3 xl:col-span-4 p-5 bg-white mt-2 rounded-sm overflow-x-hidden">
                    <form class="space-y-6">
                        <div v-for="field in collection.fields" :key="field.id">
                            <label v-formlabel>
                                {{ field.label }}
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
                                        class="h-96 mb-16 rounded-sm border-gray-200"
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
                                                <button type="button" @click="openMediaLibraryModalFn(field.name, true)">
                                                    <svg viewBox="0 0 18 18"> <rect class="ql-stroke" height="10" width="12" x="3" y="4"></rect> <circle class="ql-fill" cx="6" cy="7" r="1"></circle> <polyline class="ql-even ql-fill" points="5 12 5 11 7 9 8 10 11 7 13 9 13 12 5 12"></polyline> </svg>
                                                </button>
                                                <button class="ql-video"></button>
                                            </span>

                                            <span class="ql-formats">
                                                <button class="ql-clean"></button>
                                            </span>
                                        </div>
                                    </quill-editor>
                                    <div class="clear-both"></div>
                                </div>
                                <div v-if="field.type == 'slug'">
                                    <div v-if="field.options.slug.field === null">
                                        <input type="text" v-model="newData.data[field.name]" @input="newData.data[field.name] = $slugify(newData.data[field.name]);" :readonly="field.options.slug.readonly" :disabled="field.options.slug.readonly" :class="{'cursor-not-allowed': field.options.slug.readonly}" :placeholder="field.placeholder" v-forminput>
                                    </div>
                                    <div v-else>
                                        <input type="text" v-model="newData.data[field.name]" :readonly="field.options.slug.readonly" :disabled="field.options.slug.readonly" :class="{'cursor-not-allowed': field.options.slug.readonly}" :placeholder="field.placeholder" v-forminput>
                                    </div>
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
                                        <input :type="passwordShow[field.name] ? 'text' : 'password'" v-model="newData.data[field.name]" v-forminput class="rounded-l-none">
                                        <span class="inline-flex items-center px-3 rounded-r-sm border border-l-0 border-gray-300 bg-gray-50 text-gray-500 text-sm cursor-pointer" @click="showPassword(field.name)"><i class="fa fa-eye"></i></span>

                                        <span class="inline-flex items-center px-3 rounded-r-sm border border-l-0 border-gray-300 bg-gray-50 text-gray-500 text-sm cursor-pointer" @click="generatePassword(field.name)">Generate</span>
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
                                            <input type="checkbox" id="toggleB" class="sr-only" v-model="newData.data[field.name]" :run="newData.data[field.name] = false" >
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
                                    <div class="w-32 h-32 float-left my-1 mr-2 p-3 bg-gray-200 rounded-sm flex items-center text-center cursor-pointer hover:bg-gray-300" @click="openMediaLibraryModalFn(field.name, false, field.options.media.type)">
                                        <div class="w-full">
                                            <i class="fa fa-plus text-2xl text-gray-400"></i>
                                            <div class="block text-sm text-gray-500">
                                                <span v-if="field.options.media.type == 1">Add a single file from the library</span>
                                                <span v-if="field.options.media.type == 2">Add multiple files from the library</span>
                                            </div>
                                        </div>
                                    </div>

                                    <div v-for="file in newData.files[field.name]" :key="file.id" class="relative float-left w-32 h-32 my-1 mr-2">
                                        <div class="absolute inset-0 rounded-sm bg-black bg-opacity-70 items-center text-center flex opacity-0 hover:opacity-100 z-10"
                                        >
                                            <div class="w-full">
                                                <div class="text-xs text-white w-full mb-3" :title="file.name">
                                                    {{ file.name.substring(0,16) }}
                                                    <span v-if="file.name.length > 16">...</span>
                                                </div>
                                                <div class="text-sm w-full">
                                                    <a :href="file.full_url" target="_blank">
                                                        <i v-if="file.type == 'jpg' || file.type == 'jpeg' || file.type == 'png' || file.type == 'bmp' || file.type == 'gif'  || file.type == 'webp'" class="fa fa-eye text-gray-100 cursor-pointer hover:text-gray-200 text-lg mr-2"></i>
                                                        <i v-else class="fa fa-file-download text-gray-100 cursor-pointer hover:text-gray-200 text-lg mr-2"></i>
                                                    </a>

                                                    <i class="fa fa-minus-circle text-gray-100 cursor-pointer hover:text-gray-200 text-lg ml-2" @click="removeFile(file, field.name)"></i>
                                                </div>
                                            </div>
                                        </div>

                                        <img class="rounded-sm w-full h-full object-cover border border-gray-200" v-if="file.type == 'jpg' || file.type == 'jpeg' || file.type == 'png' || file.type == 'gif' || file.type == 'webp'" :src="file.full_url_thumb" />

                                        <div class="w-full h-full flex items-center text-center border rounded-sm border border-gray-200" v-else>
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
                                <div v-if="field.type == 'relation'" class="w-full">
                                    <div class="w-full border rounded-sm p-2">
                                        <div class="text-indigo-500 text-sm cursor-pointer p-2 hover:bg-indigo-50 rounded-sm w-full lg:w-1/3 xl:1/4" @click="openRelationModalFn(field.name, field.options.relation.collection, field.options.relation.type)">
                                            <i class="fa fa-link"></i> Select relation ({{ field.options.relation.type == 1 ? 'One to One' : 'One to Many'}})
                                        </div>

                                        <div class="overflow-x-auto sm:rounded-sm" v-if="relationRecords[field.name] !== undefined && relationRecords[field.name].length !== 0">
                                            <table class="min-w-full divide-y divide-gray-200 ">
                                                <thead>
                                                    <tr>
                                                        <th scope="col" class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider"></th>
                                                        <th scope="col" class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider"></th>
                                                        <th scope="col" class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider"></th>

                                                        <th scope="col" class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider whitespace-nowrap">
                                                            <div class="w-full flex justify-between item-center">
                                                                Created At
                                                            </div>
                                                        </th>
                                                        <th scope="col" class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider whitespace-nowrap" v-for="field in relationRecords[field.name].collection.fields" :key="field.id" v-show="field.type != 'richtext' && field.type != 'password' && field.type != 'media' && field.type != 'json' && field.type != 'relation' && !JSON.parse(field.options).hideInContentList">
                                                            <div class="w-full flex justify-between item-center">
                                                                {{ field.label }}
                                                            </div>
                                                        </th>
                                                    </tr>
                                                </thead>

                                                <tbody>
                                                    <tr v-for="item in relationRecords[field.name]" :key="item.id">
                                                        <td class="pl-2 py-4 text-sm text-center w-px">
                                                            <a class="text-red-500 px-3 py-1 rounded-sm hover:bg-gray-100 cursor-pointer bg-gray-50" @click="removeRelation(item, field.name)"><i class="fa fa-minus-circle"></i></a>
                                                        </td>
                                                        <td class="pl-2 py-4 text-sm text-center w-px whitespace-nowrap">
                                                            <span class="text-gray-500 text-sm rounded-sm bg-gray-200 px-3 py-1">
                                                                {{ relationRecords[field.name].collection.name }}
                                                            </span>
                                                        </td>
                                                        <td class="pl-2 py-4 text-sm text-center w-px">
                                                            <span v-if="item.published_at !== null" class="text-gray-500 text-sm rounded-sm bg-green-200 px-3 py-1">published</span>
                                                            <span v-else class="text-gray-500 text-sm rounded-sm bg-gray-200 px-3 py-1">draft</span>
                                                        </td>
                                                        <td class="px-6 py-3 text-sm w-px whitespace-nowrap text-gray-600">
                                                            {{ item.created_at | date('D MMM YYYY, H:mm') }}
                                                        </td>
                                                        <td class="px-6 py-3 text-sm min-w-full whitespace-nowrap"  v-for="field in relationRecords[field.name].collection.fields" :key="field.id" v-show="field.type != 'richtext' && field.type != 'password' && field.type != 'media' && field.type != 'json' && field.type != 'relation' && !JSON.parse(field.options).hideInContentList">
                                                            <span v-for="meta in item.meta" :key="meta.id">
                                                                <span v-if="meta.field_name == field.name" :class="{'rounded-md bg-gray-100 p-1 mr-1' : JSON.parse(field.options).repeatable && meta.value !== null}">
                                                                    <span v-if="field.type == 'date'">{{ meta.value | date }}</span>
                                                                    <span v-else-if="field.type == 'longtext' && meta.value !== null" :title="meta.value">
                                                                        {{ meta.value.substring(0,20) }}
                                                                        <span v-if="meta.value.length > 20">...</span>
                                                                    </span>
                                                                    <span v-else>{{ meta.value }}</span>
                                                                </span>
                                                            </span>
                                                        </td>
                                                    </tr>
                                                </tbody>
                                            </table>
                                        </div>
                                    </div>
                                </div>
                                <div v-if="field.type == 'json'">
                                    <div class="w-full rounded-sm border border-gray-300">
                                        <codemirror ref="cmEditor" v-model="newData.data[field.name]" :options="cmOptions" />
                                    </div>
                                </div>

                                <div class="clear-both"></div>
                                <small v-if="field.description != ''" class="text-gray-500">{{ field.description }}</small>
                            </div>

                            <p class="text-sm text-red-600 mt-1" v-if="newData.errors['data.'+field.name]">{{ newData.errors['data.'+field.name][0] }}</p>
                        </div>
                    </form>

                    <div class="clear-both h-32"></div>
                </div>

                <div class="col-span-2 xl:col-span-1 mt-2 ml-2">
                    <div class="bg-white mb-2 rounded-sm" v-if="Object.keys(newData.errors).length !== 0">
                        <div class="p-5">
                            <p class="text-sm text-red-600 mt-1">
                                <span v-if="Object.keys(newData.errors).length === 1">
                                    {{Object.keys(newData.errors).length}} field have invalid value, please correct it before saving.
                                </span>
                                <span v-else>
                                    {{Object.keys(newData.errors).length}} fields have invalid values, please correct them before saving.
                                </span>
                            </p>
                        </div>
                    </div>
                    <div class="bg-white mb-2 rounded-sm">
                        <div class="p-4 text-sm text-gray-700">
                            <div class="text-md font-semibold">Locale</div>

                            <div class="mt-2">
                                <input name="locale" id="default_locale" type="radio" v-model="newData.locale" :value="project.default_locale">
                                <label for="default_locale">{{ project.default_locale }} ({{ getLocale(project.default_locale) }})</label>
                            </div>
                            <div v-if="project.locales !== undefined">
                                <div class="mt-2" v-for="locale in project.locales.split(',')" :key="locale">
                                    <div v-if="locale != project.default_locale">
                                        <input name="locale" :id="'locale_'+locale" type="radio" v-model="newData.locale" :value="locale">
                                        <label :for="'locale_'+locale">{{ locale }} ({{ getLocale(locale) }})</label>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                    <div class="bg-white mb-2 rounded-sm">
                        <router-link :to="{name: 'projects.content.list', params: { project_id: $route.params.project_id, col_id: $route.params.col_id } }" class="bg-gray-200 text-gray-700 hover:bg-gray-300 hover:text-gray-800 items-center px-4 py-2 rounded-sm text-sm text-center focus:outline-none transition ease-in-out duration-150 block"><i class="fa fa-angle-double-left">
                            </i> Back to content list
                        </router-link>
                    </div>
                </div>
            </div>
        </div>

        <ui-modal maxWidth="7xl" :show="openMediaLibraryModal" :disableHeader="true" @close="closeMediaLibraryModal">
            <template #content>
                <media-library :project="project" :selected="selectedFiles" @insert="insertFiles" :disableDelete="false" :media_type="mediaModalTYPE"></media-library>
            </template>

            <template #footer>
                <ui-button color="gray-50" hover="gray-200" @click.native="closeMediaLibraryModal">
                    <span class="text-gray-800">Cancel</span>
                </ui-button>
            </template>
        </ui-modal>

        <ui-modal maxWidth="7xl" :show="openRelationModal" @close="closeRelationModal">
            <template #title>
                Select Relation ({{ relationModalCollectionTYPE == 1 ? 'One to One' : 'One to Many' }})
            </template>

            <template #content>
                <content-table v-if="relationModalCollectionID !== null" :collection_id="relationModalCollectionID" :relationSelect="true" :eachProp="5" :relation_type="relationModalCollectionTYPE" @addSelected="addSelectedRelation"></content-table>
            </template>

            <template #footer>
                <ui-button color="gray-50" hover="gray-200" @click.native="closeRelationModal">
                    <span class="text-gray-800">Cancel</span>
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
import MediaLibrary from '../MediaLibrary.vue'
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

import ContentTable from './ContentTable.vue'

import localesJson from '../../locales'

export default {
    components: {
        ProjectHeader,
        ContentSidebar,
        UiButton,
        UiModal,
        UiDropdown,
        MediaLibrary,
        codemirror,
        quillEditor,
        ContentTable
    },

    data(){
        return {
            project: {},
            collection: {},
            newData: {
                data: {},
                files: {},
                errors: {}
            },
            openMediaLibraryModal: false,
            currentMediaField: null,
            selectedFiles: [],
            mediaModalTYPE: null,
            passwordShow: [],
            cmOptions: {
                autoCloseBrackets: true,
                lineNumbers: true,
                matchBrackets: true,
                mode: {
                    name: 'javascript',
                    json: true
                },
                smartIndent: true,
                tabSize: 2,
                theme:'solarized dark',
                autoRefresh: true
            },
            insertFilesToEditor: false,
            openRelationModal: false,
            relationModalCollectionID: null,
            relationModalCollectionTYPE: null,
            currentRelationField: null,
            relationRecords: {},
        }
    },

    methods: {
        getNew(){
            axios.get('/admin/content/new/'+this.$route.params.project_id+'/'+this.$route.params.col_id).then((response) => {
                this.project = response.data.project;
                this.collection = response.data.collection;
                this.newData.project_id = response.data.project.id;
                this.newData.collection_id = response.data.collection.id;
                this.newData.locale = response.data.project.default_locale;

                this.collection.fields.forEach(element => {
                    element.options = JSON.parse(element.options);
                });

                var array = this.collection.fields;
                for (let i = 0; i < array.length; i++) {
                    this.passwordShow[array[i].name] = false;
                }
                this.collection.fields.forEach(slugField => {
                    if(slugField.options.slug !== undefined && slugField.options.slug.field !== null){
                        for (let i = 0; i < array.length; i++) {
                            if(slugField.options.slug.field === array[i].name){
                                this.collection.fields[i].slug_field = slugField.name;
                            }
                        }
                    }
                });
                this.collection.fields.forEach(field => {
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

        saveNew(published, after=null){
            this.newData.published = published;

            axios.post('/admin/content/store/'+this.$route.params.project_id+'/'+this.$route.params.col_id, this.newData).then((response) => {
                this.$toast.success('Content created!');

                if(after === null){
                    this.$router.push({name: 'projects.content.edit', params: {project_id: this.$route.params.project_id, col_id: this.$route.params.col_id, content_id: response.data.id}});
                } else if(after === 'close'){
                    this.$router.push({name: 'projects.content.list', params: {project_id: this.$route.params.project_id, col_id: this.$route.params.col_id}});
                } else if(after === 'new'){
                    this.$router.go();
                }
            }, (error) => {
                if(error.response.status == 422){
                    this.newData.errors = error.response.data.errors;
                }
            });
        },

        openMediaLibraryModalFn(field_name, insertToEditor=false, media_type){
            this.insertFilesToEditor = insertToEditor
            this.currentMediaField = field_name;
            this.openMediaLibraryModal = true;
            this.mediaModalTYPE = parseInt(media_type);

            if(!insertToEditor){
                this.selectedFiles = this.newData.data[field_name];
            } else {
                this.selectedFiles = [];
            }
        },

        closeMediaLibraryModal(){
            this.openMediaLibraryModal = false;
        },

        async insertFiles(files){
            await axios.post('/admin/content/get-selected-files/'+this.$route.params.project_id, { data: files }).then((response) => {
                if(this.insertFilesToEditor){
                    let editor_ref = 'quillEditor_'+this.currentMediaField;
                    let editor = this.$refs[editor_ref][0].quill;

                    let APP_URL = document.querySelector('meta[name="APP_URL"]').content

                    let array = response.data;
                    let file_link = '';
                    for (let i = 0; i < array.length; i++) {
                        const element = array[i];
                        let full_url = element.full_url;

                        if(element.type == 'jpg' || element.type == 'jpeg' || element.type == 'png' || element.type == 'gif' || element.type == 'webp'){
                            editor.insertEmbed(editor.getSelection(true).index, 'image', full_url);
                        } else {
                            file_link ='<br><p><a href="'+full_url+'" target="_blank">'+element.name+"</a></p>";
                            editor.clipboard.dangerouslyPasteHTML(editor.getSelection(true).index, file_link);
                        }
                    }
                    this.selectedFiles = [];
                } else {
                    this.selectedFiles = files;
                    this.newData.files[this.currentMediaField] = response.data;
                    this.newData.data[this.currentMediaField] = files;
                }
            });
            this.closeMediaLibraryModal();
        },

        removeFile(file, field_name){
            for (let i = 0; i < this.newData.data[field_name].length; i++) {
                const element = this.newData.data[field_name][i];
                if(element == file.id)
                    this.$delete(this.newData.data[field_name], i);
            }
            for (let i = 0; i < this.newData.files[field_name].length; i++) {
                const element = this.newData.files[field_name][i];
                if(element.id == file.id)
                    this.$delete(this.newData.files[field_name], i);
            }
            this.$forceUpdate();
        },

        showPassword(field){
            this.passwordShow[field] = !this.passwordShow[field];
            this.$forceUpdate();
        },

        generatePassword (field){
            let CharacterSet = '';
            let password = '';

            CharacterSet += 'abcdefghijklmnopqrstuvwxyz';
            CharacterSet += 'ABCDEFGHIJKLMNOPQRSTUVWXYZ';
            CharacterSet += '0123456789';
            CharacterSet += '![]{}()%&*$#^<>~@|';

            for(let i=0; i < 12; i++) {
                password += CharacterSet.charAt(Math.floor(Math.random() * CharacterSet.length));
            }
            this.newData.data[field] = password;
            this.passwordShow[field] = true;
            this.$forceUpdate();
        },

        openRelationModalFn(field_name, relation_collection, relation_type){
            this.currentRelationField = field_name;
            this.relationModalCollectionID = relation_collection;
            this.relationModalCollectionTYPE = parseInt(relation_type);
            this.openRelationModal = true;
        },

        closeRelationModal(){
            this.openRelationModal = false;
        },

        async addSelectedRelation(data){
            this.closeRelationModal();

            await axios.post('/admin/content/get-selected-records/'+this.$route.params.project_id, { data: data }).then((response) => {
                this.relationRecords[this.currentRelationField] = response.data.content;
                this.relationRecords[this.currentRelationField].collection = response.data.collection;
                this.newData.data[this.currentRelationField] = data.selected;
                this.$forceUpdate();
            });
        },

        removeRelation(item, field_name){
            for (let i = 0; i < this.newData.data[field_name].length; i++) {
                const element = this.newData.data[field_name][i];
                if(element == item.id)
                    this.$delete(this.newData.data[field_name], i);
            }
            for (let i = 0; i < this.relationRecords[field_name].length; i++) {
                const element = this.relationRecords[field_name][i];
                if(element.id == item.id)
                    this.$delete(this.relationRecords[field_name], i);
            }
            this.$forceUpdate();
        },

        getLocale(locale){
            return localesJson[locale];
        },
    },

    mounted(){
        this.getNew();
    },

    watch: {
        '$route.params.col_id'(newId, oldId) {
            this.getNew();
        },
    }
}
</script>
