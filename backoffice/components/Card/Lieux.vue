<template>
	<table v-if="$store.state.lieux.length > 0">
		<ModifyLieux
			v-if="entity"
			:lieu="entity"
			:close="() => (entity = null)"
		/>
		<thead>
			<tr>
				<!-- <th><input type="checkbox" name="all" id="all" /></th> -->
				<th>ID</th>
				<th>Nom</th>
				<th>Description</th>
				<th>Type</th>
				<th>Adresse</th>
				<th>Evenements</th>
				<th>Actions</th>
			</tr>
		</thead>
		<tbody>
			<tr v-for="lieu in $store.state.lieux" :key="lieu.id">
				<!-- <td><input type="checkbox" :name="lieu.id" :id="lieu.id" /></td> -->
				<td v-if="lieu.id">{{ lieu.id }}</td>
				<td v-else>-</td>
				<td v-if="lieu.nom">
					{{ lieu.nom }}
				</td>
				<td v-else>-</td>
				<td v-if="lieu.description">
					{{ lieu.description }}
				</td>
				<td v-else>-</td>
				<td v-if="lieu.type">
					{{ lieu.type }}
				</td>
				<td v-else>-</td>
				<td v-if="lieu.adresse">
					{{ lieu.adresse }}
				</td>
				<td v-else>-</td>
				<td v-if="lieu.evenements">
					{{ lieu.evenements.length }}
				</td>
				<td v-else>0</td>
				<td>
					<button @click="entity = lieu" class="modify">
						Modifier
					</button>
					<button @click="supprimer(lieu)" class="delete">
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
		async supprimer(lieu) {
			var lieux = [...this.$store.state.lieux]
			lieux = lieux.filter((el) => el.id !== lieu.id)
			this.$store.commit('setLieux', lieux)
			await this.$axios
				.$delete(process.env.baseUrlApi + lieu['@id'])
		},
	},
}
</script>