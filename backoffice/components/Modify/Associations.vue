<template>
	<div class="popup" v-if="associationCopy">
		<div class="box">
			<h2>Association</h2>
			<button @click="close" class="close">×</button>

			<label for="logo">Logo</label>
			<img :src="image" alt="photo étudiant" />
			<input
				@change="onImageChange"
				accept="image/jpeg, image/png"
				type="file"
				name="logo"
				id="logo"
				ref="logo"
			/>

			<label for="nom">Nom</label>
			<input
				type="text"
				nom="nom"
				id="nom"
				v-model="associationCopy.nom"
			/>

			<label for="description">Description</label>
			<textarea
				nom="description"
				id="description"
				cols="30"
				rows="10"
				v-model="associationCopy.description"
			/>

			<!-- <label for="evenements">Evènements</label>
			<ul class="tags">
				<li
					v-for="(evenement, i) in associationCopy.evenements"
					:key="i"
					@click="associationCopy.evenements.splice(i, 1)"
				>
					{{ nomEvenement(evenement) }} ×
				</li>
			</ul>
			<select
				nom="evenements"
				id="evenements"
				ref="evenements"
				@input="
					associationCopy.evenements.push($refs.evenements.value)
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

			<label for="etudiants">Etudiants</label>
			<ul class="tags">
				<li
					v-for="(etudiant, i) in associationCopy.etudiants"
					:key="etudiant"
					@click="associationCopy.etudiants.splice(i, 1)"
				>
					{{ nomEtudiant(etudiant) }} ×
				</li>
			</ul>
			<select
				nom="etudiants"
				id="etudiants"
				ref="etudiants"
				@input="
					associationCopy.etudiants.push($refs.etudiants.value)
					$refs.etudiants.value = null
				"
			>
				<optgroup label="Ajouter un étudiant">
					<option value="null" hidden>
						Sélectionnez de étudiant
					</option>
					<option
						v-for="etudiant in etudiantsDispo"
						:key="etudiant.id"
						:value="etudiant['@id']"
					>
						{{ `${etudiant.nom} ${etudiant.prenom}` }}
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
		association: {
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
			associationCopy: null,
			image: this.association.logo
				? process.env.baseUrlApi +
				  this.$store.state.images.find(
						(image) => image.id === this.association.logo
				  ).contentUrl
				: process.env.baseUrlApi + '/media/associations/default.png',
		}
	},
	mounted() {
		this.associationCopy = JSON.parse(JSON.stringify(this.association))
	},
	computed: {
		etudiantsDispo() {
			return this.$store.state.etudiants.filter(
				(etudiant) =>
					!this.associationCopy.etudiants.includes(etudiant['@id'])
			)
		},
	},
	methods: {
		onImageChange(event) {
			const file = event.target.files[0]
			this.image = URL.createObjectURL(file)
		},
		nomEtudiant(id) {
			const etudiant = this.$store.state.etudiants.find(
				(etudiant) => etudiant['@id'] === id
			)
			if (etudiant) return `${etudiant.nom} ${etudiant.prenom}`
			return '-'
		},
		async modifier() {
			var associations = JSON.parse(
				JSON.stringify(this.$store.state.associations)
			)
			this.close()
			const file = this.$refs.logo.files[0]
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
						this.associationCopy.logo = res.id
					})
			}
			if (this.association.id) {
				associations = associations.filter(
					(asso) => asso.id !== this.association.id
				)
				associations.push(this.associationCopy)
				this.$store.commit(
					'setAssociations',
					JSON.parse(JSON.stringify(associations))
				)
				await this.$axios
					.$put(
						process.env.baseUrlApi + this.association['@id'],
						this.associationCopy,
						{
							headers: {
								Accept: 'application/json',
							},
						}
					)
			} else {
				await this.$axios
					.$post(
						process.env.baseUrlApi + '/associations',
						this.associationCopy,
						{
							headers: {
								Accept: 'application/json',
							},
						}
					)
					.then((res) => {
						var resp = res
						resp['@id'] = '/associations/' + res.id
						associations.push(res)
						this.$store.commit(
							'setAssociations',
							JSON.parse(JSON.stringify(associations))
						)
					})
			}
		},
	},
}
</script>