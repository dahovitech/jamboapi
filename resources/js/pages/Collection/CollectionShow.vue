<template>
    <div class="flex h-full overflow-hidden">
        <div class="w-96 overflow-x-hidden h-full bg-white">
            <project-header :project="project" class="bg-white"></project-header>

            <collection-sidebar :project="project"></collection-sidebar>
        </div>

        <div class="p-4 w-9/12 overflow-x-auto">
            <div class="mb-2 p-2 font-bold text-lg flex">
                <div class="flex">
                    {{ collection.name }} <small class="text-gray-400 ml-1">#{{ collection.slug }}</small>
                </div>
                <div class="flex ml-1">
                    <ui-dropdown align="left">
                        <template #trigger>
                            <button class="text-indigo-500 hover:bg-gray-100 px-2 rounded-sm"><i class="fa fa-ellipsis-h object-bottom"></i></button>
                        </template>

                        <template #content>
                            <ui-button type="button" color="white" hover="gray-200" class="w-full h-full" @click.native="openEditCollectionModal = true">
                                <div class="text-black text-sm text-left"><i class="fa fa-edit"></i> Edit Collection</div>
                            </ui-button>
                            <ui-button type="button" color="white" hover="gray-200" class="w-full h-full" @click.native="deleteCollection(collection)">
                                <div class="text-black text-sm text-left"><i class="fa fa-trash-alt text-red-500"></i> Delete Collection</div>
                            </ui-button>
                        </template>
                    </ui-dropdown>
                </div>
            </div>

            <ul>
                <draggable :list="collection.fields" @end="sortFields" v-bind="dragOptions" handle=".handle">
                    <transition-group type="transition">
                        <li class="w-full mb-2" v-for="field in collection.fields" :key="field.id">
                            <div class="flex items-center w-full bg-white rounded-sm p-4">
                                <i class="fas fa-grip-vertical mr-4 text-gray-500 cursor-pointer handle"></i>
                                <div :class="fieldDetails[field.type].bg" class="mr-4 text-gray-100 rounded-sm text-xl items-center text-center flex field_icon_xl"><i :class="fieldDetails[field.type].icon" class="w-full"></i></div>
                                <div class="items-center w-full">
                                    <div class="text-lg">{{ field.label }}</div>

                                    <div class="w-full flex justify-between">
                                        <div class="flex space-x-1 items-center">
                                            <span class="text-blue-900 text-sm rounded-sm bg-gray-200 px-3">#{{ field.name }}</span>
                                            <span class="text-blue-900 text-sm rounded-sm bg-indigo-200 px-3">
                                                {{ field.type }}
                                                <span v-if="field.type == 'enumeration'">
                                                    <span v-if="field.options.multiple">: multiple</span>
                                                </span>
                                                <span v-if="field.type == 'date'">
                                                    <span v-if="field.options.timepicker">: time</span>
                                                </span>
                                                <span v-if="field.type == 'media'">
                                                    <span v-if="field.options.media.type == 1">: single</span>
                                                    <span v-else-if="field.options.media.type == 2">: multiple</span>
                                                </span>
                                                <span v-else-if="field.type == 'relation'">
                                                    <span v-if="field.options.relation.type == 1">: one-to-one</span>
                                                    <span v-else-if="field.options.relation.type == 2">: one-to-many</span>
                                                </span>
                                            </span>
                                            <span class="text-blue-900 text-sm rounded-sm bg-gray-100 px-3" v-if="field.validations.required.status"><i class="fas fa-star-of-life text-xs"></i> required</span>
                                            <span class="text-blue-900 text-sm rounded-sm bg-gray-100 px-3" v-if="field.validations.unique.status"><i class="fas fa-fingerprint text-xs"></i> unique</span>
                                            <span class="text-blue-900 text-sm rounded-sm bg-gray-100 px-3" v-if="field.options.repeatable"><i class="fas fa-redo text-xs"></i> repeatable</span>
                                            <span class="text-blue-900 text-sm rounded-sm bg-gray-100 px-3" v-if="field.options.hideInContentList">hide in content list</span>
                                            <span class="text-blue-900 text-sm rounded-sm bg-gray-100 px-3" v-if="field.options.hiddenInAPI">hidden in api</span>
                                        </div>

                                        <div>
                                            <a @click="openNewFieldModal(field.type, true, field)" class="text-white text-sm rounded-sm bg-indigo-500 px-3 cursor-pointer hover:bg-indigo-600 whitespace-nowrap"><i class="fa fa-edit text-xs"></i> Edit</a>
                                            <a @click="deleteField(field)" class="text-white text-sm rounded-sm bg-red-500 px-3 cursor-pointer hover:bg-red-600 whitespace-nowrap"><i class="fa fa-trash-alt text-xs"></i> Delete</a>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </li>
                    </transition-group>
                </draggable>
            </ul>
        </div>

        <div class="w-96 overflow-x-hidden h-full">
            <div class="bg-white p-4 h-full" v-if="collection.name">
                <h4 class="mb-2 p-2 font-bold text-lg">
                    + Fields
                </h4>
                <ul>
                    <li class="mb-2">
                        <a @click="openNewFieldModal('text')" class="block w-full p-2 cursor-pointer hover:bg-gray-100 bg-gray-50 rounded-sm">
                            <div class="flex">
                                <div :class="fieldDetails.text.bg" class="mr-2 text-gray-100 rounded-sm text-sm items-center text-center flex field_icon"><i :class="fieldDetails.text.icon" class="w-full"></i></div>
                                <div>
                                    <div class="text-sm">{{ fieldDetails.text.label }}</div>
                                    <div class="text-xs mt-1">{{ fieldDetails.text.desc }}</div>
                                </div>
                            </div>
                        </a>
                    </li>
                    <li class="mb-2">
                        <a @click="openNewFieldModal('longtext')" class="block w-full p-2 cursor-pointer hover:bg-gray-100 bg-gray-50 rounded-sm">
                            <div class="flex">
                                <div :class="fieldDetails.longtext.bg" class="mr-2 text-gray-100 rounded-sm text-sm p-3 items-center text-center field_icon"><i :class="fieldDetails.longtext.icon"></i></div>
                                <div>
                                    <div class="text-sm">{{ fieldDetails.longtext.label }}</div>
                                    <div class="text-xs mt-1">{{ fieldDetails.longtext.desc }}</div>
                                </div>
                            </div>
                        </a>
                    </li>
                    <li class="mb-2">
                        <a @click="openNewFieldModal('richtext')" class="block w-full p-2 cursor-pointer hover:bg-gray-100 bg-gray-50 rounded-sm">
                            <div class="flex">
                                <div :class="fieldDetails.richtext.bg" class="mr-2 text-gray-100 rounded-sm text-sm p-3 items-center text-center field_icon"><i :class="fieldDetails.richtext.icon"></i></div>
                                <div>
                                    <div class="text-sm">{{ fieldDetails.richtext.label }}</div>
                                    <div class="text-xs mt-1">{{ fieldDetails.richtext.desc }}</div>
                                </div>
                            </div>
                        </a>
                    </li>
                    <li class="mb-2">
                        <a @click="openNewFieldModal('slug')" class="block w-full p-2 cursor-pointer hover:bg-gray-100 bg-gray-50 rounded-sm">
                            <div class="flex">
                                <div :class="fieldDetails.slug.bg" class="mr-2 text-gray-100 rounded-sm text-sm p-3 items-center text-center field_icon"><i :class="fieldDetails.slug.icon"></i></div>
                                <div>
                                    <div class="text-sm">{{ fieldDetails.slug.label }}</div>
                                    <div class="text-xs mt-1">{{ fieldDetails.slug.desc }}</div>
                                </div>
                            </div>
                        </a>
                    </li>
                    <li class="mb-2">
                        <a @click="openNewFieldModal('email')" class="block w-full p-2 cursor-pointer hover:bg-gray-100 bg-gray-50 rounded-sm">
                            <div class="flex">
                                <div :class="fieldDetails.email.bg" class="mr-2 text-gray-100 rounded-sm text-sm p-3 items-center text-center field_icon"><i :class="fieldDetails.email.icon"></i></div>
                                <div>
                                    <div class="text-sm">{{ fieldDetails.email.label }}</div>
                                    <div class="text-xs mt-1">{{ fieldDetails.email.desc }}</div>
                                </div>
                            </div>
                        </a>
                    </li>
                    <li class="mb-2">
                        <a @click="openNewFieldModal('password')" class="block w-full p-2 cursor-pointer hover:bg-gray-100 bg-gray-50 rounded-sm">
                            <div class="flex">
                                <div :class="fieldDetails.password.bg" class="mr-2 text-gray-100 rounded-sm text-sm p-3 items-center text-center field_icon"><i :class="fieldDetails.password.icon"></i></div>
                                <div>
                                    <div class="text-sm">{{ fieldDetails.password.label }}</div>
                                    <div class="text-xs mt-1">{{ fieldDetails.password.desc }}</div>
                                </div>
                            </div>
                        </a>
                    </li>
                    <li class="mb-2">
                        <a @click="openNewFieldModal('number')" class="block w-full p-2 cursor-pointer hover:bg-gray-100 bg-gray-50 rounded-sm">
                            <div class="flex">
                                <div :class="fieldDetails.number.bg" class="mr-2 text-gray-100 rounded-sm text-sm p-3 items-center text-center field_icon"><i :class="fieldDetails.number.icon"></i></div>
                                <div>
                                    <div class="text-sm">{{ fieldDetails.number.label }}</div>
                                    <div class="text-xs mt-1">{{ fieldDetails.number.desc }}</div>
                                </div>
                            </div>
                        </a>
                    </li>
                    <li class="mb-2">
                        <a @click="openNewFieldModal('enumeration')" class="block w-full p-2 cursor-pointer hover:bg-gray-100 bg-gray-50 rounded-sm">
                            <div class="flex">
                                <div :class="fieldDetails.enumeration.bg" class="mr-2 text-gray-100 rounded-sm text-sm p-3 items-center text-center field_icon"><i :class="fieldDetails.enumeration.icon"></i></div>
                                <div>
                                    <div class="text-sm">{{ fieldDetails.enumeration.label }}</div>
                                    <div class="text-xs mt-1">{{ fieldDetails.enumeration.desc }}</div>
                                </div>
                            </div>
                        </a>
                    </li>
                    <li class="mb-2">
                        <a @click="openNewFieldModal('boolean')" class="block w-full p-2 cursor-pointer hover:bg-gray-100 bg-gray-50 rounded-sm">
                            <div class="flex">
                                <div :class="fieldDetails.boolean.bg" class="mr-2 text-gray-100 rounded-sm text-sm p-3 items-center text-center field_icon"><i :class="fieldDetails.boolean.icon"></i></div>
                                <div>
                                    <div class="text-sm">{{ fieldDetails.boolean.label }}</div>
                                    <div class="text-xs mt-1">{{ fieldDetails.boolean.desc }}</div>
                                </div>
                            </div>
                        </a>
                    </li>
                    <li class="mb-2">
                        <a @click="openNewFieldModal('color')" class="block w-full p-2 cursor-pointer hover:bg-gray-100 bg-gray-50 rounded-sm">
                            <div class="flex">
                                <div :class="fieldDetails.color.bg" class="mr-2 text-gray-100 rounded-sm text-sm p-3 items-center text-center field_icon"><i :class="fieldDetails.color.icon"></i></div>
                                <div>
                                    <div class="text-sm">{{ fieldDetails.color.label }}</div>
                                    <div class="text-xs mt-1">{{ fieldDetails.color.desc }}</div>
                                </div>
                            </div>
                        </a>
                    </li>
                    <li class="mb-2">
                        <a @click="openNewFieldModal('date')" class="block w-full p-2 cursor-pointer hover:bg-gray-100 bg-gray-50 rounded-sm">
                            <div class="flex">
                                <div :class="fieldDetails.date.bg" class="mr-2 text-gray-100 rounded-sm text-sm p-3 items-center text-center field_icon"><i :class="fieldDetails.date.icon"></i></div>
                                <div>
                                    <div class="text-sm">{{ fieldDetails.date.label }}</div>
                                    <div class="text-xs mt-1">{{ fieldDetails.date.desc }}</div>
                                </div>
                            </div>
                        </a>
                    </li>
                    <li class="mb-2">
                        <a @click="openNewFieldModal('time')" class="block w-full p-2 cursor-pointer hover:bg-gray-100 bg-gray-50 rounded-sm">
                            <div class="flex">
                                <div :class="fieldDetails.time.bg" class="mr-2 text-gray-100 rounded-sm text-sm p-3 items-center text-center field_icon"><i :class="fieldDetails.time.icon"></i></div>
                                <div>
                                    <div class="text-sm">{{ fieldDetails.time.label }}</div>
                                    <div class="text-xs mt-1">{{ fieldDetails.time.desc }}</div>
                                </div>
                            </div>
                        </a>
                    </li>
                    <li class="mb-2">
                        <a @click="openNewFieldModal('media')" class="block w-full p-2 cursor-pointer hover:bg-gray-100 bg-gray-50 rounded-sm">
                            <div class="flex">
                                <div :class="fieldDetails.media.bg" class="mr-2 text-gray-100 rounded-sm text-sm p-3 items-center text-center field_icon"><i :class="fieldDetails.media.icon"></i></div>
                                <div>
                                    <div class="text-sm">{{ fieldDetails.media.label }}</div>
                                    <div class="text-xs mt-1">{{ fieldDetails.media.desc }}</div>
                                </div>
                            </div>
                        </a>
                    </li>
                    <li class="mb-2">
                        <a @click="openNewFieldModal('relation')" class="block w-full p-2 cursor-pointer hover:bg-gray-100 bg-gray-50 rounded-sm">
                            <div class="flex">
                                <div :class="fieldDetails.relation.bg" class="mr-2 text-gray-100 rounded-sm text-sm p-3 items-center text-center field_icon"><i :class="fieldDetails.relation.icon"></i></div>
                                <div>
                                    <div class="text-sm">{{ fieldDetails.relation.label }}</div>
                                    <div class="text-xs mt-1">{{ fieldDetails.relation.desc }}</div>
                                </div>
                            </div>
                        </a>
                    </li>
                    <li class="mb-2">
                        <a @click="openNewFieldModal('json')" class="block w-full p-2 cursor-pointer hover:bg-gray-100 bg-gray-50 rounded-sm">
                            <div class="flex">
                                <div :class="fieldDetails.json.bg" class="mr-2 text-gray-100 rounded-sm text-sm p-3 items-center text-center field_icon"><i :class="fieldDetails.json.icon"></i></div>
                                <div>
                                    <div class="text-sm">{{ fieldDetails.json.label }}</div>
                                    <div class="text-xs mt-1">{{ fieldDetails.json.desc }}</div>
                                </div>
                            </div>
                        </a>
                    </li>
                </ul>
            </div>
        </div>

        <ui-modal :show="openNewFieldModalStatus" @close="closeNewFieldModal">
            <template #title>
                <div class="flex">
                    <div>
                        <div :class="fieldDetails[current_field_type].bg" class="mr-2 text-gray-100 rounded-sm text-sm p-3 items-center text-center field_icon">
                            <i :class="fieldDetails[current_field_type].icon"></i>
                        </div>
                    </div>
                    <div class="items-center h-auto flex w-64">
                        <span v-if="!editStatus">Add new Field</span>
                        <span v-if="editStatus">Edit Field</span>
                    </div>
                    <div class="text-right float-right w-full">
                        <div>{{ fieldDetails[current_field_type].label }}</div>
                        <div class="text-sm mt-1">{{ fieldDetails[current_field_type].desc }}</div>
                    </div>
                </div>
            </template>

            <template #content>
                <div>
                    <form @submit.prevent="addNewFieldSubmit">
                        <div class="mt-2">
                            <label v-formlabel>Label</label>
                            <input v-if="!editStatus" type="text" v-model="new_field.label" v-forminput @input="new_field.name = $slugify(new_field.label);" autofocus>
                            <input v-else type="text" v-model="new_field.label" v-forminput autofocus>

                            <p class="text-sm text-red-600 mt-1" v-if="new_field.errors.label">{{ new_field.errors.label[0] }}</p>
                        </div>
                        <div class="mt-6">
                            <label v-formlabel>Field Name</label>
                            <input type="text" v-model="new_field.name" v-forminput>
                            <p class="text-sm text-yellow-600 mt-1" v-if="editStatus"><i class="fa fa-exclamation-triangle"></i> Changing the field name can cause content data loss</p>
                            <p class="text-sm text-red-600 mt-1" v-if="new_field.errors.name">{{ new_field.errors.name[0] }}</p>
                        </div>
                        <div class="mt-6">
                            <label v-formlabel>Description (optional)</label>
                            <input type="text" v-model="new_field.description" v-forminput>
                            <p class="text-xs text-gray-600 mt-1">Displays a hint for the field when creating or editing content</p>
                            <p class="text-sm text-red-600 mt-1" v-if="new_field.errors.description">{{ new_field.errors.description[0] }}</p>
                        </div>
                        <div class="mt-6" v-if="current_field_type !== 'password' && current_field_type !== 'number' && current_field_type !== 'enumeration'&& current_field_type !== 'boolean' && current_field_type != 'color' && current_field_type !== 'date' && current_field_type !== 'time' && current_field_type !== 'media' && current_field_type !== 'relation' && current_field_type !== 'json'">
                            <label v-formlabel>Placeholder (optional)</label>
                            <input type="text" v-model="new_field.placeholder" v-forminput>
                            <p class="text-sm text-red-600 mt-1" v-if="new_field.errors.placeholder">{{ new_field.errors.placeholder[0] }}</p>
                        </div>

                        <div class="mt-6" v-if="current_field_type == 'slug'">
                            <div>
                                <label v-formlabel>Attach slug to a field (optional)</label>

                                <v-select :options="collection.fields.filter(o => o.type == 'text')" :get-option-label="(option) => option.name" :reduce="(option) => option.name"  class="v-select" placeholder="Select Field" v-model="slug.field"></v-select>

                                <p class="text-xs text-gray-600 mt-1">Slug will be generated from selected field. Only <strong>text fields</strong> can be selected</p>
                            </div>
                            <div class="mt-6" v-if="slug.field !== null">
                                <div class="flex items-start">
                                    <div class="flex items-center h-5">
                                        <input v-model="new_field.options.slug.readonly" id="readonly" type="checkbox" v-formcheckbox>
                                    </div>
                                    <div class="ml-3 text-sm">
                                        <label for="readonly" class="font-medium text-gray-700">Read Only</label>
                                        <p class="text-gray-500">Prevents editing slug field</p>
                                    </div>
                                </div>
                            </div>
                        </div>

                        <div class="mt-6" v-if="current_field_type == 'enumeration'">
                            <h3 v-formlabel>List of Values</h3>
                            <div class="grid grid-cols-8 w-6/12">
                                <div class="col-span-5">
                                    <input type="text" v-model="enumeration.item" v-forminput>
                                </div>
                                <div class="col-span-3 pl-3">
                                    <ui-button type="button" color="indigo-500" class="w-full h-full" @click.native="addToEnumList">+ Add</ui-button>
                                </div>
                            </div>
                            <div class="clear-both mt-3">
                                <p class="text-sm text-red-600 mt-1" v-if="new_field.errors['options.enumeration']">{{ new_field.errors["options.enumeration"][0] }}</p>
                                <span class="text-red-300" v-if="!enumeration.itemvalid">! Enter a value</span>
                            </div>
                            <div class="clear-both mt-3">
                                <span class="text-gray-600 text-md rounded-sm bg-gray-100 py-2 px-3 mr-2" v-for="(item, index) in enumeration.list" :key="index">{{ item }} <span class="fas fa-times-circle cursor-pointer ml-2 text-gray-300" @click="removeEnumItem(index)"></span></span>
                            </div>
                            <div class="w-full mt-5">
                                <div class="flex items-start">
                                    <div class="flex items-center h-5">
                                        <input v-model="new_field.options.multiple" id="required" type="checkbox" v-formcheckbox>
                                    </div>
                                    <div class="ml-3 text-sm">
                                        <label for="required" class="font-medium text-gray-700">Allow multiple</label>
                                    </div>
                                </div>
                            </div>
                        </div>

                        <div class="mt-6" v-if="current_field_type == 'date'">
                            <div class="w-full">
                                <div class="flex items-start">
                                    <div class="flex items-center h-5">
                                        <input v-model="new_field.options.timepicker" id="required" type="checkbox" v-formcheckbox>
                                    </div>
                                    <div class="ml-3 text-sm">
                                        <label for="required" class="font-medium text-gray-700">Include time picker</label>
                                    </div>
                                </div>
                            </div>
                        </div>

                        <div class="mt-6" v-if="current_field_type == 'media'">
                            <div>
                                <div v-formlabel>Media Type</div>

                                <div class="block mt-2">
                                    <div class="grid grid-cols-3 space-x-2">
                                        <div class="col-span-1">
                                            <label for="media_single_type" class="p-5 border border-gray-300 rounded-sm text-sm flex items-center space-x-2 cursor-pointer">
                                                <input type="radio" id="media_single_type" v-model="media.type" value="1">
                                                <span>Single File</span>
                                            </label>
                                        </div>
                                        <div class="col-span-1">
                                            <label for="media_multiple_type" class="p-5 border border-gray-300 rounded-sm text-sm flex items-center space-x-2 cursor-pointer">
                                                <input type="radio" id="media_multiple_type" v-model="media.type" value="2">
                                                <span>Multiple Files</span>
                                            </label>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                        <div class="mt-6" v-if="current_field_type == 'relation'">
                            <div>
                                <label v-formlabel>Relation Collection</label>

                                <v-select :options="project.collections" :get-option-label="(option) => option.name+' ('+option.slug+')'" :reduce="(option) => option.id" class="v-select" placeholder="Select Collection" v-model="relation.collection"></v-select>

                                <p class="text-sm text-red-600 mt-1" v-if="new_field.errors['options.relation.collection']">{{ new_field.errors['options.relation.collection'][0] }}</p>
                            </div>
                            <div class="mt-6">
                                <div v-formlabel>Relation Type</div>

                                <div class="block mt-2">
                                    <div class="grid grid-cols-3 space-x-2">
                                        <div class="col-span-1">
                                            <label for="relation_one_to_one" class="p-5 border border-gray-300 rounded-sm text-sm flex items-center space-x-2 cursor-pointer">
                                                <input type="radio" id="relation_one_to_one" v-model="relation.type" value="1">
                                                <span>One to One</span>
                                                <div>
                                                    <svg width="41" height="41" xmlns="http://www.w3.org/2000/svg">
                                                        <g fill="none" fill-rule="evenodd">
                                                            <rect width="41" height="41" rx="2" fill="#FFF"></rect>
                                                            <rect :stroke="relation.type == 1 ? '#1C5DE7' : '#101622'" x=".5" y=".5" width="40" height="40" rx="2" :stroke-opacity="relation.type == 1 ? '' : '.1'"></rect>
                                                            <path :stroke="relation.type == 1 ? '#1C5DE7' : '#919BAE'" d="M14 21.25h14v1H14z"></path>
                                                            <rect :stroke="relation.type == 1 ? '#1C5DE7' : '#919BAE'" x="7.5" y="18.5" width="6" height="6" rx="3"></rect>
                                                            <path :stroke="relation.type == 1 ? '#1C5DE7' : '#919BAE'" d="M33.5 21.5l-5 3v-6z"></path>
                                                        </g>
                                                    </svg>
                                                </div>
                                            </label>
                                        </div>
                                        <div class="col-span-1">
                                            <label for="relation_one_to_many" class="p-5 border border-gray-300 rounded-sm text-sm flex items-center space-x-2 cursor-pointer">
                                                <input type="radio" id="relation_one_to_many" v-model="relation.type" value="2">
                                                <span>One to Many</span>
                                                <div>
                                                    <svg width="41" height="41" xmlns="http://www.w3.org/2000/svg">
                                                        <g fill="none" fill-rule="evenodd">
                                                            <rect width="41" height="41" rx="2" fill="#FFF"></rect>
                                                            <rect :stroke="relation.type == 2 ? '#1C5DE7' : '#E3E9F3'" x=".5" y=".5" width="40" height="40" rx="2"></rect>
                                                            <g transform="translate(7.5 6)">
                                                                <path :stroke="relation.type == 2 ? '#1C5DE7' : '#ABB3C2'" d="M6.5 15.25h14v1h-14z"></path>
                                                                <rect :stroke="relation.type == 2 ? '#1C5DE7' : '#ABB3C2'" y="12.5" width="6" height="6" rx="3"></rect>
                                                                <path :stroke="relation.type == 2 ? '#1C5DE7' : '#ABB3C4'" d="M26 15.5l-5 3v-6z"></path>
                                                                <path :stroke="relation.type == 2 ? '#1C5DE7' : '#ABB3C2'" d="M5.965 17.283l12.124 7-.5.867-12.124-7z"></path>
                                                                <path :stroke="relation.type == 2 ? '#1C5DE7' : '#ABB3C4'" d="M22.727 27.25l-5.83.098 3-5.196z"></path>
                                                                <path :stroke="relation.type == 2 ? '#1C5DE7' : '#ABB3C2'" d="M5.965 13.717l12.124-7-.5-.867-12.124 7z"></path>
                                                                <path :stroke="relation.type == 2 ? '#1C5DE7' : '#ABB3C4'" d="M22.727 3.75l-5.83-.098 3 5.196z"></path>
                                                            </g>
                                                        </g>
                                                    </svg>
                                                </div>
                                            </label>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>

                        <div class="mt-6">
                            <h4 class="font-semibold mt-2 mb-2">VALIDATIONS</h4>

                            <div class="w-full">
                                <div class="flex items-start">
                                    <div class="flex items-center h-5">
                                        <input v-model="new_field.validations.required.status" id="required" type="checkbox" v-formcheckbox>
                                    </div>
                                    <div class="ml-3 text-sm">
                                        <label for="required" class="font-medium text-gray-700">Required</label>
                                        <p class="text-gray-500">Prevents saving content if this field is empty</p>
                                    </div>
                                </div>
                                <div class="w-3/5 block mt-1 ml-7" v-if="new_field.validations.required.status">
                                    <input type="text" v-model="new_field.validations.required.message" placeholder="Custom error message" v-forminput>
                                </div>
                            </div>

                            <!-- Unique validation -->
                            <div class="w-full" :class="{'opacity-60' : new_field.options.repeatable}" v-if="current_field_type != 'password' && current_field_type != 'enumeration' && current_field_type != 'boolean' && current_field_type != 'color' && current_field_type != 'date' && current_field_type != 'time' && current_field_type != 'media' && current_field_type != 'relation' && current_field_type != 'json' ">
                                <div class="flex items-start mt-2">
                                    <div class="flex items-center h-5">
                                        <input v-model="new_field.validations.unique.status" id="unique" type="checkbox" v-formcheckbox :disabled="new_field.options.repeatable">
                                    </div>
                                    <div class="ml-3 text-sm">
                                        <label for="unique" class="font-medium text-gray-700">Unique</label>
                                        <p class="text-gray-500">Prevents saving content if there is a record with the same content.</p>
                                    </div>
                                </div>
                                <div class="w-3/5 block mt-1 ml-7" v-if="new_field.validations.unique.status">
                                    <input type="text" v-model="new_field.validations.unique.message" placeholder="Custom error message" v-forminput>
                                </div>
                            </div>

                            <!-- Charcout validation -->
                            <div class="w-full" v-if="current_field_type != 'richtext' && current_field_type != 'enumeration' && current_field_type != 'boolean' && current_field_type != 'color' && current_field_type != 'date' && current_field_type != 'time' && current_field_type != 'media' && current_field_type != 'relation' && current_field_type != 'json' ">
                                <div class="flex items-start mt-2">
                                    <div class="flex items-center h-5">
                                        <input v-model="new_field.validations.charcount.status" id="charcount" type="checkbox" v-formcheckbox>
                                    </div>
                                    <div class="ml-3 text-sm">
                                        <label for="charcount" class="font-medium text-gray-700">
                                            <span v-if="current_field_type != 'number'">Character count</span>
                                            <span v-if="current_field_type == 'number'">Integer limitations</span>
                                        </label>
                                        <p class="text-gray-500" v-if="current_field_type != 'number'">Specifies a minimum and/or maximum allowed number of characters</p>
                                        <p class="text-gray-500" v-if="current_field_type == 'number'">Specifies a minimum and/or maximum allowed numbers</p>
                                    </div>
                                </div>
                                <div class="w-3/5 block mt-1 ml-7" v-if="new_field.validations.charcount.status">
                                    <div class="grid grid-cols-5">
                                        <div class="col-span-3">
                                            <v-select :options="charcount.fields" class="v-select" v-model="new_field.validations.charcount.type" :clearable="false"></v-select>
                                            <p class="text-sm text-red-600 mt-1" v-if="new_field.errors['validations.charcount.type']">{{ new_field.errors['validations.charcount.type'][0] }}</p>
                                        </div>
                                        <div class="col-span-1 ml-1" v-if="new_field.validations.charcount.type == 'Between'|| new_field.validations.charcount.type == 'Min'">
                                            <input type="number" min="0" onkeypress="return event.charCode >= 48 && event.charCode <= 57" v-model="new_field.validations.charcount.min" placeholder="Min" v-forminput>
                                            <p class="text-sm text-red-600 mt-1" v-if="new_field.errors['validations.charcount.min']">{{ new_field.errors['validations.charcount.min'][0] }}</p>
                                        </div>
                                        <div class="col-span-1 ml-1" v-if="new_field.validations.charcount.type == 'Between'|| new_field.validations.charcount.type == 'Max'">
                                            <input type="number" min="0" onkeypress="return event.charCode >= 48 && event.charCode <= 57" v-model="new_field.validations.charcount.max" placeholder="Max" v-forminput>
                                            <p class="text-sm text-red-600 mt-1" v-if="new_field.errors['validations.charcount.max']">{{ new_field.errors['validations.charcount.max'][0] }}</p>
                                        </div>
                                    </div>
                                </div>
                                <div class="w-3/5 block mt-1 ml-7" v-if="new_field.validations.charcount.status">
                                    <input type="text" v-model="new_field.validations.charcount.message" placeholder="Custom error message" v-forminput>
                                </div>
                            </div>
                        </div>

                        <div class="mt-6" v-if="current_field_type !== 'password'">
                            <h4 class="font-semibold mt-2 mb-2">OTHER OPTIONS</h4>

                            <div class="w-full">
                                <div class="flex items-start" v-if="current_field_type !== 'richtext' && current_field_type !== 'slug' && current_field_type !== 'enumeration' && current_field_type !== 'boolean' && current_field_type !== 'media' && current_field_type !== 'relation' && current_field_type !== 'json'">
                                    <div class="flex items-center h-5">
                                        <input v-model="new_field.options.repeatable" id="repeatable" type="checkbox" v-formcheckbox>
                                    </div>
                                    <div class="ml-3 text-sm">
                                        <label for="repeatable" class="font-medium text-gray-700">Repeatable Field</label>
                                        <p class="text-gray-500">Allow multiple entries for this field</p>
                                    </div>
                                </div>

                                <div class="mt-2 flex items-start">
                                    <div class="flex items-center h-5">
                                        <input v-model="new_field.options.hideInContentList" id="hideInContentList" type="checkbox" v-formcheckbox>
                                    </div>
                                    <div class="ml-3 text-sm">
                                        <label for="hideInContentList" class="font-medium text-gray-700">Hide in content list</label>
                                        <p class="text-gray-500">Field will be invisible in content list table</p>
                                    </div>
                                </div>

                                <div class="mt-2 flex items-start">
                                    <div class="flex items-center h-5">
                                        <input v-model="new_field.options.hiddenInAPI" id="hiddenInAPI" type="checkbox" v-formcheckbox>
                                    </div>
                                    <div class="ml-3 text-sm">
                                        <label for="hiddenInAPI" class="font-medium text-gray-700">Hidden in API</label>
                                        <p class="text-gray-500">Field will be invisible in API.</p>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </form>
                </div>
            </template>

            <template #footer>
                <ui-button color="gray-50" hover="gray-200" @click.native="closeNewFieldModal">
                    <span class="text-gray-800">Cancel</span>
                </ui-button>

                <ui-button color="indigo-500" @click.native="addNewFieldSubmit" :class="{ 'opacity-25': processingAddNewField }" :disabled="processingAddNewField">
                    <span v-if="!editStatus">Add new Field</span>
                    <span v-if="editStatus">Save Changes</span>
                </ui-button>
            </template>
        </ui-modal>

        <ui-modal :show="openEditCollectionModal" @close="closeEditCollectionModal">
            <template #title>
                Update Collection
            </template>

            <template #content>
                <div class="mt-4">
                    <form @submit.prevent="editCollectionSubmit">
                        <div class="grid grid-cols-1 gap-4">
                            <div class="col-span-12 sm:col-span-12">
                                <label v-formlabel>Name</label>
                                <input type="text" v-model="editCollectionData.name" v-forminput>
                                <p class="text-sm text-red-600 mt-2" v-if="editCollectionData.errors.name">{{ editCollectionData.errors.name[0] }}</p>
                            </div>
                            <div class="col-span-12 sm:col-span-12">
                                <label v-formlabel>Slug</label>
                                <input type="text" v-model="editCollectionData.slug" v-forminput>
                                <p class="text-sm text-red-600 mt-2" v-if="editCollectionData.errors.slug">{{ editCollectionData.errors.slug[0] }}</p>
                            </div>
                        </div>
                    </form>
                </div>
            </template>

            <template #footer>
                <ui-button color="gray-50" hover="gray-200" @click.native="closeEditCollectionModal">
                    <span class="text-gray-800">Cancel</span>
                </ui-button>

                <ui-button color="indigo-500" @click.native="editCollectionSubmit">
                    Save Collection
                </ui-button>
            </template>
        </ui-modal>
    </div>
</template>

<script>
import ProjectHeader from '../Project/ProjectHeader'
import UiButton from '../../UI/Button.vue';
import UiModal from '../../UI/Modal.vue'
import CollectionSidebar from './CollectionSidebar'
import draggable from 'vuedraggable'
import UiDropdown from '../../UI/Dropdown.vue'

export default {
    components: {
        ProjectHeader,
        UiButton,
        UiModal,
        CollectionSidebar,
        draggable,
        UiDropdown
    },

    data(){
        return {
            project: {},
            collection: {},
            openNewFieldModalStatus: false,
            current_field_type: "text",
            processingAddNewField: false,
            editStatus: false,
            openEditCollectionModal: false,
            editCollectionData: {
                errors: {
                    name: '',
                    slug: ''
                }
            },
            new_field: {
                type: null,
                options: {},
                project_id: this.$route.params.project_id,
                collection_id: this.$route.params.col_id,
                errors: {
                    label: '',
                    name: '',
                    "options.enumeration": ''
                },
                options: {
                    enumeration: {},
                    relation: {},
                    slug: {},
                    hideInContentList: false,
                    hiddenInAPI: false,
                    repeatable: false,
                },
                validations: {
                    required: {
                        status: false,
                        message: null
                    },
                    unique: {
                        status: false,
                        message: null
                    },
                    charcount: {
                        status: false,
                        message: null,
                        type: 'Between',
                        min: null,
                        max: null
                    }
                }
            },
            slug: {
                field: null,
            },
            enumeration: {
                item: null,
                list: [],
                itemvalid: true
            },
            media: {
                type: 1
            },
            relation: {
                collection: null,
                type: 1
            },
            charcount: {
                fields: ['Between', 'Min', 'Max']
            },
            fieldDetails: {
                text: {
                    label: "Text",
                    desc: "Single line text (headings, titles)",
                    icon: "fas fa-font",
                    bg: "bg-yellow-400"
                },
                longtext: {
                    label: "Long Text",
                    desc: "Multi line text like descriptions",
                    icon: "fas fa-align-left",
                    bg: "bg-yellow-600"
                },
                richtext: {
                    label: "Rich Text",
                    desc: "Rich text editor with formatting",
                    icon: "fas fa-outdent",
                    bg: "bg-yellow-900"
                },
                slug: {
                    label: "Slug",
                    desc: "Like Urls and permalinks",
                    icon: "fas fa-link",
                    bg: "bg-green-400"
                },
                email: {
                    label: "E-mail",
                    desc: "Email field with validation",
                    icon: "fas fa-at",
                    bg: "bg-red-400"
                },
                password: {
                    label: "Password",
                    desc: "Password field with encryption",
                    icon: "fas fa-lock",
                    bg: "bg-blue-400"
                },
                number: {
                    label: "Number",
                    desc: "Integer, decimal, float numbers",
                    icon: "fas fa-sort-numeric-up",
                    bg: "bg-pink-600"
                },
                enumeration: {
                    label: "Enumeration",
                    desc: "List of values",
                    icon: "fas fa-list-ol",
                    bg: "bg-green-600"
                },
                boolean: {
                    label: "Boolean",
                    desc: "True or false",
                    icon: "fas fa-check-square",
                    bg: "bg-red-600"
                },
                color: {
                    label: "Color",
                    desc: "Color picker",
                    icon: "fas fa-tint",
                    bg: "bg-orange-400"
                },
                date: {
                    label: "Date",
                    desc: "Calendar date picker",
                    icon: "fas fa-calendar-alt",
                    bg: "bg-indigo-600"
                },
                time: {
                    label: "Time",
                    desc: "Time picker",
                    icon: "fas fa-calendar-check",
                    bg: "bg-purple-600"
                },
                media: {
                    label: "Media",
                    desc: "Files, images, videos from the library",
                    icon: "fas fa-photo-video",
                    bg: "bg-gray-500"
                },
                relation: {
                    label: "Relation",
                    desc: "Collection relations",
                    icon: "fas fa-exchange-alt",
                    bg: "bg-pink-400"
                },
                json: {
                    label: "JSON",
                    desc: "Data in JSON format",
                    icon: "fas fa-code",
                    bg: "bg-red-700"
                },
            }
        }
    },

    methods: {
        getCollection(){
            axios.get('/admin/collections/show/'+this.$route.params.project_id+'/'+this.$route.params.col_id).then((response) => {
                this.project = response.data.project;
                this.collection = response.data.collection;


                this.editCollectionData.id = response.data.collection.id;
                this.editCollectionData.project_id = response.data.project.id;
                this.editCollectionData.name = response.data.collection.name;
                this.editCollectionData.slug = response.data.collection.slug;
            });
        },

        openNewFieldModal(field_type, edit, field){
            this.openNewFieldModalStatus = true;
            this.new_field.type = field_type;
            this.current_field_type = field_type;

            if(edit){
                this.editStatus = true;
                if(field_type == "slug"){
                    this.slug = field.options.slug;
                }
                if(field_type == "media"){
                    this.media.type = field.options.media.type;
                }
                if(field_type == "relation"){
                    this.relation.collection = field.options.relation.collection;
                    this.relation.type = field.options.relation.type;
                }
                if(field_type == "enumeration"){
                    this.enumeration.list = field.options.enumeration;
                }

                this.new_field = {
                    id: field.id,
                    label: field.label,
                    name: field.name,
                    description: field.description,
                    placeholder: field.placeholder,
                    type: field_type,
                    options: field.options,
                    project_id: this.$route.params.project_id,
                    collection_id: this.$route.params.col_id,
                    errors: {
                        label: '',
                        name: '',
                        "options.enumeration": ''
                    },
                    validations: field.validations,
                }
            }
        },

        closeNewFieldModal(){
            this.openNewFieldModalStatus = false;
            this.clearData();
        },

        addNewFieldSubmit(){
            if(this.new_field.type == "slug"){
                this.new_field.options.slug = this.slug;
            }
            if(this.new_field.type == "media"){
                this.new_field.options.media = {
                    type: this.media.type,
                };
            }
            if(this.new_field.type == "relation"){
                this.new_field.options.relation = {
                    collection: this.relation.collection,
                    type: this.relation.type,
                };
            }

            if(this.editStatus){
                axios.post('/admin/collections/fields/update/'+this.$route.params.project_id+'/'+this.$route.params.col_id+'/'+this.new_field.id, this.new_field)
                    .then((response) => {
                        this.$toast.success('Field has been updated.');
                        this.closeNewFieldModal();
                        this.getCollection();
                    }, (error) => {
                        if(error.response.status == 422){
                            this.new_field.errors = error.response.data.errors;
                        }
                    });
            } else {
                axios.post('/admin/collections/fields/store/'+this.$route.params.project_id+'/'+this.$route.params.col_id, this.new_field)
                    .then((response) => {
                        this.$toast.success('New field added.');
                        this.closeNewFieldModal();
                        this.getCollection();
                    }, (error) => {
                        if(error.response.status == 422){
                            this.new_field.errors = error.response.data.errors;
                        }
                    });
            }
        },

        clearData(){
            this.editStatus = false;
            this.new_field = {
                type: null,
                options: {},
                project_id: this.$route.params.project_id,
                collection_id: this.$route.params.col_id,
                errors: {
                    label: '',
                    name: '',
                    "options.enumeration": ''
                },
                options: {
                    enumeration: {},
                    media: {},
                    relation: {},
                    slug: {},
                    timepicker: false,
                    hideInContentList: false
                },
                validations: {
                    required: {
                        status: false,
                        message: null
                    },
                    unique: {
                        status: false,
                        message: null
                    },
                    charcount: {
                        status: false,
                        message: null,
                        type: 'Between',
                        min: null,
                        max: null
                    }
                }
            },
            this.slug = {
                field: null,
            };
            this.enumeration = {
                item: null,
                list: [],
                itemvalid: true
            };
            this.media = {
                type: 1,
            };
            this.relation = {
                collection: null,
                type: 1,
            };
        },

        sortFields(){
            this.collection.fields.map((field, index) => {
                field.order = index + 1;
            });
            axios.post('/admin/collections/fields/update-order/'+this.$route.params.project_id+'/'+this.$route.params.col_id, this.collection.fields);
        },

        addToEnumList(){
            if(this.enumeration.item != null){
                this.enumeration.list.push(this.enumeration.item);
                this.enumeration.item= null;
                this.enumeration.itemvalid = true;
                this.new_field.options.enumeration = this.enumeration.list;
            } else {
                this.enumeration.itemvalid = false;
            }
        },

        removeEnumItem(index){
            this.enumeration.list.splice(index, 1);
        },

        deleteField(field){
            this.$swal.fire({
                title: 'Are you sure',
                text: "you want to delete this field? All the content will be lost!",
            }).then((result) => {
                if (result.value) {
                    axios.delete('/admin/collections/fields/delete/'+this.$route.params.project_id+'/'+this.$route.params.col_id+'/'+field.id).then((response) => {
                        this.$toast.success('Field deleted.');
                        this.getCollection();
                    });
                }
            });
        },

        closeEditCollectionModal(){
            this.openEditCollectionModal = false;
            this.getCollection();
            this.editCollectionData = {
                errors: {
                    name: '',
                    slug: ''
                }
             };
        },

        editCollectionSubmit(){
            axios.post('/admin/collections/update/'+this.$route.params.project_id+'/'+this.$route.params.col_id, this.editCollectionData)
                .then((response) => {
                    this.$toast.success('Collection updated.');
                    this.closeEditCollectionModal();
                }, (error) => {
                    if(error.response.status == 422){
                        this.editCollectionData.errors = error.response.data.errors;
                    }
                });
        },

        deleteCollection(collection){
            this.$swal.fire({
                title: 'Are you sure',
                text: "you want to delete this collection? All the content will be lost!",
            }).then((result) => {
                if (result.value) {
                    axios.delete('/admin/collections/delete/'+this.$route.params.project_id+'/'+collection.id).then((response) => {
                        this.$toast.success('Collection deleted.');
                        this.$router.push({ name: 'projects', params: { id: this.project.id } });
                    });
                }
            });
        }
    },

    mounted(){
        this.getCollection();
    },

    computed: {
        dragOptions() {
            return {
                animation: 200,
            };
        }
    },

    watch: {
        '$route.params.col_id'(newId, oldId) {
            this.getCollection();
            this.clearData();
        }
    }
}
</script>
