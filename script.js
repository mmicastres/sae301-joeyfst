function calculcases() {
  let categories = document.getElementsByClassName('selectcategories')
  if (categories.length <= 2) {
    let dernierecategorie = categories[categories.length - 1]
    dernierecategorie.addEventListener('input', ajoutercase)
  }

  let tags = document.getElementsByClassName('selecttags')
  if (tags.length <= 2) {
    let derniertag = tags[tags.length - 1]
    derniertag.addEventListener('input', ajoutercase)
  }

  let ressources = document.getElementsByClassName('selectressources')
  if (ressources.length <= 2) {
    let derniereressource = ressources[ressources.length - 1]
    derniereressource.addEventListener('input', ajoutercase)
  }

  let contributeurs = document.getElementsByClassName('selectcontributeurs')
  if (contributeurs.length <= 2) {
    let derniercontributeur = contributeurs[contributeurs.length - 1]
    derniercontributeur.addEventListener('input', ajoutercase)
  }
}

function getAllElementsValues(param) {
  let options = document.getElementsByClassName(param);
  let values = [];
  for (let i = 0; i < options.length; i++) {
    values.push(options[i].value);
  }
  return values;
}

function getAllElementsText(param) {
  let options = document.getElementsByClassName(param);
  let text = [];
  for (let i = 0; i < options.length; i++) {
    text.push(options[i].textContent);
  }
  return text;
}

function ajoutercase(event) {
  event.target.removeEventListener('input', ajoutercase);
  let newSelect = document.createElement('select');
  let option = 'option' + event.target.id;
  let classname = 'select' + event.target.id;
  newSelect.className = classname + " form-select";
  newSelect.name = event.target.name;
  newSelect.id = event.target.id;

  let values = getAllElementsValues(option);
  let text = getAllElementsText(option);
  let firstoption = document.createElement('option');
  firstoption.setAttribute('selected', '');
  firstoption.textContent = '';
  newSelect.appendChild(firstoption);

  for (let i = 0; i < values.length; i++) {
    let defaultOption = document.createElement('option');
    defaultOption.value = values[i]
    defaultOption.text = text[i];
    newSelect.appendChild(defaultOption);
  }

  event.target.insertAdjacentElement('afterend', newSelect);
  calculcases();
}

document.addEventListener('DOMContentLoaded', calculcases);