<template>
	<div class="navbar-menu">
		<div v-if="isAuthenticated" class="navbar-end">
			<span class="navbar-item">
				{{ loggedInUser.username }}
			</span>
			<a class="navbar-item" @click="logout">Déconnexion</a>
		</div>
		<div v-else class="navbar-end">
			<nuxt-link class="navbar-item" to="login">Connexion</nuxt-link>
			<nuxt-link class="navbar-item" to="register">Inscription</nuxt-link>
		</div>
	</div>
</template>

<script>
import { mapGetters } from 'vuex'

export default {
	computed: {
		...mapGetters(['isAuthenticated', 'loggedInUser']),
	},
	methods: {
		async logout() {
			await this.$auth
				.logout()
				.then(() => ({
					message: 'Vous êtes desormais déconnecté(e)',
					type: 'is-info',
				}))
		},
	},
}
</script>

<style lang="scss" scoped>
</style>