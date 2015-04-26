var GITHUB_API_URL = "https://api.github.com";

//To make jslint error message go away: http://stackoverflow.com/questions/11978566/jslint-com-does-not-recognize-localstorage
/*global localStorage: false, document: false, XMLHttpRequest: false */

function clearChildren(resultsDiv) {
  var children = resultsDiv.children;
  var i;
  var toDelete = [];
  for (i = 0; i < children.length; i++) {
    if (!children[i].hasAttribute("id")) {
      toDelete.push(children[i]);
    }
  }
  for (i = 0; i < toDelete.length; i++) {
    resultsDiv.removeChild(toDelete[i]);
  }
}

function loadFavorites() {
  var userFavoritesStr = localStorage.getItem("userFavorites");
  if (userFavoritesStr === null) {
    userFavoritesStr = "{}";
  }
  return JSON.parse(userFavoritesStr);
}

function getLanguage(gist) {
  var language;
  var fileName = Object.keys(gist.files)[0];;
  return gist.files[fileName].language;
}

function fillGistTemplate(gistTemplate, gist) {
  var description;
  var language;
  var link;
  var button;
  description = gistTemplate.getElementsByClassName("description");
  if (gist.description === null || gist.description === "") {
    description[0].innerHTML = "No description provided.";
  } else {
    description[0].innerHTML = gist.description;
  }
  language = gistTemplate.getElementsByClassName("language");
  language[0].innerHTML = getLanguage(gist);
  link = gistTemplate.getElementsByClassName("url");
  link[0].innerHTML = gist.url;
  link[0].setAttribute("href", gist.url);
  button = gistTemplate.getElementsByTagName("BUTTON");
  button[0].setAttribute("gist-id", gist.id);
  //make gistTemplate visible since template is hidden in CSS: http://www.w3schools.com/jsref/met_element_removeattribute.asp
  gistTemplate.removeAttribute("id");
}

function createFavoritesList(favorites, userFavorites) {
  var gist;
  var gistDiv;
  var description;
  var language;
  var link;
  var button;
  var favoriteTemplate = document.getElementById("favorite-template");
  if (Object.keys(userFavorites).length > 0) {
    document.getElementById("no-favorites").innerHTML = "";
  } else {
    document.getElementById("no-favorites").innerHTML = "There are currently no saved favorites.";
  }
  Object.keys(userFavorites).forEach(function(gistID) {
    //cloning the gist templates from the index.html file: http://www.w3schools.com/jsref/met_node_clonenode.asp
    gistDiv = favoriteTemplate.cloneNode(true);
    gist = userFavorites[gistID];
    description = gistDiv.getElementsByClassName("description");
    description[0].innerHTML = gist.description;
    language = gistDiv.getElementsByClassName("language");
    language[0].innerHTML = gist.language;
    link = gistDiv.getElementsByClassName("url");
    link[0].innerHTML = gist.link;
    link[0].setAttribute("href", gist.link);
    button = gistDiv.getElementsByTagName("BUTTON");
    button[0].setAttribute("gist-id", gistID);
    gistDiv.removeAttribute("id");
    favorites.appendChild(gistDiv);
  });
}

function createGistList(results, favorites, gists) {
  var gistTemplate = document.getElementById("gist-template");
  var gist;
  var gistDiv;
  var userFavorites;
  var i;
  var inputs;
  var languageArr = [];
  clearChildren(results);
  clearChildren(favorites);
  userFavorites = loadFavorites();
  inputs = document.getElementsByTagName("input");
  for (i = 0; i < inputs.length; i++) {
    if (inputs[i].type === "checkbox" && inputs[i].checked) {
      languageArr.push(inputs[i].value);
    }
  }
  for (i = 0; i < gists.length; i++) {
    gist = gists[i];
    //This will filter for language, but it does not affect the gists diplayed in favorites.
    //This is a design decision because the requirements are unclear.
    if (languageArr.length > 0 && languageArr.indexOf(getLanguage(gist)) === -1) { //a language checkbox is checked, but not the language of this gist
      continue;
    }
    if (userFavorites.hasOwnProperty(gist.id)) { //gist is a favorite
      continue;
    }
    // gist is not a favorite
    gistDiv = gistTemplate.cloneNode(true);
    fillGistTemplate(gistDiv, gist);
    results.appendChild(gistDiv);
  }  
  createFavoritesList(favorites, userFavorites);
}

function requestGists() {
  var request = new XMLHttpRequest();
  var r = document.getElementById("max-results");
  var maxResults = r.options[r.selectedIndex].value;
  if (!request) {
    throw "Unable to create new HTTP request.";
  }
  var url = GITHUB_API_URL + "/gists/public?per_page=" + maxResults;
  request.onreadystatechange = function () {
    if (this.readyState === 4) {
      if (this.status === 200) {
        var gists = JSON.parse(this.responseText);
        createGistList(document.getElementById("results"),
                       document.getElementById("favorites"),
                       gists);
      }
    }
  };
  request.open("GET", url);
  request.send();
}

function saveFavorites(favorites) {
  var favoritesStr = JSON.stringify(favorites);
  localStorage.setItem("userFavorites", favoritesStr);
}

function markFavorite(element) {
  var description;
  var language;
  var link;
  var favoriteGist = {};
  var userFavorites = loadFavorites();
  var id = element.getAttribute("gist-id");
  var div = element.parentElement.parentElement.parentElement.parentElement.parentElement;
  description = div.getElementsByClassName("description");
  favoriteGist.description = description[0].innerHTML;
  language = div.getElementsByClassName("language");
  favoriteGist.language = language[0].innerHTML;
  link = div.getElementsByClassName("url");
  favoriteGist.link = link[0].innerHTML;
  userFavorites[id] = favoriteGist;
  saveFavorites(userFavorites);
  requestGists();
}

function removeFavorite(element) {
  var userFavorites = loadFavorites();
  var id = element.getAttribute("gist-id");
  delete userFavorites[id];
  saveFavorites(userFavorites);
  requestGists();
}

window.onload = requestGists;