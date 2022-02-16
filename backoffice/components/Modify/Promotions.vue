<template>
	<div class="popup" v-if="promotionCopy">
		<div class="box">
			<h2>promotions</h2>
			<button @click="close" class="close">×</button>

			<label for="nom">Nom</label>
			<input type="text" nom="nom" id="nom" v-model="promotionCopy.nom" />

			<label for="ecole">Ecole</label>
			<input
				type="text"
				nom="ecole"
				id="ecole"
				v-model="promotionCopy.ecole"
			/>
			<button @click="modifier" class="validate">Valider</button>
		</div>
	</div>
</template>

<script>
export default {
	props: {
		promotion: {
			type: Object,
			required: true,
		},
		close: {
			type: Function,
			required: true,
		},
	},
	data() {
		return {
			promotionCopy: null,
		}
	},
	mounted() {
		this.promotionCopy = JSON.parse(JSON.stringify(this.promotion))
	},
	methods: {
		async modifier() {
			var promotions = JSON.parse(
				JSON.stringify(this.$store.state.promotions)
			)
			this.close()
			if (this.promotion.id) {
				promotions = promotions.filter(
					(asso) => asso.id !== this.promotion.id
				)
				promotions.push(this.promotionCopy)
				this.$store.commit(
					'setPromotions',
					JSON.parse(JSON.stringify(promotions))
				)
				await this.$axios.$put(
					process.env.baseUrlApi + this.promotion['@id'],
					this.promotionCopy,
					{
						headers: {
							Accept: 'application/json',
						},
					}
				)
			} else {
				await this.$axios
					.$post(
						process.env.baseUrlApi + '/promotions',
						this.promotionCopy,
						{
							headers: {
								Accept: 'application/json',
							},
						}
					)
					.then((res) => {
						var resp = res
						resp['@id'] = '/promotions/' + res.id
						promotions.push(res)
						this.$store.commit(
							'setPromotions',
							JSON.parse(JSON.stringify(promotions))
						)
					})
			}
		},
	},
}
</script>