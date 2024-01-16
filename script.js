function calculcases(){
  let categories = document.getElementsByClassName('selectcategories')
  let dernierecategorie = categories[categories.length -1]
  dernierecategorie.addEventListener('input', ajoutercase)

  let tags = document.getElementsByClassName('selecttags')
  let derniertag = tags[tags.length -1]
  derniertag.addEventListener('input', ajoutercase)

  let ressources = document.getElementsByClassName('selectressources')
  let derniereressource = ressources[ressources.length -1]
  derniereressource.addEventListener('input', ajoutercase)

  let contributeurs = document.getElementsByClassName('selectcontributeurs')
  let derniercontributeur = contributeurs[contributeurs.length -1]
  derniercontributeur.addEventListener('input', ajoutercase)

  let images = document.getElementsByClassName('selectimages')
  let derniereimage = images[images.length -1]
  derniereimage.addEventListener('input', ajoutercase)
}

function getAllElementValues(param) {
  let options = document.getElementsByClassName(param);
  let values = [];
  for (let i = 0; i < options.length; i++) {
    values.push(options[i].value);
  }
  return values;
}

function getAllElementText(param) {
  let options = document.getElementsByClassName(param);
  let text = [];
  for (let i = 0; i < options.length; i++) {
    text.push(options[i].textContent);
  }
  return text;
}

function ajoutercase(event) {
  let newSelect = document.createElement('select');
  let param = 'option' + event.target.name
  let classname = 'select' + event.target.name;
  newSelect.className = classname;
  newSelect.name = event.target.name;

  let values = getAllElementValues(param);
  let text = getAllElementText(param);
  for(let i = 0;i < values.length;i++){
    let defaultOption = document.createElement('option');
    defaultOption.value = values[i]
    defaultOption.text = text[i];
    newSelect.appendChild(defaultOption);
  }

  event.target.insertAdjacentElement('afterend', newSelect);
  event.target.classList.remove(classname)
  calculcases();
}

document.addEventListener('DOMContentLoaded', calculcases);