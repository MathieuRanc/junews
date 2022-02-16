<template>
	<div class="popup" v-if="etudiantCopy">
		<div class="box">
			<h2>Etudiants</h2>
			<button @click="close" class="close">×</button>

			<label for="photo">Photo</label>
			<img :src="image" alt="photo étudiant" />
			<input
				@change="onImageChange"
				accept="image/jpeg, image/png"
				type="file"
				name="photo"
				id="photo"
				ref="photo"
			/>

			<label for="nom">Nom</label>
			<input type="text" nom="nom" id="nom" v-model="etudiantCopy.nom" />

			<label for="prenom">Prénom</label>
			<input
				type="text"
				nom="prenom"
				id="prenom"
				v-model="etudiantCopy.prenom"
			/>

			<label for="administre">Administre</label>
			<input
				min="1"
				type="number"
				nom="administre"
				id="administre"
				v-model.number="etudiantCopy.administre"
			/>

			<label for="email">Email - Username</label>
			<input
				type="text"
				nom="email"
				id="email"
				v-model="etudiantCopy.email"
			/>

			<label for="password">Mot de passe</label>
			<input
				type="password"
				nom="password"
				id="password"
				v-model="password"
			/>

			<label for="evenements">Evènements</label>
			<ul class="tags">
				<li
					v-for="(evenement, i) in etudiantCopy.evenements"
					:key="i"
					@click="etudiantCopy.evenements.splice(i, 1)"
				>
					{{ nomEvenement(evenement) }} ×
				</li>
			</ul>
			<select
				nom="evenements"
				id="evenements"
				ref="evenements"
				@input="
					etudiantCopy.evenements.push($refs.evenements.value)
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
			<select
				nom="promotion"
				id="promotion"
				v-model="etudiantCopy.promotion"
			>
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
			</select>
			<button @click="modifier" class="validate">Valider</button>
		</div>
	</div>
</template>

<script>
export default {
	props: {
		etudiant: {
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
			password: null,
			etudiantCopy: null,
			image: this.etudiant.photo
				? process.env.baseUrlApi +
				  this.$store.state.images.find(
						(image) => image.id === this.etudiant.photo
				  ).contentUrl
				: process.env.baseUrlApi + '/media/etudiants/default.png',
		}
	},
	mounted() {
		this.etudiantCopy = JSON.parse(JSON.stringify(this.etudiant))
	},
	computed: {
		evenementsDispo() {
			return this.$store.state.evenements.filter(
				(evenement) =>
					!this.etudiantCopy.evenements.includes(evenement['@id'])
			)
		},
	},
	methods: {
		onImageChange(event) {
			const file = event.target.files[0]
			this.image = URL.createObjectURL(file)
		},
		nomEvenement(id) {
			const evenement = this.$store.state.evenements.find(
				(evenement) => evenement['@id'] === id
			)
			if (evenement) return evenement.nom
			return '-'
		},
		async modifier() {
			var etudiants = JSON.parse(
				JSON.stringify(this.$store.state.etudiants)
			)
			this.close()
			const file = this.$refs.photo.files[0]
			let formData = new FormData()
			formData.append('file', file)
			if (file) {
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
						this.etudiantCopy.photo = res.id
					})
			}
			if (this.etudiant.id) {
				etudiants = etudiants.filter(
					(asso) => asso.id !== this.etudiant.id
				)
				etudiants.push(this.etudiantCopy)
				this.$store.commit(
					'setEtudiants',
					JSON.parse(JSON.stringify(etudiants))
				)
				delete this.etudiantCopy.id
				delete this.etudiantCopy.username
				delete this.etudiantCopy['@id']
				delete this.etudiantCopy['@type']
				this.etudiantCopy.administre = parseInt(
					this.etudiantCopy.administre
				)
				if (this.password !== null && this.password !== '') {
					await this.$axios
						.$post(
							process.env.baseUrlApi + '/encode-password',
							{ password: this.password },
							{
								headers: {
									Accept: 'application/json',
									'Content-Type': 'application/json',
								},
							}
						)
						.then(
							(res) => (this.etudiantCopy.password = res.password)
						)
				}
				await this.$axios.$put(
					process.env.baseUrlApi + this.etudiant['@id'],
					this.etudiantCopy,
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
						process.env.baseUrlApi + '/etudiants',
						this.etudiantCopy,
						{
							headers: {
								Accept: 'application/json',
							},
						}
					)
					.then((res) => {
						var resp = res
						resp['@id'] = '/etudiants/' + res.id
						etudiants.push(res)
						this.$store.commit(
							'setEtudiants',
							JSON.parse(JSON.stringify(etudiants))
						)
					})
			}
			this.password = null
			this.etudiantCopy = null
		},
	},
}
</script>