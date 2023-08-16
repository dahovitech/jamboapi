<template>
    <div class="p-4 bg-white">
        <div class="mb-4">
            <h4 class="mb-2 p-2 font-bold text-lg flex justify-between items-center h-10" v-if="!openSearchInput">
                <div>
                    Content    
                </div> 
                <div>
                    <a class="text-sm text-blue-500 p-1 px-3 cursor-pointer rounded-md hover:bg-gray-100" @click="openSearchInput = true"><i class="fas fa-search"></i></a>
                </div>
            </h4>

            <div v-if="openSearchInput" class="mb-2 relative flex w-full flex-wrap items-stretch mb-2 h-10">
                <span class="z-9 h-full leading-snug font-normal absolute text-center text-gray-400 absolute bg-transparent rounded-sm text-base items-center justify-center w-8 pl-3 py-3">
                    <i class="fas fa-search"></i>
                </span>
                <input type="text" v-model="searchCollection" placeholder="Search..." class="px-3 py-3 placeholder-gray-400 text-gray-700 bg-white rounded-sm text-sm w-full pl-10 border-gray-200 focus:border-gray-300" autofocus>
                <span class="z-9 h-full leading-snug font-normal absolute text-center text-gray-400 absolute bg-transparent rounded-sm text-base items-center justify-center w-8 py-3 right-0 pr-3 cursor-pointer" @click="searchCollection = '', openSearchInput = false">
                    <i class="fas fa-times-circle"></i>
                </span>
            </div>
        </div>

        <ul>
            <li class="mb-2" v-for="collection in filterSearch" :key="collection.id">
                <router-link :to="{ name: 'projects.content.list', params: { project_id: project.id, col_id: collection.id } }" class="block w-full p-2 cursor-pointer hover:bg-gray-100 rounded">{{ collection.name }}</router-link>
            </li>
            <li class="mb-2"><hr></li>
            <li class="mb-2">
                <router-link v-if="typeof project.id !== 'undefined'" :to="{ name: 'projects.media_library', params: { id: project.id } }" class="block w-full p-2 cursor-pointer hover:bg-gray-100 rounded"><i class="fas fa-images text-gray-600 mr-3"></i> Media Library</router-link>
            </li>
        </ul>
    </div>
</template>

<script>
export default {

    data(){
        return {
            searchCollection: '',
            openSearchInput : false
        }
    },

    computed: {
        filterSearch() {
            if(this.project.collections !== undefined){
                return this.project.collections.filter(collection => {
                    return !this.searchCollection || collection.name.toLowerCase().indexOf(this.searchCollection.toLowerCase()) > -1
                })
            }
        }
    },

    props: ['project'],
}
</script>
