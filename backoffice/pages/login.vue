<template>
	<div class="container">
		<form @submit.prevent="userLogin">
			<div>
				<h1>Connection au backoffice</h1>
			</div>
			<div>
				<!-- <label for="username">Username</label> -->
				<input
					type="text"
					id="username"
					name="username"
					placeholder="Nom d'utilisateur"
					v-model="login.username"
				/>
			</div>
			<div>
				<!-- <label for="password">Password</label> -->
				<input
					type="password"
					id="password"
					name="password"
					placeholder="Mot de passe"
					v-model="login.password"
				/>
			</div>
			<div>
				<button type="submit">Submit</button>
			</div>
			<div v-if="error" class="error">Il y a eu une erreur</div>
		</form>
	</div>
</template>

<script>
export default {
	data() {
		return {
			login: {
				username: null,
				password: null,
			},
			config: {
				headers: {
					Accept: 'application/json',
					'Content-Type': 'application/json',
				},
			},
			error: null,
		}
	},
	methods: {
		async userLogin() {
			try {
				let response = await this.$axios.$post(
					'https://junews.mathieuranc.fr/login_check',
					this.login,
					this.config
				)
				this.$auth.strategy.token.set(response.token)
				this.$axios.setHeader(
					'Authorization',
					this.$auth.strategy.token.get()
				)
				this.$router.push('/')
			} catch (err) {
				this.error = true
			}
		},
	},
}
</script>

<style lang="scss" scoped>
.container {
	min-height: 100vh;
}
button {
	background-color: white;
	transition: 200ms;
	box-shadow: -4px 4px 10px rgba(0, 0, 0, 0.2);
	&:hover {
		box-shadow: -10px 10px 20px rgba(0, 0, 0, 0.2);
		transform: scale(1.02);
	}
}
</style>

<style lang="scss">
.container {
	width: 100vw;
	display: flex;
}
form {
	box-shadow: -4px 4px 10px rgba(0, 0, 0, 0.2);
	border-radius: 4px;
	padding: 40px 20px;
	background-color: #f1f3f6;
	margin: auto;
	display: flex;
	flex-direction: column;
	div {
		display: flex;
		flex-direction: column;
		&:not(:last-child) {
			margin-bottom: 40px;
		}
	}
}
input {
	outline: none;
	border: none;
	background-color: transparent;
	padding-bottom: 8px;
	border-bottom: solid 1px rgb(128, 128, 128);
}
.error {
	color: red;
}
</style>