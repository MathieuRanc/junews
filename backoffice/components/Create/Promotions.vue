<template>
	<div>
		<ModifyPromotion
			v-if="creation"
			:promotion="entity"
			:close="() => (creation = false)"
		/>
		<button class="create" @click="creation = true">
			<img src="../../assets/add.svg" alt="" /> Créer une promotion
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
				ecole: '',
				evenements: [],
				etudiants: [],
			},
		}
	},
}
</script>