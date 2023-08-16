<template>
    <div>
        <div class="w-full xl:w-1/2 lg:w-3/4 m-auto mt-5">
            <div class="p-4 flex border-b">
                <div class="w-full">
                    <div class="text-xl font-bold">
                        My Profile
                    </div>
                </div>
            </div>

            <div class="bg-white mt-2 rounded-md p-4 w-full">
                <form class="space-y-6">
                    <div>
                        <label v-formlabel>Name</label>
                        <div class="mt-1 relative">
                            <input type="text" v-model="user.name" autofocus v-forminput>
                            <p class="text-sm text-red-600 mt-1" v-if="user.errors.name">{{ user.errors.name[0] }}</p>
                        </div>
                    </div>
                    <div>
                        <label v-formlabel></label>
                        <div class="mt-1 relative">
                            <ui-button :color="'indigo-500'" @click.native="saveName()">Update Name</ui-button>
                        </div>
                    </div>
                </form>
            </div>

            <div class="bg-white mt-2 rounded-md p-4 w-full">
                <form class="space-y-6">
                    <div>
                        <label v-formlabel>E-mail</label>
                        <div class="mt-1 relative">
                            <input type="text" v-model="user.email" autofocus v-forminput>
                            <p class="text-sm text-red-600 mt-1" v-if="user.errors.email">{{ user.errors.email[0] }}</p>
                        </div>
                    </div>
                    <div>
                        <label v-formlabel></label>
                        <div class="mt-1 relative">
                            <ui-button :color="'indigo-500'" @click.native="saveEmail()">Update E-mail</ui-button>
                        </div>
                    </div>
                </form>
            </div>

            <div class="bg-white mt-2 rounded-md p-4">
                <div>
                    <label v-formlabel>Password</label>
                    <div class="mt-1 relative">
                        <input type="password" v-model="user.password" autofocus v-forminput>
                        <p class="text-sm text-red-600 mt-1" v-if="user.errors.password">{{ user.errors.password[0] }}</p>
                    </div>
                </div>
                <div class="mt-2">
                    <label v-formlabel>Confirm Password</label>
                    <div class="mt-1 relative">
                        <input type="password" v-model="user.password_confirmation" autofocus v-forminput>
                    </div>
                </div>
                <div>
                    <label v-formlabel></label>
                    <div class="mt-1 relative">
                        <ui-button :color="'indigo-500'" @click.native="savePassword()">Update Password</ui-button>
                    </div>
                </div>
            </div>

        </div>
    </div>
</template>

<script>
import UiButton from '../UI/Button.vue'
import store from '../store'

export default {
    components: {
        UiButton,
    },

    data(){
        return {
            user: {
                name: store.state.user.name,
                email: store.state.user.email,
                errors: {}
            },
        }
    },

    methods: {
        saveName(){
            axios.post('/admin/user/update_name', this.user)
                .then((response) => {
                    this.$toast.success('Saved.');
                    this.user.errors = {};
                }, (error) => {
                   if(error.response.status == 422){
                        this.user.errors = error.response.data.errors;
                    }
                });
        },

        saveEmail(){
            if(this.user.email !== store.state.user.email){
                this.$swal.fire({
                    title: 'Are you sure',
                    text: "you want to change your e-mail address",
                }).then((result) => {
                    if (result.value) {
                        axios.post('/admin/user/update_email', this.user)
                            .then((response) => {
                                this.$toast.success('Saved.');
                                this.user.errors = {};
                            }, (error) => {
                            if(error.response.status == 422){
                                    this.user.errors = error.response.data.errors;
                                }
                            });
                    }
                });
            } else {
                axios.post('/admin/user/update_email', this.user)
                    .then((response) => {
                        this.$toast.success('Saved.');
                        this.user.errors = {};
                    }, (error) => {
                    if(error.response.status == 422){
                            this.user.errors = error.response.data.errors;
                        }
                    });
            }
        },

        savePassword(){
            axios.post('/admin/user/update_password', this.user)
                .then((response) => {
                    this.$toast.success('Saved.');
                    this.user.errors = {};
                    this.user.password = '';
                    this.user.password_confirmation = '';
                }, (error) => {
                   if(error.response.status == 422){
                        this.user.errors = error.response.data.errors;
                    }
                });
        },
    },
};
</script>
