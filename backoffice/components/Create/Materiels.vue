<template>
	<div>
		<ModifyMateriels
			v-if="creation"
			:materiel="entity"
			:close="() => (creation = false)"
		/>
		<button class="create" @click="creation = true">
			<img src="../../assets/add.svg" alt="" /> Créer un materiel
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
				evenements: [],
			},
		}
	},
}
</script>