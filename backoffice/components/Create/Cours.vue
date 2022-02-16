<template>
	<div>
		<ModifyCours
			v-if="creation"
			:cour="entity"
			:close="() => (creation = false)"
		/>
		<button class="create" @click="creation = true">
			<img src="../../assets/add.svg" alt="" /> Créer un cours
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
				lieu: '',
				evenements: [],
				etudiants: [],
			},
		}
	},
}
</script>