<template>
    <div class="p-4 flex border-b">
        <div class="w-full">
            <div class="text-xl font-bold">
                {{ project.name }}
            </div>
            <div class="text-sm">{{ project.description }}</div>
        </div>
        <projectActions :project="project"></projectActions>
    </div>
</template>

<script>
import UiModal from '../../UI/Modal.vue'
import UiButton from '../../UI/Button.vue'
import ProjectActions from './ProjectActions'


export default {
    props: ['project'],
    components: {
        UiModal,
        UiButton,
        ProjectActions,

    }
}
</script>