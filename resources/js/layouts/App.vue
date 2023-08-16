<template>
    <div class="flex h-screen bg-gray-50">
        <div :class="sidebarOpen ? 'block' : 'hidden'" @click="sidebarOpen = false" class="fixed z-1 inset-0 bg-black opacity-50 transition-opacity lg:hidden"></div>

        <div :class="sidebarOpen ? 'translate-x-0 ease-out' : '-translate-x-full ease-in'" class="fixed z-2 inset-y-0 left-0 transition duration-300 transform bg-gray-900 overflow-y-auto lg:translate-x-0 lg:static lg:inset-0 text-white">
            <div class="flex flex-col justify-between h-screen">
                <div>
                    <div class="text-center">
                        <brand />
                    </div>

                    <nav class="mt-10">
                        <router-link :to="{name: 'home'}" :exact-active-class="'bg-gray-700'" class="text-md my-2 mx-6 p-3 text-center block hover:bg-gray-700 rounded-sm">
                            <i class="fas fa-tv"></i>
                            <div class="text-xs mt-2">Dashboard</div>
                        </router-link>

                        <router-link v-if="isProjectPage && $route.params.col_id == undefined && checkRole(['admin'+$route.params.project_id])" :to="{name: 'projects.collections', params: { project_id: $route.params.project_id }}" :active-class="'bg-gray-700'" class="text-md my-2 mx-6 p-3 text-center block hover:bg-gray-700 rounded-sm">
                            <i class="fas fa-table"></i>
                            <div class="text-xs mt-2">Collections</div>
                        </router-link>
                        <router-link v-if="isProjectPage && $route.params.col_id != undefined && checkRole(['admin'+$route.params.project_id])" :to="{name: 'projects.collections.show', params: { project_id: $route.params.project_id, col_id: $route.params.col_id }}" :active-class="'bg-gray-700'" class="text-md my-2 mx-6 p-3 text-center block hover:bg-gray-700 rounded-sm">
                            <i class="fas fa-table"></i>
                            <div class="text-xs mt-2">Collections</div>
                        </router-link>

                        <router-link v-if="isProjectPage && $route.params.col_id == undefined" :to="{name: 'projects.content', params: { project_id: $route.params.project_id } }" :active-class="'bg-gray-700'" class="text-md my-2 mx-6 p-3 text-center block hover:bg-gray-700 rounded-sm">
                            <i class="fas fa-edit"></i>
                            <div class="text-xs mt-2">Content</div>
                        </router-link>
                        <router-link v-if="isProjectPage && $route.params.col_id != undefined" :to="{name: 'projects.content.list', params: { project_id: $route.params.project_id, col_id: $route.params.col_id } }" :active-class="'bg-gray-700'"  class="text-md my-2 mx-6 p-3 text-center block hover:bg-gray-700 rounded-sm">
                            <i class="fas fa-edit"></i>
                            <div class="text-xs mt-2">Content</div>
                        </router-link>
                        <router-link v-if="isProjectPage && checkRole(['super_admin'])" :to="{name: 'projects.settings.webhooks', params: { project_id: $route.params.project_id }}" :active-class="'bg-gray-700'"  class="text-md my-2 mx-6 p-3 text-center block hover:bg-gray-700 rounded-sm">
                            <i class="fas fa-network-wired"></i>
                            <div class="text-xs mt-2">Webhooks</div>
                        </router-link>
                        <router-link v-if="isProjectPage && checkRole(['super_admin'])" :to="{name: 'projects.settings', params: { project_id: $route.params.project_id }}" :exact-active-class="'bg-gray-700'"  class="text-md my-2 mx-6 p-3 text-center block hover:bg-gray-700 rounded-sm">
                            <i class="fas fa-cog"></i>
                            <div class="text-xs mt-2">Settings</div>
                        </router-link>
                    </nav>
                </div>

                <div class="bottom-0">
                    <router-link :to="{ name: 'profile' }" :active-class="'bg-gray-700'" class="text-md my-2 mx-6 p-3 text-center block hover:bg-gray-700 rounded-sm cursor-pointer">
                        <i class="fas fa-user"></i>
                        <div class="text-xs mt-2">My Profile</div>
                    </router-link>
                    <div @click="logout()" class="text-md my-2 mx-6 p-3 text-center block hover:bg-gray-700 rounded-sm cursor-pointer">
                        <i class="fas fa-sign-out-alt"></i>
                        <div class="text-xs mt-2">Logout</div>
                    </div>
                </div>
            </div>
        </div>

        <div class="flex-1 flex flex-col overflow-hidden">
             <header class="flex items-center py-1 px-3 bg-white border-b border-gray-200 lg:hidden">
                <div class="m-r-2 flex items-center">
                    <button @click="sidebarOpen = !sidebarOpen" class="text-gray-500 focus:outline-none lg:hidden">
                        <svg class="h-6 w-6" viewBox="0 0 24 24" fill="none" xmlns="http://www.w3.org/2000/svg">
                            <path d="M4 6H20M4 12H20M4 18H11" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"></path>
                        </svg>
                    </button>
                </div>
            </header>

            <div class="flex-1 overflow-x-hidden">
                <router-view></router-view>
            </div>
        </div>
    </div>
</template>

<script>
import Brand from './Brand.vue'
import UiDropdown from '../UI/Dropdown.vue'
import checkRole from '../checkrole'
import store from '../store'

export default {
    components: {
       Brand,
       UiDropdown
    },
    data() {
        return {
            sidebarOpen: false,
        }
    },

    methods: {
        checkRole,

        logout() {
            store.commit('LOGOUT');

            axios.post('/logout').then(response => {
               location.reload();
            }).catch(error => {
               location.reload();
            });
        },
    },

    computed: {
        isProjectPage() {
            var path = this.$route.fullPath.split('/')[1];
            if(path == 'projects'){
                return true;
            }
            return false;
        }
    }
}
</script>
