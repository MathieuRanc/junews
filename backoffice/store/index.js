export const state = () => ({
    clientToken: null,
    associations: [],
    cours: [],
    etudiants: [],
    evenements: [],
    lieux: [],
    materiels: [],
    promotions: [],
    images: [],
  })
  
  function compare(a, b) {
    if (a.nom < b.nom) {
      return -1
    }
    if (a.nom > b.nom) {
      return 1
    }
    return 0
  }
  
  function compareEtudiant(a, b) {
    if (a.nom < b.nom) {
      return -1
    }
    if (a.nom > b.nom) {
      return 1
    }
    if (a.prenom < b.prenom) {
      return -1
    }
    if (a.prenom > b.prenom) {
      return 1
    }
    return 0
  }
  
  export const mutations = {
    setClientToken(state, clientToken) {
      if (clientToken)
        state.clientToken = clientToken.sort(compare)
    },
    setAssociations(state, associations) {
      if (associations)
        state.associations = associations.sort(compare)
    },
    setCours(state, cours) {
      if (cours)
        state.cours = cours.sort(compare)
    },
    setEtudiants(state, etudiants) {
  
      if (etudiants)
        state.etudiants = etudiants.sort(compare)
  
    },
    setEvenements(state, evenements) {
      if (evenements)
        state.evenements = evenements.sort(compare)
    },
    setLieux(state, lieux) {
      if (lieux)
        state.lieux = lieux.sort(compare)
    },
    setMateriels(state, materiels) {
      if (materiels)
        state.materiels = materiels.sort(compare)
    },
    setPromotions(state, promotions) {
      if (promotions)
        state.promotions = promotions.sort(compare)
    },
    setImages(state, images) {
      state.images = images
    },
  }
  
  
  