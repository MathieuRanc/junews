<template>
	<div class="popup" v-if="courCopy">
		<div class="box">
			<h2>Cours</h2>
			<button @click="close" class="close">×</button>

			<label for="nom">Nom</label>
			<input type="text" nom="nom" id="nom" v-model="courCopy.nom" />

			<label for="description">Description</label>
			<textarea
				nom="description"
				id="description"
				cols="30"
				rows="10"
				v-model="courCopy.description"
			/>

			<label for="examen">Est un examen</label>
			<input type="checkbox" name="examen" id="examen" v-model="courCopy.examen" />

			<label for="dateDebut">Date de début</label>
			<input
				type="datetime-local"
				nom="dateDebut"
				id="dateDebut"
				v-model="courCopy.dateDebut"
			/>

			<label for="dateFin">Date de début</label>
			<input
				type="datetime-local"
				nom="dateFin"
				id="dateFin"
				v-model="courCopy.dateFin"
			/>

			<label for="lieu">Lieu</label>
			<select nom="lieu" id="lieu" v-model="courCopy.lieu">
				<optgroup label="Sélectionnez un lieu">
					<option value="" hidden>Sélectionnez de étudiant</option>
					<option
						v-for="lieu in $store.state.lieux"
						:key="lieu.id"
						:value="lieu['@id']"
					>
						{{ lieu.nom }}
					</option>
				</optgroup>
			</select>

			<!-- <label for="evenements">Evènements</label>
			<ul class="tags">
				<li
					v-for="(evenement, i) in courCopy.evenements"
					:key="i"
					@click="courCopy.evenements.splice(i, 1)"
				>
					{{ nomEvenement(evenement) }} ×
				</li>
			</ul>
			<select
				nom="evenements"
				id="evenements"
				ref="evenements"
				@input="
					courCopy.evenements.push($refs.evenements.value)
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
					v-for="(etudiant, i) in courCopy.etudiants"
					:key="etudiant"
					@click="courCopy.etudiants.splice(i, 1)"
				>
					{{ nomEtudiant(etudiant) }} ×
				</li>
			</ul>
			<select
				nom="etudiants"
				id="etudiants"
				ref="etudiants"
				@input="
					courCopy.etudiants.push($refs.etudiants.value)
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
		cour: {
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
			courCopy: null,
		}
	},
	mounted() {
		this.courCopy = JSON.parse(JSON.stringify(this.cour))
		this.courCopy.dateDebut = this.courCopy.dateDebut.substring(
			0,
			16
		)
		this.courCopy.dateFin = this.courCopy.dateFin.substring(0, 16)
	},
	computed: {
		// evenementsDispo() {
		// 	return this.$store.state.evenements.filter(
		// 		(evenement) =>
		// 			!this.courCopy.evenements.includes(evenement['@id'])
		// 	)
		// },
		etudiantsDispo() {
			return this.$store.state.etudiants.filter(
				(etudiant) => !this.courCopy.etudiants.includes(etudiant['@id'])
			)
		},
	},
	methods: {
		// nomEvenement(id) {
		// 	const evenement = this.$store.state.evenements.find(
		// 		(evenement) => evenement['@id'] === id
		// 	)
		// 	if (evenement) return evenement.nom
		// 	return '-'
		// },
		nomEtudiant(id) {
			const etudiant = this.$store.state.etudiants.find(
				(etudiant) => etudiant['@id'] === id
			)
			if (etudiant) return `${etudiant.nom} ${etudiant.prenom}`
			return '-'
		},
		async modifier() {
			var cours = JSON.parse(JSON.stringify(this.$store.state.cours))
			this.close()
			if (this.cour.id) {
				cours = cours.filter((asso) => asso.id !== this.cour.id)
				cours.push(this.courCopy)
				this.$store.commit(
					'setCours',
					JSON.parse(JSON.stringify(cours))
				)
				await this.$axios
					.$put(
						process.env.baseUrlApi + this.cour['@id'],
						this.courCopy,
						{
							headers: {
								Accept: 'application/json',
							},
						}
					)
			} else {
				await this.$axios
					.$post(process.env.baseUrlApi + '/cours', this.courCopy, {
						headers: {
							Accept: 'application/json',
						},
					})
					.then((res) => {
						var resp = res
						resp['@id'] = '/cours/' + res.id
						cours.push(res)
						this.$store.commit(
							'setCours',
							JSON.parse(JSON.stringify(cours))
						)
					})
			}
		},
	},
}
</script>