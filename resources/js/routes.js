require('./bootstrap');

import Vue from 'vue'
import VueRouter from 'vue-router'
import store from './store';

Vue.use(VueRouter)

const Home = () => import(/* webpackChunkName: "home" */'./pages/Home')
const Profile = () => import(/* webpackChunkName: "profile" */'./pages/Profile')
const Project = () => import(/* webpackChunkName: "project" */'./pages/Project/Project')
const ProjectSettings = () => import(/* webpackChunkName: "project.settings" */'./pages/Project/Settings')
const ProjectSettingsLocales = () => import(/* webpackChunkName: "project.settings.locales" */'./pages/Project/SettingsLocales')
const ProjectSettingsUsers = () => import(/* webpackChunkName: "project.settings.users" */'./pages/Project/SettingsUsers')
const ProjectSettingsAPI = () => import(/* webpackChunkName: "project.settings.api" */'./pages/Project/SettingsAPI')
const Webhooks = () => import(/* webpackChunkName: "project.settings.webhooks" */'./pages/Project/Webhooks')
const WebhookLogs = () => import(/* webpackChunkName: "project.settings.webhooks.logs" */'./pages/Project/WebhookLogs')

const Collection = () => import(/* webpackChunkName: "project.collection" */'./pages/Collection/Collection')
const CollectionShow = () => import(/* webpackChunkName: "project.collection.show" */'./pages/Collection/CollectionShow')
const Content = () => import(/* webpackChunkName: "project.content" */'./pages/Content/Content')
const ContentList = () => import(/* webpackChunkName: "project.content.list" */'./pages/Content/ContentList')
const ContentNew = () => import(/* webpackChunkName: "project.content.new" */'./pages/Content/ContentNew')
const ContentEdit = () => import(/* webpackChunkName: "project.content.edit" */'./pages/Content/ContentEdit')
const ContentForms = () => import(/* webpackChunkName: "project.content.forms" */'./pages/Content/ContentForms')
const ContentFormsShow = () => import(/* webpackChunkName: "project.content.forms.show" */'./pages/Content/ContentFormsShow')
const Media = () => import(/* webpackChunkName: "project.media" */'./pages/Content/Media')

const routes = [
	{ path: '/', name: 'home', component: Home },
	{ path: '/profile', name: 'profile', component: Profile },
	{ path: '/projects/:project_id', name: 'projects', component: Project,
		beforeEnter: (to ,from, next) => {
			const roles = store.getters && store.getters.user.roles;

			if(roles.includes('super_admin'))
				return next();

			if(!roles.includes('admin'+to.params.project_id) && !roles.includes('editor'+to.params.project_id))
				return next('/');

			return next();
		}
	},
	{ path: '/projects/:project_id/settings', name: 'projects.settings', component: ProjectSettings,
		beforeEnter: (to ,from, next) => {
			const roles = store.getters && store.getters.user.roles;

			if(roles.includes('super_admin'))
				return next();

			if(!roles.includes('admin'+to.params.project_id))
				return next('/');

			return next();
		}
	},
	{ path: '/projects/:project_id/settings/locales', name: 'projects.settings.locales', component: ProjectSettingsLocales,
		beforeEnter: (to ,from, next) => {
			const roles = store.getters && store.getters.user.roles;

			if(roles.includes('super_admin'))
				return next();

			if(!roles.includes('admin'+to.params.project_id))
				return next('/');

			return next();
		}
	},
	{ path: '/projects/:project_id/settings/users', name: 'projects.settings.users', component: ProjectSettingsUsers,
		beforeEnter: (to ,from, next) => {
			const roles = store.getters && store.getters.user.roles;

			if(!roles.includes('super_admin'))
				return next('/');

			return next();
		}
	},
	{ path: '/projects/:project_id/settings/api', name: 'projects.settings.api', component: ProjectSettingsAPI,
		beforeEnter: (to ,from, next) => {
			const roles = store.getters && store.getters.user.roles;

			if(!roles.includes('super_admin'))
				return next('/');

			return next();
		}
	},
    { path: '/projects/:project_id/settings/webhooks', name: 'projects.settings.webhooks', component: Webhooks,
		beforeEnter: (to ,from, next) => {
			const roles = store.getters && store.getters.user.roles;

			if(!roles.includes('super_admin'))
				return next('/');

			return next();
		}
	},
	{ path: '/projects/:project_id/settings/webhooks/:webhook_id/logs', name: 'projects.settings.webhooks.logs', component: WebhookLogs,
		beforeEnter: (to ,from, next) => {
			const roles = store.getters && store.getters.user.roles;

			if(!roles.includes('super_admin'))
				return next('/');

			return next();
		}
	},
	{ path: '/projects/:project_id/collections', name: 'projects.collections', component: Collection,
		beforeEnter: (to ,from, next) => {
			const roles = store.getters && store.getters.user.roles;

			if(roles.includes('super_admin'))
				return next();

			if(!roles.includes('admin'+to.params.project_id))
				return next('/');

			return next();
		}
	},
	{ path: '/projects/:project_id/collections/:col_id', name: 'projects.collections.show', component: CollectionShow,
		beforeEnter: (to ,from, next) => {
			const roles = store.getters && store.getters.user.roles;

			if(roles.includes('super_admin'))
				return next();

			if(!roles.includes('admin'+to.params.project_id))
				return next('/');

			return next();
		}
	},
	{ path: '/projects/:project_id/content', name: 'projects.content', component: Content,
		beforeEnter: (to ,from, next) => {
			const roles = store.getters && store.getters.user.roles;

			if(roles.includes('super_admin'))
				return next();

			if(!roles.includes('admin'+to.params.project_id) && !roles.includes('editor'+to.params.project_id))
				return next('/');

			return next();
		}
	},
	{ path: '/projects/:project_id/content/:col_id', name: 'projects.content.list', component: ContentList,
		beforeEnter: (to ,from, next) => {
			const roles = store.getters && store.getters.user.roles;

			if(roles.includes('super_admin'))
				return next();

			if(!roles.includes('admin'+to.params.project_id) && !roles.includes('editor'+to.params.project_id))
				return next('/');

			return next();
		}
	},
	{ path: '/projects/:project_id/content/:col_id/new', name: 'projects.content.new', component: ContentNew,
		beforeEnter: (to ,from, next) => {
			const roles = store.getters && store.getters.user.roles;

			if(roles.includes('super_admin'))
				return next();

			if(!roles.includes('admin'+to.params.project_id) && !roles.includes('editor'+to.params.project_id))
				return next('/');

			return next();
		}
	},
	{ path: '/projects/:project_id/content/:col_id/edit/:content_id', name: 'projects.content.edit', component: ContentEdit,
		beforeEnter: (to ,from, next) => {
			const roles = store.getters && store.getters.user.roles;

			if(roles.includes('super_admin'))
				return next();

			if(!roles.includes('admin'+to.params.project_id) && !roles.includes('editor'+to.params.project_id))
				return next('/');

			return next();
		}
	},
    { path: '/projects/:project_id/content/:col_id/forms', name: 'projects.content.forms', component: ContentForms,
		beforeEnter: (to ,from, next) => {
			const roles = store.getters && store.getters.user.roles;

			if(roles.includes('super_admin'))
				return next();

			if(!roles.includes('admin'+to.params.project_id) && !roles.includes('editor'+to.params.project_id))
				return next('/');

			return next();
		}
	},
	{ path: '/projects/:project_id/content/:col_id/forms/:form_id', name: 'projects.content.forms.show', component: ContentFormsShow,
		beforeEnter: (to ,from, next) => {
			const roles = store.getters && store.getters.user.roles;

			if(roles.includes('super_admin'))
				return next();

			if(!roles.includes('admin'+to.params.project_id) && !roles.includes('editor'+to.params.project_id))
				return next('/');

			return next();
		}
	},
	{ path: '/projects/:project_id/media_library', name: 'projects.media_library', component: Media,
		beforeEnter: (to ,from, next) => {
			const roles = store.getters && store.getters.user.roles;

			if(roles.includes('super_admin'))
				return next();

			if(!roles.includes('admin'+to.params.project_id) && !roles.includes('editor'+to.params.project_id))
				return next('/');

			return next();
		}
	},
];
const router = new VueRouter({
	routes: routes,
	linkExactActiveClass: 'bg-gray-100',
});

router.beforeEach(async(to, from, next) => {
	const hasRoles = store.getters.user.roles && store.getters.user.roles.length > 0;

	if (hasRoles) {
        next();
    } else {
		await store.dispatch('getUser');

		next();
	}

});
export default router
