/* Uusi projekti: */
document.addEventListener('DOMContentLoaded', function () {
    toggleCityField();  // Piilotetaan kenttä alussa, jos ei ole valittu Lähityötä
  });
  
  function toggleCityField() {
    var locationSelect = document.getElementById('location');
    var cityContainer = document.getElementById('city-container');
  
    if (locationSelect.value === 'Lähityö') {
        cityContainer.style.display = 'block';  // Näytetään Paikkakunta-kenttä
    } else {
        cityContainer.style.display = 'none';  // Piilotetaan Paikkakunta-kenttä
    }
  }
  