document.addEventListener('DOMContentLoaded', function () {
    const inputField = document.getElementById('tags-input');
    const suggestionsContainer = document.getElementById('suggestions');
    const tagsChipsContainer = document.getElementById('tag-container');
    let selectedTags = []; // Pour stocker les tags sélectionnés

    // Fonction de suggestion de tags
    inputField.addEventListener('input', function () {
        const query = inputField.value;
        if (query.length >= 2) {
            fetch(`/tags/search?query=${query}`)
                .then(response => response.json())
                .then(tags => {
                    suggestionsContainer.innerHTML = '';
                    tags.forEach(tag => {
                        const div = document.createElement('li');
                        div.classList.add('px-4', 'py-2', 'cursor-pointer');
                        div.textContent = tag.name;
                        div.onclick = () => {
                            addTag(tag.name);
                            inputField.value = '';
                            suggestionsContainer.classList.add('hidden');
                        };
                        suggestionsContainer.appendChild(div);
                    });
                    suggestionsContainer.classList.toggle('hidden', !suggestionsContainer.innerHTML);
                })
                .catch(error => console.error('Error fetching tags:', error));
        } else {
            suggestionsContainer.classList.add('hidden');
        }
    });

    // Ajouter un tag à la liste des chips
    function addTag(tagName) {
        if (!selectedTags.includes(tagName)) {
            selectedTags.push(tagName);
            updateTagsChips();
        }
    }

    // Mettre à jour l'affichage des tags sélectionnés
    function updateTagsChips() {
        tagsChipsContainer.innerHTML = ''; // Clear previous chips
        selectedTags.forEach(tag => {
            const chip = document.createElement('span');
            chip.classList.add('bg-blue-500', 'text-white', 'px-3', 'py-1', 'rounded-full', 'mr-2', 'mt-2', 'cursor-pointer');
            chip.textContent = tag;
            chip.onclick = () => removeTag(tag);  // Supprimer le tag lorsqu'on clique
            tagsChipsContainer.appendChild(chip);
        });
    }

    // Supprimer un tag des chips
    function removeTag(tagName) {
        selectedTags = selectedTags.filter(tag => tag !== tagName);
        updateTagsChips();
    }

    // Mise à jour des tags dans un champ caché avant l'envoi du formulaire
    document.querySelector('form').addEventListener('submit', function () {
        const tagsListInput = document.getElementById('tags-list');  // Assurez-vous qu'il existe
        tagsListInput.value = selectedTags.join(',');
    });
});
