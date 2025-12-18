document.addEventListener('DOMContentLoaded', () => {
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

        collection.parentElement.appendChild(addBtn);

        collection.querySelectorAll('.js-collection-item').forEach((wrapper) => {
            if (!wrapper.querySelector('.btn-outline-danger')) {
                addRemoveButton(wrapper);
            }
        });
    });
});
