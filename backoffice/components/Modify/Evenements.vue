<template>
	<div class="popup" v-if="evenementCopy">
		<div class="box">
			<h2>Évènement</h2>
			<button @click="close" class="close">×</button>
			<label for="image">Image</label>
			<img :src="image" alt="image évènement" />
			<input
				@change="onImageChange"
				type="file"
				accept="image/jpeg"
				name="image"
				id="image"
				ref="image"
			/>

			<label for="nom">Nom</label>
			<input type="text" nom="nom" id="nom" v-model="evenementCopy.nom" />

			<label for="description">Description</label>
			<textarea
				nom="description"
				id="description"
				cols="30"
				rows="10"
				v-model="evenementCopy.description"
			/>

			<label for="dateDebut">Date de début</label>
			<input
				type="datetime-local"
				nom="dateDebut"
				id="dateDebut"
				v-model="evenementCopy.dateDebut"
			/>

			<label for="dateFin">Date de début</label>
			<input
				type="datetime-local"
				nom="dateFin"
				id="dateFin"
				v-model="evenementCopy.dateFin"
			/>

			<label for="materiels">Materiels</label>
			<ul class="tags">
				<li
					v-for="(materiel, i) in evenementCopy.materiels"
					:key="i"
					@click="evenementCopy.materiels.splice(i, 1)"
				>
					{{ nomMateriel(materiel) }} ×
				</li>
			</ul>
			<select
				nom="materiels"
				id="materiels"
				ref="materiels"
				@input="
					evenementCopy.materiels.push($refs.materiels.value)
					$refs.materiels.value = null
				"
			>
				<optgroup label="Ajouter un materiel">
					<option value="null" hidden>
						Sélectionnez un materiel
					</option>
					<option
						v-for="materiel in materielsDispo"
						:key="materiel.id"
						:value="materiel['@id']"
					>
						{{ materiel.nom }}
					</option>
				</optgroup>
			</select>

			<label for="promotions">Promotions</label>
			<ul class="tags">
				<li
					v-for="(promotion, i) in evenementCopy.promos"
					:key="i"
					@click="evenementCopy.promos.splice(i, 1)"
				>
					{{ nomPromotion(promotion) }} ×
				</li>
			</ul>
			<select
				nom="promotions"
				id="promotions"
				ref="promotions"
				@input="
					evenementCopy.promos.push($refs.promotions.value)
					$refs.promotions.value = null
				"
			>
				<optgroup label="Ajouter un promotion">
					<option value="null" hidden>
						Sélectionnez une promotion
					</option>
					<option
						v-for="promotion in promotionsDispo"
						:key="promotion.id"
						:value="promotion['@id']"
					>
						{{ promotion.nom }}
					</option>
				</optgroup>
			</select>

			<label for="association">associations</label>
			<select
				nom="association"
				id="association"
				v-model="evenementCopy.association"
			>
				<optgroup label="Sélectionnez un association">
					<option value="" hidden>
						Sélectionnez une association
					</option>
					<option
						v-for="association in $store.state.associations"
						:key="association.id"
						:value="association['@id']"
					>
						{{ association.nom }}
					</option>
				</optgroup>
			</select>

			<label for="lieu">Lieu</label>
			<select nom="lieu" id="lieu" v-model="evenementCopy.lieu">
				<optgroup label="Sélectionnez un lieu">
					<option value="" hidden>Sélectionnez une lieu</option>
					<option
						v-for="lieu in $store.state.lieux"
						:key="lieu.id"
						:value="lieu['@id']"
					>
						{{ lieu.nom }}
					</option>
				</optgroup>
			</select>

			<label for="etudiants">Étudiants</label>
			<ul class="tags">
				<li
					v-for="(etudiant, i) in evenementCopy.etudiants"
					:key="i"
					@click="evenementCopy.etudiants.splice(i, 1)"
				>
					{{ nomEtudiant(etudiant) }} ×
				</li>
			</ul>
			<select
				nom="etudiants"
				id="etudiants"
				ref="etudiants"
				@input="
					evenementCopy.etudiants.push($refs.etudiants.value)
					$refs.etudiants.value = null
				"
			>
				<optgroup label="Ajouter un etudiant">
					<option value="null" hidden>
						Sélectionnez un etudiant
					</option>
					<option
						v-for="etudiant in etudiantsDispo"
						:key="etudiant.id"
						:value="etudiant['@id']"
					>
						{{ etudiant.nom + ' ' + etudiant.prenom }}
					</option>
				</optgroup>
			</select>
			<button @click="modifier" class="validate">Valider</button>
		</div>
	</div>
</template>

<script>
export default {
	props: {
		evenement: {
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
			evenementCopy: null,
			image: this.evenement.image
				? process.env.baseUrlApi +
				  this.$store.state.images.find(
						(image) => image.id === this.evenement.image
				  ).contentUrl
				: process.env.baseUrlApi + '/media/evenements/default.png',
		}
	},
	mounted() {
		this.evenementCopy = JSON.parse(JSON.stringify(this.evenement))
		this.evenementCopy.dateDebut = this.evenementCopy.dateDebut.substring(
			0,
			16
		)
		this.evenementCopy.dateFin = this.evenementCopy.dateFin.substring(0, 16)
	},
	computed: {
		materielsDispo() {
			return this.$store.state.materiels.filter(
				(materiel) =>
					!this.evenementCopy.materiels.includes(materiel['@id'])
			)
		},
		promotionsDispo() {
			return this.$store.state.promotions.filter(
				(promotion) =>
					!this.evenementCopy.promos.includes(promotion['@id'])
			)
		},
		etudiantsDispo() {
			return this.$store.state.etudiants.filter(
				(etudiant) =>
					!this.evenementCopy.promos.includes(etudiant['@id'])
			)
		},
	},
	methods: {
		onImageChange(event) {
			const file = event.target.files[0]
			this.image = URL.createObjectURL(file)
		},
		nomMateriel(id) {
			const materiel = this.$store.state.materiels.find(
				(materiel) => materiel['@id'] === id
			)
			if (materiel) return materiel.nom
			return '-'
		},
		nomPromotion(id) {
			const promotion = this.$store.state.promotions.find(
				(promotion) => promotion['@id'] === id
			)
			if (promotion) return promotion.nom
			return '-'
		},
		nomEtudiant(id) {
			const etudiant = this.$store.state.etudiants.find(
				(etudiant) => etudiant['@id'] === id
			)
			if (etudiant) return etudiant.nom + ' ' + etudiant.prenom
			return '-'
		},
		async modifier() {
			var evenements = JSON.parse(
				JSON.stringify(this.$store.state.evenements)
			)
			this.close()
			const file = this.$refs.image.files[0]
			if (file) {
				let formData = new FormData()
				formData.append('file', file)
				await this.$axios
					.$post(
						process.env.baseUrlApi + '/media_objects',
						formData,
						{
							headers: {
								Accept: 'application/ld+json',
								'Content-Type': 'multipart/form-data',
							},
						}
					)
					.then((res) => {
						var images = JSON.parse(
							JSON.stringify(this.$store.state.images)
						)
						images.push(res)
						this.$store.commit('setImages', images)
						this.evenementCopy.image = res.id
					})
			}
			if (this.evenement.id) {
				evenements = evenements.filter(
					(asso) => asso.id !== this.evenement.id
				)
				evenements.push(this.evenementCopy)
				this.$store.commit(
					'setEvenements',
					JSON.parse(JSON.stringify(evenements))
				)
				delete this.evenementCopy.id
				delete this.evenementCopy.username
				delete this.evenementCopy['@id']
				delete this.evenementCopy['@type']
				this.evenementCopy.administre = parseInt(
					this.evenementCopy.administre
				)
				await this.$axios.$put(
					process.env.baseUrlApi + this.evenement['@id'],
					this.evenementCopy,
					{
						headers: {
							Accept: 'application/json',
							'Content-Type': 'application/json',
						},
					}
				)
			} else {
				await this.$axios
					.$post(
						process.env.baseUrlApi + '/evenements',
						this.evenementCopy,
						{
							headers: {
								Accept: 'application/json',
							},
						}
					)
					.then((res) => {
						var resp = res
						resp['@id'] = '/evenements/' + res.id
						evenements.push(res)
						this.$store.commit(
							'setEvenements',
							JSON.parse(JSON.stringify(evenements))
						)
					})
			}
		},
	},
}
</script>