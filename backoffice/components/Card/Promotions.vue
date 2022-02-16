<template>
	<table v-if="$store.state.promotions.length > 0">
		<ModifyPromotions
			v-if="entity"
			:promotion="entity"
			:close="() => (entity = null)"
		/>
		<thead>
			<tr>
				<!-- <th><input type="checkbox" name="all" id="all" /></th> -->
				<th>ID</th>
				<th>Nom</th>
				<th>Ecole</th>
				<th>Evenements</th>
				<th>Etudiants</th>
				<th>Actions</th>
			</tr>
		</thead>
		<tbody>
			<tr
				v-for="promotion in $store.state.promotions"
				:key="promotion.id"
			>
				<!-- <td>
					<input
						type="checkbox"
						:name="promotion.id"
						:id="promotion.id"
					/>
				</td> -->
				<td v-if="promotion.id">{{ promotion.id }}</td>
				<td v-else>-</td>
				<td v-if="promotion.nom">
					{{ promotion.nom }}
				</td>
				<td v-else>-</td>
				<td v-if="promotion.ecole">
					{{ promotion.ecole }}
				</td>
				<td v-else>-</td>
				<td v-if="promotion.evenements">
					{{ promotion.evenements.length }}
				</td>
				<td v-else>0</td>
				<td v-if="promotion.etudiants">
					{{ promotion.etudiants.length }}
				</td>
				<td v-else>0</td>
				<td>
					<button @click="entity = promotion" class="modify">
						Modifier
					</button>
					<button @click="supprimer(promotion)" class="delete">
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
		async supprimer(promotion) {
			var promotions = [...this.$store.state.promotions]
			promotions = promotions.filter((el) => el.id !== promotion.id)
			this.$store.commit('setPromotions', promotions)
			await this.$axios.$delete(process.env.baseUrlApi + promotion['@id'])
		},
	},
}
</script>