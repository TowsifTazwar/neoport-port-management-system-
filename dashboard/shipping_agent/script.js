document.addEventListener('DOMContentLoaded', () => {
    const api = 'shipping_agent_api.php';

    const requestsTable = document.getElementById('requests-table')?.querySelector('tbody');
    const invoicesTable = document.getElementById('invoices-table')?.querySelector('tbody');
    const berthSelect = document.getElementById('requested-berth');
    const arrivalForm = document.getElementById('ship-arrival-form');

    // --- Data Fetching and Rendering ---

    async function fetchData(url) {
        try {
            const response = await fetch(url);
            if (!response.ok) {
                // Try to get a more detailed error message from the response body
                const errorData = await response.json().catch(() => null);
                const message = errorData?.message || `HTTP error! Status: ${response.status}`;
                throw new Error(message);
            }
            return await response.json();
        } catch (error) {
            console.error('Fetch error:', error);
            return { success: false, message: error.message };
        }
    }

    function populateRequests(requests) {
        if (!requestsTable) return;
        requestsTable.innerHTML = ''; // Clear existing rows
        if (requests.length === 0) {
            requestsTable.innerHTML = '<tr><td colspan="4">No ship requests found.</td></tr>';
            return;
        }
        requests.forEach(req => {
            const row = `<tr>
                <td>REQ-${String(req.id).padStart(3, '0')}</td>
                <td>${escapeHTML(req.ship_name)}</td>
                <td>${escapeHTML(req.status)}</td>
                <td>${escapeHTML(req.rejection_reason || '')}</td>
            </tr>`;
            requestsTable.insertAdjacentHTML('beforeend', row);
        });
    }

    function populateInvoices(invoices) {
        if (!invoicesTable) return;
        invoicesTable.innerHTML = ''; // Clear existing rows
        if (invoices.length === 0) {
            invoicesTable.innerHTML = '<tr><td colspan="4">No invoices found.</td></tr>';
            return;
        }
        invoices.forEach(inv => {
            const downloadLink = inv.file_path ? `<a href="${escapeHTML(inv.file_path)}" download>Download</a>` : 'N/A';
            const row = `<tr>
                <td>INV-${String(inv.invoice_number).padStart(4, '0')}</td>
                <td>${escapeHTML(inv.amount)}</td>
                <td>${escapeHTML(inv.status)}</td>
                <td>${downloadLink}</td>
            </tr>`;
            invoicesTable.insertAdjacentHTML('beforeend', row);
        });
    }

    function populateBerths(berths) {
        if (!berthSelect) return;
        berths.forEach(berth => {
            const option = new Option(berth.berth_name, berth.id);
            berthSelect.add(option);
        });
    }

    async function loadDashboardData() {
        const [berthData, mainData] = await Promise.all([
            fetchData(`${api}?action=get_berths`),
            fetchData(`${api}?action=get_requests_and_invoices`)
        ]);

        if (berthData.success && Array.isArray(berthData.data)) {
            populateBerths(berthData.data);
        } else {
            console.error('Failed to load berths:', berthData.message || 'Unknown error');
            if (berthSelect) {
                berthSelect.innerHTML = '<option value="" disabled selected>Could not load berths</option>';
            }
        }

        if (mainData.success) {
            populateRequests(mainData.data.requests);
            populateInvoices(mainData.data.invoices);
        } else {
            const errorMessage = mainData.message || 'An unknown error occurred.';
            if (requestsTable) requestsTable.innerHTML = `<tr><td colspan="4">Error loading data: ${escapeHTML(errorMessage)}</td></tr>`;
            if (invoicesTable) invoicesTable.innerHTML = `<tr><td colspan="4">Error loading data: ${escapeHTML(errorMessage)}</td></tr>`;
            console.error('Failed to load requests and invoices:', mainData.message);
        }
    }

    // --- Form Handling ---

    if (arrivalForm) {
        arrivalForm.addEventListener('submit', async (e) => {
            e.preventDefault();
            const formData = new FormData(arrivalForm);
            formData.append('action', 'submit_ship_request');

            try {
                const response = await fetch(api, {
                    method: 'POST',
                    body: formData
                });
                const result = await response.json();

                if (result.success) {
                    alert('Request submitted successfully!');
                    arrivalForm.reset();
                    // Refresh only the requests table for efficiency
                    const updatedData = await fetchData(`${api}?action=get_requests_and_invoices`);
                    if (updatedData.success) {
                        populateRequests(updatedData.data.requests);
                    }
                } else {
                    throw new Error(result.message || 'An unknown error occurred.');
                }
            } catch (error) {
                console.error('Submission error:', error);
                alert(`Failed to submit request: ${error.message}`);
            }
        });
    }

    // --- Utility ---
    
    function escapeHTML(str) {
        const p = document.createElement('p');
        p.appendChild(document.createTextNode(str));
        return p.innerHTML;
    }

    // --- Initial Load ---
    loadDashboardData();

    const updateLocationForm = document.getElementById('update-location-form');
    if (updateLocationForm) {
        updateLocationForm.addEventListener('submit', async (e) => {
            e.preventDefault();
            const formData = new FormData(updateLocationForm);
            formData.append('action', 'update_location');

            try {
                const response = await fetch(api, {
                    method: 'POST',
                    body: formData
                });
                const result = await response.json();

                if (result.success) {
                    alert('Location updated successfully!');
                    updateLocationForm.reset();
                } else {
                    throw new Error(result.message || 'An unknown error occurred.');
                }
            } catch (error) {
                console.error('Update error:', error);
                alert(`Failed to update location: ${error.message}`);
            }
        });
    }
});
