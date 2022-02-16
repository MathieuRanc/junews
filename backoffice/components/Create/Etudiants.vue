<template>
	<div>
		<ModifyEtudiants
			v-if="creation"
			:etudiant="entity"
			:close="() => (creation = false)"
		/>
		<button class="create" @click="creation = true">
			<img src="../../assets/add.svg" alt="" /> Créer un étudiant
		</button>
	</div>
</template>

<script>
export default {
	data() {
		return {
			creation: false,
			entity: {
				nom: '',
				prenom: '',
				email: '',
				password: '',
				evenements: [],
				associations: [],
				cours: [],
				promotion: '',
			},
		}
	},
}
</script>