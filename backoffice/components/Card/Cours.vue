<template>
	<table v-if="$store.state.cours.length > 0">
		<ModifyCours
			v-if="entity"
			:cour="entity"
			:close="() => (entity = null)"
		/>
		<thead>
			<tr>
				<!-- <th><input type="checkbox" name="all" id="all" /></th> -->
				<th>ID</th>
				<th>Nom</th>
				<th>Description</th>
				<th>Type</th>
				<th>Date début</th>
				<th>Date fin</th>
				<th>Lieu</th>
				<th>Etudiants</th>
				<th>Actions</th>
			</tr>
		</thead>
		<tbody>
			<tr v-for="cour in $store.state.cours" :key="cour.id">
				<!-- <td>
					<input type="checkbox" :name="cour.id" :id="cour.id" />
				</td> -->
				<td v-if="cour.id">{{ cour.id }}</td>
				<td v-else>-</td>
				<td v-if="cour.nom">
					{{ cour.nom }}
				</td>
				<td v-else>-</td>
				<td v-if="cour.description">
					{{ cour.description }}
				</td>
				<td v-else>-</td>
				<td v-if="cour.examen">examen</td>
				<td v-else>cours</td>
				<td v-if="cour.dateDebut">
					{{ dateToString(cour.dateDebut) }}
				</td>
				<td v-else>-</td>
				<td v-if="cour.dateDebut">{{ dateToString(cour.dateFin) }}</td>
				<td v-else>-</td>
				<td v-if="cour.lieu">
					{{ nomLieu(cour.lieu) }}
				</td>
				<td v-else>-</td>
				<td v-if="cour.etudiants">
					{{ cour.etudiants.length }}
				</td>
				<td v-else>0</td>
				<td>
					<button @click="entity = cour" class="modify">
						Modifier
					</button>
					<button @click="supprimer(cour)" class="delete">
						Supprimer
					</button>
				</td>
			</tr>
		</tbody>
	</table>
</template>

<script>
export default {
	data() {
		return {
			entity: null,
		}
	},
	methods: {
		nomLieu(id) {
			const lieu = this.$store.state.lieux.find(
				(lieu) => lieu['@id'] === id
			)
			if (lieu) return lieu.nom
			else return '-'
		},
		supprimer(cour) {
			this.$axios
				.$delete(process.env.baseUrlApi + cour['@id'])
				.then((res) => {
					this.$fetch()
				})
		},
		dateToString(date) {
			const unixTimeZero = new Date(date)
			return unixTimeZero.toLocaleDateString('fr-FR')
		},
		async supprimer(cour) {
			var cours = [...this.$store.state.cours]
			cours = cours.filter((el) => el.id !== cour.id)
			this.$store.commit('setCours', cours)
			await this.$axios.$delete(process.env.baseUrlApi + cour['@id'])
		},
	},
}
</script>

<style lang="scss" scoped>
</style>