<template>
	<div class="popup" v-if="lieuCopy">
		<div class="box">
			<h2>Lieux</h2>
			<button @click="close" class="close">×</button>

			<label for="nom">Nom</label>
			<input type="text" nom="nom" id="nom" v-model="lieuCopy.nom" />

			<label for="type">Type</label>
			<input type="text" nom="type" id="type" v-model="lieuCopy.type" />

			<label for="adresse">Adresse</label>
			<input
				type="text"
				nom="adresse"
				id="adresse"
				v-model="lieuCopy.adresse"
			/>

			<label for="description">Description</label>
			<textarea
				nom="description"
				id="description"
				cols="30"
				rows="10"
				v-model="lieuCopy.description"
			/>

			<!-- <label for="evenements">Evènements</label>
			<ul class="tags">
				<li
					v-for="(evenement, i) in lieuCopy.evenements"
					:key="i"
					@click="lieuCopy.evenements.splice(i, 1)"
				>
					{{ nomEvenement(evenement) }} ×
				</li>
			</ul>
			<select
				nom="evenements"
				id="evenements"
				ref="evenements"
				@input="
					lieuCopy.evenements.push($refs.evenements.value)
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
			</select>

			<label for="promotion">Promotion</label>
			<select nom="promotion" id="promotion" v-model="lieuCopy.promotion">
				<optgroup label="Sélectionnez un promotion">
					<option value="" hidden>Sélectionnez une promotion</option>
					<option
						v-for="promotion in $store.state.promotions"
						:key="promotion.id"
						:value="promotion['@id']"
					>
						{{ promotion.nom }}
					</option>
				</optgroup>
			</select> -->

			<!-- <label for="lieux">lieux</label>
			<ul class="tags">
				<li
					v-for="(lieu, i) in lieuCopy.lieux"
					:key="lieu"
					@click="lieuCopy.lieux.splice(i, 1)"
				>
					{{ nomlieu(lieu) }} ×
				</li>
			</ul>
			<select
				nom="lieux"
				id="lieux"
				ref="lieux"
				@input="
					lieuCopy.lieux.push($refs.lieux.value)
					$refs.lieux.value = null
				"
			>
				<optgroup label="Ajouter un étudiant">
					<option value="null" hidden>
						Sélectionnez de étudiant
					</option>
					<option
						v-for="lieu in lieuxDispo"
						:key="lieu.id"
						:value="lieu['@id']"
					>
						{{ `${lieu.nom} ${lieu.username}` }}
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
		lieu: {
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
			lieuCopy: null,
		}
	},
	mounted() {
		this.lieuCopy = JSON.parse(JSON.stringify(this.lieu))
	},
	methods: {
		async modifier() {
			var lieux = JSON.parse(JSON.stringify(this.$store.state.lieux))
			this.close()
			if (this.lieu.id) {
				lieux = lieux.filter((asso) => asso.id !== this.lieu.id)
				lieux.push(this.lieuCopy)
				this.$store.commit(
					'setLieux',
					JSON.parse(JSON.stringify(lieux))
				)
				await this.$axios.$put(
					process.env.baseUrlApi + this.lieu['@id'],
					this.lieuCopy,
					{
						headers: {
							Accept: 'application/json',
						},
					}
				)
			} else {
				await this.$axios
					.$post(process.env.baseUrlApi + '/lieus', this.lieuCopy, {
						headers: {
							Accept: 'application/json',
						},
					})
					.then((res) => {
						var resp = res
						resp['@id'] = '/lieus/' + res.id
						lieux.push(res)
						this.$store.commit(
							'setLieux',
							JSON.parse(JSON.stringify(lieux))
						)
					})
			}
		},
	},
}
</script>