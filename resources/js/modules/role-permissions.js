function updateSelectAllState(selectAll, checkboxes) {
    const checkedCount = checkboxes.filter((checkbox) => checkbox.checked).length;

    selectAll.checked = checkboxes.length > 0 && checkedCount === checkboxes.length;
    selectAll.indeterminate = checkedCount > 0 && checkedCount < checkboxes.length;
}

function updateParentState(parentCheckbox) {
    const childCheckboxes = Array.from(
        document.querySelectorAll(`.child-checkbox[data-parent-id="${parentCheckbox.value}"]`)
    );

    if (childCheckboxes.length === 0) {
        parentCheckbox.indeterminate = false;
        return;
    }

    const checkedChildren = childCheckboxes.filter((checkbox) => checkbox.checked).length;

    parentCheckbox.checked = checkedChildren === childCheckboxes.length;
    parentCheckbox.indeterminate = checkedChildren > 0 && checkedChildren < childCheckboxes.length;
}

function initRolePermissionCheckboxes() {
    const selectAll = document.getElementById('select-all');
    const permissionContainer = document.getElementById('category-checkboxes');

    if (!selectAll || !permissionContainer) {
        return;
    }

    const parentCheckboxes = Array.from(permissionContainer.querySelectorAll('.parent-checkbox'));
    const childCheckboxes = Array.from(permissionContainer.querySelectorAll('.child-checkbox'));
    const allPermissionCheckboxes = [...parentCheckboxes, ...childCheckboxes];

    parentCheckboxes.forEach((parentCheckbox) => {
        parentCheckbox.addEventListener('change', () => {
            childCheckboxes
                .filter((childCheckbox) => childCheckbox.dataset.parentId === parentCheckbox.value)
                .forEach((childCheckbox) => {
                    childCheckbox.checked = parentCheckbox.checked;
                });

            parentCheckbox.indeterminate = false;
            updateSelectAllState(selectAll, allPermissionCheckboxes);
        });
    });

    childCheckboxes.forEach((childCheckbox) => {
        childCheckbox.addEventListener('change', () => {
            const parentCheckbox = permissionContainer.querySelector(
                `.parent-checkbox[value="${childCheckbox.dataset.parentId}"]`
            );

            if (parentCheckbox) {
                updateParentState(parentCheckbox);
            }

            updateSelectAllState(selectAll, allPermissionCheckboxes);
        });
    });

    selectAll.addEventListener('change', () => {
        allPermissionCheckboxes.forEach((checkbox) => {
            checkbox.checked = selectAll.checked;
            checkbox.indeterminate = false;
        });

        selectAll.indeterminate = false;
    });

    parentCheckboxes.forEach(updateParentState);
    updateSelectAllState(selectAll, allPermissionCheckboxes);
}

document.addEventListener('DOMContentLoaded', initRolePermissionCheckboxes);
