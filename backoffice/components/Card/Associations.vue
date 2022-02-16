<template>
	<table v-if="$store.state.associations.length > 0">
		<ModifyAssociations
			v-if="entity"
			:association="entity"
			:close="() => (entity = null)"
		/>
		<thead>
			<tr>
				<!-- <th><input type="checkbox" name="all" id="all" /></th> -->
				<th>ID</th>
				<th>Image</th>
				<th>Nom</th>
				<th>Description</th>
				<th>Evenements</th>
				<th>Etudiants</th>
				<th>Actions</th>
			</tr>
		</thead>
		<tbody>
			<tr
				v-for="association in $store.state.associations"
				:key="association.id"
			>
				<!-- <td>
					<input
						type="checkbox"
						:name="association.id"
						:id="association.id"
					/>
				</td> -->
				<td v-if="association.id">
					{{ association.id }}
				</td>
				<td v-else>-</td>
				<td v-if="association.logo">
					<img
						v-if="$store.state.images[association.logo - 1]"
						:src="
							baseUrlApi +
							$store.state.images[association.logo - 1].contentUrl
						"
						alt="logo association"
					/>
				</td>
				<td v-else>
					<img
						:src="baseUrlApi + '/media/associations/default.png'"
						alt="logo association"
					/>
				</td>
				<td v-if="association.nom">
					{{ association.nom }}
				</td>
				<td v-else>-</td>
				<td v-if="association.description">
					{{ association.description }}
				</td>
				<td v-else>-</td>
				<td v-if="association.evenements">
					{{ association.evenements.length }}
				</td>
				<td v-else>0</td>
				<td v-if="association.etudiants">
					{{ association.etudiants.length }}
				</td>
				<td v-else>0</td>
				<td>
					<button @click="entity = association" class="modify">
						Modifier
					</button>
					<button @click="supprimer(association)" class="delete">
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
		async supprimer(association) {
			var associations = [...this.$store.state.associations]
			associations = associations.filter(
				(asso) => asso.id !== association.id
			)
			this.$store.commit('setAssociations', associations)
			await this.$axios.$delete(
				process.env.baseUrlApi + association['@id']
			)
		},
	},
}
</script>