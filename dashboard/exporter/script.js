document.addEventListener('DOMContentLoaded', function() {
    // Smooth scrolling for hero card links
    document.querySelectorAll('.hero-card').forEach(anchor => {
        anchor.addEventListener('click', function (e) {
            e.preventDefault();

            document.querySelector(this.getAttribute('href')).scrollIntoView({
                behavior: 'smooth'
            });
        });
    });

    const uploadForm = document.getElementById('upload-form');
    if (uploadForm) {
        uploadForm.addEventListener('submit', function(event) {
            event.preventDefault();
            const formData = new FormData(this);
            formData.append('action', 'upload_document');

            fetch('exporter_api.php', {
                method: 'POST',
                body: formData
            })
            .then(response => response.json())
            .then(data => {
                if (data.success) {
                    alert('Document uploaded successfully!');
                    uploadForm.reset();
                } else {
                    alert('Failed to upload document: ' + data.message);
                }
            })
            .catch(error => {
                console.error('Error:', error);
                alert('An error occurred while uploading the document.');
            });
        });
    }

    const cargoSearchForm = document.getElementById('cargo-search-form');
    cargoSearchForm.addEventListener('submit', handleCargoSearch);

    const deliverySearchForm = document.getElementById('delivery-search-form');
    deliverySearchForm.addEventListener('submit', handleDeliverySearch);

    // --- Map Functionality ---
    const map = L.map('map').setView([22.3333, 91.8333], 13); // Centered on Chittagong Port
    L.tileLayer('https://{s}.tile.openstreetmap.org/{z}/{x}/{y}.png', {
        attribution: '&copy; <a href="https://www.openstreetmap.org/copyright">OpenStreetMap</a> contributors'
    }).addTo(map);

    const trackShipBtn = document.getElementById('track-ship-btn');
    const shipIdInput = document.getElementById('ship-id-selector');
    const shipInfoDiv = document.getElementById('ship-info');
    let shipMarker;

    trackShipBtn.addEventListener('click', async () => {
        const shipId = shipIdInput.value;
        if (!shipId) {
            alert('Please enter a Ship ID.');
            return;
        }

        try {
            const response = await fetch(`exporter_api.php?action=get_location&ship_id=${shipId}`);
            const result = await response.json();

            if (result.success && result.data) {
                const { latitude, longitude, eta, distance_to_port, last_updated } = result.data;

                if (shipMarker) {
                    map.removeLayer(shipMarker);
                }

                shipMarker = L.marker([latitude, longitude]).addTo(map)
                    .bindPopup(`<b>Ship ID:</b> ${shipId}<br><b>ETA:</b> ${eta}<br><b>Distance to Port:</b> ${distance_to_port} km`)
                    .openPopup();

                map.setView([latitude, longitude], 13);

                shipInfoDiv.innerHTML = `
                    <p><b>Last Updated:</b> ${last_updated}</p>
                `;
                document.getElementById('map').style.display = 'block';
            } else {
                if (shipMarker) {
                    map.removeLayer(shipMarker);
                }
                shipInfoDiv.innerHTML = `<p>No location available for this ship.</p>`;
                document.getElementById('map').style.display = 'none';
            }
        } catch (error) {
            console.error('Error:', error);
            alert('An error occurred while fetching the ship location.');
        }
    });
});

function handleCargoSearch(event) {
    event.preventDefault();
    const searchTerm = document.getElementById('cargo-search').value;
    const resultDiv = document.getElementById('cargo-status-result');

    fetch(`exporter_api.php?action=search_cargo&term=${searchTerm}`)
        .then(response => response.json())
        .then(data => {
            if (data.success && data.data) {
                resultDiv.innerHTML = `<p><strong>Status:</strong> ${data.data.status}</p>`;
            } else {
                resultDiv.innerHTML = `<p>${data.message || 'No status found.'}</p>`;
            }
        })
        .catch(error => {
            console.error('Error:', error);
            resultDiv.innerHTML = '<p>An error occurred while searching.</p>';
        });
}

function handleDeliverySearch(event) {
    event.preventDefault();
    const searchTerm = document.getElementById('delivery-search').value;
    const resultDiv = document.getElementById('delivery-info-result');

    fetch(`exporter_api.php?action=search_delivery_info&term=${searchTerm}`)
        .then(response => response.json())
        .then(data => {
            if (data.success && data.data) {
                const item = data.data;
                resultDiv.innerHTML = `
                    <table>
                        <thead>
                            <tr>
                                <th>Company Name</th>
                                <th>Contact No</th>
                                <th>Container Name</th>
                                <th>Batch Number</th>
                                <th>Address</th>
                                <th>Assigned Transport</th>
                            </tr>
                        </thead>
                        <tbody>
                            <tr>
                                <td>${item.company_name}</td>
                                <td>${item.contact_no}</td>
                                <td>${item.container_name}</td>
                                <td>${item.batch_number}</td>
                                <td>${item.address}</td>
                                <td>${item.assigned_transport}</td>
                            </tr>
                        </tbody>
                    </table>
                `;
            } else {
                resultDiv.innerHTML = `<p>${data.message || 'No delivery info found.'}</p>`;
            }
        })
        .catch(error => {
            console.error('Error:', error);
            resultDiv.innerHTML = '<p>An error occurred while searching.</p>';
        });
}
