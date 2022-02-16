<template>
	<table v-if="$store.state.evenements.length > 0">
		<ModifyEvenements
			v-if="entity"
			:evenement="entity"
			:close="() => (entity = null)"
		/>
		<thead>
			<tr>
				<!-- <th><input type="checkbox" name="all" id="all" /></th> -->
				<th>ID</th>
				<th>Image</th>
				<th>Nom</th>
				<th>Description</th>
				<th>Date de début</th>
				<th>Date de fin</th>
				<th>Materiels</th>
				<th>Promos</th>
				<th>Associations</th>
				<th>Lieu</th>
				<th>Etudiants</th>
				<th>Actions</th>
			</tr>
		</thead>
		<tbody>
			<tr
				v-for="evenement in $store.state.evenements"
				:key="evenement.id"
			>
				<!-- <td>
					<input
						type="checkbox"
						:name="evenement.id"
						:id="evenement.id"
					/>
				</td> -->
				<td v-if="evenement.id">{{ evenement.id }}</td>
				<td v-else>-</td>
				<td v-if="evenement.image">
					<img
						v-if="$store.state.images[evenement.image - 1]"
						:src="
							baseUrlApi +
							$store.state.images[evenement.image - 1].contentUrl
						"
						alt=""
					/>
				</td>
				<td v-else>
					<img
						:src="baseUrlApi + '/media/evenements/default.png'"
						alt=""
					/>
				</td>
				<td v-if="evenement.nom">
					{{ evenement.nom }}
				</td>
				<td v-else>-</td>
				<td v-if="evenement.description">
					{{ evenement.description }}
				</td>
				<td v-else>-</td>
				<td v-if="evenement.dateDebut">
					{{ dateToString(evenement.dateDebut) }}
				</td>
				<td v-else>-</td>
				<td v-if="evenement.dateFin">
					{{ dateToString(evenement.dateFin) }}
				</td>
				<td v-else>-</td>
				<td v-if="evenement.materiels">
					{{ evenement.materiels.length }}
				</td>
				<td v-else>0</td>
				<td v-if="evenement.promos">
					{{ evenement.promos.length }}
				</td>
				<td v-else>0</td>
				<td v-if="evenement.association">
					{{ nomAssociation(evenement.association) }}
				</td>
				<td v-else>-</td>
				<td v-if="evenement.lieu">
					{{ nomLieu(evenement.lieu) }}
				</td>
				<td v-else>-</td>
				<td v-if="evenement.etudiants">
					{{ evenement.etudiants.length }}
				</td>
				<td v-else>0</td>
				<td>
					<button @click="entity = evenement" class="modify">
						Modifier
					</button>
					<button @click="supprimer(evenement)" class="delete">
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
			baseUrlApi: process.env.baseUrlApi,
			entity: null,
		}
	},
	methods: {
		nomAssociation(id) {
			const association = this.$store.state.associations.find(
				(association) => association['@id'] === id
			)
			if (association) return association.nom
			else return '-'
		},
		nomLieu(id) {
			const lieu = this.$store.state.lieux.find(
				(lieu) => lieu['@id'] === id
			)
			if (lieu) return lieu.nom
			else return '-'
		},
		dateToString(date) {
			const unixTimeZero = new Date(date)
			return unixTimeZero.toLocaleDateString('fr-FR')
		},
		async supprimer(evenement) {
			var evenements = [...this.$store.state.evenements]
			evenements = evenements.filter((el) => el.id !== evenement.id)
			this.$store.commit('setEvenements', evenements)
			await this.$axios.$delete(process.env.baseUrlApi + evenement['@id'])
		},
	},
}
</script>

<style lang="scss" scoped>
table {
	width: 100%;
}
</style>