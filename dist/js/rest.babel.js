import Vue from 'vue';
import VueRouter from 'vue-router';
import Root from './Root.vue';
import Global from './pages/Global.vue';
import Hoops from './pages/Hoops.vue';
import Settings from './pages/Settings.vue';
import Theme from './pages/Theme.vue';
import Usage from './pages/Usage.vue';

Vue.use( VueRouter );

const routes = [
	{ path: '/global', component: Global },
	{ path: '/hoops', component: Hoops },
	{ path: '/settings', component: Settings },
	{ path: '/theme', component: Theme },
	{ path: '/usage', component: Usage }
];

const router = new VueRouter({
	routes,
	linkActiveClass: 'nav-tab-active'
});

/* eslint-disable no-new */
new Vue({
	router,
	el: '#scripts-n-styles',
	render() {
		return <Root/>;
	}
});

$.ajax({
	url: snsREST.api.root + 'sns/0.1/global',
	method: 'GET',
	beforeSend: function( xhr ) {
		xhr.setRequestHeader( 'X-WP-Nonce', snsREST.api.nonce );
	}
}).always( function( response, textStatus, jqXHR ) {
	console.log( 'response', response );
});
