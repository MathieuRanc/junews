<template>
	<div class="popup" v-if="materielCopy">
		<div class="box">
			<h2>materiels</h2>
			<button @click="close" class="close">×</button>

			<label for="nom">Nom</label>
			<input type="text" nom="nom" id="nom" v-model="materielCopy.nom" />

			<label for="description">Description</label>
			<textarea
				nom="description"
				id="description"
				cols="30"
				rows="10"
				v-model="materielCopy.description"
			/>

			<!-- <label for="evenements">Evènements</label>
			<ul class="tags">
				<li
					v-for="(evenement, i) in materielCopy.evenements"
					:key="i"
					@click="materielCopy.evenements.splice(i, 1)"
				>
					{{ nomEvenement(evenement) }} ×
				</li>
			</ul>
			<select
				nom="evenements"
				id="evenements"
				ref="evenements"
				@input="
					materielCopy.evenements.push($refs.evenements.value)
					$refs.evenements.value = null
				"
			>
				<optgroup label="Ajouter un évènements">
					<option value="null" hidden>
						Sélectionnez un évènement
					</option>
					<option
						v-for="evenement in evenementsDispo"
						:key="evenement.id"
						:value="evenement['@id']"
					>
						{{ evenement.nom }}
					</option>
				</optgroup>
			</select> -->
			<button @click="modifier" class="validate">Valider</button>
		</div>
	</div>
</template>

<script>
export default {
	props: {
		materiel: {
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
			materielCopy: null,
		}
	},
	mounted() {
		this.materielCopy = JSON.parse(JSON.stringify(this.materiel))
	},
	methods: {
		async modifier() {
			var materiels = JSON.parse(
				JSON.stringify(this.$store.state.materiels)
			)
			this.close()
			if (this.materiel.id) {
				materiels = materiels.filter(
					(asso) => asso.id !== this.materiel.id
				)
				materiels.push(this.materielCopy)
				this.$store.commit(
					'setMateriels',
					JSON.parse(JSON.stringify(materiels))
				)
				await this.$axios.$put(
					process.env.baseUrlApi + this.materiel['@id'],
					this.materielCopy,
					{
						headers: {
							Accept: 'application/json',
						},
					}
				)
			} else {
				await this.$axios
					.$post(
						process.env.baseUrlApi + '/materiels',
						this.materielCopy,
						{
							headers: {
								Accept: 'application/json',
							},
						}
					)
					.then((res) => {
						var resp = res
						resp['@id'] = '/materiels/' + res.id
						materiels.push(res)
						this.$store.commit(
							'setMateriels',
							JSON.parse(JSON.stringify(materiels))
						)
					})
			}
		},
	},
}
</script>