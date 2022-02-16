<template>
	<div>
		<ModifyAssociations
			v-if="creation"
			:association="entity"
			:close="() => (creation = false)"
		/>
		<button class="create" @click="creation = true">
			<img src="../../assets/add.svg" alt="" /> Créer une
			association
		</button>
	</div>
</template>

<script>
export default {
	data() {
		return {
			creation: false,
			entity: { nom: '', description: '', evenements: [], etudiants: [] },
		}
	},
}
</script>