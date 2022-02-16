<template>
	<table v-if="$store.state.materiels.length > 0">
		<ModifyMateriels
			v-if="entity"
			:materiel="entity"
			:close="() => (entity = null)"
		/>
		<thead>
			<tr>
				<!-- <th><input type="checkbox" name="all" id="all" /></th> -->
				<th>ID</th>
				<th>Nom</th>
				<th>Description</th>
				<th>Evenements</th>
				<th>Actions</th>
			</tr>
		</thead>
		<tbody>
			<tr v-for="materiel in $store.state.materiels" :key="materiel.id">
				<!-- <td>
					<input
						type="checkbox"
						:name="materiel.id"
						:id="materiel.id"
					/>
				</td> -->
				<td v-if="materiel.id">{{ materiel.id }}</td>
				<td v-else>-</td>
				<td v-if="materiel.nom">
					{{ materiel.nom }}
				</td>
				<td v-else>-</td>
				<td v-if="materiel.description">
					{{ materiel.description }}
				</td>
				<td v-else>-</td>
				<td v-if="materiel.evenements">
					{{ materiel.evenements.length }}
				</td>
				<td v-else>0</td>
				<td>
					<button @click="entity = materiel" class="modify">Modifier</button>
					<button @click="supprimer(materiel)" class="delete">
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
		async supprimer(materiel) {
			var materiels = [...this.$store.state.materiels]
			materiels = materiels.filter((el) => el.id !== materiel.id)
			this.$store.commit('setMateriels', materiels)
			await this.$axios
				.$delete(process.env.baseUrlApi+materiel['@id'])
		},
	},
}
</script>