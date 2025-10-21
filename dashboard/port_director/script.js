document.addEventListener('DOMContentLoaded', () => {
    const dateInput = document.getElementById('date');
    
    const kpiShipsArriving = document.getElementById('kpi-ships-arriving');
    const kpiShipRequests = document.getElementById('kpi-ship-requests');
    const kpiBerths = document.getElementById('kpi-berths');
    const kpiContainers = document.getElementById('kpi-containers');
    
    const movementsTableBody = document.querySelector('#movementsTable tbody');
    const noticeList = document.getElementById('noticeList');
    const noticeForm = document.getElementById('noticeForm');
    const toast = document.getElementById('toast');

    const API_URL = 'director_api.php';

    const fetchData = async (endpoint, params = {}) => {
        const url = new URL(API_URL, window.location.href);
        url.search = new URLSearchParams(params).toString();
        try {
            const response = await fetch(url);
            if (!response.ok) {
                throw new Error(`HTTP error! status: ${response.status}`);
            }
            return await response.json();
        } catch (error) {
            console.error(`Failed to fetch ${endpoint}:`, error);
            showToast(`Error fetching data for ${endpoint.replace('_', ' ')}.`, 'error');
            return null;
        }
    };

    const updateDashboard = async (date) => {
        updateShipOverview(date);
        updateBerthOccupancy();
        updateContainersInYard();
        updateArrivalsAndDepartures(date);
    };

    const updateShipOverview = async (date) => {
        const data = await fetchData('ship_overview', { action: 'ship_overview', date });
        if (data) {
            kpiShipsArriving.textContent = data.arriving_ships ?? '0';
            kpiShipRequests.textContent = data.ship_requests ?? '0';
        }
    };

    const updateBerthOccupancy = async () => {
        const data = await fetchData('berth_occupancy', { action: 'berth_occupancy' });
        if (data) {
            kpiBerths.textContent = data.occupied_berths ?? '0';
        }
    };

    const updateContainersInYard = async () => {
        const data = await fetchData('containers_in_yard', { action: 'containers_in_yard' });
        if (data) {
            kpiContainers.textContent = data.total_containers ?? '0';
        }
    };

    const updateArrivalsAndDepartures = async (date) => {
        const data = await fetchData('arrivals_departures', { action: 'arrivals_departures', date });
        movementsTableBody.innerHTML = '';
        if (data && data.length > 0) {
            data.forEach(item => {
                const row = document.createElement('tr');
                row.innerHTML = `
                    <td>${item.ship_name}</td>
                    <td>${new Date(item.eta).toLocaleString()}</td>
                    <td>${item.requested_berth || 'N/A'}</td>
                    <td><span class="status status-${item.status.toLowerCase()}">${item.status}</span></td>
                `;
                movementsTableBody.appendChild(row);
            });
        } else {
            movementsTableBody.innerHTML = '<tr><td colspan="4">No arrivals or departures for this date.</td></tr>';
        }
    };

    const fetchAndRenderNotices = async () => {
        const notices = await fetchData('get_notices', { action: 'get_notices' });
        noticeList.innerHTML = '';
        if (notices && notices.length > 0) {
            notices.forEach(notice => {
                const li = document.createElement('li');
                li.innerHTML = `
                    <strong>${notice.title}</strong>
                    <p>${notice.message}</p>
                    <small>${new Date(notice.created_at).toLocaleString()}</small>
                `;
                noticeList.appendChild(li);
            });
        } else {
            noticeList.innerHTML = '<li>No notices found.</li>';
        }
    };

    const handleNoticeSubmit = async (e) => {
        e.preventDefault();
        const formData = new FormData(noticeForm);
        const title = formData.get('title');
        const message = formData.get('message');

        try {
            const response = await fetch(API_URL + '?action=add_notice', {
                method: 'POST',
                headers: { 'Content-Type': 'application/json' },
                body: JSON.stringify({ title, message })
            });
            const result = await response.json();
            if (result.success) {
                showToast('Notice posted successfully.');
                noticeForm.reset();
                fetchAndRenderNotices();
            } else {
                throw new Error(result.error || 'Failed to post notice.');
            }
        } catch (error) {
            console.error('Failed to submit notice:', error);
            showToast(error.message, 'error');
        }
    };
    
    function showToast(message, type = 'success') {
        toast.textContent = message;
        toast.className = `toast show ${type}`;
        setTimeout(() => {
            toast.className = toast.className.replace('show', '');
        }, 3000);
    }

    // Event Listeners
    dateInput.addEventListener('change', () => {
        updateDashboard(dateInput.value);
    });

    noticeForm.addEventListener('submit', handleNoticeSubmit);

    // Initial Load
    updateDashboard(dateInput.value);
    fetchAndRenderNotices();
});
