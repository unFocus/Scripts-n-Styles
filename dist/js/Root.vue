<template>
	<div id="scripts-n-styles" class="wrap">
		<h2>Scripts n Styles</h2>
		<h3 class="nav-tab-wrapper">
			<router-link class="nav-tab" to="/global">Global</router-link>
			<router-link class="nav-tab" to="/hoops">Hoops</router-link>
			<router-link class="nav-tab" to="/theme">Theme</router-link>
			<router-link class="nav-tab" to="/settings">Settings</router-link>
			<router-link class="nav-tab" to="/usage">Usage</router-link>
		</h3>
		<router-view></router-view>
	</div>
</template>
