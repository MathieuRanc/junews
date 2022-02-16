<template>
	<table v-if="$store.state.etudiants.length > 0">
		<ModifyEtudiants
			v-if="entity"
			:etudiant="entity"
			:close="() => (entity = null)"
		/>
		<thead>
			<tr>
				<!-- <th><input type="checkbox" name="all" id="all" /></th> -->
				<th>ID</th>
				<th>Photo</th>
				<th>Nom</th>
				<th>Administre</th>
				<th>Evenements</th>
				<th>Association</th>
				<th>Cours</th>
				<th>Promotion</th>
				<th>Actions</th>
			</tr>
		</thead>
		<tbody>
			<tr v-for="etudiant in $store.state.etudiants" :key="etudiant.id">
				<!-- <td>
					<input
						type="checkbox"
						:name="etudiant.id"
						:id="etudiant.id"
					/>
				</td> -->
				<td v-if="etudiant.id">
					{{ etudiant.id }}
				</td>
				<td v-else>-</td>
				<td v-if="etudiant.photo">
					<img
						v-if="$store.state.images[etudiant.photo - 1]"
						:src="
							baseUrlApi +
							$store.state.images[etudiant.photo - 1].contentUrl
						"
						alt=""
					/>
				</td>
				<td v-else>
					<img
						:src="baseUrlApi + '/media/etudiants/default.png'"
						alt=""
					/>
				</td>
				<td v-if="etudiant.nom && etudiant.prenom">
					{{ `${etudiant.nom} ${etudiant.prenom}` }}
				</td>
				<td v-else>-</td>
				<td v-if="etudiant.administre">
					{{ etudiant.administre }}
				</td>
				<td v-else>0</td>
				<td v-if="etudiant.evenements">
					{{ etudiant.evenements.length }}
				</td>
				<td v-else>0</td>
				<td v-if="etudiant.associations">
					{{ etudiant.associations.length }}
				</td>
				<td v-else>0</td>
				<td v-if="etudiant.cours">
					{{ etudiant.cours.length }}
				</td>
				<td v-else>0</td>
				<td>
					{{ nomPromotion(etudiant.promotion) }}
				</td>
				<td>
					<button @click="entity = etudiant" class="modify">
						Modifier
					</button>
					<button @click="supprimer(etudiant)" class="delete">
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
			baseUrlApi: process.env.baseUrlApi,
		}
	},
	methods: {
		nomPromotion(id) {
			const promo = this.$store.state.promotions.find(
				(promotion) => promotion['@id'] === id
			)
			if (promo) return promo.nom
			else return '-'
		},
		async supprimer(etudiant) {
			var etudiants = [...this.$store.state.etudiants]
			etudiants = etudiants.filter((el) => el.id !== etudiant.id)
			this.$store.commit('setEtudiants', etudiants)
			await this.$axios.$delete(process.env.baseUrlApi + etudiant['@id'])
		},
	},
}
</script>

<style lang="scss" scoped>
table img {
	border-radius: 100%;
	object-fit: cover;
}
</style>