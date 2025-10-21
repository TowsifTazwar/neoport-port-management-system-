document.addEventListener('DOMContentLoaded', () => {
    const api = 'harbor_api.php';

    // --- Toast Notification ---
    function showToast(message, isError = false) {
        const toast = document.createElement('div');
        toast.textContent = message;
        toast.className = `toast ${isError ? 'error' : ''}`;
        document.body.appendChild(toast);
        setTimeout(() => {
            toast.remove();
        }, 3000);
    }

    // --- Event Delegation for Action Forms ---
    const requestsTable = document.getElementById('requestsTable');
    if (requestsTable) {
        requestsTable.addEventListener('submit', async (e) => {
            if (e.target.classList.contains('action-form')) {
                e.preventDefault();
                const form = e.target;
                const formData = new FormData(form);
                const submitter = e.submitter; // The button that was clicked

                // Ensure the action from the clicked button is included in the form data.
                if (submitter && submitter.name) {
                    formData.append(submitter.name, submitter.value);
                } else {
                    console.error("Could not determine submitter button.");
                    showToast("Could not determine action.", true);
                    return;
                }
                
                try {
                    const response = await fetch(api, {
                        method: 'POST',
                        body: formData
                    });

                    const result = await response.json();

                    if (result.success) {
                        showToast(result.message);
                        const action = submitter.value; // Get action from the button
                        const row = form.closest('tr');
                        updateRowOnSuccess(row, action);
                    } else {
                        throw new Error(result.message || 'An unknown error occurred.');
                    }
                } catch (error) {
                    console.error('Action error:', error);
                    showToast(error.message, true);
                }
            }
        });
    }

    // --- Berth Allocation ---
    window.allocateBerth = async () => {
        const form = document.getElementById('berthForm');
        const shipId = document.getElementById('shipId').value;
        const berth = document.getElementById('berthSelect').value;
        const dockTime = document.getElementById('dockTime').value;

        if (!shipId || !berth || !dockTime) {
            showToast('All berth allocation fields are required.', true);
            return;
        }

        const formData = new FormData();
        formData.append('action', 'allocate_berth');
        formData.append('ship_id', shipId);
        formData.append('berth', berth);
        formData.append('dock_time', dockTime);

        try {
            const response = await fetch(api, {
                method: 'POST',
                body: formData
            });
            const result = await response.json();

            if (result.success) {
                showToast(result.message);
                form.reset();
                loadBerthUsage(); // Refresh berth usage table
            } else {
                throw new Error(result.message);
            }
        } catch (error) {
            showToast(error.message, true);
        }
    };

    // --- Load Berth Usage ---
    async function loadBerthUsage() {
        // This function would fetch and display current berth usage.
        // For now, it's a placeholder.
        console.log("Refreshing berth usage...");
    }

    // --- UI Update on Success ---
    function updateRowOnSuccess(row, action) {
        if (!row) return;

        const statusBadge = row.querySelector('.status-badge');
        if (statusBadge) {
            statusBadge.className = `status-badge status-${action}`;
            statusBadge.textContent = action.charAt(0).toUpperCase() + action.slice(1);
        }
        row.dataset.status = action;

        const actionsCell = row.querySelector('.actions');
        if (actionsCell) {
            actionsCell.innerHTML = '<span>No actions available</span>';
        }
    }

    // --- Filtering and Searching ---
    const filterStatus = document.getElementById('filterStatus');
    const searchShip = document.getElementById('searchShip');

    function filterAndSearch() {
        const statusFilter = filterStatus.value.toLowerCase();
        const searchFilter = searchShip.value.toLowerCase();
        const rows = requestsTable.querySelectorAll('tbody tr');

        rows.forEach(row => {
            const status = row.dataset.status.toLowerCase();
            const shipName = row.cells[3].textContent.toLowerCase();

            const statusMatch = statusFilter === 'all' || status === statusFilter;
            const searchMatch = shipName.includes(searchFilter);

            row.style.display = (statusMatch && searchMatch) ? '' : 'none';
        });
    }

    if (filterStatus) filterStatus.addEventListener('change', filterAndSearch);
    if (searchShip) searchShip.addEventListener('keyup', filterAndSearch);
    
    window.loadRequests = filterAndSearch;
});
