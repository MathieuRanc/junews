<template>
	<div class="container">
		<nav>
			<ul>
				<li
					v-for="(entity, i) in entities"
					:key="i"
					:class="entitySelected === entity.nom ? 'active' : ''"
				>
					<label :for="entity.nom">
						<img
							v-if="entitySelected !== entity.nom"
							:src="entity.img"
							alt=""
						/>
						<img v-else :src="entity.imgSelected" alt="" />
						<input
							v-model="entitySelected"
							:value="entity.nom"
							type="radio"
							name="entity"
							:id="entity.nom"
						/>
						{{ entity.nom }}
					</label>
				</li>
			</ul>
		</nav>
		<div class="data">
			<div class="nbr">
				<div class="card-nbr">
					<h4>
						Association<span
							v-if="$store.state.associations.length > 1"
							>s</span
						>
					</h4>
					<h3>{{ $store.state.associations.length }}</h3>
				</div>
				<div class="card-nbr">
					<h4>
						Cour<span v-if="$store.state.cours.length > 1">s</span>
					</h4>
					<h3>{{ $store.state.cours.length }}</h3>
				</div>
				<div class="card-nbr">
					<h4>
						Etudiant<span v-if="$store.state.etudiants.length > 1"
							>s</span
						>
					</h4>
					<h3>{{ $store.state.etudiants.length }}</h3>
				</div>
				<div class="card-nbr">
					<h4>
						Evenement<span v-if="$store.state.evenements.length > 1"
							>s</span
						>
					</h4>
					<h3>{{ $store.state.evenements.length }}</h3>
				</div>
				<div class="card-nbr">
					<h4>
						Lieu<span v-if="$store.state.lieux.length > 1">x</span>
					</h4>
					<h3>{{ $store.state.lieux.length }}</h3>
				</div>
				<div class="card-nbr">
					<h4>
						Materiel<span v-if="$store.state.materiels.length > 1"
							>s</span
						>
					</h4>
					<h3>{{ $store.state.materiels.length }}</h3>
				</div>
				<div class="card-nbr">
					<h4>
						Promotion<span v-if="$store.state.promotions.length > 1"
							>s</span
						>
					</h4>
					<h3>{{ $store.state.promotions.length }}</h3>
				</div>
			</div>
			<div class="creer">
				<CreateAssociations v-if="entitySelected === 'associations'" />
				<CreateCours v-else-if="entitySelected === 'cours'" />
				<CreateEtudiants v-else-if="entitySelected === 'etudiants'" />
				<CreateEvenements v-else-if="entitySelected === 'evenements'" />
				<CreateLieux v-else-if="entitySelected === 'lieux'" />
				<CreateMateriels v-else-if="entitySelected === 'materiels'" />
				<CreatePromotions v-else-if="entitySelected === 'promotions'" />
			</div>
			<div class="list">
				<CardAssociations v-if="entitySelected === 'associations'" />
				<CardCours v-else-if="entitySelected === 'cours'" />
				<CardEtudiants v-else-if="entitySelected === 'etudiants'" />
				<CardEvenements v-else-if="entitySelected === 'evenements'" />
				<CardLieux v-else-if="entitySelected === 'lieux'" />
				<CardMateriels v-else-if="entitySelected === 'materiels'" />
				<CardPromotions v-else-if="entitySelected === 'promotions'" />
			</div>
		</div>
	</div>
</template>

<script>
export default {
	// middleware: 'auth',
	mounted() {
		console.log(process.env.baseUrlApi)
	},
	data() {
		return {
			entityModification: null,
			entitySelected: 'associations',
			entities: [
				{
					nom: 'associations',
					img: require('../assets/gray/associations.svg'),
					imgSelected: require('../assets/orange/associations.svg'),
				},
				{
					nom: 'cours',
					img: require('../assets/gray/cours.svg'),
					imgSelected: require('../assets/orange/cours.svg'),
				},
				{
					nom: 'etudiants',
					img: require('../assets/gray/etudiants.svg'),
					imgSelected: require('../assets/orange/etudiants.svg'),
				},
				{
					nom: 'evenements',
					img: require('../assets/gray/evenements.svg'),
					imgSelected: require('../assets/orange/evenements.svg'),
				},
				{
					nom: 'lieux',
					img: require('../assets/gray/lieux.svg'),
					imgSelected: require('../assets/orange/lieux.svg'),
				},
				{
					nom: 'materiels',
					img: require('../assets/gray/materiels.svg'),
					imgSelected: require('../assets/orange/materiels.svg'),
				},
				{
					nom: 'promotions',
					img: require('../assets/gray/promotions.svg'),
					imgSelected: require('../assets/orange/promotions.svg'),
				},
			],
			config: {
				headers: {
					'Content-Type': 'application/json',
					'Content-Type': 'application/json',
					Authorization: 'Bearer ' + this.$store.state.clientToken,
				},
			},
		}
	},
	watch: {
		entitySelected() {
			this.$fetch()
		},
	},
	async fetch() {
		this.$axios.setHeader('Content-Type', 'application/ld+json', ['get'])
		await this.$axios
			.$get(process.env.baseUrlApi + '/associations')
			.then((res) =>
				this.$store.commit('setAssociations', res['hydra:member'], {})
			)
		await this.$axios.$get(process.env.baseUrlApi + '/cours').then((res) =>
			this.$store.commit('setCours', res['hydra:member'], {
				'Content-Type': 'application/ld+json',
			})
		)
		await this.$axios
			.$get(process.env.baseUrlApi + '/etudiants')
			.then((res) =>
				this.$store.commit('setEtudiants', res['hydra:member'], {
					'Content-Type': 'application/ld+json',
				})
			)
		await this.$axios
			.$get(process.env.baseUrlApi + '/evenements')
			.then((res) =>
				this.$store.commit('setEvenements', res['hydra:member'], {
					'Content-Type': 'application/ld+json',
				})
			)
		await this.$axios.$get(process.env.baseUrlApi + '/lieus').then((res) =>
			this.$store.commit('setLieux', res['hydra:member'], {
				'Content-Type': 'application/ld+json',
			})
		)

		await this.$axios
			.$get(process.env.baseUrlApi + '/materiels')
			.then((res) =>
				this.$store.commit('setMateriels', res['hydra:member'], {
					'Content-Type': 'application/ld+json',
				})
			)
		await this.$axios
			.$get(process.env.baseUrlApi + '/promotions')
			.then((res) =>
				this.$store.commit('setPromotions', res['hydra:member'], {
					'Content-Type': 'application/ld+json',
				})
			)
		await this.$axios
			.$get(process.env.baseUrlApi + '/media_objects')
			.then((res) =>
				this.$store.commit('setImages', res['hydra:member'], {
					'Content-Type': 'application/ld+json',
				})
			)
	},
	// fetchOnServer: false,
}
</script>

<style lang="scss" scoped>
.container {
	min-height: 100vh;
	display: flex;
	justify-content: space-between;
	input[type='radio'] {
		position: absolute;
		left: -99999px;
	}
	label {
		padding: 10px 60px 10px 40px;
		cursor: pointer;
		display: flex;
		align-items: center;
	}
	nav {
		margin-top: 40px;
		flex-direction: column;
		min-width: 260px;
		ul {
			position: fixed;
			list-style: none;
			padding: 0;
			li {
				display: flex;
				align-items: center;
				margin: 10px 0;
				border-left: 4px transparent solid;
				&.active {
					border-left: 4px #ff5a36 solid;
					color: #ff5a36;
					img {
						fill: green;
					}
				}
				font-weight: 600;
				color: #585858;
				img {
					width: 28px;
					height: 28px;
					object-fit: contain;
					margin-right: 20px;
				}
			}
		}
	}
	.data {
		z-index: 100;
		width: 100%;
		margin: 20px;
		padding: 20px;
		display: flex;
		flex-direction: column;
		background-color: #f1f3f6;
		border-radius: 20px;
		.nbr {
			display: grid;
			grid-template-columns: repeat(7, minmax(0, 1fr));
			grid-gap: 1em;
			.card-nbr {
				border-radius: 10px;
				padding: 10px 20px;
				display: flex;
				flex-direction: column;
				background-color: #fff;
				h4 {
					font-size: 14px;
					font-weight: normal;
					color: #585858;
					margin-bottom: 20px;
				}
				h3 {
					font-size: 30px;
					font-weight: normal;
				}
			}
		}
	}
}
</style>
