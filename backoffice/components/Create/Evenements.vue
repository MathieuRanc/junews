<template>
	<div>
		<ModifyEvenements
			v-if="creation"
			:evenement="entity"
			:close="() => (creation = false)"
		/>
		<button class="create" @click="creation = true">
			<img src="../../assets/add.svg" alt="" /> Créer un évènement
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
				description: '',
				dateDebut: '',
				dateFin: '',
				materiels: [],
				promos: [],
				lieu: [],
				etudiants: [],
				association: '',
			},
		}
	},
}
</script>