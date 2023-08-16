<template>
    <div class="flex h-full">
        <div class="w-96 h-full bg-white overflow-x-hidden">
            <project-header :project="project" class="bg-white"></project-header>

            <settings-nav :project="project"></settings-nav>
        </div>

        <div class="w-full overflow-x-hidden">
            <div class="p-4">
                <h4 class="mb-2 p-2 font-bold text-xl">Users & Roles</h4>

                <div class="bg-white mt-2 rounded-md p-4 w-full xl:w-3/5">
                    <div class="overflow-x-auto w-full border clear-both mt-2">
                        <table class="min-w-full divide-y divide-gray-200 ">
                            <thead class="bg-gray-100">
                                <tr>
                                        <th scope="col" class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Role</th>
                                        <th scope="col" class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Users</th>
                                        <th scope="col" class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider w-64"></th>
                                </tr>
                            </thead>

                            <tbody class="bg-white divide-y divide-gray-200">
                                <tr>
                                    <td class="px-6 py-3 text-sm align-top">
                                        Admin
                                        <small class="block text-gray-600 text-xs">Can create and edit collections and content</small>
                                    </td>
                                    <td class="px-6 py-3 text-sm">
                                        <div v-for="user in super_admins" :key="user.id" class="border border-gray-100 mb-2 block w-full p-2">
                                            <div class="flex items-center">
                                                <div>
                                                    <div class="flex bg-green-500 text-white p-2 text-md rounded-full text-center mr-2 w-9"><div class="w-full text-center">{{ getUserNameInitials(user.name) }}</div></div>
                                                </div>
                                                <div>
                                                    <div class="block">{{user.name}}</div>
                                                    <div class="block text-sm">{{user.email}}</div>
                                                </div>
                                            </div>
                                        </div>
                                        <div v-for="user in admins" :key="user.id" class="border border-gray-100 mb-2 block w-full p-2">
                                            <div class="flex flex-start w-full items-center">
                                                <div>
                                                    <div class="flex bg-green-500 text-white p-2 text-md rounded-full text-center mr-2 w-9"><div class="w-full text-center">{{ getUserNameInitials(user.name) }}</div></div>
                                                </div>
                                                <div>
                                                    <div class="block">{{user.name}}</div>
                                                    <div class="block text-sm">{{user.email}}</div>
                                                </div>
                                                <div class="ml-auto">
                                                    <i class="fa fa-minus-circle text-red-400 cursor-pointer hover:text-red-500 text-lg ml-2" @click="removeUser(user, 'admin')"></i>
                                                </div>
                                            </div>
                                        </div>
                                    </td>
                                    <td class="px-6 py-3 text-sm text-right align-top">
                                        <ui-button color="indigo-500" @click.native="assignUser('admin')">
                                            + Assign User
                                        </ui-button>
                                    </td>
                                </tr>
                                <tr>
                                    <td class="px-6 py-3 text-sm align-top">
                                        Editor
                                        <small class="block text-gray-600 text-xs">Can create and edit content</small>
                                    </td>
                                    <td class="px-6 py-3 text-sm">
                                        <div v-for="user in editors" :key="user.id" class="border border-gray-100 mb-2 block w-full p-2">
                                            <div class="flex flex-start w-full items-center">
                                                <div>
                                                    <div class="flex bg-green-500 text-white p-2 text-md rounded-full text-center mr-2 w-9"><div class="w-full text-center">{{ getUserNameInitials(user.name) }}</div></div>
                                                </div>
                                                <div>
                                                    <div class="block">{{user.name}}</div>
                                                    <div class="block text-sm">{{user.email}}</div>
                                                </div>
                                                <div class="ml-auto">
                                                    <i class="fa fa-minus-circle text-red-400 cursor-pointer hover:text-red-500 text-lg ml-2" @click="removeUser(user, 'editor')"></i>
                                                </div>
                                            </div>
                                        </div>
                                    </td>
                                    <td class="px-6 py-3 text-sm text-right align-top">
                                        <ui-button color="indigo-500" @click.native="assignUser('editor')">
                                            + Assign User
                                        </ui-button>
                                    </td>
                                </tr>
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>
        </div>

        <ui-modal :show="openAssignUserModal" @close="closeAssignUserModal">
            <template #title>
                <div class="flex justify-between">
                    <div>
                        Assign User
                    </div>
                    <div>
                        <ui-button color="indigo-500" v-if="!createNewUser" @click.native="createNewUser = true">
                            Create New User
                        </ui-button>
                        <ui-button color="green-500" v-if="createNewUser" @click.native="createNewUser = false">
                            <i class="fas fa-chevron-left text-white mr-3"></i> Back
                        </ui-button>
                    </div>
                </div>
                
            </template>

            <template #content>
                <div class="mt-4">
                    <div v-if="!createNewUser">
                        <table class="min-w-full divide-y divide-gray-200 ">
                            <thead class="bg-gray-100">
                                <tr>
                                    <th scope="col" class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Name</th>
                                    <th scope="col" class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">E-mail</th>
                                    <th scope="col" class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider w-px"></th>
                                </tr>
                            </thead>
                            <tbody class="bg-white divide-y divide-gray-200">
                                <tr v-for="user in users" :key="user.id">
                                    <td class="px-6 py-3 text-sm">{{ user.name }}</td>
                                    <td class="px-6 py-3 text-sm">{{ user.email }}</td>
                                    <td class="py-3 text-sm text-right items-right">
                                        <ui-button color="indigo-500" @click.native="selectUser(user)">
                                            Select
                                        </ui-button>
                                    </td>
                                </tr>
                            </tbody>
                        </table>
                    </div>
                    <div v-if="createNewUser">
                        <form @submit.prevent="createNewUserSubmit">
                            <div class="mt-2">
                                <label v-formlabel>Name</label>
                                <input type="text" v-model="new_user.name" v-forminput autofocus>
                                <p class="text-sm text-red-600 mt-1" v-if="new_user.errors.name">{{ new_user.errors.name[0] }}</p>
                            </div>
                            <div class="mt-2">
                                <label v-formlabel>E-mail</label>
                                <div class="mt-1 flex rounded-sm">
                                    <span class="inline-flex items-center px-3 rounded-l-sm border border-r-0 border-gray-300 bg-gray-50 text-gray-500 text-sm  "><i class="fa fa-at"></i></span>
                                    <input type="email" v-model="new_user.email" v-forminput class="rounded-l-none">
                                </div>
                                <p class="text-sm text-red-600 mt-1" v-if="new_user.errors.email">{{ new_user.errors.email[0] }}</p>
                            </div>
                            <div class="mt-2">
                                <label v-formlabel>Password</label>

                                <div class="mt-1 flex rounded-sm">
                                    <span class="inline-flex items-center px-3 rounded-l-sm border border-r-0 border-gray-300 bg-gray-50 text-gray-500 text-sm  "><i class="fa fa-lock"></i></span>
                                    <input :type="passwordShow ? 'text' : 'password'" v-model="new_user.password" v-forminput class="rounded-l-none">
                                    <span class="inline-flex items-center px-3 rounded-r-sm border border-l-0 border-gray-300 bg-gray-50 text-gray-500 text-sm cursor-pointer" @click="showPassword()"><i class="fa fa-eye"></i></span>

                                    <span class="inline-flex items-center px-3 rounded-r-sm border border-l-0 border-gray-300 bg-gray-50 text-gray-500 text-sm cursor-pointer" @click="generatePassword()">Generate</span>
                                </div>
                                <p class="text-sm text-red-600 mt-1" v-if="new_user.errors.password">{{ new_user.errors.password[0] }}</p>
                            </div>
                        </form>
                    </div>
                </div>
            </template>

            <template #footer>
                <ui-button color="gray-50" hover="gray-200" @click.native="closeAssignUserModal">
                    <span class="text-gray-800">Cancel</span>
                </ui-button>
                <ui-button color="indigo-500" v-if="createNewUser" @click.native="createNewUserSubmit">
                    Create User
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

export default {
    components: {
        ProjectHeader,
        SettingsNav,
        UiButton,
        UiModal
    },

    data(){
        return {
            project: {},
            super_admins: {},
            admins: {},
            editors: {},
            users: {},
            current_assign_role: null,
            openAssignUserModal: false,
            createNewUser: false,
            new_user: {
                password: null,
                errors: {}
            },
            passwordShow: false
        }
    },

    methods: {
        getProject(){
            axios.get('/admin/projects/settings/users/'+this.$route.params.project_id).then((response) => {
                this.project = response.data.project
                this.super_admins = response.data.super_admins;
                this.admins = response.data.admins;
                this.editors = response.data.editors;
                this.users = response.data.users;
            });
        },

        getUserNameInitials(name) {
            let initials = name.split(' ');
  
            if(initials.length > 1) {
                initials = initials.shift().charAt(0) + initials.pop().charAt(0);
            } else {
                initials = name.substring(0, 2);
            }
            
            return initials.toUpperCase();
        },

        assignUser(role){
            this.openAssignUserModal = true;
            this.current_assign_role = role;
            this.createNewUser = false;
        },

        selectUser(user){
            let assignUserData = {
                role: this.current_assign_role,
                user_id: user.id,
            };
            
            axios.post('/admin/projects/settings/users/assign/'+this.project.id, assignUserData).then((response) => {
                this.$toast.success('User has assigned!');
                this.getProject();
                this.closeAssignUserModal();
            });
        },

        removeUser(user, role){
            let removeUserData = {
                role: role,
                user_id: user.id,
            };

            this.$swal.fire({
                title: 'Are you sure',
                text: "you want to remove this user from the project? User account will not be deleted.",
            }).then((result) => {
                if (result.value) {
                    axios.post('/admin/projects/settings/users/remove-user/'+this.project.id, removeUserData).then((response) => {
                        this.$toast.success('User removed.');
                        this.getProject();
                        this.closeAssignUserModal();
                    });
                }
            });
        },

        closeAssignUserModal(){
            this.openAssignUserModal = false;
            this.createNewUser = false;
        },

        showPassword(){
            this.passwordShow = !this.passwordShow;
        },

        generatePassword (){
            let CharacterSet = '';
            let password = '';
            
            CharacterSet += 'abcdefghijklmnopqrstuvwxyz';
            CharacterSet += 'ABCDEFGHIJKLMNOPQRSTUVWXYZ';
            CharacterSet += '0123456789';
            CharacterSet += '![]{}()%&*$#^<>~@|';
            
            for(let i=0; i < 12; i++) {
                password += CharacterSet.charAt(Math.floor(Math.random() * CharacterSet.length));
            }
            this.new_user.password = password;
            this.passwordShow = true;
        },

        createNewUserSubmit(){
            axios.post('/admin/projects/settings/users/new/'+this.project.id, this.new_user).then((response) => {
                this.$toast.success('User created!');
                this.getProject();
                this.createNewUser = false;
                this.new_user = {
                    password: null,
                    errors: {}
                }
            }, (error) => {
                if(error.response.status == 422){
                    this.new_user.errors = error.response.data.errors;
                }
            });
        }

    },

    mounted(){
        this.getProject();
    },
}
</script>