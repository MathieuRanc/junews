<template>
	<div>
		<ModifyLieux
			v-if="creation"
			:lieu="entity"
			:close="() => (creation = false)"
		/>
		<button class="create" @click="creation = true">
			<img src="../../assets/add.svg" alt="" /> Créer un lieu
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
				type: '',
				adresse: '',
				description: '',
				evenements: [],
				cours: [],
			},
		}
	},
}
</script>