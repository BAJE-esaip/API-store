const initLegacyCollections = () => {
    const addRemoveButton = (wrapper) => {
        const removeBtn = document.createElement('button');
        removeBtn.type = 'button';
        removeBtn.className = 'btn btn-sm btn-outline-danger ms-2';
        removeBtn.textContent = 'Supprimer';
        removeBtn.addEventListener('click', () => wrapper.remove());
        const controls = document.createElement('div');
        controls.className = 'mt-1';
        controls.appendChild(removeBtn);
        wrapper.appendChild(controls);
    };

    document.querySelectorAll('.js-collection[data-prototype]').forEach((collection) => {
        if (collection.dataset.collectionEnhanced === '1') {
            return;
        }
        collection.dataset.collectionEnhanced = '1';

        let index = collection.querySelectorAll('.js-collection-item').length;

        const addBtn = document.createElement('button');
        addBtn.type = 'button';
        addBtn.className = 'btn btn-sm btn-secondary mb-3';
        addBtn.textContent = 'Ajouter un produit';
        addBtn.addEventListener('click', () => {
            const prototype = collection.dataset.prototype;
            const newForm = prototype.replace(/__name__/g, String(index));
            index += 1;

            const wrapper = document.createElement('div');
            wrapper.className = 'mb-2 js-collection-item';
            wrapper.innerHTML = newForm;

            addRemoveButton(wrapper);
            collection.appendChild(wrapper);
        });

        if (collection.parentElement) {
            collection.parentElement.appendChild(addBtn);
        }

        collection.querySelectorAll('.js-collection-item').forEach((wrapper) => {
            if (!wrapper.querySelector('.btn-outline-danger')) {
                addRemoveButton(wrapper);
            }
        });
    });
};

const initEaCollections = () => {
    document.querySelectorAll('[data-ea-collection-field="true"]').forEach((collection) => {
        if (collection.dataset.collectionEnhanced === '1') {
            return;
        }
        collection.dataset.collectionEnhanced = '1';

        const itemsContainer = collection.querySelector('.ea-form-collection-items');
        const addBtn = collection.querySelector('.field-collection-add-button');
        if (!itemsContainer || !addBtn) {
            return;
        }

        const prototype = collection.dataset.prototype || '';
        const namePlaceholder = collection.dataset.formTypeNamePlaceholder || '__name__';
        let index = Number.parseInt(
            collection.dataset.numItems || String(itemsContainer.querySelectorAll('.field-collection-item').length),
            10,
        );
        if (Number.isNaN(index)) {
            index = 0;
        }

        const emptyBadge = itemsContainer.querySelector('.collection-empty');
        const updateEmpty = () => {
            const hasItems = itemsContainer.querySelector('.field-collection-item') !== null;
            if (emptyBadge) {
                emptyBadge.style.display = hasItems ? 'none' : '';
            }
        };

        addBtn.addEventListener('click', (event) => {
            event.preventDefault();

            const newForm = prototype.replace(new RegExp(namePlaceholder, 'g'), String(index));
            index += 1;
            collection.dataset.numItems = String(index);

            const wrapper = document.createElement('div');
            wrapper.innerHTML = newForm.trim();
            const item = wrapper.firstElementChild;
            if (!item) {
                return;
            }

            itemsContainer.appendChild(item);
            updateEmpty();
        });

        itemsContainer.addEventListener('click', (event) => {
            const deleteButton = event.target.closest('.field-collection-delete-button');
            if (!deleteButton) {
                return;
            }
            event.preventDefault();

            const item = deleteButton.closest('.field-collection-item');
            if (item) {
                item.remove();
                updateEmpty();
            }
        });

        updateEmpty();
    });
};

const initCollections = () => {
    initLegacyCollections();
    initEaCollections();
};

document.addEventListener('DOMContentLoaded', initCollections);
document.addEventListener('turbo:load', initCollections);
